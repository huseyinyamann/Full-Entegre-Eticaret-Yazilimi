<?php
// PHP yerleşik sunucusu için: php -S 127.0.0.1:8092 -t public public/router.php
$file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (is_file($file)) {
    return false;
}
require __DIR__ . '/index.php';
