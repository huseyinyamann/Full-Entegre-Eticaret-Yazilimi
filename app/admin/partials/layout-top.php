<?php
$me = current_admin();
$pendingOrders = can('orders') ? (int) db_value("SELECT COUNT(*) FROM orders WHERE status = 'beklemede'") : 0;
$nav = [
    'Genel' => [
        ['', 'Gösterge Paneli', 'bi-grid-1x2', 'dashboard'],
    ],
    'Satış' => [
        ['siparisler', 'Siparişler', 'bi-receipt', 'orders', $pendingOrders],
        ['musteriler', 'Müşteriler', 'bi-people', 'customers'],
    ],
    'Katalog' => [
        ['urunler', 'Ürünler', 'bi-box-seam', 'products'],
        ['kategoriler', 'Kategoriler', 'bi-diagram-3', 'categories'],
    ],
    'İçerik' => [
        ['slider', 'Slider', 'bi-images', 'content'],
        ['sayfalar', 'Sayfalar', 'bi-file-earmark-text', 'content'],
        ['blog', 'Blog', 'bi-journal-richtext', 'content'],
        ['yorumlar', 'Müşteri Yorumları', 'bi-chat-quote', 'content'],
    ],
    'Sistem' => [
        ['ayarlar', 'Ayarlar', 'bi-gear', 'settings'],
        ['yoneticiler', 'Yöneticiler', 'bi-person-badge', 'admins'],
        ['kayitlar', 'İşlem Kayıtları', 'bi-clock-history', 'logs'],
    ],
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title) ?> | <?= e(config('site_name')) ?> Yönetim</title>
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <script src="<?= admin_asset('theme-init.js') ?>"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Albert+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= admin_asset('admin.css') ?>">
</head>
<body>
<div class="admin">
    <aside class="sidebar" id="sidebar">
        <a href="<?= admin_url() ?>" class="sidebar__brand">
            <span class="brand-mark">Y</span>
            <span class="sidebar__brand-text"><?= e(config('site_name')) ?><small>Yönetim Paneli</small></span>
        </a>
        <nav class="sidebar__nav">
            <?php foreach ($nav as $navGroup => $navItems):
                $navItems = array_filter($navItems, fn($i) => can($i[3]));
                if (!$navItems) continue; ?>
                <div class="sidebar__group"><?= e($navGroup) ?></div>
                <?php foreach ($navItems as $navItem): ?>
                    <a href="<?= admin_url($navItem[0]) ?>" class="sidebar__link<?= $active === $navItem[0] ? ' is-active' : '' ?>">
                        <i class="bi <?= $navItem[2] ?>"></i><span><?= e($navItem[1]) ?></span>
                        <?php if (!empty($navItem[4])): ?><span class="sidebar__count"><?= (int) $navItem[4] ?></span><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </nav>
        <a href="<?= url() ?>" target="_blank" rel="noopener" class="sidebar__store">
            <i class="bi bi-shop"></i><span>Mağazayı görüntüle</span><i class="bi bi-box-arrow-up-right ms-auto"></i>
        </a>
    </aside>
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <div class="admin__main">
        <header class="topbar">
            <button class="topbar__btn d-lg-none" type="button" data-sidebar-open aria-label="Menü"><i class="bi bi-list"></i></button>
            <div class="topbar__title"><?= e($title) ?></div>
            <div class="topbar__actions">
                <button class="topbar__btn" type="button" data-theme-toggle aria-label="Tema değiştir"><i class="bi bi-moon-stars"></i></button>
                <div class="dropdown">
                    <button class="topbar__user" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar"><?= e(mb_strtoupper(mb_substr($me['name'], 0, 1))) ?></span>
                        <span class="topbar__user-text d-none d-md-flex"><?= e($me['name']) ?><small><?= e(ROLE_LABELS[$me['role']]) ?></small></span>
                        <i class="bi bi-chevron-down small d-none d-md-inline"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="<?= admin_url('profil') ?>"><i class="bi bi-person me-2"></i>Profilim</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="post" action="<?= admin_url('cikis') ?>">
                                <?= csrf_field() ?>
                                <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Çıkış Yap</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="content">
            <?php foreach (take_flashes() as [$type, $msg]): ?>
                <div class="alert alert-<?= e($type) ?> alert-dismissible fade show flash" role="alert">
                    <i class="bi <?= $type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle' ?> me-2"></i><?= e($msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                </div>
            <?php endforeach; ?>
