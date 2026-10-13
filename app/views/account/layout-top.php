<?php
/** @var array $me @var string $active @var ?string $title */
$initial = mb_strtoupper(mb_substr($me['first_name'], 0, 1));
$activeOrders = (int) db_value("SELECT COUNT(*) FROM orders WHERE customer_id = ? AND status IN ('beklemede', 'hazirlaniyor', 'kargoda')", [$me['id']]);
$accountNav = [
    ['', 'Hesabım', 'Profil bilgilerim', 'bi-person'],
    ['siparisler', 'Siparişlerim', 'Sipariş geçmişim', 'bi-bag', $activeOrders],
    ['adresler', 'Adreslerim', 'Teslimat adreslerim', 'bi-geo-alt'],
    ['@favorilerim', 'Favorilerim', 'Beğendiğim ürünler', 'bi-heart'],
    ['@siparis-takip', 'Sipariş Takibi', 'Kargo durumu', 'bi-truck'],
    ['@iletisim', 'Destek', 'Bize ulaşın', 'bi-chat-dots'],
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title) ?> | <?= e(config('site_name')) ?></title>
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Albert+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= asset('css/account.css') ?>">
</head>
<body class="acc">

<header class="acc-top">
    <div class="acc-container acc-top__inner">
        <a href="<?= url() ?>" class="acc-top__back"><i class="bi bi-arrow-left"></i><span>Ana Sayfaya Dön</span></a>
        <span class="acc-top__sep" aria-hidden="true"></span>
        <a href="<?= url('hesabim') ?>" class="acc-top__user">
            <span class="acc-avatar acc-avatar--sm"><?= e($initial) ?></span>
            <span class="acc-top__name"><?= e($me['name']) ?></span>
        </a>
        <div class="acc-top__actions">
            <a href="<?= url('sepet') ?>" class="acc-top__icon" aria-label="Sepetim"><i class="bi bi-bag"></i></a>
            <form method="post" action="<?= url('cikis') ?>">
                <?= csrf_field() ?>
                <button class="acc-top__icon" aria-label="Çıkış yap" title="Çıkış yap"><i class="bi bi-box-arrow-right"></i></button>
            </form>
        </div>
    </div>
</header>

<div class="acc-container acc-layout">
    <aside class="acc-side">
        <div class="acc-side__head">
            <span class="acc-avatar"><?= e($initial) ?></span>
            <div class="acc-side__who">
                <strong><?= e($me['name']) ?></strong>
                <small title="<?= e($me['email']) ?>"><?= e($me['email']) ?></small>
            </div>
        </div>
        <nav class="acc-nav">
            <?php foreach ($accountNav as $item):
                $external = str_starts_with($item[0], '@');
                $href = $external ? url(substr($item[0], 1)) : url('hesabim' . ($item[0] !== '' ? '/' . $item[0] : ''));
                $isActive = !$external && $active === $item[0]; ?>
                <a href="<?= $href ?>" class="acc-nav__link<?= $isActive ? ' is-active' : '' ?>"<?= $isActive ? ' aria-current="page"' : '' ?>>
                    <span class="acc-nav__icon"><i class="bi <?= $item[3] ?>"></i></span>
                    <span class="acc-nav__text"><?= e($item[1]) ?><small><?= e($item[2]) ?></small></span>
                    <?php if (!empty($item[4])): ?><span class="acc-nav__count"><?= (int) $item[4] ?></span>
                    <?php elseif ($isActive): ?><span class="acc-nav__dot"></span><?php endif; ?>
                </a>
            <?php endforeach; ?>
            <form method="post" action="<?= url('cikis') ?>" class="acc-nav__logout">
                <?= csrf_field() ?>
                <button class="acc-nav__link">
                    <span class="acc-nav__icon"><i class="bi bi-box-arrow-right"></i></span>
                    <span class="acc-nav__text">Çıkış Yap<small>Oturumu kapat</small></span>
                </button>
            </form>
        </nav>
    </aside>

    <main class="acc-main">
        <?php foreach (take_flashes() as [$type, $msg]): ?>
            <div class="acc-alert acc-alert--<?= e($type) ?>" role="alert">
                <i class="bi <?= $type === 'success' ? 'bi-check-circle-fill' : ($type === 'info' ? 'bi-info-circle-fill' : 'bi-exclamation-triangle-fill') ?>"></i>
                <span><?= e($msg) ?></span>
                <button type="button" class="acc-alert__close" data-dismiss-alert aria-label="Kapat"><i class="bi bi-x-lg"></i></button>
            </div>
        <?php endforeach; ?>
