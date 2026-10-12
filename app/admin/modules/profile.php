<?php

$me = current_admin();
$errors = [];

if (is_post()) {
    $name = mb_substr(input('name'), 0, 100);
    $current = is_string($_POST['current_password'] ?? null) ? $_POST['current_password'] : '';
    $new = is_string($_POST['new_password'] ?? null) ? $_POST['new_password'] : '';
    $confirm = is_string($_POST['new_password_confirm'] ?? null) ? $_POST['new_password_confirm'] : '';

    if ($name === '') $errors['name'] = 'Ad zorunludur.';

    $data = ['name' => $name];
    if ($new !== '') {
        $hash = db_value('SELECT password_hash FROM admins WHERE id = ?', [$me['id']]);
        if (!password_verify($current, $hash)) {
            $errors['current_password'] = 'Mevcut şifre hatalı.';
        } elseif ($err = password_problem($new)) {
            $errors['new_password'] = $err;
        } elseif (!hash_equals($new, $confirm)) {
            $errors['new_password_confirm'] = 'Şifreler eşleşmiyor.';
        } elseif (password_verify($new, $hash)) {
            $errors['new_password'] = 'Yeni şifre mevcut şifreyle aynı olamaz.';
        } else {
            $data['password_hash'] = password_hash($new, PASSWORD_ARGON2ID);
        }
    }

    if (!$errors) {
        db_update('admins', $data, (int) $me['id']);
        if (isset($data['password_hash'])) {
            session_regenerate_id(true);
            audit('update', 'admin', (int) $me['id'], 'Şifre değiştirildi');
        }
        flash('success', isset($data['password_hash']) ? 'Profil ve şifre güncellendi.' : 'Profil güncellendi.');
        redirect('profil');
    }
    $me['name'] = $name;
}

admin_render('profile', ['title' => 'Profilim', 'active' => 'profil', 'me' => $me, 'errors' => $errors]);
