<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Giriş | <?= e(config('site_name')) ?> Yönetim</title>
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Albert+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= admin_asset('admin.css') ?>">
</head>
<body class="auth-page">
<div class="auth">
    <aside class="auth__visual">
        <div class="auth__brand">
            <span class="brand-mark">Y</span>
            <span><?= e(config('site_name')) ?></span>
        </div>
        <div class="auth__pitch">
            <h1>Mağazanı tek panelden yönet.</h1>
            <p>Ürünler, siparişler, müşteriler ve içerik — hepsi bir arada.</p>
            <ul>
                <li><i class="bi bi-shield-lock"></i> Argon2id şifreleme ve brute-force koruması</li>
                <li><i class="bi bi-activity"></i> Tüm işlemler için kayıt geçmişi</li>
                <li><i class="bi bi-people"></i> Rol bazlı yetkilendirme</li>
            </ul>
        </div>
        <small class="auth__foot">&copy; <?= date('Y') ?> <?= e(config('site_name')) ?></small>
    </aside>

    <main class="auth__form">
        <form method="post" action="<?= admin_url('giris') ?>" class="auth-card" novalidate>
            <?= csrf_field() ?>
            <div class="auth-card__head">
                <span class="brand-mark d-lg-none mb-3">Y</span>
                <h2>Yönetim Paneli</h2>
                <p>Devam etmek için hesabınıza giriş yapın.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger d-flex gap-2 align-items-center" role="alert">
                    <i class="bi bi-exclamation-circle"></i><span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <label class="form-label" for="email">E-posta</label>
            <div class="input-icon mb-3">
                <i class="bi bi-envelope"></i>
                <input type="email" class="form-control form-control-lg" id="email" name="email" value="<?= e($email !== '' ? $email : config('autofill')['admin']['email']) ?>" autocomplete="username" required autofocus>
            </div>

            <label class="form-label" for="password">Şifre</label>
            <div class="input-icon mb-4">
                <i class="bi bi-lock"></i>
                <input type="password" class="form-control form-control-lg" id="password" name="password" value="<?= e(config('autofill')['admin']['password']) ?>" autocomplete="current-password" required>
                <button type="button" class="input-icon__toggle" data-toggle-password="#password" aria-label="Şifreyi göster"><i class="bi bi-eye"></i></button>
            </div>

            <button class="btn btn-brand btn-lg w-100">Giriş Yap <i class="bi bi-arrow-right ms-1"></i></button>
            <a href="<?= url() ?>" class="auth-card__back"><i class="bi bi-shop"></i> Mağazaya dön</a>
        </form>
    </main>
</div>
<script src="<?= admin_asset('admin.js') ?>"></script>
</body>
</html>
