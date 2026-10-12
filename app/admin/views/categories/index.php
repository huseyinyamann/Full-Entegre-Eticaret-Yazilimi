<div class="page-head">
    <div>
        <h1>Kategoriler</h1>
        <p>Ana kategoriler ve alt kategoriler. Sıralama mağaza menüsüne yansır.</p>
    </div>
    <a href="<?= admin_url('kategoriler/yeni') ?>" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Yeni Kategori</a>
</div>

<?php if (!$tree): ?>
    <div class="card-x empty-state">
        <i class="bi bi-diagram-3"></i>
        <h2>Henüz kategori yok</h2>
        <p>Ürünlerini gruplamak için ilk kategorini oluştur.</p>
    </div>
<?php endif; ?>

<div class="row g-4">
    <?php foreach ($tree as $cat): ?>
        <div class="col-md-6 col-xxl-4">
            <section class="card-x cat-card<?= $cat['is_active'] ? '' : ' is-muted' ?>">
                <div class="cat-card__head">
                    <img src="<?= e(img($cat['image'], 120, 120, 'cat-' . $cat['slug'])) ?>" alt="">
                    <div class="flex-grow-1 min-w-0">
                        <a href="<?= admin_url('kategoriler/' . $cat['id']) ?>" class="cat-card__name"><?= e($cat['name']) ?></a>
                        <small>
                            <?= (int) $cat['product_count'] + array_sum(array_column($cat['children'], 'product_count')) ?> ürün
                            · <?= count($cat['children']) ?> alt kategori
                            <?php if (!$cat['is_active']): ?> · <span class="text-danger">Pasif</span><?php endif; ?>
                        </small>
                    </div>
                    <a href="<?= admin_url('kategoriler/' . $cat['id']) ?>" class="btn btn-icon" title="Düzenle"><i class="bi bi-pencil"></i></a>
                </div>
                <ul class="cat-card__children">
                    <?php foreach ($cat['children'] as $child): ?>
                        <li>
                            <a href="<?= admin_url('kategoriler/' . $child['id']) ?>"><?= e($child['name']) ?></a>
                            <?php if (!$child['is_active']): ?><span class="status status--secondary">Pasif</span><?php endif; ?>
                            <span class="ms-auto text-muted small"><?= (int) $child['product_count'] ?> ürün</span>
                        </li>
                    <?php endforeach; ?>
                    <li class="cat-card__add">
                        <a href="<?= admin_url('kategoriler/yeni?ust=' . $cat['id']) ?>"><i class="bi bi-plus"></i> Alt kategori ekle</a>
                    </li>
                </ul>
            </section>
        </div>
    <?php endforeach; ?>
</div>
