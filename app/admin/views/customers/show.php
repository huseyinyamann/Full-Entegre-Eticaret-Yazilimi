<div class="page-head">
    <div>
        <a href="<?= admin_url('musteriler') ?>" class="back-link"><i class="bi bi-arrow-left"></i> Müşteriler</a>
        <h1><?= e($customer['first_name'] . ' ' . $customer['last_name']) ?></h1>
        <p>Müşteri · <?= fmt_date($customer['created_at'], false) ?> tarihinden beri</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-4">
        <section class="card-x card-x--pad mb-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="avatar avatar--lg avatar--soft"><?= e(mb_substr($customer['first_name'], 0, 1) . mb_substr($customer['last_name'], 0, 1)) ?></span>
                <div>
                    <div class="fw-semibold"><?= e($customer['first_name'] . ' ' . $customer['last_name']) ?></div>
                    <a href="mailto:<?= e($customer['email']) ?>" class="small"><?= e($customer['email']) ?></a>
                    <div class="small text-muted"><?= e($customer['phone']) ?></div>
                </div>
            </div>
            <div class="mini-stats">
                <div><span><?= count($orders) ?></span><small>Sipariş</small></div>
                <div><span><?= money($spent) ?></span><small>Toplam harcama</small></div>
            </div>
        </section>

        <form method="post" class="card-x card-x--pad">
            <?= csrf_field() ?>
            <h2 class="card-x__title">Hesap durumu</h2>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active"<?= checked((bool) $customer['is_active']) ?>>
                <label class="form-check-label" for="is_active">Hesap aktif (giriş yapabilir)</label>
            </div>
            <button class="btn btn-brand w-100">Kaydet</button>
        </form>
    </div>

    <div class="col-xl-8">
        <section class="card-x">
            <header class="card-x__head"><div><h2>Siparişleri</h2></div></header>
            <?php if (!$orders): ?>
                <p class="text-muted px-4 pb-4 mb-0">Henüz siparişi yok.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-x align-middle mb-0">
                        <thead><tr><th>Sipariş</th><th>Tarih</th><th>Durum</th><th class="text-end">Tutar</th></tr></thead>
                        <tbody>
                        <?php foreach ($orders as $o): ?>
                            <tr class="row-link" data-href="<?= admin_url('siparisler/' . $o['id']) ?>">
                                <td><a href="<?= admin_url('siparisler/' . $o['id']) ?>" class="fw-semibold">#<?= e($o['order_no']) ?></a></td>
                                <td class="text-muted"><?= fmt_date($o['created_at']) ?></td>
                                <td><?= status_badge($o['status']) ?></td>
                                <td class="text-end fw-semibold"><?= money((float) $o['grand_total']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>
