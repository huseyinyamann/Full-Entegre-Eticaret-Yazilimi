<?php
/**
 * Admin panel çekirdeği: oturum, kimlik doğrulama, CSRF, yetki, yükleme ve yardımcılar.
 */

require_once APP_PATH . '/lib/html_sanitizer.php';

const ADMIN_IDLE_TIMEOUT = 2 * 3600;      // 2 saat işlem yapılmazsa oturum düşer
const ADMIN_ABSOLUTE_TIMEOUT = 12 * 3600; // en fazla 12 saat
const LOGIN_MAX_PER_EMAIL = 5;            // 15 dk içinde e-posta başına
const LOGIN_MAX_PER_IP = 20;              // 15 dk içinde IP başına
const UPLOAD_MAX_BYTES = 5 * 1024 * 1024;

const ROLE_LABELS = ['owner' => 'Mağaza Sahibi', 'admin' => 'Yönetici', 'editor' => 'Editör'];

/** Hangi rol hangi bölüme erişebilir */
const PERMISSIONS = [
    'dashboard' => ['owner', 'admin', 'editor'],
    'products' => ['owner', 'admin', 'editor'],
    'categories' => ['owner', 'admin', 'editor'],
    'content' => ['owner', 'admin', 'editor'],
    'orders' => ['owner', 'admin'],
    'customers' => ['owner', 'admin'],
    'settings' => ['owner', 'admin'],
    'admins' => ['owner'],
    'logs' => ['owner'],
];

function admin_boot(): void
{
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
    header('Cache-Control: no-store, max-age=0');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; "
        . "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; "
        . "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; img-src 'self' data: https://picsum.photos https://fastly.picsum.photos; "
        . "connect-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('yt_admin');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => url('admin'),
        'secure' => (bool) config('session_secure'),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
    }
}

// ---------------------------------------------------------------------------
// Kimlik doğrulama
// ---------------------------------------------------------------------------

function current_admin(): ?array
{
    static $admin = false;
    if ($admin !== false) return $admin;

    $id = $_SESSION['admin_id'] ?? null;
    if (!$id) return $admin = null;

    $now = time();
    $expired = $now - ($_SESSION['last_seen'] ?? 0) > ADMIN_IDLE_TIMEOUT
        || $now - ($_SESSION['login_at'] ?? 0) > ADMIN_ABSOLUTE_TIMEOUT
        || !hash_equals($_SESSION['ua'] ?? '', hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? ''));
    $row = $expired ? null : db_one('SELECT id, name, email, role, is_active FROM admins WHERE id = ?', [$id]);

    if (!$row || !$row['is_active']) {
        admin_logout();
        return $admin = null;
    }
    $_SESSION['last_seen'] = $now;
    return $admin = $row;
}

function attempt_login(string $email, string $password): ?string
{
    $email = mb_strtolower(trim($email));
    $ip = client_ip();

    if (too_many_attempts('admin', $email, LOGIN_MAX_PER_EMAIL, LOGIN_MAX_PER_IP)) {
        return 'Çok fazla hatalı deneme yapıldı. Lütfen 15 dakika sonra tekrar deneyin.';
    }

    $admin = db_one('SELECT id, password_hash, is_active FROM admins WHERE email = ?', [$email]);
    $hash = $admin['password_hash'] ?? DUMMY_PASSWORD_HASH;
    $ok = password_verify($password, $hash) && $admin && $admin['is_active'];

    record_attempt('admin', $email, $ok);
    if (!$ok) {
        return 'E-posta veya şifre hatalı.';
    }

    if (password_needs_rehash($hash, PASSWORD_ARGON2ID)) {
        db_run('UPDATE admins SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_ARGON2ID), $admin['id']]);
    }

    session_regenerate_id(true); // session fixation koruması
    $_SESSION = [
        'admin_id' => (int) $admin['id'],
        'login_at' => time(),
        'last_seen' => time(),
        'ua' => hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? ''),
        'csrf' => bin2hex(random_bytes(32)),
    ];
    db_run('UPDATE admins SET last_login_at = NOW(), last_login_ip = ? WHERE id = ?', [$ip, $admin['id']]);
    audit('login', 'admin', (int) $admin['id'], 'Panele giriş yapıldı');
    return null;
}

function admin_logout(): void
{
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => $p['path'], 'secure' => $p['secure'], 'httponly' => true, 'samesite' => 'Strict']);
        session_destroy();
    }
}

function can(string $permission): bool
{
    $admin = current_admin();
    return $admin && in_array($admin['role'], PERMISSIONS[$permission] ?? [], true);
}

function require_permission(string $permission): void
{
    if (!can($permission)) {
        http_response_code(403);
        admin_render('error', ['title' => 'Yetkisiz erişim', 'message' => 'Bu bölümü görüntüleme yetkiniz yok.']);
        exit;
    }
}

// ---------------------------------------------------------------------------
// Yardımcılar
// ---------------------------------------------------------------------------

