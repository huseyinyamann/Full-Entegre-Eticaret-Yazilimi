<?php
/** @var string $heading @var array $crumbs @var array $items @var array $filter @var ?array $category */
$sorts = ['' => 'Önerilen', 'yeni' => 'En Yeniler', 'fiyat-artan' => 'Fiyat: Düşükten Yükseğe', 'fiyat-azalan' => 'Fiyat: Yüksekten Düşüğe', 'indirim' => 'İndirim Oranı'];
$qs = fn(array $extra) => '?' . http_build_query(array_filter(array_merge($_GET, $extra), fn($v) => $v !== '' && $v !== null));
?>
<?php partial('breadcrumb', ['heading' => $heading, 'items' => $crumbs]); ?>

<section class="section pt-4">
    <div class="container-xl">
        <div class="listing-toolbar">
            <button class="btn btn-outline-brand btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterPanel">
                <i class="bi bi-funnel"></i> Filtrele
            </button>
            <span class="listing-toolbar__count"><?= count($items) ?> ürün listeleniyor</span>
            <div class="listing-toolbar__right">
                <div class="grid-switch d-none d-md-flex">
                    <?php foreach ([2, 3, 4] as $c): ?>
                        <button type="button" data-grid="<?= $c ?>" class="<?= $c === 4 ? 'active' : '' ?>" aria-label="<?= $c ?> sütun">
                            <?= str_repeat('<i></i>', $c) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <select class="form-select form-select-sm" data-sort-select>
                    <?php foreach ($sorts as $val => $label): ?>
                        <option value="<?= e($qs(['sirala' => $val])) ?>" <?= ($filter['sort'] ?? '') === $val ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row g-4">
            <aside class="col-lg-3">
                <div class="offcanvas-lg offcanvas-start filter-panel" tabindex="-1" id="filterPanel">
                    <div class="offcanvas-header">
                        <h5 class="mb-0">Filtrele</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#filterPanel" aria-label="Kapat"></button>
                    </div>
                    <div class="offcanvas-body flex-column">
                        <div class="filter-block">
                            <h6>Kategoriler</h6>
                            <ul class="filter-list">
                                <?php foreach (categories() as $c): ?>
                                    <li class="<?= ($category['slug'] ?? '') === $c['slug'] ? 'active' : '' ?>">
                                        <a href="<?= url('kategori/' . $c['slug']) ?>"><?= e($c['name']) ?> <span><?= count(products_by(['category' => $c['slug']])) ?></span></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php if ($category): ?>
                            <div class="filter-block">
                                <h6>Alt Kategoriler</h6>
                                <ul class="filter-list">
                                    <?php foreach ($category['children'] as $child): ?>
                                        <li class="<?= ($_GET['alt'] ?? '') === $child['slug'] ? 'active' : '' ?>"><a href="<?= e($qs(['alt' => $child['slug']])) ?>"><?= e($child['name']) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <form class="filter-block" method="get">
                            <h6>Fiyat Aralığı</h6>
                            <?php foreach (['q', 'sirala', 'alt'] as $keep): if (!empty($_GET[$keep])): ?>
                                <input type="hidden" name="<?= $keep ?>" value="<?= e($_GET[$keep]) ?>">
                            <?php endif; endforeach; ?>
                            <div class="d-flex gap-2 mb-2">
                                <input type="number" name="min" class="form-control form-control-sm" placeholder="En az" value="<?= e($_GET['min'] ?? '') ?>" min="0">
                                <input type="number" name="max" class="form-control form-control-sm" placeholder="En çok" value="<?= e($_GET['max'] ?? '') ?>" min="0">
                            </div>
                            <button class="btn btn-dark-brand btn-sm w-100">Uygula</button>
                        </form>
                        <div class="filter-block">
                            <h6>Renk</h6>
                            <div class="filter-swatches">
                                <?php foreach (['#111111' => 'Siyah', '#1c355e' => 'Lacivert', '#d8c3a5' => 'Bej', '#f5f5f5' => 'Beyaz', '#6b4a2f' => 'Kahve'] as $hex => $name): ?>
                                    <span style="--swatch: <?= $hex ?>" title="<?= e($name) ?>"></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <a href="<?= url('kategori/kadin') ?>" class="promo-banner promo-banner--small d-none d-lg-block">
                            <img src="<?= img('demo/lookbook-3.jpg', 500, 640, 'side-banner') ?>" alt="" loading="lazy">
                            <div class="promo-banner__text"><small>Yeni Sezon</small><h3>%30 İndirim</h3></div>
                        </a>
                    </div>
                </div>
            </aside>

            <div class="col-lg-9">
                <?php if ($items): ?>
                    <div class="product-grid" data-product-grid style="--cols: 3">
                        <?php foreach ($items as $p): ?>
                            <?php partial('product-card', ['p' => $p]); ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-search"></i>
                        <h4>Ürün bulunamadı</h4>
                        <p>Filtreleri değiştirerek tekrar deneyebilirsin.</p>
                        <a href="<?= url('kategoriler') ?>" class="btn btn-dark-brand">Tüm Kategoriler</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
