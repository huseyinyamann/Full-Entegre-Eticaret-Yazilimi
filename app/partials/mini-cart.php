<div class="offcanvas offcanvas-end mini-cart" tabindex="-1" id="miniCart" aria-label="Sepet">
    <div class="offcanvas-header">
        <h5 class="mb-0">Sepetim (<span data-cart-count>0</span>)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Kapat"></button>
    </div>
    <div class="mini-cart__shipping">
        <div class="progress"><div class="progress-bar" data-ship-bar style="width:0%"></div></div>
        <p data-ship-text>Ücretsiz kargo için <?= money((float) config('free_shipping_limit')) ?> daha ekle.</p>
    </div>
    <div class="offcanvas-body">
        <div class="mini-cart__items" data-cart-items></div>
        <div class="mini-cart__empty" data-cart-empty>
            <i class="bi bi-bag-x"></i>
            <p>Sepetin şu an boş.</p>
            <a href="<?= url('kategoriler') ?>" class="btn btn-dark-brand">Alışverişe Başla</a>
        </div>
    </div>
    <div class="mini-cart__footer" data-cart-footer>
        <div class="d-flex justify-content-between mb-3">
            <span>Ara Toplam</span><strong data-cart-total>0</strong>
        </div>
        <a href="<?= url('sepet') ?>" class="btn btn-outline-brand w-100 mb-2">Sepete Git</a>
        <a href="<?= url('odeme') ?>" class="btn btn-dark-brand w-100">Ödemeye Geç</a>
    </div>
</div>
