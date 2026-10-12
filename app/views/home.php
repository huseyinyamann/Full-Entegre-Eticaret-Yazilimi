<!-- Slider -->
<section class="hero">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            <?php foreach (slides() as $i => $s): ?>
                <div class="swiper-slide">
                    <div class="hero__slide">
                        <img src="<?= img($s['image'], 1920, 820, 'hero' . $i) ?>" alt="" <?= $i ? 'loading="lazy"' : 'fetchpriority="high"' ?>>
                        <div class="container-xl">
                            <div class="hero__content">
                                <h2><?= nl2br(e($s['title'])) ?></h2>
                                <p><?= e($s['text']) ?></p>
                                <a href="<?= url($s['link']) ?>" class="btn btn-light-brand"><?= e($s['button']) ?> <i class="bi bi-arrow-up-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<!-- Kayan yazı -->
<div class="marquee">
    <div class="marquee__track">
        <?php for ($r = 0; $r < 2; $r++): ?>
            <?php foreach (['Ücretsiz Kargo', 'Güvenli Ödeme', '14 Gün İade', 'Yeni Sezon', 'Taksit İmkânı', 'Hızlı Teslimat'] as $t): ?>
                <span><?= e($t) ?> <i class="bi bi-asterisk"></i></span>
            <?php endforeach; ?>
        <?php endfor; ?>
    </div>
</div>

<!-- Kategoriler -->
<section class="section">
    <div class="container-xl">
        <div class="section-head">
            <h2 class="section-title">Kategorileri Keşfet</h2>
            <div class="section-head__nav">
                <button class="slider-arrow" data-prev aria-label="Önceki"><i class="bi bi-chevron-left"></i></button>
                <button class="slider-arrow" data-next aria-label="Sonraki"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
        <div class="swiper category-swiper">
            <div class="swiper-wrapper">
                <?php foreach (categories() as $cat): ?>
                    <div class="swiper-slide">
                        <a href="<?= url('kategori/' . $cat['slug']) ?>" class="category-card">
                            <img src="<?= img($cat['image'], 500, 640, 'cat-' . $cat['slug']) ?>" alt="<?= e($cat['name']) ?>" loading="lazy">
                            <span class="category-card__label"><?= e($cat['name']) ?> <i class="bi bi-arrow-up-right"></i></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php partial('product-slider', ['heading' => 'Yeni Gelenler', 'subheading' => 'Bu hafta raflara eklenen ürünler', 'items' => products_by(['sort' => 'yeni'], 10), 'more' => 'arama?sirala=yeni']); ?>

<!-- Çift banner -->
<section class="section pt-0">
    <div class="container-xl">
        <div class="row g-3">
            <div class="col-md-6">
                <a href="<?= url('kategori/kadin') ?>" class="promo-banner">
                    <img src="<?= img('demo/lookbook-3.jpg', 900, 560, 'promo1') ?>" alt="" loading="lazy">
                    <div class="promo-banner__text">
                        <small>Kadın Koleksiyonu</small>
                        <h3>Sezonun Favorileri</h3>
                        <span class="btn btn-light-brand btn-sm">İncele</span>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a href="<?= url('kategori/erkek') ?>" class="promo-banner">
                    <img src="<?= img('demo/lookbook-4.jpg', 900, 560, 'promo2') ?>" alt="" loading="lazy">
                    <div class="promo-banner__text">
                        <small>Erkek Koleksiyonu</small>
                        <h3>Şehir Stili</h3>
                        <span class="btn btn-light-brand btn-sm">İncele</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<?php partial('product-slider', ['heading' => 'İndirimdeki Ürünler', 'subheading' => 'Kaçırılmayacak fiyatlar', 'items' => products_by(['sort' => 'indirim'], 10), 'more' => 'arama?sirala=indirim']); ?>
<?php partial('product-slider', ['heading' => 'Çok Satanlar', 'subheading' => 'Müşterilerimizin en sevdikleri', 'items' => array_merge(products_by(['badge' => 'cok-satan']), products_by([], 6)), 'more' => 'kategoriler']); ?>

<!-- Geri sayımlı kampanya -->
<section class="section pt-0">
    <div class="container-xl">
        <div class="deal-box">
            <div class="deal-box__text">
                <small>Sadece bugüne özel</small>
                <h3>Günün Fırsatı</h3>
                <p>Seçili ürünlerde ek %15 indirim. Kampanya bitmeden yakala!</p>
                <div class="countdown" data-countdown="<?= date('Y-m-d') ?>T23:59:59">
                    <div><b data-d>00</b><span>Gün</span></div>
                    <div><b data-h>00</b><span>Saat</span></div>
                    <div><b data-m>00</b><span>Dak</span></div>
                    <div><b data-s>00</b><span>San</span></div>
                </div>
                <a href="<?= url('arama?sirala=indirim') ?>" class="btn btn-light-brand mt-3">Fırsatları Gör</a>
            </div>
            <div class="deal-box__img">
                <img src="<?= img('demo/8.jpg', 900, 600, 'deal') ?>" alt="" loading="lazy">
            </div>
        </div>
    </div>
</section>

<?php partial('product-slider', ['heading' => 'Çanta Modelleri', 'items' => products_by(['category' => 'canta']), 'more' => 'kategori/canta']); ?>
<?php partial('product-slider', ['heading' => 'Ayakkabı Modelleri', 'items' => products_by(['category' => 'ayakkabi']), 'more' => 'kategori/ayakkabi']); ?>

<!-- Yorumlar -->
<section class="section section--soft">
    <div class="container-xl">
        <div class="section-head justify-content-center text-center">
            <div>
                <h2 class="section-title">Mutlu Müşterilerimiz</h2>
                <p class="section-sub">Alışveriş yapanlar ne diyor?</p>
            </div>
        </div>
        <div class="swiper testimonial-swiper">
            <div class="swiper-wrapper">
                <?php foreach (testimonials() as $t): ?>
                    <div class="swiper-slide">
                        <div class="testimonial">
                            <div class="stars"><?= str_repeat('<i class="bi bi-star-fill"></i>', $t['rating']) . str_repeat('<i class="bi bi-star"></i>', 5 - $t['rating']) ?></div>
                            <p>“<?= e($t['text']) ?>”</p>
                            <div class="testimonial__author"><strong><?= e($t['name']) ?></strong><span><?= e($t['city']) ?></span></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<!-- Blog -->
<section class="section">
    <div class="container-xl">
        <div class="section-head">
            <h2 class="section-title">Blogdan</h2>
            <a href="<?= url('blog') ?>" class="link-more">Tüm Yazılar <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach (posts(3) as $i => $post): ?>
                <div class="col-md-4"><?php partial('post-card', ['post' => $post, 'i' => $i]); ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Avantajlar -->
<section class="section pt-0">
    <div class="container-xl">
        <div class="features">
            <?php foreach ([
                ['truck', 'Ücretsiz Kargo', money((float) config('free_shipping_limit')) . ' üzeri siparişlerde'],
                ['arrow-counterclockwise', 'Kolay İade', '14 gün içinde ücretsiz iade'],
                ['shield-check', 'Güvenli Ödeme', '256-bit SSL ile korunur'],
                ['headset', 'Destek', 'Hafta içi 09:00 – 18:00'],
            ] as [$icon, $title, $text]): ?>
                <div class="feature">
                    <i class="bi bi-<?= $icon ?>"></i>
                    <h6><?= e($title) ?></h6>
                    <p><?= e($text) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
