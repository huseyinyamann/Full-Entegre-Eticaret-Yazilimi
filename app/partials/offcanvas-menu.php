<div class="offcanvas offcanvas-start mobile-menu" tabindex="-1" id="mobileMenu" aria-label="Menü">
    <div class="offcanvas-header">
        <a href="<?= url() ?>" class="logo"><span class="logo__mark">Y</span><span class="logo__text">Yaman<strong>Ticaret</strong></span></a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Kapat"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="mobile-nav">
            <li><a href="<?= url() ?>">Anasayfa</a></li>
            <?php foreach (categories() as $cat): ?>
                <li>
                    <div class="mobile-nav__row">
                        <a href="<?= url('kategori/' . $cat['slug']) ?>"><?= e($cat['name']) ?></a>
                        <button type="button" data-bs-toggle="collapse" data-bs-target="#mm-<?= e($cat['slug']) ?>" aria-label="Alt kategoriler"><i class="bi bi-chevron-down"></i></button>
                    </div>
                    <ul class="collapse" id="mm-<?= e($cat['slug']) ?>">
                        <?php foreach ($cat['children'] as $child): ?>
                            <li><a href="<?= url('kategori/' . $cat['slug'] . '?alt=' . $child['slug']) ?>"><?= e($child['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
            <li><a href="<?= url('blog') ?>">Blog</a></li>
            <li><a href="<?= url('iletisim') ?>">İletişim</a></li>
        </ul>
        <div class="mobile-menu__footer">
            <a href="<?= url('giris') ?>"><i class="bi bi-person"></i> Giriş Yap / Kayıt Ol</a>
            <a href="tel:<?= e(preg_replace('/\s+/', '', config('phone'))) ?>"><i class="bi bi-telephone"></i> <?= e(config('phone')) ?></a>
            <a href="mailto:<?= e(config('email')) ?>"><i class="bi bi-envelope"></i> <?= e(config('email')) ?></a>
        </div>
    </div>
</div>
