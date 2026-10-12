<div class="page-head">
    <div>
        <a href="<?= admin_url('yoneticiler') ?>" class="back-link"><i class="bi bi-arrow-left"></i> Yöneticiler</a>
        <h1><?= $id ? e($admin['name']) : 'Yeni Yönetici' ?></h1>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <form method="post" class="card-x card-x--pad" autocomplete="off">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="name">Ad soyad *</label>
                    <input class="form-control<?= invalid($errors, 'name') ?>" id="name" name="name" value="<?= e($admin['name']) ?>" required maxlength="100">
                    <?= error_text($errors, 'name') ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">E-posta *</label>
                    <input type="email" class="form-control<?= invalid($errors, 'email') ?>" id="email" name="email" value="<?= e($admin['email']) ?>" required>
                    <?= error_text($errors, 'email') ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="role">Rol</label>
                    <select class="form-select<?= invalid($errors, 'role') ?>" id="role" name="role">
                        <?php foreach (ROLE_LABELS as $k => $label): ?>
                            <option value="<?= $k ?>"<?= selected($admin['role'] === $k) ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= error_text($errors, 'role') ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="password"><?= $id ? 'Yeni şifre' : 'Şifre *' ?></label>
                    <div class="input-icon">
                        <i class="bi bi-lock"></i>
                        <input type="password" class="form-control<?= invalid($errors, 'password') ?>" id="password" name="password" autocomplete="new-password"<?= $id ? '' : ' required' ?>>
                        <button type="button" class="input-icon__toggle" data-toggle-password="#password" aria-label="Şifreyi göster"><i class="bi bi-eye"></i></button>
                    </div>
                    <?= error_text($errors, 'password') ?>
                    <div class="form-text">En az 10 karakter, harf ve rakam.<?= $id ? ' Değiştirmeyecekseniz boş bırakın.' : '' ?></div>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active"<?= checked((bool) $admin['is_active']) ?>>
                        <label class="form-check-label" for="is_active">Hesap aktif</label>
                    </div>
                </div>
            </div>
            <hr>
            <button class="btn btn-brand"><i class="bi bi-check2 me-1"></i>Kaydet</button>
        </form>
    </div>
    <div class="col-xl-5">
        <section class="card-x card-x--pad">
            <h2 class="card-x__title">Rol yetkileri</h2>
            <ul class="role-list">
                <li><strong>Mağaza Sahibi</strong><span>Her şey, yönetici hesapları ve işlem kayıtları dahil.</span></li>
                <li><strong>Yönetici</strong><span>Siparişler, müşteriler, ayarlar, katalog ve içerik.</span></li>
                <li><strong>Editör</strong><span>Yalnızca ürün, kategori ve içerik (sayfa, blog, slider, yorum).</span></li>
            </ul>
        </section>
    </div>
</div>

<?php if ($id && !$isSelf): ?>
    <form method="post" action="<?= admin_url('yoneticiler/' . $id . '/sil') ?>" class="danger-zone" data-confirm="Bu yönetici hesabı silinsin mi?">
        <?= csrf_field() ?>
        <div><strong>Hesabı sil</strong><p>Kişi panele erişimini kaybeder. İşlem kayıtları korunur.</p></div>
        <button class="btn btn-outline-danger">Hesabı sil</button>
    </form>
<?php endif; ?>
