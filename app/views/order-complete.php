<?php /** @var array $order @var ?array $me */ ?>
<section class="section">
    <div class="container-xl">
        <div class="order-complete" data-order-complete>
            <div class="order-complete__icon"><i class="bi bi-check-lg"></i></div>
            <h1>Siparişiniz alındı!</h1>
            <p class="lead">Sipariş numaranız <strong>#<?= e($order['order_no']) ?></strong></p>
            <p class="text-muted">Toplam tutar: <strong><?= money((float) $order['grand_total']) ?></strong> · Ödeme: <?= e(PAYMENT_METHODS[$order['payment_method']]) ?></p>
            <?php if ($order['payment_method'] === 'havale'): ?>
                <div class="alert alert-info text-start">
                    <i class="bi bi-bank me-1"></i> Havale/EFT açıklamasına sipariş numaranızı (<strong><?= e($order['order_no']) ?></strong>) yazmayı unutmayın.
                    <a href="<?= url('banka-hesaplari') ?>">Banka hesaplarımızı görüntüleyin</a>.
                </div>
            <?php endif; ?>
            <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
                <?php if ($me && (int) $order['customer_id'] === (int) $me['id']): ?>
                    <a href="<?= url('hesabim/siparisler/' . rawurlencode($order['order_no'])) ?>" class="btn btn-dark-brand">Siparişimi Görüntüle</a>
                <?php else: ?>
                    <a href="<?= url('kayit-ol') ?>" class="btn btn-dark-brand">Üye Ol, Siparişlerini Takip Et</a>
                <?php endif; ?>
                <a href="<?= url() ?>" class="btn btn-outline-brand">Alışverişe Devam Et</a>
            </div>
        </div>
    </div>
</section>
