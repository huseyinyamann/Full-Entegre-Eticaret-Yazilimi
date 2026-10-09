<?php /** @var array $p */
$cartData = json_encode(['id' => $p['id'], 'name' => $p['name'], 'price' => $p['price'], 'image' => img($p['images'][0], 300, 400, 'p' . $p['id']), 'url' => url('urun/' . $p['slug'])], JSON_UNESCAPED_UNICODE);
?>
<div class="row g-0">
    <div class="col-md-6"><img class="quick-view__img" src="<?= img($p['images'][0], 700, 900, 'p' . $p['id']) ?>" alt="<?= e($p['name']) ?>"></div>
    <div class="col-md-6">
        <div class="product-info p-4 p-lg-5" data-product="<?= e($cartData) ?>">
            <h3 class="product-info__title"><?= e($p['name']) ?></h3>
            <div class="product-info__price">
                <span class="price"><?= money($p['price']) ?></span>
                <?php if ($p['old_price']): ?><del><?= money($p['old_price']) ?></del><?php endif; ?>
            </div>
            <p class="product-info__summary"><?= e($p['summary']) ?></p>
            <?php if ($p['sizes']): ?>
                <div class="product-option">
                    <div class="product-option__label">Beden: <strong data-size-label>Seçiniz</strong></div>
                    <div class="size-options">
                        <?php foreach ($p['sizes'] as $s): ?>
                            <label><input type="radio" name="qv-size" value="<?= e($s) ?>"><span><?= e($s) ?></span></label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="product-buy">
                <div class="qty">
                    <button type="button" data-qty="-1">−</button>
                    <input type="number" value="1" min="1" data-qty-input>
                    <button type="button" data-qty="1">+</button>
                </div>
                <button type="button" class="btn btn-dark-brand flex-grow-1" data-add-detail <?= $p['stock'] ? '' : 'disabled' ?>>Sepete Ekle</button>
            </div>
            <a href="<?= url('urun/' . $p['slug']) ?>" class="link-more mt-3 d-inline-block">Ürün detayına git <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</div>
