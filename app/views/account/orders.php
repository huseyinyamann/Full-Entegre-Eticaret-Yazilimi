<?php /** @var array $orders @var array $items @var array $filters @var string $filter @var array $counts */ ?>
<div class="acc-head">
    <h1>Siparişlerim</h1>
    <p>Verdiğiniz tüm siparişleri ve güncel durumlarını buradan takip edebilirsiniz</p>
</div>

<nav class="acc-tabs" aria-label="Sipariş filtresi">
    <?php foreach ($filters as $key => [$label]): ?>
        <a href="<?= url('hesabim/siparisler' . ($key !== '' ? '?durum=' . $key : '')) ?>" class="acc-tabs__item<?= $filter === $key ? ' is-active' : '' ?>">
            <?= e($label) ?> <span><?= (int) $counts[$key] ?></span>
        </a>
    <?php endforeach; ?>
</nav>

<?php if (!$orders): ?>
    <section class="acc-card">
        <div class="acc-empty">
            <span class="acc-empty__icon"><i class="bi bi-bag"></i></span>
            <h2><?= $filter === '' ? 'Henüz siparişiniz yok' : 'Bu filtrede sipariş yok' ?></h2>
            <p>Beğendiğiniz ürünleri sepete ekleyip birkaç adımda sipariş verebilirsiniz.</p>
            <a href="<?= url() ?>" class="acc-btn"><i class="bi bi-shop"></i> Alışverişe Başla</a>
        </div>
    </section>
<?php endif; ?>

<?php foreach ($orders as $o):
    $lines = $items[$o['id']] ?? [];
    $qty = array_sum(array_column($lines, 'quantity'));
    $link = url('hesabim/siparisler/' . rawurlencode($o['order_no'])); ?>
    <article class="acc-card acc-order">
        <header class="acc-order__head">
            <div>
                <small>Sipariş No</small>
                <a href="<?= $link ?>"><strong>#<?= e($o['order_no']) ?></strong></a>
            </div>
            <div>
                <small>Tarih</small>
                <strong><?= fmt_date($o['created_at']) ?></strong>
            </div>
            <div>
                <small>Tutar</small>
                <strong><?= money((float) $o['grand_total']) ?></strong>
            </div>
            <div class="ms-md-auto"><?= order_status($o['status']) ?></div>
        </header>
        <div class="acc-order__body">
            <div class="acc-order__thumbs">
                <?php foreach (array_slice($lines, 0, 4) as $i => $line): ?>
                    <img src="<?= e(img($line['image'], 120, 160, 'p' . $line['product_id'])) ?>" alt="<?= e($line['product_name']) ?>" title="<?= e($line['product_name']) ?>" loading="lazy">
                <?php endforeach; ?>
                <?php if (count($lines) > 4): ?><span class="acc-order__more">+<?= count($lines) - 4 ?></span><?php endif; ?>
            </div>
            <div class="acc-order__summary">
                <strong><?= e($lines[0]['product_name'] ?? 'Ürün') ?></strong>
                <small><?= $qty ?> ürün<?= count($lines) > 1 ? ' · ' . (count($lines) - 1) . ' ürün daha' : '' ?> · <?= e(PAYMENT_METHODS[$o['payment_method']]) ?></small>
            </div>
            <a href="<?= $link ?>" class="acc-btn acc-btn--ghost">Detaylar <i class="bi bi-chevron-right"></i></a>
        </div>
    </article>
<?php endforeach; ?>
