<?php
/** @var string $heading @var array $items @var ?string $subheading @var ?string $more */
?>
<section class="section">
    <div class="container-xl">
        <div class="section-head">
            <div>
                <h2 class="section-title"><?= e($heading) ?></h2>
                <?php if (!empty($subheading)): ?><p class="section-sub"><?= e($subheading) ?></p><?php endif; ?>
            </div>
            <div class="section-head__nav">
                <?php if (!empty($more)): ?><a href="<?= url($more) ?>" class="link-more">Tümünü Gör <i class="bi bi-arrow-right"></i></a><?php endif; ?>
                <button class="slider-arrow" data-prev aria-label="Önceki"><i class="bi bi-chevron-left"></i></button>
                <button class="slider-arrow" data-next aria-label="Sonraki"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
        <div class="swiper product-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($items as $p): ?>
                    <div class="swiper-slide"><?php partial('product-card', ['p' => $p]); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