function admin_url(string $path = ''): string
{
    return url('admin' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
}

function redirect(string $adminPath): never
{
    header('Location: ' . admin_url($adminPath));
    exit;
}

function input_int(string $key, ?int $default = null): ?int
{
    $v = input($key);
    return $v === '' || !preg_match('/^-?\d+$/', $v) ? $default : (int) $v;
}

/** "1.299,90" veya "1299.90" biçimlerini kabul eder */
function input_money(string $key): ?float
{
    $v = str_replace([' ', '₺'], '', input($key));
    if ($v === '') return null;
    if (str_contains($v, ',')) $v = str_replace(['.', ','], ['', '.'], $v);
    return is_numeric($v) && (float) $v >= 0 ? round((float) $v, 2) : null;
}

function input_bool(string $key): int
{
    return isset($_POST[$key]) ? 1 : 0;
}

function query(string $key, string $default = ''): string
{
    $v = $_GET[$key] ?? $default;
    return is_string($v) ? trim($v) : $default;
}

/** Tabloda benzersiz slug üretir (aynısı varsa -2, -3 … ekler) */
function unique_slug(string $table, string $text, ?int $ignoreId = null): string
{
    $base = slugify($text) ?: 'kayit';
    $slug = $base;
    for ($i = 2; db_value("SELECT id FROM `$table` WHERE slug = ? AND id <> ?", [$slug, $ignoreId ?? 0]); $i++) {
        $slug = $base . '-' . $i;
    }
    return $slug;
}

function audit(string $action, string $entity, ?int $entityId = null, ?string $summary = null): void
{
    db_insert('admin_logs', [
        'admin_id' => $_SESSION['admin_id'] ?? null,
        'action' => $action,
        'entity' => $entity,
        'entity_id' => $entityId,
        'summary' => $summary !== null ? mb_substr($summary, 0, 255) : null,
        'ip' => client_ip(),
    ]);
}

function paginate(string $countSql, array $params, int $perPage = 20): array
{
    $total = (int) db_value($countSql, $params);
    $pages = max(1, (int) ceil($total / $perPage));
    $page = min($pages, max(1, (int) query('sayfa', '1')));
    return ['total' => $total, 'page' => $page, 'pages' => $pages, 'limit' => $perPage, 'offset' => ($page - 1) * $perPage];
}

/** Mevcut sorgu parametrelerini koruyarak link üretir */
function qs(array $override = []): string
{
    $params = array_filter(array_merge($_GET, $override), fn($v) => $v !== '' && $v !== null);
    return $params ? '?' . http_build_query($params) : '';
}

function status_badge(string $status): string
{
    [$label, $class] = ORDER_STATUSES[$status] ?? [$status, 'secondary'];
    return '<span class="status status--' . e($class) . '">' . e($label) . '</span>';
}

const BADGES = ['yeni' => 'Yeni', 'indirim' => 'İndirim', 'cok-satan' => 'Çok Satan'];

// ---------------------------------------------------------------------------
// Görsel yükleme
// ---------------------------------------------------------------------------

/**
 * Yüklenen görseli doğrular ve GD ile yeniden kodlayarak kaydeder.
 * Yeniden kodlama, görselin içine gizlenmiş PHP/HTML yüklerini (polyglot) temizler.
 * Başarılıysa uploads/ altındaki göreli yolu, değilse null döner; hata $error'a yazılır.
 */
function upload_image(array $file, string $folder, ?string &$error = null): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        $error = 'Dosya yüklenemedi.';
        return null;
    }
    if ($file['size'] > UPLOAD_MAX_BYTES) {
        $error = 'Görsel en fazla 5 MB olabilir.';
        return null;
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $info = @getimagesize($file['tmp_name']);
    if (!isset($types[$mime]) || !$info || $info[0] > 8000 || $info[1] > 8000) {
        $error = 'Yalnızca JPG, PNG veya WEBP görsel yükleyebilirsiniz.';
        return null;
    }

    $image = @imagecreatefromstring((string) file_get_contents($file['tmp_name']));
    if (!$image) {
        $error = 'Görsel okunamadı.';
        return null;
    }

    $relDir = trim($folder, '/') . '/' . date('Y/m');
    $absDir = PUBLIC_PATH . '/uploads/' . $relDir;
    if (!is_dir($absDir) && !mkdir($absDir, 0755, true) && !is_dir($absDir)) {
        $error = 'Yükleme klasörü oluşturulamadı.';
        return null;
    }
    $ext = $types[$mime];
    $name = bin2hex(random_bytes(12)) . '.' . $ext;
    $target = $absDir . '/' . $name;

    $saved = match ($ext) {
        'jpg' => imagejpeg($image, $target, 86),
        'png' => (imagesavealpha($image, true) && imagepng($image, $target, 6)),
        'webp' => (imagesavealpha($image, true) && imagewebp($image, $target, 85)),
    };
    imagedestroy($image);
    if (!$saved) {
        $error = 'Görsel kaydedilemedi.';
        return null;
    }
    return $relDir . '/' . $name;
}

/** Panelden yüklenmiş bir dosyayı siler; uploads/ dışına veya demo görsellerine dokunmaz. */
function delete_upload(?string $path): void
{
    if (!$path || str_starts_with($path, 'demo/')) return;
    $base = realpath(PUBLIC_PATH . '/uploads');
    $file = realpath(PUBLIC_PATH . '/uploads/' . $path);
    if ($base && $file && str_starts_with($file, $base . DIRECTORY_SEPARATOR) && is_file($file)) {
        unlink($file);
    }
}

// ---------------------------------------------------------------------------
// Görünüm
// ---------------------------------------------------------------------------

function admin_render(string $view, array $vars = []): void
{
    $vars += ['title' => 'Panel', 'active' => ''];
    extract($vars);
    require APP_PATH . '/admin/partials/layout-top.php';
    require APP_PATH . '/admin/views/' . $view . '.php';
    require APP_PATH . '/admin/partials/layout-bottom.php';
}

function admin_partial(string $name, array $vars = []): void
{
    extract($vars);
    require APP_PATH . '/admin/partials/' . $name . '.php';
}

function admin_asset(string $file): string
{
    return asset('admin/' . $file);
}

function selected(bool $cond): string
{
    return $cond ? ' selected' : '';
}

function checked(bool $cond): string
{
    return $cond ? ' checked' : '';
}

/** Tutarı form alanında Türkçe biçimde gösterir (1299.9 → 1299,90) */
function money_input(mixed $value): string
{
    return is_numeric($value) ? number_format((float) $value, 2, ',', '') : '';
}

