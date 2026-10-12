<?php

if (current_admin()) {
    redirect('');
}

$error = null;
$email = '';
if (is_post()) {
    $email = input('email');
    $error = attempt_login($email, input('password'));
    if ($error === null) {
        redirect('');
    }
}

require APP_PATH . '/admin/views/login.php';
