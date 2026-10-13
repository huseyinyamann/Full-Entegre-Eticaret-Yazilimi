<?php /** @var array $me @var array $old @var array $errors @var array $stats */ ?>
<div class="acc-head">
    <h1>Hesabım</h1>
    <p>Kişisel bilgilerinizi güncelleyebilirsiniz</p>
</div>

<div class="acc-stats">
    <a href="<?= url('hesabim/siparisler') ?>" class="acc-stat">
        <span class="acc-stat__icon acc-tone--orange"><i class="bi bi-bag"></i></span>
        <span><strong><?= (int) $stats['total'] ?></strong><small>Toplam sipariş</small></span>
    </a>
    <a href="<?= url('hesabim/siparisler?durum=aktif') ?>" class="acc-stat">
        <span class="acc-stat__icon acc-tone--blue"><i class="bi bi-truck"></i></span>
        <span><strong><?= (int) $stats['active'] ?></strong><small>Devam eden</small></span>
    </a>
    <div class="acc-stat">
        <span class="acc-stat__icon acc-tone--green"><i class="bi bi-wallet2"></i></span>
        <span><strong><?= money((float) $stats['spent']) ?></strong><small>Toplam alışveriş</small></span>
    </div>
</div>

<section class="acc-card">
    <header class="acc-card__head">
        <span class="acc-card__icon acc-tone--orange"><i class="bi bi-person"></i></span>
        <h2>Profil Bilgileri</h2>
    </header>
    <form method="post" action="<?= url('hesabim') ?>" class="acc-card__body" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="form" value="profile">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="acc-label" for="first_name">Ad</label>
                <input class="form-control<?= invalid($errors, 'first_name') ?>" id="first_name" name="first_name" value="<?= e($old['first_name']) ?>" autocomplete="given-name" required maxlength="80">
                <?= error_text($errors, 'first_name') ?>
            </div>
            <div class="col-md-6">
                <label class="acc-label" for="last_name">Soyad</label>
                <input class="form-control<?= invalid($errors, 'last_name') ?>" id="last_name" name="last_name" value="<?= e($old['last_name']) ?>" autocomplete="family-name" required maxlength="80">
                <?= error_text($errors, 'last_name') ?>
            </div>
            <div class="col-md-6">
                <label class="acc-label" for="phone"><i class="bi bi-telephone"></i> Telefon</label>
                <input type="tel" class="form-control<?= invalid($errors, 'phone') ?>" id="phone" name="phone" value="<?= e((string) $old['phone']) ?>" autocomplete="tel" placeholder="05xx xxx xx xx" maxlength="30">
                <?= error_text($errors, 'phone') ?>
            </div>
            <div class="col-md-6">
                <label class="acc-label" for="email_ro">E-posta Adresi</label>
                <input type="email" class="form-control" id="email_ro" value="<?= e($me['email']) ?>" disabled>
            </div>
        </div>
        <div class="acc-card__foot">
            <span class="acc-muted"><i class="bi bi-calendar3"></i> Üyelik tarihi: <?= fmt_date($me['created_at'], false) ?></span>
            <button class="acc-btn"><i class="bi bi-floppy"></i> Değişiklikleri Kaydet</button>
        </div>
    </form>
</section>

<section class="acc-card">
    <header class="acc-card__head">
        <span class="acc-card__icon acc-tone--blue"><i class="bi bi-envelope"></i></span>
        <h2>E-posta Değiştir</h2>
    </header>
    <form method="post" action="<?= url('hesabim') ?>" class="acc-card__body" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="form" value="email">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="acc-label" for="new_email">Yeni E-posta Adresi</label>
                <input type="email" class="form-control<?= invalid($errors, 'new_email') ?>" id="new_email" name="new_email" value="<?= e($old['new_email'] ?? '') ?>" autocomplete="email" required maxlength="190">
                <?= error_text($errors, 'new_email') ?>
            </div>
            <div class="col-md-6">
                <label class="acc-label" for="email_current">Mevcut Şifreniz</label>
                <input type="password" class="form-control<?= invalid($errors, 'email_current') ?>" id="email_current" name="current_password" autocomplete="current-password" required>
                <?= error_text($errors, 'email_current') ?>
            </div>
        </div>
        <div class="acc-card__foot">
            <span class="acc-muted"><i class="bi bi-shield-check"></i> Güvenliğiniz için mevcut şifreniz istenir.</span>
            <button class="acc-btn acc-btn--blue"><i class="bi bi-envelope-check"></i> E-postayı Güncelle</button>
        </div>
    </form>
</section>

<section class="acc-card">
    <header class="acc-card__head">
        <span class="acc-card__icon acc-tone--purple"><i class="bi bi-shield-lock"></i></span>
        <h2>Şifre Değiştir</h2>
    </header>
    <form method="post" action="<?= url('hesabim') ?>" class="acc-card__body" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="form" value="password">
        <div class="row g-4">
            <div class="col-md-4">
                <label class="acc-label" for="password_current">Mevcut Şifre</label>
                <input type="password" class="form-control<?= invalid($errors, 'password_current') ?>" id="password_current" name="current_password" autocomplete="current-password" required>
                <?= error_text($errors, 'password_current') ?>
            </div>
            <div class="col-md-4">
                <label class="acc-label" for="new_password">Yeni Şifre</label>
                <input type="password" class="form-control<?= invalid($errors, 'new_password') ?>" id="new_password" name="new_password" autocomplete="new-password" required minlength="<?= CUSTOMER_PASSWORD_MIN ?>">
                <?= error_text($errors, 'new_password') ?>
            </div>
            <div class="col-md-4">
                <label class="acc-label" for="new_password_confirm">Yeni Şifre (tekrar)</label>
                <input type="password" class="form-control<?= invalid($errors, 'new_password_confirm') ?>" id="new_password_confirm" name="new_password_confirm" autocomplete="new-password" required>
                <?= error_text($errors, 'new_password_confirm') ?>
            </div>
        </div>
        <div class="acc-card__foot">
            <span class="acc-muted"><i class="bi bi-info-circle"></i> En az <?= CUSTOMER_PASSWORD_MIN ?> karakter, harf ve rakam. Diğer cihazlardaki oturumlar kapanır.</span>
            <button class="acc-btn acc-btn--purple"><i class="bi bi-key"></i> Şifreyi Değiştir</button>
        </div>
    </form>
</section>
