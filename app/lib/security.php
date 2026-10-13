<?php
/**
 * Vitrin ve yönetim paneli için ortak güvenlik / form yardımcıları.
 * Hangi oturum (yt_session veya yt_admin) açıksa onun üzerinde çalışır.
 */

function client_ip(): string
{
    // Proxy arkasındaysanız güvendiğiniz proxy'nin başlığını burada okuyun; X-Forwarded-For doğrudan güvenilmez.
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// ---------------------------------------------------------------------------
// CSRF
// ---------------------------------------------------------------------------

function csrf_token(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    // Tarayıcı Origin gönderiyorsa kendi sitemiz olmalı
    $origin = $_SERVER['HTTP_ORIGIN'] ?? null;
    if ($origin !== null && parse_url($origin, PHP_URL_HOST) !== parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST)) {
        csrf_fail();
    }
    $token = $_POST['_csrf'] ?? '';
    if (!is_string($token) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
        csrf_fail();
    }
}

function csrf_fail(): never
{
    http_response_code(419);
    exit('Oturum doğrulaması başarısız (CSRF). Sayfayı yenileyip tekrar deneyin.');
}

// ---------------------------------------------------------------------------
// Deneme sınırı (brute-force koruması)
// ---------------------------------------------------------------------------

/** Son $minutes dakikada e-posta veya IP başına başarısız deneme sınırı aşıldı mı? */
function too_many_attempts(string $scope, string $email, int $maxPerEmail, int $maxPerIp, int $minutes = 15): bool
{
    $since = 'attempted_at > NOW() - INTERVAL ' . (int) $minutes . ' MINUTE';
    $byEmail = $email === '' ? 0 : (int) db_value("SELECT COUNT(*) FROM login_attempts WHERE scope = ? AND email = ? AND success = 0 AND $since", [$scope, $email]);
    $byIp = (int) db_value("SELECT COUNT(*) FROM login_attempts WHERE scope = ? AND ip = ? AND success = 0 AND $since", [$scope, client_ip()]);
    return $byEmail >= $maxPerEmail || $byIp >= $maxPerIp;
}

function record_attempt(string $scope, string $email, bool $success): void
{
    db_insert('login_attempts', ['scope' => $scope, 'ip' => client_ip(), 'email' => mb_substr($email, 0, 190), 'success' => $success ? 1 : 0]);
    if (mt_rand(1, 50) === 1) {
        db_run('DELETE FROM login_attempts WHERE attempted_at < NOW() - INTERVAL 30 DAY');
    }
}

/** Kullanıcı yokken de hash doğrulaması yapılır; yanıt süresinden e-postanın varlığı anlaşılmasın */
const DUMMY_PASSWORD_HASH = '$argon2id$v=19$m=65536,t=4,p=1$ZFp4S0p6OFIybDBhSWZPRQ$7XP7vQq1RDrtahM6IIggtPN/FWWPmAwGtVTyd4bRh5c';

/** Şifre politikası: en az $min karakter, harf ve rakam içermeli. Sorun yoksa null döner. */
function password_problem(string $password, int $min = 10): ?string
{
    if (mb_strlen($password) < $min) return "Şifre en az $min karakter olmalı.";
    if (mb_strlen($password) > 200) return 'Şifre çok uzun.';
    if (!preg_match('/\pL/u', $password) || !preg_match('/\d/', $password)) return 'Şifre en az bir harf ve bir rakam içermeli.';
    return null;
}

// ---------------------------------------------------------------------------
// Form / istek yardımcıları
// ---------------------------------------------------------------------------

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = [$type, $message];
}

function take_flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function input(string $key, string $default = ''): string
{
    $v = $_POST[$key] ?? $default;
    return is_string($v) ? trim($v) : $default;
}

function invalid(array $errors, string $key): string
{
    return isset($errors[$key]) ? ' is-invalid' : '';
}

function error_text(array $errors, string $key): string
{
    return isset($errors[$key]) ? '<div class="invalid-feedback d-block">' . e($errors[$key]) . '</div>' : '';
}

function fmt_date(?string $dt, bool $withTime = true): string
{
    return $dt ? date($withTime ? 'd.m.Y H:i' : 'd.m.Y', strtotime($dt)) : '—';
}

/** Telefonu yalnızca rakam ve + bırakarak doğrular; geçersizse null */
function clean_phone(string $phone): ?string
{
    $digits = preg_replace('/[^\d+]/', '', $phone);
    return preg_match('/^\+?\d{10,14}$/', $digits) ? $phone : null;
}
