<?php
/** @var array $p */
$cat = categories()[$p['category']];
$rate = discount_rate($p);
$cartData = json_encode(['id' => $p['id'], 'name' => $p['name'], 'price' => $p['price'], 'image' => img($p['images'][0], 300, 400, 'p' . $p['id']), 'url' => url('urun/' . $p['slug'])], JSON_UNESCAPED_UNICODE);
?>
<div class="container-xl">
    <nav aria-label="breadcrumb" class="pt-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= url() ?>">Anasayfa</a></li>
            <li class="breadcrumb-item"><a href="<?= url('kategori/' . $cat['slug']) ?>"><?= e($cat['name']) ?></a></li>
            <li class="breadcrumb-item active"><?= e($p['name']) ?></li>
        </ol>
    </nav>
</div>

<section class="section pt-2">
    <div class="container-xl">
        <div class="row g-4 g-lg-5">
            <div class="col-lg-7">
                <div class="product-gallery">
                    <div class="swiper gallery-thumbs">
                        <div class="swiper-wrapper">
                            <?php foreach ($p['images'] as $i => $im): ?>
                                <div class="swiper-slide"><img src="<?= img($im, 150, 200, 'p' . $p['id'] . ($i ? chr(97 + $i) : '')) ?>" alt=""></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="swiper gallery-main">
                        <div class="swiper-wrapper">
                            <?php foreach ($p['images'] as $i => $im): ?>
                                <div class="swiper-slide"><img src="<?= img($im, 900, 1200, 'p' . $p['id'] . ($i ? chr(97 + $i) : '')) ?>" alt="<?= e($p['name']) ?>"></div>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="product-info" data-product="<?= e($cartData) ?>">
                    <?php if ($p['badge'] === 'cok-satan'): ?><span class="badge-tag badge-tag--cok-satan mb-2">Çok Satan</span><?php endif; ?>
                    <h1 class="product-info__title"><?= e($p['name']) ?></h1>
                    <div class="product-info__meta">
                        <span class="stars"><?php for ($s = 1; $s <= 5; $s++): ?><i class="bi bi-star<?= $s <= $p['rating'] ? '-fill' : ($s - 0.5 <= $p['rating'] ? '-half' : '') ?>"></i><?php endfor; ?></span>
                        <span>(<?= $p['reviews'] ?> değerlendirme)</span>
                        <span>Stok kodu: <?= e($p['sku']) ?></span>
                    </div>
                    <div class="product-info__price">
                        <span class="price"><?= money($p['price']) ?></span>
                        <?php if ($p['old_price']): ?>
                            <del><?= money($p['old_price']) ?></del>
                            <span class="badge-tag badge-tag--sale">-%<?= $rate ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="product-info__summary"><?= e($p['summary']) ?></p>

                    <div class="product-option">
                        <div class="product-option__label">Renk: <strong data-color-label><?= e($p['colors'][0][0]) ?></strong></div>
                        <div class="color-options">
                            <?php foreach ($p['colors'] as $i => [$cname, $hex]): ?>
                                <label><input type="radio" name="color" value="<?= e($cname) ?>" <?= $i ? '' : 'checked' ?>><span style="--swatch: <?= e($hex) ?>" title="<?= e($cname) ?>"></span></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ($p['sizes']): ?>
                        <div class="product-option">
                            <div class="product-option__label d-flex justify-content-between">
                                <span>Beden: <strong data-size-label>Seçiniz</strong></span>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#sizeGuide">Beden tablosu</a>
                            </div>
                            <div class="size-options">
                                <?php foreach ($p['sizes'] as $s): ?>
                                    <label><input type="radio" name="size" value="<?= e($s) ?>"><span><?= e($s) ?></span></label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="product-buy">
                        <div class="qty">
                            <button type="button" data-qty="-1" aria-label="Azalt">−</button>
                            <input type="number" value="1" min="1" max="<?= max(1, $p['stock']) ?>" data-qty-input>
                            <button type="button" data-qty="1" aria-label="Arttır">+</button>
                        </div>
                        <?php if ($p['stock']): ?>
                            <button type="button" class="btn btn-dark-brand flex-grow-1" data-add-detail>Sepete Ekle — <span><?= money($p['price']) ?></span></button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary flex-grow-1" disabled>Stokta Yok</button>
                        <?php endif; ?>
                        <button type="button" class="icon-btn icon-btn--bordered" data-wishlist="<?= $p['id'] ?>" aria-label="Favorilere ekle"><i class="bi bi-heart"></i></button>
                    </div>
                    <?php if ($p['stock']): ?>
                        <a href="<?= url('odeme') ?>" class="btn btn-outline-brand w-100 mt-2" data-buy-now>Hemen Al</a>
                    <?php endif; ?>

                    <ul class="product-perks">
                        <li><i class="bi bi-truck"></i> <?= money((float) config('free_shipping_limit')) ?> üzeri ücretsiz kargo</li>
                        <li><i class="bi bi-clock"></i> Saat 15:00'e kadar verilen siparişler aynı gün kargoda</li>
                        <li><i class="bi bi-arrow-counterclockwise"></i> 14 gün içinde ücretsiz iade</li>
                    </ul>

                    <div class="product-share">
                        <span>Paylaş:</span>
                        <a href="https://wa.me/?text=<?= urlencode($p['name']) ?>" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i></a>
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter-x"></i></a>
                        <a href="#"><i class="bi bi-pinterest"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="product-tabs">
            <ul class="nav nav-underline" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-desc" type="button">Ürün Açıklaması</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-info" type="button">Ek Bilgiler</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-reviews" type="button">Yorumlar (<?= $p['reviews'] ?>)</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-return" type="button">İade & Kargo</button></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-desc">
                    <p><?= e($p['summary']) ?></p>
                    <ul>
                        <li>Nefes alabilen, cilt dostu malzeme</li>
                        <li>Günlük kullanıma uygun dayanıklı yapı</li>
                        <li>Türkiye'de üretilmiştir</li>
                    </ul>
                </div>
                <div class="tab-pane fade" id="tab-info">
                    <table class="table table-sm w-auto">
                        <tr><th>Stok kodu</th><td><?= e($p['sku']) ?></td></tr>
                        <tr><th>Kategori</th><td><?= e($cat['name']) ?></td></tr>
                        <tr><th>Renkler</th><td><?= e(implode(', ', array_column($p['colors'], 0))) ?></td></tr>
                        <?php if ($p['sizes']): ?><tr><th>Bedenler</th><td><?= e(implode(', ', $p['sizes'])) ?></td></tr><?php endif; ?>
                    </table>
                </div>
                <div class="tab-pane fade" id="tab-reviews">
                    <p class="text-muted">Bu ürün için henüz yorum yazılmamış. İlk yorumu sen yaz!</p>
                </div>
                <div class="tab-pane fade" id="tab-return">
                    <p>Siparişin, onaylandıktan sonra 1-3 iş günü içinde kargoya verilir. Ürünü teslim aldıktan sonra 14 gün içinde ücretsiz iade edebilirsin.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php partial('product-slider', ['heading' => 'Benzer Ürünler', 'items' => array_filter(products_by(['category' => $p['category']]), fn($x) => $x['id'] !== $p['id']), 'more' => 'kategori/' . $p['category']]); ?>
<?php partial('product-slider', ['heading' => 'Bunlar da İlgini Çekebilir', 'items' => products_by(['sort' => 'indirim'], 8)]); ?>

<div class="modal fade" id="sizeGuide" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Beden Tablosu</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <table class="table table-bordered text-center mb-0">
                    <thead><tr><th>Beden</th><th>Göğüs</th><th>Bel</th><th>Basen</th></tr></thead>
                    <tbody>
                        <?php foreach ([['XS', 82, 64, 88], ['S', 86, 68, 92], ['M', 90, 72, 96], ['L', 96, 78, 102], ['XL', 102, 84, 108]] as $row): ?>
                            <tr><?php foreach ($row as $v): ?><td><?= $v ?></td><?php endforeach; ?></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="sticky-buy d-lg-none" data-sticky-buy>
    <img src="<?= img($p['images'][0], 80, 100, 'p' . $p['id']) ?>" alt="">
    <div><strong><?= e($p['name']) ?></strong><span><?= money($p['price']) ?></span></div>
    <button type="button" class="btn btn-dark-brand btn-sm" data-add-detail <?= $p['stock'] ? '' : 'disabled' ?>>Sepete Ekle</button>
</div>
