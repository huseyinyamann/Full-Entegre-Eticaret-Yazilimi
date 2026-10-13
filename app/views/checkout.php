<?php
/** @var ?array $me @var array $addresses @var array $old @var array $errors */
partial('breadcrumb', ['heading' => 'Ödeme', 'items' => ['Sepetim' => 'sepet', 'Ödeme' => null]]);
$field = function (string $name, string $label, string $type = 'text', string $auto = '') use ($old, $errors) {
    return '<label class="form-label" for="f_' . $name . '">' . e($label) . '</label>'
        . '<input type="' . $type . '" class="form-control' . invalid($errors, $name) . '" id="f_' . $name . '" name="' . $name . '" value="' . e((string) $old[$name]) . '"'
        . ($auto ? ' autocomplete="' . $auto . '"' : '') . ' required>' . error_text($errors, $name);
};
?>

<section class="section pt-4">
    <div class="container-xl">
        <?php partial('flashes'); ?>
        <?php if (!empty($errors['form'])): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2" role="alert"><i class="bi bi-exclamation-triangle"></i><span><?= e($errors['form']) ?></span></div>
        <?php endif; ?>

        <?php if (!$me): ?>
            <div class="checkout-login">
                <i class="bi bi-person-circle"></i>
                <div><strong>Üye misin?</strong> Giriş yaparsan siparişini hesabından takip edebilir, adreslerini kaydedebilirsin.</div>
                <a href="<?= url('giris?donus=odeme') ?>" class="btn btn-sm btn-dark-brand ms-md-auto">Giriş Yap</a>
                <a href="<?= url('kayit-ol?donus=odeme') ?>" class="btn btn-sm btn-outline-brand">Kayıt Ol</a>
            </div>
        <?php endif; ?>

        <form class="row g-4" method="post" action="<?= url('odeme') ?>" data-checkout novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="cart" value="">
            <div class="col-lg-7">
                <div class="form-card">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <h5 class="mb-0">Teslimat Bilgileri</h5>
                        <?php if ($addresses): ?>
                            <select class="form-select form-select-sm w-auto" data-address-select aria-label="Kayıtlı adres seç">
                                <option value="">Kayıtlı adreslerim</option>
                                <?php foreach ($addresses as $a):
                                    $parts = preg_split('/\s+/', trim($a['full_name']));
                                    $last = count($parts) > 1 ? array_pop($parts) : '';
                                    $json = json_encode(['first_name' => implode(' ', $parts), 'last_name' => $last, 'phone' => $a['phone'],
                                        'city' => $a['city'], 'district' => $a['district'], 'address' => $a['address']], JSON_UNESCAPED_UNICODE); ?>
                                    <option value="<?= (int) $a['id'] ?>" data-address="<?= e($json) ?>"><?= e($a['title']) ?> — <?= e($a['district'] . ' / ' . $a['city']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><?= $field('first_name', 'Ad', 'text', 'given-name') ?></div>
                        <div class="col-md-6"><?= $field('last_name', 'Soyad', 'text', 'family-name') ?></div>
                        <div class="col-md-6"><?= $field('email', 'E-posta', 'email', 'email') ?></div>
                        <div class="col-md-6"><?= $field('phone', 'Telefon', 'tel', 'tel') ?></div>
                        <div class="col-md-6"><?= $field('city', 'İl', 'text', 'address-level1') ?></div>
                        <div class="col-md-6"><?= $field('district', 'İlçe', 'text', 'address-level2') ?></div>
                        <div class="col-12">
                            <label class="form-label" for="f_address">Adres</label>
                            <textarea class="form-control<?= invalid($errors, 'address') ?>" id="f_address" name="address" rows="3" autocomplete="street-address" required maxlength="500"><?= e($old['address']) ?></textarea>
                            <?= error_text($errors, 'address') ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="f_note">Sipariş notu <span class="text-muted small">(isteğe bağlı)</span></label>
                            <input class="form-control" id="f_note" name="note" value="<?= e($old['note']) ?>" maxlength="500" placeholder="Örn. zile basmayın, kapıya bırakın">
                        </div>
                        <?php if ($me): ?>
                            <div class="col-12">
                                <label class="form-check small"><input type="checkbox" class="form-check-input" name="save_address" value="1" <?= $old['save_address'] ? 'checked' : '' ?>>
                                    <span class="form-check-label">Bu adresi adres defterime kaydet</span></label>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-card">
                    <h5>Ödeme Yöntemi</h5>
                    <?php foreach (PAYMENT_METHODS as $k => $label): ?>
                        <label class="pay-option">
                            <input type="radio" name="payment" value="<?= $k ?>" <?= $old['payment'] === $k ? 'checked' : '' ?>>
                            <span><?= e($label) ?>
                                <?php if ($k === 'havale'): ?><small class="d-block text-muted">Sipariş sonrası <a href="<?= url('banka-hesaplari') ?>" target="_blank">banka hesaplarımıza</a> ödeme yapabilirsiniz.</small>
                                <?php elseif ($k === 'kredi-karti'): ?><small class="d-block text-muted">Sanal POS entegrasyonu henüz bağlanmadı; sipariş “ödeme bekliyor” olarak oluşturulur.</small><?php endif; ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                    <?= error_text($errors, 'payment') ?>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="summary-box">
                    <h5>Sipariş Özeti</h5>
                    <div class="checkout-items" data-checkout-items></div>
                    <div class="summary-row"><span>Ara Toplam</span><strong data-cart-total>0</strong></div>
                    <div class="summary-row"><span>Kargo</span><strong data-cart-shipping>—</strong></div>
                    <div class="summary-row summary-row--total"><span>Toplam</span><strong data-cart-grand>0</strong></div>
                    <label class="form-check small my-3">
                        <input type="checkbox" class="form-check-input<?= invalid($errors, 'agree') ?>" name="agree" value="1" required>
                        <span class="form-check-label"><a href="<?= url('sayfa/mesafeli-satis') ?>" target="_blank">Mesafeli satış sözleşmesini</a> okudum, onaylıyorum.</span>
                    </label>
                    <?= error_text($errors, 'agree') ?>
                    <button type="submit" class="btn btn-dark-brand w-100">Siparişi Tamamla</button>
                    <p class="small text-muted text-center mt-3 mb-0"><i class="bi bi-shield-lock me-1"></i>Tutarlar sipariş anında güncel fiyatlarla yeniden hesaplanır.</p>
                </div>
            </div>
        </form>
    </div>
</section>
