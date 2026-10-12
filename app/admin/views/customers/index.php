<div class="page-head">
    <div>
        <h1>Müşteriler</h1>
        <p><?= $pager['total'] ?> kayıtlı müşteri</p>
    </div>
</div>

<section class="card-x">
    <form class="filters" method="get">
        <div class="input-icon flex-grow-1">
            <i class="bi bi-search"></i>
            <input type="search" name="q" class="form-control" placeholder="Ad, e-posta veya telefon…" value="<?= e(query('q')) ?>">
        </div>
        <button class="btn btn-light-x">Ara</button>
    </form>

    <?php if (!$customers): ?>
        <div class="empty-state"><i class="bi bi-people"></i><h2>Müşteri bulunamadı</h2><p>Aramayı değiştirin.</p></div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-x align-middle mb-0">
                <thead><tr><th>Müşteri</th><th>Telefon</th><th class="text-center">Sipariş</th><th class="text-end">Harcama</th><th>Son sipariş</th><th>Kayıt</th></tr></thead>
                <tbody>
                <?php foreach ($customers as $c): ?>
                    <tr class="row-link" data-href="<?= admin_url('musteriler/' . $c['id']) ?>">
                        <td>
                            <div class="product-cell">
                                <span class="avatar avatar--soft"><?= e(mb_substr($c['first_name'], 0, 1) . mb_substr($c['last_name'], 0, 1)) ?></span>
                                <div>
                                    <a href="<?= admin_url('musteriler/' . $c['id']) ?>" class="fw-semibold"><?= e($c['first_name'] . ' ' . $c['last_name']) ?></a>
                                    <small><?= e($c['email']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted text-nowrap"><?= e($c['phone']) ?></td>
                        <td class="text-center"><?= (int) $c['order_count'] ?></td>
                        <td class="text-end fw-semibold text-nowrap"><?= money((float) $c['total_spent']) ?></td>
                        <td class="text-muted text-nowrap"><?= fmt_date($c['last_order_at'], false) ?></td>
                        <td class="text-muted text-nowrap">
                            <?= fmt_date($c['created_at'], false) ?>
                            <?php if (!$c['is_active']): ?><span class="status status--danger ms-1">Engelli</span><?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php admin_partial('pagination', ['p' => $pager]); ?>
    <?php endif; ?>
</section>
