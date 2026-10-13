<?php
/**
 * Müşteri oturumu: kayıt, giriş, çıkış.
 *
 * Oturum çerezi (yt_session) yalnızca gerektiğinde açılır: giriş/kayıt/ödeme/hesabım sayfalarında
 * ya da tarayıcı zaten çerez gönderiyorsa. Böylece sıradan ziyaretçilere gereksiz çerez yazılmaz.
 */

const CUSTOMER_SESSION = 'yt_session';
const CUSTOMER_IDLE_TIMEOUT = 24 * 3600;          // "beni hatırla" yoksa 1 gün işlem yapılmazsa düşer
const CUSTOMER_REMEMBER_TIMEOUT = 30 * 24 * 3600; // "beni hatırla" ile 30 gün
const CUSTOMER_LOGIN_MAX_PER_EMAIL = 5;           // 15 dk içinde
const CUSTOMER_LOGIN_MAX_PER_IP = 30;
const CUSTOMER_REGISTER_MAX_PER_IP = 5;           // 60 dk içinde
const CUSTOMER_PASSWORD_MIN = 8;

function store_session(bool $start = true): bool
{
    if (session_status() === PHP_SESSION_ACTIVE) return true;
    if (!$start && empty($_COOKIE[CUSTOMER_SESSION])) return false;

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.gc_maxlifetime', (string) CUSTOMER_REMEMBER_TIMEOUT);
    session_name(CUSTOMER_SESSION);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => url(),
        'secure' => (bool) config('session_secure'),
        'httponly' => true,
        'samesite' => 'Lax', // dış sitelerden gelen linklerle girişli kalabilmek için Lax; POST'lar CSRF token ister
    ]);
    session_start();
    return true;
}

/** "Beni hatırla" seçildiyse çerezi tarayıcı kapansa da 30 gün sakla */
function persist_session_cookie(): void
{
    if (empty($_SESSION['remember'])) return;
    $p = session_get_cookie_params();
    setcookie(session_name(), session_id(), [
        'expires' => time() + CUSTOMER_REMEMBER_TIMEOUT,
        'path' => $p['path'], 'secure' => $p['secure'], 'httponly' => true, 'samesite' => 'Lax',
    ]);
}

/** Şifre değişince diğer cihazlardaki oturumların düşmesi için hash'ten türetilen sürüm etiketi */
function password_version(?string $hash): string
{
    return substr(hash('sha256', (string) $hash), 0, 20);
}

function current_customer(): ?array
{
    static $customer = false;
    if ($customer !== false) return $customer;

    if (!store_session(false) || empty($_SESSION['customer_id'])) return $customer = null;

    $now = time();
    $limit = empty($_SESSION['remember']) ? CUSTOMER_IDLE_TIMEOUT : CUSTOMER_REMEMBER_TIMEOUT;
    $expired = $now - ($_SESSION['last_seen'] ?? 0) > $limit
        || !hash_equals($_SESSION['ua'] ?? '', hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? ''));
    $row = $expired ? null : db_one('SELECT id, first_name, last_name, email, phone, password_hash, is_active, created_at FROM customers WHERE id = ?', [$_SESSION['customer_id']]);

    if (!$row || !$row['is_active'] || !hash_equals($_SESSION['pv'] ?? '', password_version($row['password_hash']))) {
        customer_logout();
        return $customer = null;
    }
    $_SESSION['last_seen'] = $now;
    unset($row['password_hash']);
    $row['name'] = $row['first_name'] . ' ' . $row['last_name'];
    return $customer = $row;
}

/** Oturumu müşteri için yeniden kurar (giriş ve kayıt sonrası) */
function start_customer_session(int $id, string $passwordHash, bool $remember): void
{
    $flash = $_SESSION['flash'] ?? [];
    session_regenerate_id(true); // session fixation koruması
    $_SESSION = [
        'customer_id' => $id,
        'pv' => password_version($passwordHash),
        'remember' => $remember,
        'last_seen' => time(),
        'ua' => hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? ''),
        'csrf' => bin2hex(random_bytes(32)),
        'flash' => $flash,
    ];
    persist_session_cookie();
    db_run('UPDATE customers SET last_login_at = NOW() WHERE id = ?', [$id]);
}

function customer_login(string $email, string $password, bool $remember): ?string
{
    $email = mb_strtolower(trim($email));
    if (too_many_attempts('customer', $email, CUSTOMER_LOGIN_MAX_PER_EMAIL, CUSTOMER_LOGIN_MAX_PER_IP)) {
        return 'Çok fazla hatalı deneme yapıldı. Lütfen 15 dakika sonra tekrar deneyin.';
    }

    $row = db_one('SELECT id, password_hash, is_active FROM customers WHERE email = ?', [$email]);
    // Üyeliği olmayan (şifresiz) müşteri kaydı da "hatalı" sayılır; hangi e-postanın kayıtlı olduğu sızmasın
    $hash = $row['password_hash'] ?? DUMMY_PASSWORD_HASH;
    $ok = password_verify($password, $hash) && $row && $row['password_hash'] && $row['is_active'];

    record_attempt('customer', $email, $ok);
    if (!$ok) return 'E-posta veya şifre hatalı.';

    if (password_needs_rehash($hash, PASSWORD_ARGON2ID)) {
        $hash = password_hash($password, PASSWORD_ARGON2ID);
        db_run('UPDATE customers SET password_hash = ? WHERE id = ?', [$hash, $row['id']]);
    }
    start_customer_session((int) $row['id'], $hash, $remember);
    return null;
}

function customer_logout(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) return;
    $_SESSION = [];
    $p = session_get_cookie_params();
    setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => $p['path'], 'secure' => $p['secure'], 'httponly' => true, 'samesite' => 'Lax']);
    session_destroy();
}

/** Giriş gerektiren sayfalar için; girişli değilse giriş sayfasına, dönüş adresiyle yönlendirir */
function require_customer(): array
{
    $c = current_customer();
    if (!$c) {
        store_session();
        flash('info', 'Devam etmek için giriş yapın.');
        go('giris?donus=' . rawurlencode(current_path()));
    }
    return $c;
}

/** Hesap simgesinin gideceği adres */
function account_url(): string
{
    return url(current_customer() ? 'hesabim' : 'giris');
}

/** Girişten sonra dönülecek iç adres; dış site / protokol içeren değerler reddedilir (open redirect koruması) */
function safe_return(?string $path, string $default = 'hesabim'): string
{
    $path = (string) $path;
    return preg_match('#^[a-z0-9][a-z0-9\-/]*$#', $path) ? $path : $default;
}

function current_path(): string
{
    $path = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    $base = trim((string) config('base_url'), '/');
    return $base !== '' && str_starts_with($path, $base) ? trim(substr($path, strlen($base)), '/') : $path;
}

function go(string $path): never
{
    header('Location: ' . url($path));
    exit;
}
