<div class="page-head">
    <div>
        <h1>Ürünler</h1>
        <p><?= $pager['total'] ?> ürün listeleniyor</p>
    </div>
    <a href="<?= admin_url('urunler/yeni') ?>" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Yeni Ürün</a>
</div>

<section class="card-x">
    <form class="filters" method="get">
        <div class="input-icon flex-grow-1">
            <i class="bi bi-search"></i>
            <input type="search" name="q" class="form-control" placeholder="Ürün adı veya stok kodu…" value="<?= e(query('q')) ?>">
        </div>
        <select name="kategori" class="form-select" data-autosubmit>
            <option value="">Tüm kategoriler</option>
            <?php foreach ($categoryOptions as $c): ?>
                <option value="<?= $c['id'] ?>"<?= selected((int) query('kategori') === (int) $c['id']) ?>><?= $c['parent_name'] ? '— ' : '' ?><?= e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="durum" class="form-select" data-autosubmit>
            <option value="">Tüm durumlar</option>
            <option value="aktif"<?= selected(query('durum') === 'aktif') ?>>Yayında</option>
            <option value="pasif"<?= selected(query('durum') === 'pasif') ?>>Pasif</option>
            <option value="stoksuz"<?= selected(query('durum') === 'stoksuz') ?>>Stokta yok</option>
        </select>
        <button class="btn btn-light-x">Filtrele</button>
    </form>

    <?php if (!$products): ?>
        <div class="empty-state">
            <i class="bi bi-box-seam"></i>
            <h2>Ürün bulunamadı</h2>
            <p>Filtreleri değiştirin ya da yeni bir ürün ekleyin.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-x align-middle mb-0">
                <thead>
                <tr><th>Ürün</th><th>Kategori</th><th class="text-end">Fiyat</th><th class="text-end">Stok</th><th>Durum</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td>
                            <div class="product-cell">
                                <img src="<?= e(img($p['image'], 80, 100, 'p' . $p['id'])) ?>" alt="">
                                <div>
                                    <a href="<?= admin_url('urunler/' . $p['id']) ?>" class="fw-semibold"><?= e($p['name']) ?></a>
                                    <small><?= e($p['sku']) ?><?php if ($p['badge']): ?> · <?= e(BADGES[$p['badge']]) ?><?php endif; ?></small>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted"><?= e($p['category_name'] ?? '—') ?></td>
                        <td class="text-end text-nowrap">
                            <span class="fw-semibold"><?= money((float) $p['price']) ?></span>
                            <?php if ($p['old_price']): ?><br><small class="text-muted text-decoration-line-through"><?= money((float) $p['old_price']) ?></small><?php endif; ?>
                        </td>
                        <td class="text-end">
                            <span class="<?= $p['stock'] == 0 ? 'text-danger fw-semibold' : ($p['stock'] <= 10 ? 'text-warning-emphasis fw-semibold' : '') ?>"><?= (int) $p['stock'] ?></span>
                        </td>
                        <td><?= $p['is_active'] ? '<span class="status status--success">Yayında</span>' : '<span class="status status--secondary">Pasif</span>' ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= admin_url('urunler/' . $p['id']) ?>" class="btn btn-icon" title="Düzenle"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="<?= admin_url('urunler/' . $p['id'] . '/sil') ?>" class="d-inline" data-confirm="“<?= e($p['name']) ?>” silinsin mi? Bu işlem geri alınamaz.">
                                <?= csrf_field() ?>
                                <button class="btn btn-icon btn-icon--danger" title="Sil"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php admin_partial('pagination', ['p' => $pager]); ?>
    <?php endif; ?>
</section>
