<div class="top-bar">
    <div class="container-xl">
        <div class="top-bar__wrap">
            <ul class="top-bar__social d-none d-lg-flex">
                <?php foreach (config('social') as $name => $link): ?>
                    <li><a href="<?= e($link) ?>" aria-label="<?= e($name) ?>"><i class="bi bi-<?= $name === 'x' ? 'twitter-x' : e($name) ?>"></i></a></li>
                <?php endforeach; ?>
            </ul>

            <div class="swiper top-bar__ticker">
                <div class="swiper-wrapper">
                    <?php foreach (config('announcements') as $text): ?>
                        <div class="swiper-slide"><?= e($text) ?> <a href="<?= url('kategori/kadin') ?>">Alışverişe başla</a></div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="top-bar__links d-none d-lg-flex">
                <a href="tel:<?= e(preg_replace('/\s+/', '', config('phone'))) ?>"><i class="bi bi-telephone"></i> <?= e(config('phone')) ?></a>
                <a href="<?= url('sayfa/sikca-sorulan-sorular') ?>">Yardım</a>
            </div>
        </div>
    </div>
</div>
