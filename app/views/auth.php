<?php /** @var string $mode giris|kayit|sifre */
$titles = ['giris' => 'Giriş Yap', 'kayit' => 'Kayıt Ol', 'sifre' => 'Şifremi Unuttum'];
partial('breadcrumb', ['heading' => $titles[$mode], 'items' => [$titles[$mode] => null]]);
?>
<section class="section pt-4">
    <div class="container-xl">
        <div class="row g-5 justify-content-center">
            <div class="col-lg-5">
                <form class="form-card" onsubmit="event.preventDefault(); App.toast('Üyelik sistemi yakında aktif olacak.', 'info')">
                    <h5><?= e($titles[$mode]) ?></h5>
                    <?php if ($mode === 'kayit'): ?>
                        <div class="row g-3 mb-3">
                            <div class="col-6"><input class="form-control" placeholder="Ad" required></div>
                            <div class="col-6"><input class="form-control" placeholder="Soyad" required></div>
                        </div>
                    <?php endif; ?>
                    <input type="email" class="form-control mb-3" placeholder="E-posta adresi" required>
                    <?php if ($mode !== 'sifre'): ?>
                        <input type="password" class="form-control mb-3" placeholder="Şifre" required minlength="6">
                    <?php endif; ?>
                    <?php if ($mode === 'giris'): ?>
                        <div class="d-flex justify-content-between small mb-3">
                            <label class="form-check"><input type="checkbox" class="form-check-input"> <span class="form-check-label">Beni hatırla</span></label>
                            <a href="<?= url('sifremi-unuttum') ?>">Şifremi unuttum</a>
                        </div>
                    <?php elseif ($mode === 'kayit'): ?>
                        <label class="form-check small mb-3"><input type="checkbox" class="form-check-input" required> <span class="form-check-label"><a href="<?= url('sayfa/gizlilik-politikasi') ?>">KVKK aydınlatma metnini</a> okudum.</span></label>
                    <?php endif; ?>
                    <button class="btn btn-dark-brand w-100"><?= $mode === 'sifre' ? 'Sıfırlama Bağlantısı Gönder' : e($titles[$mode]) ?></button>
                </form>
            </div>
            <div class="col-lg-5">
                <div class="form-card form-card--soft h-100">
                    <?php if ($mode === 'giris'): ?>
                        <h5>Hesabın yok mu?</h5>
                        <p>Üye ol; siparişlerini takip et, favorilerini kaydet ve kampanyalardan ilk sen haberdar ol.</p>
                        <a href="<?= url('kayit-ol') ?>" class="btn btn-outline-brand">Kayıt Ol</a>
                    <?php else: ?>
                        <h5>Zaten üye misin?</h5>
                        <p>Hesabına giriş yaparak alışverişe kaldığın yerden devam et.</p>
                        <a href="<?= url('giris') ?>" class="btn btn-outline-brand">Giriş Yap</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
