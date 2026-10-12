<?php
/**
 * Kurulum: veritabanını oluşturur, şemayı yükler, demo veriyi ve ilk yöneticiyi ekler.
 *
 *   php bin/install.php                 # boş veritabanına kurar
 *   php bin/install.php --fresh         # veritabanını SİLİP baştan kurar
 *   php bin/install.php --no-demo       # demo ürün/sipariş olmadan kurar
 *   php bin/install.php --email=a@b.com --password=GucluSifre123
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

define('APP_PATH', dirname(__DIR__) . '/app');
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
mb_internal_encoding('UTF-8');
date_default_timezone_set('Europe/Istanbul');

require APP_PATH . '/lib/env.php';
load_env(ROOT_PATH . '/.env');
$config = require APP_PATH . '/config.php';
require APP_PATH . '/helpers.php';
require APP_PATH . '/lib/db.php';
require APP_PATH . '/lib/html_sanitizer.php';

$opts = getopt('', ['fresh', 'no-demo', 'email:', 'password:', 'name:']);
$adminEmail = $opts['email'] ?? 'admin@yamanticaret.com';
$adminPass = $opts['password'] ?? 'Yaman.2026!';
$adminName = $opts['name'] ?? 'Site Yöneticisi';

if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) exit("Geçersiz e-posta.\n");
if (mb_strlen($adminPass) < 10) exit("Şifre en az 10 karakter olmalı.\n");

$c = config('db');
if (!preg_match('/^[A-Za-z0-9_]+$/', $c['name'])) exit("DB_NAME yalnızca harf, rakam ve _ içerebilir.\n");

$server = new PDO(sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $c['host'], $c['port']), $c['user'], $c['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
if (isset($opts['fresh'])) {
    $server->exec("DROP DATABASE IF EXISTS `{$c['name']}`");
    echo "• `{$c['name']}` silindi\n";
}
$server->exec("CREATE DATABASE IF NOT EXISTS `{$c['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

if (db_value('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ?', [$c['name']]) > 0) {
    exit("`{$c['name']}` veritabanında zaten tablo var. Baştan kurmak için --fresh kullanın.\n");
}

foreach (array_filter(array_map('trim', explode(';', file_get_contents(ROOT_PATH . '/database/schema.sql')))) as $sql) {
    if (preg_match('/^\s*(--.*\n\s*)*$/', $sql)) continue;
    db()->exec($sql);
}
echo "• Şema yüklendi\n";

$adminId = db_insert('admins', [
    'name' => $adminName,
    'email' => mb_strtolower($adminEmail),
    'password_hash' => password_hash($adminPass, PASSWORD_ARGON2ID),
    'role' => 'owner',
]);
db_insert('themes', ['name' => 'Varsayılan Tema', 'slug' => 'default', 'is_active' => 1]);
echo "• Yönetici oluşturuldu\n";

if (!isset($opts['no-demo'])) {
    require ROOT_PATH . '/database/seeds/demo.php';
    seed_demo();
}

echo "\nKurulum tamam.\n";
echo "  Panel : /admin\n";
echo "  E-posta: {$adminEmail}\n";
echo "  Şifre  : {$adminPass}\n";
echo "  (İlk girişten sonra Profilim sayfasından şifreyi değiştirin.)\n";
