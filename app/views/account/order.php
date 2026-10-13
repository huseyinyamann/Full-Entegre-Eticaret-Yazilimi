<?php
/** @var array $order @var array $items */
$steps = ['beklemede' => ['Sipariş alındı', 'bi-receipt'], 'hazirlaniyor' => ['Hazırlanıyor', 'bi-box-seam'],
    'kargoda' => ['Kargoya verildi', 'bi-truck'], 'teslim-edildi' => ['Teslim edildi', 'bi-house-check']];
$current = array_search($order['status'], array_keys($steps), true);
$closed = $current === false; // iptal / iade
?>
<div class="acc-head acc-head--row">
    <div>
        <a href="<?= url('hesabim/siparisler') ?>" class="acc-backlink"><i class="bi bi-arrow-left"></i> Siparişlerim</a>
        <h1>Sipariş #<?= e($order['order_no']) ?></h1>
        <p><?= fmt_date($order['created_at']) ?> tarihinde verildi</p>
    </div>
    <?= order_status($order['status']) ?>
</div>

<section class="acc-card">
    <div class="acc-card__body">
        <?php if ($closed): ?>
            <div class="acc-alert acc-alert--danger mb-0">
                <i class="bi bi-x-circle-fill"></i>
                <span>Bu sipariş <?= $order['status'] === 'iade' ? 'iade edildi' : 'iptal edildi' ?>.
                    <?= $order['payment_status'] === 'iade' ? 'Ödemeniz iade süreci başlatıldı; bankanıza bağlı olarak 3-10 iş günü içinde hesabınıza yansır.' : '' ?></span>
            </div>
        <?php else: ?>
            <ol class="acc-steps">
                <?php $n = 0; foreach ($steps as $key => [$label, $icon]): ?>
                    <li class="<?= $n < $current ? 'is-done' : ($n === $current ? 'is-current' : '') ?>">
                        <span class="acc-steps__dot"><i class="bi <?= $n < $current ? 'bi-check-lg' : $icon ?>"></i></span>
                        <span class="acc-steps__label"><?= e($label) ?></span>
                    </li>
                <?php $n++; endforeach; ?>
            </ol>
            <?php if ($order['tracking_no']): ?>
                <div class="acc-tracking">
                    <i class="bi bi-truck"></i>
                    <div><small>Kargo firması</small><strong><?= e($order['cargo_company'] ?: 'Kargo') ?></strong></div>
                    <div><small>Takip numarası</small><strong class="acc-mono"><?= e($order['tracking_no']) ?></strong></div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<div class="row g-4">
    <div class="col-xl-8">
        <section class="acc-card h-100 mb-0">
            <header class="acc-card__head">
                <span class="acc-card__icon acc-tone--orange"><i class="bi bi-bag"></i></span>
                <h2>Ürünler</h2>
            </header>
            <div class="acc-card__body acc-card__body--flush">
                <?php foreach ($items as $line): ?>
                    <div class="acc-line">
                        <img src="<?= e(img($line['image'], 120, 160, 'p' . $line['product_id'])) ?>" alt="" loading="lazy">
                        <div class="acc-line__info">
                            <?php if ($line['slug']): ?>
                                <a href="<?= url('urun/' . $line['slug']) ?>"><?= e($line['product_name']) ?></a>
                            <?php else: ?>
                                <span><?= e($line['product_name']) ?></span>
                            <?php endif; ?>
                            <small><?= e($line['options'] ?: 'Standart') ?> · <?= (int) $line['quantity'] ?> × <?= money((float) $line['unit_price']) ?></small>
                        </div>
                        <strong><?= money((float) $line['line_total']) ?></strong>
                    </div>
                <?php endforeach; ?>
                <div class="acc-totals">
                    <div><span>Ara toplam</span><span><?= money((float) $order['subtotal']) ?></span></div>
                    <div><span>Kargo</span><span><?= (float) $order['shipping_total'] > 0 ? money((float) $order['shipping_total']) : 'Ücretsiz' ?></span></div>
                    <?php if ((float) $order['discount_total'] > 0): ?>
                        <div><span>İndirim</span><span>−<?= money((float) $order['discount_total']) ?></span></div>
                    <?php endif; ?>
                    <div class="acc-totals__grand"><span>Toplam</span><span><?= money((float) $order['grand_total']) ?></span></div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-xl-4">
        <section class="acc-card mb-4">
            <header class="acc-card__head">
                <span class="acc-card__icon acc-tone--blue"><i class="bi bi-geo-alt"></i></span>
                <h2>Teslimat Adresi</h2>
            </header>
            <div class="acc-card__body acc-info">
                <strong><?= e($order['customer_name']) ?></strong>
                <p><?= nl2br(e((string) $order['address'])) ?><br><?= e($order['district'] . ' / ' . $order['city']) ?></p>
                <small><i class="bi bi-telephone"></i> <?= e((string) $order['phone']) ?></small>
            </div>
        </section>
        <section class="acc-card mb-4">
            <header class="acc-card__head">
                <span class="acc-card__icon acc-tone--green"><i class="bi bi-credit-card"></i></span>
                <h2>Ödeme</h2>
            </header>
            <div class="acc-card__body acc-info">
                <strong><?= e(PAYMENT_METHODS[$order['payment_method']]) ?></strong>
                <p class="mb-0"><?= e(PAYMENT_STATUSES[$order['payment_status']]) ?></p>
                <?php if ($order['payment_method'] === 'havale' && $order['payment_status'] === 'bekliyor' && !$closed): ?>
                    <small>Açıklamaya sipariş numaranızı yazmayı unutmayın. <a href="<?= url('banka-hesaplari') ?>">Banka hesapları</a></small>
                <?php endif; ?>
            </div>
        </section>
        <?php if ($order['customer_note']): ?>
            <section class="acc-card mb-4">
                <div class="acc-card__body acc-info"><small>Sipariş notunuz</small><p class="mb-0"><?= e($order['customer_note']) ?></p></div>
            </section>
        <?php endif; ?>
        <?php if ($order['status'] === 'beklemede'): ?>
            <form method="post" action="<?= url('hesabim/siparisler/' . rawurlencode($order['order_no']) . '/iptal') ?>" data-confirm="Siparişi iptal etmek istediğinize emin misiniz?">
                <?= csrf_field() ?>
                <button class="acc-btn acc-btn--danger w-100"><i class="bi bi-x-circle"></i> Siparişi İptal Et</button>
                <p class="acc-muted small text-center mt-2 mb-0">Sipariş hazırlanmaya başlamadan iptal edebilirsiniz.</p>
            </form>
        <?php else: ?>
            <a href="<?= url('iletisim') ?>" class="acc-btn acc-btn--ghost w-100"><i class="bi bi-chat-dots"></i> Bu sipariş için destek al</a>
        <?php endif; ?>
    </div>
</div>
