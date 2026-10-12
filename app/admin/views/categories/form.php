<div class="page-head">
    <div>
        <a href="<?= admin_url('kategoriler') ?>" class="back-link"><i class="bi bi-arrow-left"></i> Kategoriler</a>
        <h1><?= $id ? e($category['name']) : 'Yeni Kategori' ?></h1>
    </div>
</div>

<form method="post" enctype="multipart/form-data" class="row g-4" data-dirty-check>
    <?= csrf_field() ?>
    <div class="col-xl-8">
        <section class="card-x card-x--pad mb-4">
            <h2 class="card-x__title">Kategori bilgileri</h2>
            <div class="mb-3">
                <label class="form-label" for="name">Kategori adı *</label>
                <input class="form-control<?= invalid($errors, 'name') ?>" id="name" name="name" value="<?= e($category['name']) ?>" required maxlength="120" data-slug-source="#slug">
                <?= error_text($errors, 'name') ?>
            </div>
            <div class="mb-3">
                <label class="form-label" for="parent_id">Üst kategori</label>
                <select class="form-select<?= invalid($errors, 'parent_id') ?>" id="parent_id" name="parent_id"<?= $hasChildren ? ' disabled' : '' ?>>
                    <option value="">— Ana kategori —</option>
                    <?php foreach ($parents as $p): ?>
                        <option value="<?= $p['id'] ?>"<?= selected((int) $category['parent_id'] === (int) $p['id']) ?>><?= e($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= error_text($errors, 'parent_id') ?>
                <?php if ($hasChildren): ?><div class="form-text">Alt kategorileri olduğu için ana kategori olarak kalmalı.</div><?php endif; ?>
            </div>
            <div>
                <label class="form-label" for="description">Açıklama</label>
                <textarea class="form-control" id="description" name="description" rows="3" maxlength="2000"><?= e($category['description']) ?></textarea>
            </div>
        </section>

        <section class="card-x card-x--pad">
            <h2 class="card-x__title">Arama motoru (SEO)</h2>
            <div class="mb-3">
                <label class="form-label" for="slug">URL</label>
                <div class="input-group">
                    <span class="input-group-text"><?= e(url('kategori/')) ?></span>
                    <input class="form-control" id="slug" name="slug" value="<?= e($category['slug']) ?>" placeholder="otomatik oluşturulur">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="meta_title">Sayfa başlığı</label>
                <input class="form-control" id="meta_title" name="meta_title" value="<?= e($category['meta_title']) ?>" maxlength="190">
            </div>
            <div>
                <label class="form-label" for="meta_description">Meta açıklama</label>
                <textarea class="form-control" id="meta_description" name="meta_description" rows="2" maxlength="300"><?= e($category['meta_description']) ?></textarea>
            </div>
        </section>
    </div>

    <div class="col-xl-4">
        <div class="sticky-side">
            <section class="card-x card-x--pad mb-4">
                <h2 class="card-x__title">Yayın</h2>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active"<?= checked((bool) $category['is_active']) ?>>
                    <label class="form-check-label" for="is_active">Mağazada görünsün</label>
                </div>
                <label class="form-label" for="sort_order">Sıra</label>
                <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= (int) $category['sort_order'] ?>">
                <div class="form-text">Küçük sayı önce gösterilir.</div>
            </section>

            <section class="card-x card-x--pad mb-4">
                <h2 class="card-x__title">Görsel</h2>
                <?php if ($category['image']): ?>
                    <img src="<?= e(img($category['image'], 400, 500, 'cat')) ?>" alt="" class="preview-img mb-2">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                        <label class="form-check-label" for="remove_image">Görseli kaldır</label>
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control<?= invalid($errors, 'image') ?>" name="image" accept="image/jpeg,image/png,image/webp">
                <?= error_text($errors, 'image') ?>
            </section>

            <button class="btn btn-brand btn-lg w-100"><i class="bi bi-check2 me-1"></i>Kaydet</button>
        </div>
    </div>
</form>

<?php if ($id): ?>
    <form method="post" action="<?= admin_url('kategoriler/' . $id . '/sil') ?>" class="danger-zone" data-confirm="Bu kategori silinsin mi?">
        <?= csrf_field() ?>
        <div><strong>Kategoriyi sil</strong><p>İçindeki ürünler silinmez, kategorisiz kalır.</p></div>
        <button class="btn btn-outline-danger"<?= $hasChildren ? ' disabled title="Önce alt kategorileri kaldırın"' : '' ?>>Kategoriyi sil</button>
    </form>
<?php endif; ?>
