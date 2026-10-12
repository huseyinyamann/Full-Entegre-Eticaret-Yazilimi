<div class="page-head">
    <div>
        <a href="<?= admin_url('siparisler') ?>" class="back-link"><i class="bi bi-arrow-left"></i> Siparişler</a>
        <h1 class="d-flex align-items-center gap-3 flex-wrap">#<?= e($order['order_no']) ?> <?= status_badge($order['status']) ?></h1>
        <p><?= fmt_date($order['created_at']) ?> · <?= e(PAYMENT_METHODS[$order['payment_method']]) ?></p>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <section class="card-x mb-4">
            <header class="card-x__head"><div><h2>Ürünler</h2></div></header>
            <div class="table-responsive">
                <table class="table table-x align-middle mb-0">
                    <thead><tr><th>Ürün</th><th class="text-end">Birim</th><th class="text-center">Adet</th><th class="text-end">Toplam</th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $i => $item): ?>
                        <tr>
                            <td>
                                <div class="product-cell">
                                    <img src="<?= e(img($item['image'], 80, 100, 'oi' . $i)) ?>" alt="">
                                    <div>
                                        <?php if ($item['product_id'] && can('products')): ?>
                                            <a href="<?= admin_url('urunler/' . $item['product_id']) ?>" class="fw-semibold"><?= e($item['product_name']) ?></a>
                                        <?php else: ?>
                                            <span class="fw-semibold"><?= e($item['product_name']) ?></span>
                                        <?php endif; ?>
                                        <small><?= e($item['sku']) ?><?= $item['options'] ? ' · ' . e($item['options']) : '' ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end text-nowrap"><?= money((float) $item['unit_price']) ?></td>
                            <td class="text-center"><?= (int) $item['quantity'] ?></td>
                            <td class="text-end fw-semibold text-nowrap"><?= money((float) $item['line_total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <dl class="totals">
                <dt>Ara toplam</dt><dd><?= money((float) $order['subtotal']) ?></dd>
                <dt>Kargo</dt><dd><?= (float) $order['shipping_total'] > 0 ? money((float) $order['shipping_total']) : 'Ücretsiz' ?></dd>
                <?php if ((float) $order['discount_total'] > 0): ?><dt>İndirim</dt><dd>−<?= money((float) $order['discount_total']) ?></dd><?php endif; ?>
                <dt class="totals__grand">Genel toplam</dt><dd class="totals__grand"><?= money((float) $order['grand_total']) ?></dd>
            </dl>
        </section>

        <div class="row g-4">
            <div class="col-md-6">
                <section class="card-x card-x--pad h-100">
                    <h2 class="card-x__title">Müşteri</h2>
                    <p class="mb-1 fw-semibold">
                        <?php if ($order['customer_id'] && can('customers')): ?>
                            <a href="<?= admin_url('musteriler/' . $order['customer_id']) ?>"><?= e($order['customer_name']) ?></a>
                        <?php else: ?>
                            <?= e($order['customer_name']) ?>
                        <?php endif; ?>
                    </p>
                    <p class="mb-1"><a href="mailto:<?= e($order['email']) ?>"><?= e($order['email']) ?></a></p>
                    <p class="mb-0 text-muted"><?= e($order['phone']) ?></p>
                </section>
            </div>
            <div class="col-md-6">
                <section class="card-x card-x--pad h-100">
                    <h2 class="card-x__title">Teslimat adresi</h2>
                    <p class="mb-1"><?= nl2br(e($order['address'])) ?></p>
                    <p class="mb-0 text-muted"><?= e($order['district']) ?> / <?= e($order['city']) ?></p>
                </section>
            </div>
            <?php if ($order['customer_note']): ?>
                <div class="col-12">
                    <section class="card-x card-x--pad">
                        <h2 class="card-x__title">Müşteri notu</h2>
                        <p class="mb-0"><?= nl2br(e($order['customer_note'])) ?></p>
                    </section>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-xl-4">
        <form method="post" class="card-x card-x--pad mb-4">
            <?= csrf_field() ?>
            <h2 class="card-x__title">Siparişi yönet</h2>
            <label class="form-label" for="status">Sipariş durumu</label>
            <select class="form-select mb-3" id="status" name="status">
                <?php foreach (ORDER_STATUSES as $key => [$label]): ?>
                    <option value="<?= $key ?>"<?= selected($order['status'] === $key) ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <label class="form-label" for="payment_status">Ödeme durumu</label>
            <select class="form-select mb-3" id="payment_status" name="payment_status">
                <?php foreach (PAYMENT_STATUSES as $key => $label): ?>
                    <option value="<?= $key ?>"<?= selected($order['payment_status'] === $key) ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label" for="cargo_company">Kargo firması</label>
                    <input class="form-control" id="cargo_company" name="cargo_company" value="<?= e($order['cargo_company']) ?>" list="cargoList" maxlength="60">
                    <datalist id="cargoList"><option value="Yurtiçi Kargo"><option value="Aras Kargo"><option value="MNG Kargo"><option value="PTT Kargo"><option value="Sürat Kargo"><option value="Trendyol Express"></datalist>
                </div>
                <div class="col-6">
                    <label class="form-label" for="tracking_no">Takip no</label>
                    <input class="form-control" id="tracking_no" name="tracking_no" value="<?= e($order['tracking_no']) ?>" maxlength="60">
                </div>
            </div>
            <label class="form-label" for="admin_note">İç not <small class="text-muted">(müşteri görmez)</small></label>
            <textarea class="form-control mb-3" id="admin_note" name="admin_note" rows="3" maxlength="2000"><?= e($order['admin_note']) ?></textarea>
            <button class="btn btn-brand w-100"><i class="bi bi-check2 me-1"></i>Güncelle</button>
        </form>

        <section class="card-x card-x--pad">
            <h2 class="card-x__title">Geçmiş</h2>
            <ul class="timeline">
                <?php foreach ($history as $h): ?>
                    <li><strong><?= e($h['summary']) ?></strong><small><?= e($h['name'] ?? 'Sistem') ?> · <?= fmt_date($h['created_at']) ?></small></li>
                <?php endforeach; ?>
                <li><strong>Sipariş oluşturuldu</strong><small><?= fmt_date($order['created_at']) ?></small></li>
            </ul>
        </section>
    </div>
</div>
