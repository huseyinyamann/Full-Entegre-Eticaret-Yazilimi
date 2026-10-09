<?php partial('breadcrumb', ['heading' => 'Blog', 'items' => ['Blog' => null]]); ?>

<section class="section pt-4">
    <div class="container-xl">
        <div class="row g-4">
            <?php foreach (posts() as $i => $post): ?>
                <div class="col-md-6 col-lg-4"><?php partial('post-card', ['post' => $post, 'i' => $i]); ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
