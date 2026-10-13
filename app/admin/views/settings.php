<div class="page-head">
    <div>
        <h1>Ayarlar</h1>
        <p>Mağazanın genel bilgileri. Değişiklikler kaydettiğiniz anda mağazaya yansır.</p>
    </div>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Lütfen işaretli alanları kontrol edin.</div>
<?php endif; ?>

<form method="post" class="row g-4" data-dirty-check>
    <?= csrf_field() ?>
    <div class="col-xl-8">
        <section class="card-x card-x--pad mb-4">
            <h2 class="card-x__title">Mağaza</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="site_name">Mağaza adı *</label>
                    <input class="form-control<?= invalid($errors, 'site_name') ?>" id="site_name" name="site_name" value="<?= e($s['site_name']) ?>" required maxlength="80">
                    <?= error_text($errors, 'site_name') ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="site_slogan">Slogan</label>
                    <input class="form-control" id="site_slogan" name="site_slogan" value="<?= e($s['site_slogan']) ?>" maxlength="160">
                </div>
                <div class="col-12">
                    <label class="form-label" for="description">Site açıklaması (SEO)</label>
                    <textarea class="form-control" id="description" name="description" rows="2" maxlength="300"><?= e($s['description']) ?></textarea>
                </div>
            </div>
        </section>

        <section class="card-x card-x--pad mb-4">
            <h2 class="card-x__title">İletişim</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="phone">Telefon</label>
                    <input class="form-control" id="phone" name="phone" value="<?= e($s['phone']) ?>" maxlength="30">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">E-posta</label>
                    <input type="email" class="form-control<?= invalid($errors, 'email') ?>" id="email" name="email" value="<?= e($s['email']) ?>">
                    <?= error_text($errors, 'email') ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="whatsapp">WhatsApp numarası</label>
                    <input class="form-control" id="whatsapp" name="whatsapp" value="<?= e($s['whatsapp']) ?>" placeholder="905xxxxxxxxx">
                    <div class="form-text">Ülke koduyla, boşluksuz.</div>
                </div>
                <div class="col-12">
                    <label class="form-label" for="address">Adres</label>
                    <textarea class="form-control" id="address" name="address" rows="2" maxlength="300"><?= e($s['address']) ?></textarea>
                </div>
            </div>
        </section>

        <section class="card-x card-x--pad">
            <h2 class="card-x__title">Banka hesapları</h2>
            <textarea class="form-control font-monospace small<?= invalid($errors, 'bank_accounts') ?>" name="bank_accounts" rows="4" aria-label="Banka hesapları"><?php
                foreach ($s['bank_accounts'] ?? [] as $a) echo e($a['bank'] . ' | ' . $a['holder'] . ' | ' . $a['iban']) . "\n";
            ?></textarea>
            <?= error_text($errors, 'bank_accounts') ?>
            <div class="form-text">Her satıra bir hesap: <code>Banka | Hesap sahibi | IBAN</code>. Havale sayfasında listelenir.</div>
        </section>
    </div>

    <div class="col-xl-4">
        <div class="sticky-side">
            <section class="card-x card-x--pad mb-4">
                <h2 class="card-x__title">Kargo & duyurular</h2>
                <label class="form-label" for="free_shipping_limit">Ücretsiz kargo limiti</label>
                <div class="input-group mb-3">
                    <input class="form-control" id="free_shipping_limit" name="free_shipping_limit" value="<?= e(money_input($s['free_shipping_limit'])) ?>" inputmode="decimal">
                    <span class="input-group-text">₺</span>
                </div>
                <label class="form-label" for="shipping_fee">Kargo ücreti</label>
                <div class="input-group mb-3">
                    <input class="form-control" id="shipping_fee" name="shipping_fee" value="<?= e(money_input($s['shipping_fee'] ?? 0)) ?>" inputmode="decimal">
                    <span class="input-group-text">₺</span>
                </div>
                <label class="form-label" for="announcements">Duyuru bandı</label>
                <textarea class="form-control" id="announcements" name="announcements" rows="4"><?= e(implode("\n", $s['announcements'] ?? [])) ?></textarea>
                <div class="form-text">Her satır ayrı bir duyuru olarak döner.</div>
            </section>

            <section class="card-x card-x--pad mb-4">
                <h2 class="card-x__title">Sosyal medya</h2>
                <?php foreach (['instagram' => 'Instagram', 'facebook' => 'Facebook', 'x' => 'X (Twitter)', 'youtube' => 'YouTube'] as $net => $label): $v = $s['social'][$net] ?? '#'; ?>
                    <label class="form-label" for="social_<?= $net ?>"><?= $label ?></label>
                    <div class="input-icon mb-3">
                        <i class="bi bi-<?= $net === 'x' ? 'twitter-x' : $net ?>"></i>
                        <input class="form-control<?= invalid($errors, 'social_' . $net) ?>" id="social_<?= $net ?>" name="social_<?= $net ?>" value="<?= e($v === '#' ? '' : $v) ?>" placeholder="https://…">
                    </div>
                    <?= error_text($errors, 'social_' . $net) ?>
                <?php endforeach; ?>
            </section>

            <button class="btn btn-brand btn-lg w-100"><i class="bi bi-check2 me-1"></i>Ayarları Kaydet</button>
        </div>
    </div>
</form>
