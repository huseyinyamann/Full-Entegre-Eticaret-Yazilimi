<?php partial('breadcrumb', ['heading' => 'Ödeme', 'items' => ['Sepetim' => 'sepet', 'Ödeme' => null]]); ?>

<section class="section pt-4">
    <div class="container-xl">
        <form class="row g-4" data-checkout>
            <div class="col-lg-7">
                <div class="form-card">
                    <h5>Teslimat Bilgileri</h5>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Ad</label><input class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Soyad</label><input class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">E-posta</label><input type="email" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Telefon</label><input type="tel" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">İl</label><input class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">İlçe</label><input class="form-control" required></div>
                        <div class="col-12"><label class="form-label">Adres</label><textarea class="form-control" rows="3" required></textarea></div>
                    </div>
                </div>
                <div class="form-card">
                    <h5>Ödeme Yöntemi</h5>
                    <?php foreach (['kart' => 'Kredi / Banka Kartı', 'havale' => 'Havale / EFT', 'kapida' => 'Kapıda Ödeme'] as $k => $label): ?>
                        <label class="pay-option">
                            <input type="radio" name="odeme" value="<?= $k ?>" <?= $k === 'kart' ? 'checked' : '' ?>>
                            <span><?= e($label) ?></span>
                        </label>
                    <?php endforeach; ?>
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
                        <input type="checkbox" class="form-check-input" required>
                        <span class="form-check-label"><a href="<?= url('sayfa/mesafeli-satis') ?>" target="_blank">Mesafeli satış sözleşmesini</a> okudum, onaylıyorum.</span>
                    </label>
                    <button class="btn btn-dark-brand w-100">Siparişi Tamamla</button>
                </div>
            </div>
        </form>
    </div>
</section>
