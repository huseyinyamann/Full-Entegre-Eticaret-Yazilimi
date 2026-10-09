<?php /** @var string $heading @var array $crumbs @var string $content (güvenli HTML) */
partial('breadcrumb', ['heading' => $heading, 'items' => $crumbs]);
?>
<section class="section pt-4">
    <div class="container-xl">
        <div class="content-body">
            <?= $content ?>
        </div>
    </div>
</section>
