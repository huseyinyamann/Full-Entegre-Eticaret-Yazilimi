<?php
declare(strict_types=1);

mb_internal_encoding('UTF-8');
date_default_timezone_set('Europe/Istanbul');

define('APP_PATH', __DIR__);
define('PUBLIC_PATH', dirname(__DIR__) . '/public');

$config = require APP_PATH . '/config.php';
require APP_PATH . '/helpers.php';
require APP_PATH . '/data/demo.php';
