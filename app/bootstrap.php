<?php
declare(strict_types=1);

mb_internal_encoding('UTF-8');
date_default_timezone_set('Europe/Istanbul');

define('APP_PATH', __DIR__);
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');

require APP_PATH . '/lib/env.php';
load_env(ROOT_PATH . '/.env');

$config = require APP_PATH . '/config.php';
require APP_PATH . '/helpers.php';
require APP_PATH . '/lib/db.php';
require APP_PATH . '/lib/security.php';
require APP_PATH . '/lib/shop.php';
require APP_PATH . '/lib/customer.php';
require APP_PATH . '/data/repository.php';

// Panelden kaydedilen ayarlar config.php'deki varsayılanları ezer
try {
    $config = array_replace($config, settings_all());
} catch (PDOException $e) {
    http_response_code(503);
    error_log('Veritabanı bağlantısı kurulamadı: ' . $e->getMessage());
    exit(PHP_SAPI === 'cli'
        ? "Veritabanına bağlanılamadı. .env ayarlarını kontrol edip `php bin/install.php` çalıştırın.\n"
        : '<h1 style="font-family:sans-serif">Site bakımda</h1><p style="font-family:sans-serif">Veritabanına bağlanılamadı.</p>');
}
