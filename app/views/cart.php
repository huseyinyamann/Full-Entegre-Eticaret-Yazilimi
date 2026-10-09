<?php partial('breadcrumb', ['heading' => 'Sepetim', 'items' => ['Sepetim' => null]]); ?>

<section class="section pt-4">
    <div class="container-xl">
        <div class="row g-4" data-cart-page>
            <div class="col-lg-8">
                <div class="cart-table" data-cart-rows></div>
                <div class="mini-cart__empty" data-cart-empty>
                    <i class="bi bi-bag-x"></i>
                    <p>Sepetinde ürün bulunmuyor.</p>
                    <a href="<?= url('kategoriler') ?>" class="btn btn-dark-brand">Alışverişe Başla</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="summary-box" data-cart-footer>
                    <h5>Sipariş Özeti</h5>
                    <div class="summary-row"><span>Ara Toplam</span><strong data-cart-total>0</strong></div>
                    <div class="summary-row"><span>Kargo</span><strong data-cart-shipping>—</strong></div>
                    <form class="coupon-form" onsubmit="event.preventDefault(); App.toast('Kupon kodu geçersiz', 'danger')">
                        <input type="text" placeholder="İndirim kodu" class="form-control">
                        <button class="btn btn-outline-brand">Uygula</button>
                    </form>
                    <div class="summary-row summary-row--total"><span>Toplam</span><strong data-cart-grand>0</strong></div>
                    <a href="<?= url('odeme') ?>" class="btn btn-dark-brand w-100">Ödemeye Geç</a>
                    <p class="small text-muted mt-3 mb-0"><i class="bi bi-shield-lock"></i> Ödeme bilgilerin 256-bit SSL ile korunur.</p>
                </div>
            </div>
        </div>
    </div>
</section>
