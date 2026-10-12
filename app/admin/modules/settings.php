<?php

$errors = [];

if (is_post()) {
    $values = [
        'site_name' => mb_substr(input('site_name'), 0, 80),
        'site_slogan' => mb_substr(input('site_slogan'), 0, 160),
        'description' => mb_substr(input('description'), 0, 300),
        'phone' => mb_substr(input('phone'), 0, 30),
        'email' => mb_substr(input('email'), 0, 190),
        'address' => mb_substr(input('address'), 0, 300),
        'whatsapp' => preg_replace('/\D/', '', input('whatsapp')),
        'free_shipping_limit' => input_money('free_shipping_limit') ?? 0,
        'announcements' => array_values(array_filter(array_map(fn($l) => mb_substr(trim($l), 0, 160), explode("\n", input('announcements'))))),
        'social' => [],
        'bank_accounts' => [],
    ];

    if ($values['site_name'] === '') $errors['site_name'] = 'Mağaza adı zorunludur.';
    if ($values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Geçerli bir e-posta girin.';

    foreach (['instagram', 'facebook', 'x', 'youtube'] as $net) {
        $link = input('social_' . $net);
        if ($link !== '' && $link !== '#' && !preg_match('#^https://#i', $link)) {
            $errors['social_' . $net] = 'Bağlantı https:// ile başlamalı.';
        }
        $values['social'][$net] = $link !== '' ? mb_substr($link, 0, 255) : '#';
    }

    foreach (explode("\n", input('bank_accounts')) as $n => $line) {
        if (trim($line) === '') continue;
        $parts = array_map('trim', explode('|', $line));
        if (count($parts) !== 3 || in_array('', $parts, true)) {
            $errors['bank_accounts'] = ($n + 1) . '. satır “Banka | Hesap sahibi | IBAN” biçiminde olmalı.';
            continue;
        }
        $values['bank_accounts'][] = ['bank' => mb_substr($parts[0], 0, 80), 'holder' => mb_substr($parts[1], 0, 120), 'iban' => mb_substr($parts[2], 0, 40)];
    }

    if (!$errors) {
        foreach ($values as $key => $value) {
            setting_save($key, $value);
        }
        audit('update', 'settings', null, 'Mağaza ayarları güncellendi');
        flash('success', 'Ayarlar kaydedildi.');
        redirect('ayarlar');
    }
    $s = array_merge($GLOBALS['config'], $values);
} else {
    $s = $GLOBALS['config'];
}

admin_render('settings', ['title' => 'Ayarlar', 'active' => 'ayarlar', 's' => $s, 'errors' => $errors]);
