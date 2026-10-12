<div class="page-head">
    <div>
        <h1>Profilim</h1>
        <p><?= e($me['email']) ?> · <?= e(ROLE_LABELS[$me['role']]) ?></p>
    </div>
</div>

<form method="post" class="row g-4" autocomplete="off">
    <?= csrf_field() ?>
    <div class="col-xl-5">
        <section class="card-x card-x--pad h-100">
            <h2 class="card-x__title">Bilgiler</h2>
            <label class="form-label" for="name">Ad soyad</label>
            <input class="form-control<?= invalid($errors, 'name') ?>" id="name" name="name" value="<?= e($me['name']) ?>" required maxlength="100">
            <?= error_text($errors, 'name') ?>
            <label class="form-label mt-3" for="email">E-posta</label>
            <input class="form-control" id="email" value="<?= e($me['email']) ?>" disabled>
            <div class="form-text">E-posta adresinizi mağaza sahibi değiştirebilir.</div>
        </section>
    </div>
    <div class="col-xl-7">
        <section class="card-x card-x--pad h-100">
            <h2 class="card-x__title">Şifre değiştir</h2>
            <p class="text-muted small">Değiştirmek istemiyorsanız boş bırakın.</p>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label" for="current_password">Mevcut şifre</label>
                    <input type="password" class="form-control<?= invalid($errors, 'current_password') ?>" id="current_password" name="current_password" autocomplete="current-password">
                    <?= error_text($errors, 'current_password') ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="new_password">Yeni şifre</label>
                    <input type="password" class="form-control<?= invalid($errors, 'new_password') ?>" id="new_password" name="new_password" autocomplete="new-password">
                    <?= error_text($errors, 'new_password') ?>
                    <div class="form-text">En az 10 karakter, harf ve rakam.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="new_password_confirm">Yeni şifre (tekrar)</label>
                    <input type="password" class="form-control<?= invalid($errors, 'new_password_confirm') ?>" id="new_password_confirm" name="new_password_confirm" autocomplete="new-password">
                    <?= error_text($errors, 'new_password_confirm') ?>
                </div>
            </div>
        </section>
    </div>
    <div class="col-12">
        <button class="btn btn-brand"><i class="bi bi-check2 me-1"></i>Kaydet</button>
    </div>
</form>
