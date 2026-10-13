<?php
/** @var string $mode giris|kayit|sifre @var array $errors @var array $old @var string $return */
$titles = ['giris' => 'Giriş Yap', 'kayit' => 'Kayıt Ol', 'sifre' => 'Şifremi Unuttum'];
$q = $return !== 'hesabim' ? '?donus=' . rawurlencode($return) : '';
partial('breadcrumb', ['heading' => $titles[$mode], 'items' => [$titles[$mode] => null]]);
?>
<section class="section pt-4">
    <div class="container-xl">
        <div class="row g-4 g-lg-5 justify-content-center">
            <div class="col-lg-5">
                <?php partial('flashes'); ?>
                <?php if (!empty($errors['form'])): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2" role="alert"><i class="bi bi-exclamation-triangle"></i><span><?= e($errors['form']) ?></span></div>
                <?php endif; ?>

                <?php if ($mode === 'giris'): ?>
                    <form class="form-card" method="post" action="<?= url('giris' . $q) ?>" novalidate>
                        <?= csrf_field() ?>
                        <h5>Hesabına giriş yap</h5>
                        <label class="form-label" for="email">E-posta adresi</label>
                        <input type="email" class="form-control mb-3" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" autocomplete="email" required autofocus>
                        <label class="form-label" for="password">Şifre</label>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
                            <button class="btn btn-outline-secondary" type="button" data-toggle-password="#password" aria-label="Şifreyi göster"><i class="bi bi-eye"></i></button>
                        </div>
                        <div class="d-flex justify-content-between small mb-4">
                            <label class="form-check"><input type="checkbox" class="form-check-input" name="remember" value="1"> <span class="form-check-label">Beni hatırla</span></label>
                            <a href="<?= url('sifremi-unuttum') ?>">Şifremi unuttum</a>
                        </div>
                        <button class="btn btn-dark-brand w-100">Giriş Yap</button>
                    </form>

                <?php elseif ($mode === 'kayit'): ?>
                    <form class="form-card" method="post" action="<?= url('kayit-ol' . $q) ?>" novalidate>
                        <?= csrf_field() ?>
                        <h5>Yeni hesap oluştur</h5>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label" for="first_name">Ad</label>
                                <input class="form-control<?= invalid($errors, 'first_name') ?>" id="first_name" name="first_name" value="<?= e($old['first_name'] ?? '') ?>" autocomplete="given-name" required maxlength="80">
                                <?= error_text($errors, 'first_name') ?>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="last_name">Soyad</label>
                                <input class="form-control<?= invalid($errors, 'last_name') ?>" id="last_name" name="last_name" value="<?= e($old['last_name'] ?? '') ?>" autocomplete="family-name" required maxlength="80">
                                <?= error_text($errors, 'last_name') ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">E-posta adresi</label>
                            <input type="email" class="form-control<?= invalid($errors, 'email') ?>" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" autocomplete="email" required maxlength="190">
                            <?= error_text($errors, 'email') ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="phone">Telefon <span class="text-muted small">(isteğe bağlı)</span></label>
                            <input type="tel" class="form-control<?= invalid($errors, 'phone') ?>" id="phone" name="phone" value="<?= e($old['phone'] ?? '') ?>" autocomplete="tel" placeholder="05xx xxx xx xx" maxlength="30">
                            <?= error_text($errors, 'phone') ?>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="password">Şifre</label>
                                <input type="password" class="form-control<?= invalid($errors, 'password') ?>" id="password" name="password" autocomplete="new-password" required minlength="<?= CUSTOMER_PASSWORD_MIN ?>">
                                <?= error_text($errors, 'password') ?>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="password_confirm">Şifre (tekrar)</label>
                                <input type="password" class="form-control<?= invalid($errors, 'password_confirm') ?>" id="password_confirm" name="password_confirm" autocomplete="new-password" required>
                                <?= error_text($errors, 'password_confirm') ?>
                            </div>
                            <div class="col-12 form-text mt-1">En az <?= CUSTOMER_PASSWORD_MIN ?> karakter; en az bir harf ve bir rakam.</div>
                        </div>
                        <!-- Bot tuzağı: ekran dışında, insanlar görmez -->
                        <div class="hp-field" aria-hidden="true"><label>Web sitesi <input name="website" tabindex="-1" autocomplete="off"></label></div>
                        <div class="mb-4">
                            <label class="form-check small mb-0">
                                <input type="checkbox" class="form-check-input<?= invalid($errors, 'kvkk') ?>" name="kvkk" value="1" <?= isset($_POST['kvkk']) ? 'checked' : '' ?> required>
                                <span class="form-check-label"><a href="<?= url('sayfa/gizlilik-politikasi') ?>" target="_blank" rel="noopener">KVKK aydınlatma metnini</a> okudum, kabul ediyorum.</span>
                            </label>
                            <?= error_text($errors, 'kvkk') ?>
                        </div>
                        <button class="btn btn-dark-brand w-100">Kayıt Ol</button>
                    </form>

                <?php else: ?>
                    <div class="form-card">
                        <h5>Şifreni mi unuttun?</h5>
                        <p class="text-muted">E-posta ile şifre sıfırlama, mağazanın e-posta gönderimi ayarlandığında aktif olacak.
                            Şimdilik şifreni yenilemek için bizimle iletişime geç.</p>
                        <div class="d-grid gap-2">
                            <a href="<?= url('iletisim') ?>" class="btn btn-dark-brand">İletişime Geç</a>
                            <a href="https://wa.me/<?= e(config('whatsapp')) ?>" class="btn btn-outline-brand" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-1"></i> WhatsApp'tan Yaz</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-5">
                <div class="form-card form-card--soft h-100">
                    <?php if ($mode === 'giris'): ?>
                        <h5>Hesabın yok mu?</h5>
                        <p>Üye ol; siparişlerini takip et, adreslerini kaydet ve bir sonraki alışverişini saniyeler içinde tamamla.</p>
                        <ul class="auth-perks">
                            <li><i class="bi bi-box-seam"></i> Sipariş ve kargo takibi</li>
                            <li><i class="bi bi-geo-alt"></i> Kayıtlı adreslerle hızlı ödeme</li>
                            <li><i class="bi bi-x-circle"></i> Hazırlanmayan siparişi tek tıkla iptal</li>
                        </ul>
                        <a href="<?= url('kayit-ol' . $q) ?>" class="btn btn-outline-brand">Kayıt Ol</a>
                    <?php else: ?>
                        <h5>Zaten üye misin?</h5>
                        <p>Hesabına giriş yaparak siparişlerini görüntüle ve alışverişe kaldığın yerden devam et.</p>
                        <a href="<?= url('giris' . $q) ?>" class="btn btn-outline-brand">Giriş Yap</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
