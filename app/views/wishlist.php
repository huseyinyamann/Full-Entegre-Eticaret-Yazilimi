<?php partial('breadcrumb', ['heading' => 'Favorilerim', 'items' => ['Favorilerim' => null]]); ?>

<section class="section pt-4">
    <div class="container-xl">
        <div class="product-grid" style="--cols: 4" data-wishlist-grid>
            <?php foreach (products() as $p): ?>
                <div class="d-none" data-wishlist-item="<?= $p['id'] ?>"><?php partial('product-card', ['p' => $p]); ?></div>
            <?php endforeach; ?>
        </div>
        <div class="empty-state d-none" data-wishlist-empty>
            <i class="bi bi-heart"></i>
            <h4>Favori listen boş</h4>
            <p>Beğendiğin ürünlerdeki kalp ikonuna tıklayarak listene ekleyebilirsin.</p>
            <a href="<?= url('kategoriler') ?>" class="btn btn-dark-brand">Ürünleri Keşfet</a>
        </div>
    </div>
</section>
