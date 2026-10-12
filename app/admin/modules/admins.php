<?php

$me = current_admin();

if ($action === 'index') {
    $admins = db_all("SELECT id, name, email, role, is_active, last_login_at, last_login_ip, created_at FROM admins ORDER BY FIELD(role, 'owner', 'admin', 'editor'), name");
    admin_render('admins/index', ['title' => 'Yöneticiler', 'active' => 'yoneticiler', 'admins' => $admins]);
    return;
}

$admin = null;
if ($id !== null) {
    $admin = db_one('SELECT id, name, email, role, is_active FROM admins WHERE id = ?', [$id]);
    if (!$admin) redirect('yoneticiler');
}
$activeOwners = (int) db_value("SELECT COUNT(*) FROM admins WHERE role = 'owner' AND is_active = 1");
$isLastOwner = $admin && $admin['role'] === 'owner' && $admin['is_active'] && $activeOwners <= 1;

if ($action === 'delete') {
    if ($id === (int) $me['id'] || $isLastOwner) {
        flash('danger', $id === (int) $me['id'] ? 'Kendi hesabınızı silemezsiniz.' : 'Son mağaza sahibi hesabı silinemez.');
        redirect('yoneticiler/' . $id);
    }
    db_run('DELETE FROM admins WHERE id = ?', [$id]);
    audit('delete', 'admin', $id, $admin['email']);
    flash('success', 'Yönetici silindi.');
    redirect('yoneticiler');
}

$errors = [];
if (is_post()) {
    $data = [
        'name' => mb_substr(input('name'), 0, 100),
        'email' => mb_strtolower(mb_substr(input('email'), 0, 190)),
        'role' => array_key_exists(input('role'), ROLE_LABELS) ? input('role') : 'editor',
        'is_active' => input_bool('is_active'),
    ];
    $password = $_POST['password'] ?? '';
    $password = is_string($password) ? $password : '';

    if ($data['name'] === '') $errors['name'] = 'Ad zorunludur.';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Geçerli bir e-posta girin.';
    } elseif (db_value('SELECT id FROM admins WHERE email = ? AND id <> ?', [$data['email'], $id ?? 0])) {
        $errors['email'] = 'Bu e-posta ile kayıtlı bir yönetici zaten var.';
    }
    if (!$id || $password !== '') {
        if ($err = password_problem($password)) $errors['password'] = $err;
    }
    // Kendini kilitlemeyi ve sahipsiz mağazayı engelle
    if ($id === (int) $me['id'] && (!$data['is_active'] || $data['role'] !== $me['role'])) {
        $errors['role'] = 'Kendi rolünüzü veya durumunuzu değiştiremezsiniz.';
    }
    if ($isLastOwner && ($data['role'] !== 'owner' || !$data['is_active'])) {
        $errors['role'] = 'Mağazanın en az bir aktif sahibi olmalı.';
    }

    if ($errors) {
        $admin = array_merge($admin ?? [], $data);
    } else {
        if ($password !== '') $data['password_hash'] = password_hash($password, PASSWORD_ARGON2ID);
        if ($id) {
            db_update('admins', $data, $id);
            audit('update', 'admin', $id, $data['email'] . ($password !== '' ? ' (şifre değişti)' : ''));
        } else {
            $id = db_insert('admins', $data);
            audit('create', 'admin', $id, $data['email']);
        }
        flash('success', 'Yönetici kaydedildi.');
        redirect('yoneticiler/' . $id);
    }
}

$admin ??= ['name' => '', 'email' => '', 'role' => 'editor', 'is_active' => 1];

admin_render('admins/form', [
    'title' => $id ? 'Yöneticiyi Düzenle' : 'Yeni Yönetici',
    'active' => 'yoneticiler',
    'id' => $id,
    'admin' => $admin,
    'errors' => $errors,
    'isSelf' => $id === (int) $me['id'],
]);
