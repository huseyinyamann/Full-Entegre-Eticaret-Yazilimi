<?php partial('breadcrumb', ['heading' => 'Kategoriler', 'items' => ['Kategoriler' => null]]); ?>

<section class="section pt-4">
    <div class="container-xl">
        <div class="row g-3">
            <?php foreach (categories() as $cat): ?>
                <div class="col-6 col-md-4">
                    <a href="<?= url('kategori/' . $cat['slug']) ?>" class="category-card">
                        <img src="<?= img($cat['image'], 500, 640, 'cat-' . $cat['slug']) ?>" alt="<?= e($cat['name']) ?>" loading="lazy">
                        <span class="category-card__label"><?= e($cat['name']) ?> <i class="bi bi-arrow-up-right"></i></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
