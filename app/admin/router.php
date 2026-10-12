<?php
/**
 * /admin altındaki tüm istekler buradan geçer.
 *   /admin/{bölüm}               liste
 *   /admin/{bölüm}/yeni          ekleme formu
 *   /admin/{bölüm}/{id}          düzenleme / detay
 *   /admin/{bölüm}/{id}/sil      silme (yalnızca POST)
 */

require APP_PATH . '/admin/core.php';
admin_boot();

$section = $seg[1] ?? '';
$id = isset($seg[2]) && ctype_digit($seg[2]) ? (int) $seg[2] : null;
$action = match (true) {
    ($seg[2] ?? '') === 'yeni' => 'new',
    $id !== null && ($seg[3] ?? '') === 'sil' => 'delete',
    $id !== null && !isset($seg[3]) => 'edit',
    !isset($seg[2]) => 'index',
    default => null,
};

if ($section === 'giris') {
    require APP_PATH . '/admin/modules/auth.php';
    exit;
}

if (!current_admin()) {
    redirect('giris');
}

if ($section === 'cikis') {
    if (is_post()) {
        audit('logout', 'admin', (int) $_SESSION['admin_id']);
        admin_logout();
    }
    redirect('giris');
}

if ($action === 'delete' && !is_post()) {
    http_response_code(405);
    exit;
}

// bölüm => [modül dosyası, yetki]
$modules = [
    '' => ['dashboard', 'dashboard'],
    'urunler' => ['products', 'products'],
    'kategoriler' => ['categories', 'categories'],
    'siparisler' => ['orders', 'orders'],
    'musteriler' => ['customers', 'customers'],
    'sayfalar' => ['pages', 'content'],
    'blog' => ['posts', 'content'],
    'slider' => ['slides', 'content'],
    'yorumlar' => ['testimonials', 'content'],
    'ayarlar' => ['settings', 'settings'],
    'yoneticiler' => ['admins', 'admins'],
    'kayitlar' => ['logs', 'logs'],
    'profil' => ['profile', 'dashboard'],
];

if (!isset($modules[$section]) || $action === null) {
    http_response_code(404);
    admin_render('error', ['title' => 'Sayfa bulunamadı', 'message' => 'Aradığınız sayfa panelde bulunamadı.']);
    exit;
}

[$module, $permission] = $modules[$section];
require_permission($permission);
require APP_PATH . '/admin/modules/' . $module . '.php';
