<?php
/** @var array $p */
$link = url('urun/' . $p['slug']);
$rate = discount_rate($p);
$badgeLabels = ['yeni' => 'Yeni', 'indirim' => 'İndirim', 'cok-satan' => 'Çok Satan'];
$cartData = e(json_encode(['id' => $p['id'], 'name' => $p['name'], 'price' => $p['price'], 'image' => img($p['images'][0], 300, 400, 'p' . $p['id']), 'url' => $link], JSON_UNESCAPED_UNICODE));
?>
<div class="product-card">
    <div class="product-card__media">
        <a href="<?= $link ?>" class="product-card__img">
            <img class="img-main" src="<?= img($p['images'][0], 600, 800, 'p' . $p['id']) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
            <img class="img-hover" src="<?= img($p['images'][1], 600, 800, 'p' . $p['id'] . 'b') ?>" alt="" loading="lazy">
        </a>

        <div class="product-card__badges">
            <?php if ($rate): ?><span class="badge-tag badge-tag--sale">-%<?= $rate ?></span><?php endif; ?>
            <?php if ($p['badge'] && $p['badge'] !== 'indirim'): ?>
                <span class="badge-tag badge-tag--<?= e($p['badge']) ?>"><?= e($badgeLabels[$p['badge']]) ?></span>
            <?php endif; ?>
            <?php if (!$p['stock']): ?><span class="badge-tag badge-tag--soldout">Tükendi</span><?php endif; ?>
        </div>

        <div class="product-card__actions">
            <button type="button" class="action-btn" data-add-cart="<?= $cartData ?>" title="Sepete ekle" <?= $p['stock'] ? '' : 'disabled' ?>><i class="bi bi-bag-plus"></i></button>
            <button type="button" class="action-btn" data-wishlist="<?= $p['id'] ?>" title="Favorilere ekle"><i class="bi bi-heart"></i></button>
            <button type="button" class="action-btn" data-quick-view="<?= e($p['slug']) ?>" title="Hızlı bakış"><i class="bi bi-eye"></i></button>
        </div>

        <?php if ($p['sizes']): ?>
            <div class="product-card__sizes">
                <?php foreach ($p['sizes'] as $s): ?><span><?= e($s) ?></span><?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="product-card__body">
        <a href="<?= $link ?>" class="product-card__title"><?= e($p['name']) ?></a>
        <div class="product-card__price">
            <span class="price"><?= money($p['price']) ?></span>
            <?php if ($p['old_price']): ?><del><?= money($p['old_price']) ?></del><?php endif; ?>
        </div>
        <ul class="product-card__colors">
            <?php foreach ($p['colors'] as [$cname, $hex]): ?>
                <li style="--swatch: <?= e($hex) ?>" title="<?= e($cname) ?>"></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
