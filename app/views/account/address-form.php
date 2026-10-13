<?php /** @var ?array $address @var array $old @var array $errors */
$cities = ['Adana', 'Adıyaman', 'Afyonkarahisar', 'Ağrı', 'Aksaray', 'Amasya', 'Ankara', 'Antalya', 'Ardahan', 'Artvin', 'Aydın', 'Balıkesir', 'Bartın', 'Batman', 'Bayburt', 'Bilecik', 'Bingöl', 'Bitlis', 'Bolu', 'Burdur', 'Bursa', 'Çanakkale', 'Çankırı', 'Çorum', 'Denizli', 'Diyarbakır', 'Düzce', 'Edirne', 'Elazığ', 'Erzincan', 'Erzurum', 'Eskişehir', 'Gaziantep', 'Giresun', 'Gümüşhane', 'Hakkari', 'Hatay', 'Iğdır', 'Isparta', 'İstanbul', 'İzmir', 'Kahramanmaraş', 'Karabük', 'Karaman', 'Kars', 'Kastamonu', 'Kayseri', 'Kırıkkale', 'Kırklareli', 'Kırşehir', 'Kilis', 'Kocaeli', 'Konya', 'Kütahya', 'Malatya', 'Manisa', 'Mardin', 'Mersin', 'Muğla', 'Muş', 'Nevşehir', 'Niğde', 'Ordu', 'Osmaniye', 'Rize', 'Sakarya', 'Samsun', 'Şanlıurfa', 'Siirt', 'Sinop', 'Şırnak', 'Sivas', 'Tekirdağ', 'Tokat', 'Trabzon', 'Tunceli', 'Uşak', 'Van', 'Yalova', 'Yozgat', 'Zonguldak'];
if ($old['city'] !== '' && !in_array($old['city'], $cities, true)) array_unshift($cities, $old['city']);
?>
<div class="acc-head">
    <a href="<?= url('hesabim/adresler') ?>" class="acc-backlink"><i class="bi bi-arrow-left"></i> Adreslerim</a>
    <h1><?= $address ? 'Adresi Düzenle' : 'Yeni Adres' ?></h1>
    <p>Teslimat için kullanılacak adres bilgileri</p>
</div>

<section class="acc-card">
    <header class="acc-card__head">
        <span class="acc-card__icon acc-tone--blue"><i class="bi bi-geo-alt"></i></span>
        <h2>Adres Bilgileri</h2>
    </header>
    <form method="post" action="<?= url('hesabim/adresler/' . ($address ? (int) $address['id'] : 'yeni')) ?>" class="acc-card__body" novalidate>
        <?= csrf_field() ?>
        <div class="row g-4">
            <div class="col-md-4">
                <label class="acc-label" for="title">Adres Adı</label>
                <input class="form-control<?= invalid($errors, 'title') ?>" id="title" name="title" value="<?= e($old['title']) ?>" placeholder="Ev, İş …" list="titleList" required maxlength="60">
                <datalist id="titleList"><option value="Ev"><option value="İş"><option value="Yazlık"></datalist>
                <?= error_text($errors, 'title') ?>
            </div>
            <div class="col-md-4">
                <label class="acc-label" for="full_name">Alıcı Ad Soyad</label>
                <input class="form-control<?= invalid($errors, 'full_name') ?>" id="full_name" name="full_name" value="<?= e($old['full_name']) ?>" autocomplete="name" required maxlength="160">
                <?= error_text($errors, 'full_name') ?>
            </div>
            <div class="col-md-4">
                <label class="acc-label" for="phone"><i class="bi bi-telephone"></i> Telefon</label>
                <input type="tel" class="form-control<?= invalid($errors, 'phone') ?>" id="phone" name="phone" value="<?= e($old['phone']) ?>" autocomplete="tel" placeholder="05xx xxx xx xx" required maxlength="30">
                <?= error_text($errors, 'phone') ?>
            </div>
            <div class="col-md-6">
                <label class="acc-label" for="city">İl</label>
                <select class="form-select<?= invalid($errors, 'city') ?>" id="city" name="city" required>
                    <option value="">Seçin</option>
                    <?php foreach ($cities as $c): ?>
                        <option<?= $old['city'] === $c ? ' selected' : '' ?>><?= e($c) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= error_text($errors, 'city') ?>
            </div>
            <div class="col-md-6">
                <label class="acc-label" for="district">İlçe</label>
                <input class="form-control<?= invalid($errors, 'district') ?>" id="district" name="district" value="<?= e($old['district']) ?>" autocomplete="address-level2" required maxlength="60">
                <?= error_text($errors, 'district') ?>
            </div>
            <div class="col-12">
                <label class="acc-label" for="address">Açık Adres</label>
                <textarea class="form-control<?= invalid($errors, 'address') ?>" id="address" name="address" rows="3" autocomplete="street-address" placeholder="Mahalle, cadde/sokak, bina ve daire no" required maxlength="500"><?= e($old['address']) ?></textarea>
                <?= error_text($errors, 'address') ?>
            </div>
            <div class="col-12">
                <label class="form-check">
                    <input type="checkbox" class="form-check-input" name="is_default" value="1"<?= !empty($old['is_default']) ? ' checked' : '' ?>>
                    <span class="form-check-label">Varsayılan teslimat adresim olsun</span>
                </label>
            </div>
        </div>
        <div class="acc-card__foot">
            <a href="<?= url('hesabim/adresler') ?>" class="acc-btn acc-btn--ghost">Vazgeç</a>
            <button class="acc-btn"><i class="bi bi-floppy"></i> Adresi Kaydet</button>
        </div>
    </form>
</section>
