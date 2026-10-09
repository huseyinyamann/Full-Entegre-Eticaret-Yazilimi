<footer class="site-footer">
    <div class="container-xl">
        <div class="row g-4 site-footer__body">
            <div class="col-lg-3 col-md-6">
                <a href="<?= url() ?>" class="logo logo--light mb-3">
                    <span class="logo__mark">Y</span>
                    <span class="logo__text">Yaman<strong>Ticaret</strong></span>
                </a>
                <ul class="footer-contact">
                    <li><i class="bi bi-geo-alt"></i> <?= e(config('address')) ?></li>
                    <li><i class="bi bi-envelope"></i> <a href="mailto:<?= e(config('email')) ?>"><?= e(config('email')) ?></a></li>
                    <li><i class="bi bi-telephone"></i> <a href="tel:<?= e(preg_replace('/\s+/', '', config('phone'))) ?>"><?= e(config('phone')) ?></a></li>
                </ul>
                <ul class="footer-social">
                    <?php foreach (config('social') as $name => $link): ?>
                        <li><a href="<?= e($link) ?>" aria-label="<?= e($name) ?>"><i class="bi bi-<?= $name === 'x' ? 'twitter-x' : e($name) ?>"></i></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 footer-col">
                <h6 class="footer-heading" data-collapse>Hızlı Erişim <i class="bi bi-plus d-md-none"></i></h6>
                <ul class="footer-links">
                    <li><a href="<?= url('giris') ?>">Hesabım</a></li>
                    <li><a href="<?= url('sepet') ?>">Sepetim</a></li>
                    <li><a href="<?= url('favorilerim') ?>">Favorilerim</a></li>
                    <li><a href="<?= url('siparis-takip') ?>">Sipariş Takibi</a></li>
                    <li><a href="<?= url('banka-hesaplari') ?>">Banka Hesapları</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 footer-col">
                <h6 class="footer-heading" data-collapse>Kurumsal <i class="bi bi-plus d-md-none"></i></h6>
                <ul class="footer-links">
                    <?php foreach (pages() as $slug => $label): ?>
                        <li><a href="<?= url('sayfa/' . $slug) ?>"><?= e($label) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 footer-col">
                <h6 class="footer-heading" data-collapse>Kategoriler <i class="bi bi-plus d-md-none"></i></h6>
                <ul class="footer-links">
                    <?php foreach (categories() as $cat): ?>
                        <li><a href="<?= url('kategori/' . $cat['slug']) ?>"><?= e($cat['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="col-lg-3 col-md-12">
                <h6 class="footer-heading">Bültene Kayıt Ol</h6>
                <p class="footer-text">Kampanya ve yeni ürünlerden ilk sen haberdar ol.</p>
                <form class="newsletter-form" data-newsletter>
                    <input type="email" name="email" placeholder="E-posta adresin" required>
                    <button type="submit" aria-label="Kayıt ol"><i class="bi bi-arrow-right"></i></button>
                </form>
            </div>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="container-xl">
            <p>© <?= date('Y') ?> <?= e(config('site_name')) ?>. Tüm hakları saklıdır.</p>
            <div class="payment-icons">
                <i class="bi bi-credit-card-2-front"></i>
                <span>VISA</span><span>Mastercard</span><span>Troy</span>
                <i class="bi bi-shield-lock"></i> <small>256-bit SSL</small>
            </div>
        </div>
    </div>
</footer>
