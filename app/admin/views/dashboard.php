<?php
$change = $stats['revenuePrev30'] > 0 ? ($stats['revenue30'] - $stats['revenuePrev30']) * 100 / $stats['revenuePrev30'] : null;
$hour = (int) date('G');
$greeting = $hour < 12 ? 'Günaydın' : ($hour < 18 ? 'İyi günler' : 'İyi akşamlar');
?>
<div class="page-head">
    <div>
        <h1><?= $greeting ?>, <?= e(explode(' ', current_admin()['name'])[0]) ?></h1>
        <p>Mağazanın son 30 gündeki durumu.</p>
    </div>
    <?php if (can('products')): ?>
        <a href="<?= admin_url('urunler/yeni') ?>" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Ürün Ekle</a>
    <?php endif; ?>
</div>

<div class="kpis">
    <div class="kpi">
        <div class="kpi__icon kpi__icon--brand"><i class="bi bi-cash-stack"></i></div>
        <div class="kpi__label">Ciro (30 gün)</div>
        <div class="kpi__value"><?= money((float) $stats['revenue30']) ?></div>
        <?php if ($change !== null): ?>
            <div class="kpi__delta <?= $change >= 0 ? 'is-up' : 'is-down' ?>">
                <i class="bi <?= $change >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' ?>"></i>
                %<?= number_format(abs($change), 1, ',', '.') ?> <span>önceki 30 güne göre</span>
            </div>
        <?php endif; ?>
    </div>
    <div class="kpi">
        <div class="kpi__icon kpi__icon--green"><i class="bi bi-bag-check"></i></div>
        <div class="kpi__label">Sipariş (30 gün)</div>
        <div class="kpi__value"><?= (int) $stats['orders30'] ?></div>
        <div class="kpi__meta">Bugün <?= (int) $stats['ordersToday'] ?> sipariş</div>
    </div>
    <div class="kpi">
        <div class="kpi__icon kpi__icon--amber"><i class="bi bi-hourglass-split"></i></div>
        <div class="kpi__label">İşlem bekleyen</div>
        <div class="kpi__value"><?= $stats['pending'] ?></div>
        <div class="kpi__meta">Beklemede + hazırlanıyor</div>
    </div>
    <div class="kpi">
        <div class="kpi__icon kpi__icon--violet"><i class="bi bi-basket"></i></div>
        <div class="kpi__label">Ortalama sepet</div>
        <div class="kpi__value"><?= money((float) $stats['avgBasket']) ?></div>
        <div class="kpi__meta"><?= $stats['customers'] ?> müşteri · <?= $stats['products'] ?> aktif ürün</div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <section class="card-x h-100">
            <header class="card-x__head">
                <div>
                    <h2>Günlük ciro</h2>
                    <p>Son 14 gün · iptal ve iadeler hariç</p>
                </div>
            </header>
            <div class="chart-box">
                <canvas id="salesChart" aria-label="Son 14 günün günlük cirosu" role="img"></canvas>
            </div>
            <script type="application/json" id="salesChartData"><?= json_encode($chartData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?></script>
            <details class="chart-table">
                <summary>Tablo olarak göster</summary>
                <table class="table table-sm mb-0">
                    <thead><tr><th>Gün</th><th class="text-end">Sipariş</th><th class="text-end">Ciro</th></tr></thead>
                    <tbody>
                    <?php foreach ($chartData as $row): ?>
                        <tr><td><?= e($row['label']) ?></td><td class="text-end"><?= $row['count'] ?></td><td class="text-end"><?= money($row['total']) ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </details>
        </section>
    </div>
    <div class="col-xl-4">
        <section class="card-x h-100">
            <header class="card-x__head"><div><h2>En çok satanlar</h2><p>Son 30 gün, adet</p></div></header>
            <?php if (!$topProducts): ?>
                <p class="text-muted px-4 pb-4 mb-0">Henüz satış yok.</p>
            <?php else: $max = max(array_column($topProducts, 'qty')); ?>
                <ul class="rank">
                    <?php foreach ($topProducts as $i => $t): ?>
                        <li>
                            <span class="rank__no"><?= $i + 1 ?></span>
                            <div class="rank__body">
                                <div class="rank__name"><?= e($t['product_name']) ?></div>
                                <div class="rank__bar"><span style="width: <?= round($t['qty'] * 100 / $max) ?>%"></span></div>
                            </div>
                            <div class="rank__val"><?= (int) $t['qty'] ?> <small>adet</small></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </div>

    <?php if ($recentOrders): ?>
        <div class="col-xl-8">
            <section class="card-x">
                <header class="card-x__head">
                    <div><h2>Son siparişler</h2></div>
                    <a href="<?= admin_url('siparisler') ?>" class="btn btn-sm btn-light-x">Tümü <i class="bi bi-arrow-right"></i></a>
                </header>
                <div class="table-responsive">
                    <table class="table table-x align-middle mb-0">
                        <thead><tr><th>Sipariş</th><th>Müşteri</th><th>Tarih</th><th>Durum</th><th class="text-end">Tutar</th></tr></thead>
                        <tbody>
                        <?php foreach ($recentOrders as $o): ?>
                            <tr class="row-link" data-href="<?= admin_url('siparisler/' . $o['id']) ?>">
                                <td><a href="<?= admin_url('siparisler/' . $o['id']) ?>" class="fw-semibold">#<?= e($o['order_no']) ?></a></td>
                                <td><?= e($o['customer_name']) ?></td>
                                <td class="text-muted"><?= fmt_date($o['created_at']) ?></td>
                                <td><?= status_badge($o['status']) ?></td>
                                <td class="text-end fw-semibold"><?= money((float) $o['grand_total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    <?php endif; ?>

    <div class="<?= $recentOrders ? 'col-xl-4' : 'col-12' ?>">
        <section class="card-x">
            <header class="card-x__head"><div><h2>Stoğu azalanlar</h2><p>10 adet ve altı</p></div></header>
            <?php if (!$lowStock): ?>
                <p class="text-muted px-4 pb-4 mb-0">Stok sorunu yok.</p>
            <?php else: ?>
                <ul class="mini-list">
                    <?php foreach ($lowStock as $p): ?>
                        <li>
                            <img src="<?= e(img($p['image'], 80, 100, 'p' . $p['id'])) ?>" alt="">
                            <div>
                                <a href="<?= admin_url('urunler/' . $p['id']) ?>"><?= e($p['name']) ?></a>
                                <small><?= e($p['sku']) ?></small>
                            </div>
                            <span class="status <?= $p['stock'] == 0 ? 'status--danger' : 'status--warning' ?>"><?= $p['stock'] == 0 ? 'Tükendi' : (int) $p['stock'] . ' adet' ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </div>
</div>
