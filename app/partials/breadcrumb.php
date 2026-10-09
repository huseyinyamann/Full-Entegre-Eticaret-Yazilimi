<?php /** @var string $heading @var array $items [label => url|null] */ ?>
<div class="page-head">
    <div class="container-xl">
        <h1><?= e($heading) ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Anasayfa</a></li>
                <?php foreach ($items as $label => $link): ?>
                    <?php if ($link): ?>
                        <li class="breadcrumb-item"><a href="<?= url($link) ?>"><?= e($label) ?></a></li>
                    <?php else: ?>
                        <li class="breadcrumb-item active" aria-current="page"><?= e($label) ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>
    </div>
</div>
