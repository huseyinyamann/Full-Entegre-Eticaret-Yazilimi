<div class="page-head">
    <div>
        <h1>Siparişler</h1>
        <p><?= $pager['total'] ?> sipariş</p>
    </div>
</div>

<div class="tabs-x">
    <a href="<?= e(qs(['durum' => null, 'sayfa' => null])) ?: '?' ?>" class="<?= query('durum') === '' ? 'is-active' : '' ?>">Tümü <span><?= array_sum($counts) ?></span></a>
    <?php foreach (ORDER_STATUSES as $key => [$label]): ?>
        <a href="<?= e(qs(['durum' => $key, 'sayfa' => null])) ?>" class="<?= query('durum') === $key ? 'is-active' : '' ?>"><?= e($label) ?> <span><?= (int) ($counts[$key] ?? 0) ?></span></a>
    <?php endforeach; ?>
</div>

<section class="card-x">
    <form class="filters" method="get">
        <?php if (query('durum') !== ''): ?><input type="hidden" name="durum" value="<?= e(query('durum')) ?>"><?php endif; ?>
        <div class="input-icon flex-grow-1">
            <i class="bi bi-search"></i>
            <input type="search" name="q" class="form-control" placeholder="Sipariş no, müşteri adı veya e-posta…" value="<?= e(query('q')) ?>">
        </div>
        <button class="btn btn-light-x">Ara</button>
    </form>

    <?php if (!$orders): ?>
        <div class="empty-state"><i class="bi bi-receipt"></i><h2>Sipariş bulunamadı</h2><p>Arama veya filtreyi değiştirin.</p></div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-x align-middle mb-0">
                <thead><tr><th>Sipariş</th><th>Müşteri</th><th>Tarih</th><th>Ödeme</th><th>Durum</th><th class="text-end">Tutar</th></tr></thead>
                <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr class="row-link" data-href="<?= admin_url('siparisler/' . $o['id']) ?>">
                        <td>
                            <a href="<?= admin_url('siparisler/' . $o['id']) ?>" class="fw-semibold">#<?= e($o['order_no']) ?></a>
                            <small class="d-block text-muted"><?= (int) $o['item_count'] ?> ürün</small>
                        </td>
                        <td><?= e($o['customer_name']) ?><small class="d-block text-muted"><?= e($o['city']) ?></small></td>
                        <td class="text-muted text-nowrap"><?= fmt_date($o['created_at']) ?></td>
                        <td>
                            <?= e(PAYMENT_METHODS[$o['payment_method']]) ?>
                            <small class="d-block <?= $o['payment_status'] === 'odendi' ? 'text-success' : 'text-muted' ?>"><?= e(PAYMENT_STATUSES[$o['payment_status']]) ?></small>
                        </td>
                        <td><?= status_badge($o['status']) ?></td>
                        <td class="text-end fw-semibold text-nowrap"><?= money((float) $o['grand_total']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php admin_partial('pagination', ['p' => $pager]); ?>
    <?php endif; ?>
</section>
