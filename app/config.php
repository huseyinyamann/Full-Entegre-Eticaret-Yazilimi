<?php
// Varsayılan site ayarları. Admin panel > Ayarlar'dan kaydedilenler (settings tablosu) bunları ezer.
return [
    'site_name'   => 'Yaman Ticaret',
    'site_slogan' => 'Aradığın her şey tek adreste',
    'description' => 'Yaman Ticaret — giyim, çanta, ayakkabı ve aksesuarda yeni sezon ürünler, hızlı kargo ve güvenli alışveriş.',
    'base_url'    => '',
    'currency'    => '₺',
    'phone'       => '0850 000 00 00',
    'email'       => 'info@yamanticaret.com',
    'address'     => 'Örnek Mah. Ticaret Cad. No:1, İstanbul',
    'whatsapp'    => '905000000000',
    'free_shipping_limit' => 750,
    'announcements' => [
        '750 ₺ ve üzeri siparişlerde kargo bedava!',
        'Yeni sezon ürünlerinde %30\'a varan indirim',
        '14 gün içinde ücretsiz iade',
    ],
    'bank_accounts' => [
        ['bank' => 'Örnek Bank', 'holder' => 'Yaman Ticaret', 'iban' => 'TR00 0000 0000 0000 0000 0000 00'],
    ],
    'social' => [
        'instagram' => '#',
        'facebook'  => '#',
        'x'         => '#',
        'youtube'   => '#',
    ],
    'db' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => (int) env('DB_PORT', '3306'),
        'name' => env('DB_NAME', 'yaman_eticaret'),
        'user' => env('DB_USER', 'root'),
        'pass' => env('DB_PASS', ''),
    ],
    'session_secure' => env('SESSION_SECURE', '0') === '1',
];
