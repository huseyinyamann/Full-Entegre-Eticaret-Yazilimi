<?php
$renderField = function (string $name, array $f) use ($row, $errors, $def): void {
    $value = $row[$name] ?? '';
    $cls = invalid($errors, $name);
    $label = e($f['label']) . (!empty($f['required']) ? ' *' : '');
    $help = isset($f['help']) ? '<div class="form-text">' . e($f['help']) . '</div>' : '';

    switch ($f['type']) {
        case 'switch': ?>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" role="switch" id="<?= $name ?>" name="<?= $name ?>"<?= checked((bool) $value) ?>>
                <label class="form-check-label" for="<?= $name ?>"><?= $label ?></label>
            </div>
            <?php return;
        case 'image': ?>
            <div class="mb-3">
                <label class="form-label" for="<?= $name ?>"><?= $label ?></label>
                <?php if ($value): ?><img src="<?= e(img($value, 600, 400, $name)) ?>" alt="" class="preview-img mb-2"><?php endif; ?>
                <input type="file" class="form-control<?= $cls ?>" id="<?= $name ?>" name="<?= $name ?>" accept="image/jpeg,image/png,image/webp">
                <?= error_text($errors, $name) ?>
                <div class="form-text">JPG, PNG veya WEBP · en fazla 5 MB<?= $value ? ' · yeni görsel seçerseniz eskisinin yerine geçer' : '' ?></div>
            </div>
            <?php return;
        case 'html':
        case 'textarea': ?>
            <div class="mb-3">
                <label class="form-label" for="<?= $name ?>"><?= $label ?></label>
                <textarea class="form-control<?= $cls ?><?= $f['type'] === 'html' ? ' font-monospace small' : '' ?>" id="<?= $name ?>" name="<?= $name ?>" rows="<?= (int) ($f['rows'] ?? 3) ?>"<?= isset($f['max']) ? ' maxlength="' . (int) $f['max'] . '"' : '' ?>><?= e((string) $value) ?></textarea>
                <?= error_text($errors, $name) ?>
                <?= $f['type'] === 'html' ? '<div class="form-text">HTML kullanabilirsiniz (p, h2–h5, strong, ul, li, a, img, table…). Script, stil ve olay öznitelikleri güvenlik nedeniyle otomatik temizlenir.</div>' : $help ?>
            </div>
            <?php return;
        case 'slug': ?>
            <div class="mb-3">
                <label class="form-label" for="<?= $name ?>"><?= $label ?></label>
                <input class="form-control" id="<?= $name ?>" name="<?= $name ?>" value="<?= e((string) $value) ?>" placeholder="otomatik oluşturulur">
                <div class="form-text"><?= e(url($f['prefix'])) ?>…</div>
            </div>
            <?php return;
        case 'rating': ?>
            <div class="mb-3">
                <label class="form-label"><?= $label ?></label>
                <div class="rating-input">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="<?= $name . $i ?>" name="<?= $name ?>" value="<?= $i ?>"<?= checked((int) $value === $i) ?>><label for="<?= $name . $i ?>" title="<?= $i ?> yıldız">★</label>
                    <?php endfor; ?>
                </div>
            </div>
            <?php return;
        default:
            $type = ['number' => 'number', 'date' => 'date'][$f['type']] ?? 'text'; ?>
            <div class="mb-3">
                <label class="form-label" for="<?= $name ?>"><?= $label ?></label>
                <input type="<?= $type ?>" class="form-control<?= $cls ?>" id="<?= $name ?>" name="<?= $name ?>" value="<?= e((string) $value) ?>"<?= isset($f['max']) ? ' maxlength="' . (int) $f['max'] . '"' : '' ?><?= !empty($f['required']) ? ' required' : '' ?><?= $name === $def['title_field'] && isset($def['fields']['slug']) ? ' data-slug-source="#slug"' : '' ?>>
                <?= error_text($errors, $name) ?>
                <?= $help ?>
            </div>
    <?php }
};
$main = array_filter($def['fields'], fn($f) => empty($f['side']));
$side = array_filter($def['fields'], fn($f) => !empty($f['side']));
?>
<div class="page-head">
    <div>
        <a href="<?= admin_url($def['route']) ?>" class="back-link"><i class="bi bi-arrow-left"></i> <?= e($def['plural']) ?></a>
        <h1><?= $id ? e(str_replace("\n", ' ', (string) $row[$def['title_field']])) : 'Yeni ' . e($def['singular']) ?></h1>
    </div>
    <?php if ($id && isset($def['public_url']) && $row['is_active']): ?>
        <a href="<?= url($def['public_url']($row)) ?>" target="_blank" rel="noopener" class="btn btn-light-x"><i class="bi bi-eye me-1"></i>Mağazada gör</a>
    <?php endif; ?>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Lütfen işaretli alanları kontrol edin.</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="row g-4" data-dirty-check>
    <?= csrf_field() ?>
    <div class="col-xl-8">
        <section class="card-x card-x--pad">
            <?php foreach ($main as $name => $f) $renderField($name, $f); ?>
        </section>
    </div>
    <div class="col-xl-4">
        <div class="sticky-side">
            <section class="card-x card-x--pad mb-4">
                <?php foreach ($side as $name => $f) $renderField($name, $f); ?>
            </section>
            <button class="btn btn-brand btn-lg w-100"><i class="bi bi-check2 me-1"></i>Kaydet</button>
        </div>
    </div>
</form>

<?php if ($id): ?>
    <form method="post" action="<?= admin_url($def['route'] . '/' . $id . '/sil') ?>" class="danger-zone" data-confirm="Bu kayıt kalıcı olarak silinsin mi?">
        <?= csrf_field() ?>
        <div><strong><?= e($def['singular']) ?> sil</strong><p>Bu işlem geri alınamaz.</p></div>
        <button class="btn btn-outline-danger">Sil</button>
    </form>
<?php endif; ?>
