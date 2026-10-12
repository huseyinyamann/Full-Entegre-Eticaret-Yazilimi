<?php
$colors = json_decode((string) $product['colors'], true) ?: [];
$sizes = json_decode((string) $product['sizes'], true) ?: [];
?>
<div class="page-head">
    <div>
        <a href="<?= admin_url('urunler') ?>" class="back-link"><i class="bi bi-arrow-left"></i> Ürünler</a>
        <h1><?= $id ? e($product['name']) : 'Yeni Ürün' ?></h1>
    </div>
    <?php if ($id && $product['is_active']): ?>
        <a href="<?= url('urun/' . $product['slug']) ?>" target="_blank" rel="noopener" class="btn btn-light-x"><i class="bi bi-eye me-1"></i>Mağazada gör</a>
    <?php endif; ?>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Lütfen işaretli alanları kontrol edin.</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="row g-4" data-dirty-check>
    <?= csrf_field() ?>
    <div class="col-xl-8">
        <section class="card-x card-x--pad mb-4">
            <h2 class="card-x__title">Temel bilgiler</h2>
            <div class="mb-3">
                <label class="form-label" for="name">Ürün adı *</label>
                <input class="form-control<?= invalid($errors, 'name') ?>" id="name" name="name" value="<?= e($product['name']) ?>" required maxlength="190" data-slug-source="#slug">
                <?= error_text($errors, 'name') ?>
            </div>
            <div class="mb-3">
                <label class="form-label" for="summary">Kısa açıklama</label>
                <textarea class="form-control" id="summary" name="summary" rows="2" maxlength="1000"><?= e($product['summary']) ?></textarea>
                <div class="form-text">Ürün sayfasında fiyatın altında görünür.</div>
            </div>
            <div>
                <label class="form-label" for="description">Detaylı açıklama</label>
                <textarea class="form-control font-monospace small" id="description" name="description" rows="8"><?= e($product['description']) ?></textarea>
                <div class="form-text">HTML kullanabilirsiniz (p, strong, ul, li, a, img, table…). Script ve stil etiketleri güvenlik nedeniyle otomatik temizlenir.</div>
            </div>
        </section>

        <section class="card-x card-x--pad mb-4">
            <h2 class="card-x__title">Görseller</h2>
            <?= error_text($errors, 'images') ?>
            <?php if ($images): ?>
                <div class="image-grid mb-3">
                    <?php foreach ($images as $i => $img): ?>
                        <div class="image-tile">
                            <img src="<?= e(img($img['path'], 300, 400, 'p' . $id . $i)) ?>" alt="">
                            <label class="image-tile__cover" title="Kapak görseli yap">
                                <input type="radio" name="cover_image" value="<?= $img['id'] ?>"<?= checked($i === 0) ?>> <span>Kapak</span>
                            </label>
                            <label class="image-tile__remove" title="Sil">
                                <input type="checkbox" name="remove_images[]" value="<?= $img['id'] ?>"> <i class="bi bi-trash"></i>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <label class="dropzone">
                <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple data-preview="#newImages">
                <i class="bi bi-cloud-arrow-up"></i>
                <strong>Görsel yüklemek için tıklayın</strong>
                <span>JPG, PNG veya WEBP · en fazla 5 MB · birden çok seçebilirsiniz</span>
            </label>
            <div class="image-grid mt-3" id="newImages"></div>
        </section>

        <section class="card-x card-x--pad mb-4">
            <h2 class="card-x__title">Varyantlar</h2>
            <label class="form-label">Renkler</label>
            <div class="color-rows" id="colorRows">
                <?php foreach ($colors ?: [['name' => '', 'hex' => '#111111']] as $c): ?>
                    <div class="color-row">
                        <input type="color" name="color_hex[]" class="form-control form-control-color" value="<?= e($c['hex']) ?>">
                        <input type="text" name="color_name[]" class="form-control" placeholder="Renk adı (ör. Siyah)" value="<?= e($c['name']) ?>" maxlength="40">
                        <button type="button" class="btn btn-icon btn-icon--danger" data-remove-row aria-label="Kaldır"><i class="bi bi-x-lg"></i></button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-sm btn-light-x mt-2" data-add-color><i class="bi bi-plus"></i> Renk ekle</button>

            <div class="mt-4">
                <label class="form-label" for="sizes">Bedenler</label>
                <input class="form-control" id="sizes" name="sizes" value="<?= e(implode(', ', $sizes)) ?>" placeholder="XS, S, M, L, XL">
                <div class="form-text">Virgülle ayırın. Beden seçimi olmayan ürünlerde boş bırakın.</div>
            </div>
        </section>

        <section class="card-x card-x--pad">
            <h2 class="card-x__title">Arama motoru (SEO)</h2>
            <div class="mb-3">
                <label class="form-label" for="slug">URL</label>
                <div class="input-group">
                    <span class="input-group-text"><?= e(url('urun/')) ?></span>
                    <input class="form-control" id="slug" name="slug" value="<?= e($product['slug']) ?>" placeholder="otomatik oluşturulur">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="meta_title">Sayfa başlığı</label>
                <input class="form-control" id="meta_title" name="meta_title" value="<?= e($product['meta_title']) ?>" maxlength="190">
            </div>
            <div>
                <label class="form-label" for="meta_description">Meta açıklama</label>
                <textarea class="form-control" id="meta_description" name="meta_description" rows="2" maxlength="300"><?= e($product['meta_description']) ?></textarea>
            </div>
        </section>
    </div>

    <div class="col-xl-4">
        <div class="sticky-side">
            <section class="card-x card-x--pad mb-4">
                <h2 class="card-x__title">Yayın</h2>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active"<?= checked((bool) $product['is_active']) ?>>
                    <label class="form-check-label" for="is_active">Mağazada yayında</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_featured" name="is_featured"<?= checked((bool) $product['is_featured']) ?>>
                    <label class="form-check-label" for="is_featured">Öne çıkan ürün</label>
                </div>
                <label class="form-label" for="badge">Rozet</label>
                <select class="form-select" id="badge" name="badge">
                    <option value="">Yok</option>
                    <?php foreach (BADGES as $k => $label): ?>
                        <option value="<?= $k ?>"<?= selected($product['badge'] === $k) ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </section>

            <section class="card-x card-x--pad mb-4">
                <h2 class="card-x__title">Fiyat & stok</h2>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label" for="price">Satış fiyatı *</label>
                        <div class="input-group">
                            <input class="form-control<?= invalid($errors, 'price') ?>" id="price" name="price" value="<?= e(money_input($product['price'])) ?>" inputmode="decimal" required>
                            <span class="input-group-text">₺</span>
                        </div>
                        <?= error_text($errors, 'price') ?>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="old_price">Eski fiyat</label>
                        <div class="input-group">
                            <input class="form-control<?= invalid($errors, 'old_price') ?>" id="old_price" name="old_price" value="<?= e(money_input($product['old_price'])) ?>" inputmode="decimal">
                            <span class="input-group-text">₺</span>
                        </div>
                    </div>
                    <?= error_text($errors, 'old_price') ?>
                    <div class="col-6">
                        <label class="form-label" for="stock">Stok</label>
                        <input type="number" class="form-control<?= invalid($errors, 'stock') ?>" id="stock" name="stock" value="<?= (int) $product['stock'] ?>" min="0">
                        <?= error_text($errors, 'stock') ?>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="sku">Stok kodu</label>
                        <input class="form-control<?= invalid($errors, 'sku') ?>" id="sku" name="sku" value="<?= e($product['sku']) ?>" placeholder="otomatik" maxlength="64">
                    </div>
                    <?= error_text($errors, 'sku') ?>
                </div>
            </section>

            <section class="card-x card-x--pad mb-4">
                <h2 class="card-x__title">Kategori</h2>
                <select class="form-select<?= invalid($errors, 'category_id') ?>" name="category_id" aria-label="Kategori">
                    <option value="">Kategorisiz</option>
                    <?php foreach ($categoryOptions as $c): ?>
                        <option value="<?= $c['id'] ?>"<?= selected((int) $product['category_id'] === (int) $c['id']) ?>><?= $c['parent_name'] ? e($c['parent_name']) . ' › ' : '' ?><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= error_text($errors, 'category_id') ?>
            </section>

            <div class="d-grid gap-2">
                <button class="btn btn-brand btn-lg"><i class="bi bi-check2 me-1"></i>Kaydet</button>
            </div>
        </div>
    </div>
</form>

<?php if ($id): ?>
    <form method="post" action="<?= admin_url('urunler/' . $id . '/sil') ?>" class="danger-zone" data-confirm="Bu ürün kalıcı olarak silinsin mi?">
        <?= csrf_field() ?>
        <div><strong>Ürünü sil</strong><p>Ürün ve görselleri kalıcı olarak silinir. Geçmiş siparişlerdeki kayıtlar korunur.</p></div>
        <button class="btn btn-outline-danger">Ürünü sil</button>
    </form>
<?php endif; ?>
