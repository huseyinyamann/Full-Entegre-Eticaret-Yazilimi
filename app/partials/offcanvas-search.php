<div class="offcanvas offcanvas-end search-panel" tabindex="-1" id="searchPanel" aria-label="Arama">
    <div class="offcanvas-header">
        <h5 class="mb-0">Ürün Ara</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Kapat"></button>
    </div>
    <div class="offcanvas-body">
        <form action="<?= url('arama') ?>" method="get" class="search-form">
            <input type="search" name="q" placeholder="Ne aramıştın?" autocomplete="off" required>
            <button type="submit" aria-label="Ara"><i class="bi bi-search"></i></button>
        </form>
        <h6 class="search-panel__label">Popüler aramalar</h6>
        <div class="search-tags">
            <?php foreach (['Mont', 'Sırt Çantası', 'Sneaker', 'Elbise', 'Saat'] as $tag): ?>
                <a href="<?= url('arama?q=' . urlencode($tag)) ?>"><?= e($tag) ?></a>
            <?php endforeach; ?>
        </div>
        <h6 class="search-panel__label">Sizin için seçtiklerimiz</h6>
        <div class="search-suggest">
            <?php foreach (products_by(['badge' => 'cok-satan'], 4) as $p): ?>
                <a href="<?= url('urun/' . $p['slug']) ?>" class="mini-product">
                    <img src="<?= img($p['images'][0], 120, 160, 'p' . $p['id']) ?>" alt="" loading="lazy">
                    <span><?= e($p['name']) ?><strong><?= money($p['price']) ?></strong></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
