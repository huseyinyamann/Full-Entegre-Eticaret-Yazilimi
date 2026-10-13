</main>

<?php partial('footer'); ?>
<?php partial('mobile-toolbar'); ?>
<?php partial('offcanvas-menu'); ?>
<?php partial('offcanvas-search'); ?>
<?php partial('mini-cart'); ?>
<?php partial('quick-view'); ?>

<a href="https://wa.me/<?= e(config('whatsapp')) ?>" class="whatsapp-float" target="_blank" rel="noopener" aria-label="WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>
<button type="button" class="back-to-top" id="backToTop" aria-label="Yukarı çık"><i class="bi bi-arrow-up"></i></button>

<div class="toast-container position-fixed top-0 end-0 p-3" id="toastBox"></div>

<script>
    window.APP = {
        baseUrl: <?= json_encode(config('base_url')) ?>,
        currency: <?= json_encode(config('currency')) ?>,
        freeShipping: <?= (float) config('free_shipping_limit') ?>,
        shippingFee: <?= (float) config('shipping_fee') ?>
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11.1.4/swiper-bundle.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
