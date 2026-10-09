<header class="site-header" id="siteHeader">
    <div class="container-xl">
        <div class="site-header__row">
            <button class="icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-label="Menü">
                <i class="bi bi-list"></i>
            </button>

            <a href="<?= url() ?>" class="logo" aria-label="<?= e(config('site_name')) ?>">
                <span class="logo__mark">Y</span>
                <span class="logo__text">Yaman<strong>Ticaret</strong></span>
            </a>

            <nav class="main-nav d-none d-lg-block">
                <ul>
                    <li><a href="<?= url() ?>">Anasayfa</a></li>
                    <li class="has-mega">
                        <a href="<?= url('kategoriler') ?>">Kategoriler <i class="bi bi-chevron-down"></i></a>
                        <div class="mega-menu">
                            <div class="container-xl">
                                <div class="row g-4">
                                    <?php foreach (array_slice(categories(), 0, 4) as $cat): ?>
                                        <div class="col-lg-2">
                                            <a class="mega-menu__title" href="<?= url('kategori/' . $cat['slug']) ?>"><?= e($cat['name']) ?></a>
                                            <ul>
                                                <?php foreach ($cat['children'] as $child): ?>
                                                    <li><a href="<?= url('kategori/' . $cat['slug'] . '?alt=' . $child['slug']) ?>"><?= e($child['name']) ?></a></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="col-lg-4">
                                        <a href="<?= url('kategori/canta') ?>" class="mega-menu__banner">
                                            <img src="<?= img('demo/fashion-slideshow-03.jpg', 600, 360, 'mega-banner') ?>" alt="" loading="lazy">
                                            <span>Çanta Haftası<br><small>%30'a varan indirim</small></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php foreach (array_slice(categories(), 0, 4) as $cat): ?>
                        <li><a href="<?= url('kategori/' . $cat['slug']) ?>"><?= e($cat['name']) ?></a></li>
                    <?php endforeach; ?>
                    <li><a href="<?= url('blog') ?>">Blog</a></li>
                    <li><a href="<?= url('iletisim') ?>">İletişim</a></li>
                </ul>
            </nav>

            <div class="header-icons">
                <button class="icon-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#searchPanel" aria-label="Ara">
                    <i class="bi bi-search"></i>
                </button>
                <a class="icon-btn d-none d-lg-inline-flex" href="<?= url('giris') ?>" aria-label="Hesabım">
                    <i class="bi bi-person"></i>
                </a>
                <a class="icon-btn d-none d-lg-inline-flex" href="<?= url('favorilerim') ?>" aria-label="Favorilerim">
                    <i class="bi bi-heart"></i><span class="count" data-wishlist-count>0</span>
                </a>
                <button class="icon-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#miniCart" aria-label="Sepet">
                    <i class="bi bi-bag"></i><span class="count" data-cart-count>0</span>
                </button>
            </div>
        </div>
    </div>
</header>
