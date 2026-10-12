# Yaman Ticaret — Full Entegre E-Ticaret Yazılımı

PHP ile yazılmış, açık kaynak e-ticaret mağaza teması ve altyapısı.

## Özellikler (ön yüz)

- Duyuru bandı, mega menülü sabit header, mobil menü ve alt araç çubuğu
- Tam genişlik slider, kayan yazı bandı, kategori ve ürün slider'ları
- Ürün kartında hover görseli, rozetler, hızlı sepete ekleme, favori ve hızlı bakış
- Filtre + sıralama + grid seçimli kategori listeleme, arama
- Galerili ürün detay, renk/beden seçimi, beden tablosu, mobil sabit satın al çubuğu
- Yan çekmece sepet (ücretsiz kargo ilerleme çubuğu), sepet ve ödeme sayfaları
- Giriş / kayıt / şifre sıfırlama, iletişim, blog, kurumsal sayfalar, 404

## Kurulum

PHP 8.1+ (pdo_mysql, gd, fileinfo) ve MySQL 8 / MariaDB 10.6+ gerekir, ek bağımlılık yok.

```bash
cp .env.example .env          # veritabanı bilgilerini düzenleyin
php bin/install.php           # şema + demo veri + ilk yönetici
php -S 127.0.0.1:8092 -t public public/router.php
```

`php bin/install.php --fresh` veritabanını silip baştan kurar, `--no-demo` demo ürün/sipariş eklemez,
`--email=… --password=…` ilk yöneticinin bilgilerini belirler.

Laragon/Apache ile kullanırken site kökünü `public/` klasörüne yönlendirin (`.htaccess` hazır).

## Yönetim paneli

`/admin` adresinden girilir. Demo kurulumda: `admin@yamanticaret.com` / `Yaman.2026!`
(canlıya almadan önce **Profilim** sayfasından değiştirin).

- Gösterge paneli (ciro, sipariş, ortalama sepet, günlük ciro grafiği, çok satanlar, stok uyarıları)
- Ürünler (görsel yükleme, renk/beden, rozet, SEO), kategoriler (2 seviye)
- Siparişler (durum, ödeme, kargo takibi, iç not, geçmiş), müşteriler
- Slider, sayfalar, blog, müşteri yorumları
- Ayarlar (iletişim, duyuru bandı, ücretsiz kargo limiti, banka hesapları, sosyal medya)
- Yöneticiler (Mağaza Sahibi / Yönetici / Editör rolleri), işlem kayıtları, açık/koyu tema

Güvenlik: Argon2id şifreler, IP + e-posta bazlı brute-force kilidi, oturum sabitleme koruması,
`HttpOnly` + `SameSite=Strict` çerez, boşta kalma zaman aşımı, tüm POST'larda CSRF + Origin kontrolü,
rol bazlı yetki, PDO prepared statement, allowlist HTML temizleyici, GD ile yeniden kodlanan görsel yüklemeleri,
`uploads/` altında kod çalıştırma yasağı, CSP / X-Frame-Options başlıkları ve işlem kaydı (audit log).

## Klasör yapısı

```
app/
  bootstrap.php     Uygulama başlangıcı
  config.php        Varsayılan ayarlar (panelden kaydedilenler bunları ezer)
  helpers.php       Yardımcı fonksiyonlar (url, img, money, render…)
  lib/              .env okuyucu, PDO yardımcıları, HTML temizleyici
  data/repository.php  Vitrin veri katmanı (MySQL)
  admin/            Yönetim paneli (router, modüller, görünümler)
  partials/         Header, footer, ürün kartı vb. parçalar
  views/            Sayfa şablonları
bin/install.php     Kurulum betiği
database/
  schema.sql        Veritabanı şeması
  seeds/demo.php    Demo veri
public/
  index.php         Front controller / yönlendirme
  assets/           CSS, JS, görseller (admin/ altında panel teması)
  uploads/          Yüklenen görseller (img() önce buraya bakar)
```

## Görseller

`img()` yardımcı fonksiyonu, verilen yol `public/uploads/` altında varsa onu,
yoksa aynı boyutta geçici bir placeholder görsel döndürür. Kendi görsellerini
`uploads/` klasörüne koyup veri katmanında yolunu vermen yeterli.

## Yol haritası

- [x] Veritabanı katmanı (MySQL)
- [x] Admin paneli (ürün, kategori, sipariş, sayfa yönetimi)
- [ ] Tema profili + AI ile tema düzenleme (Twig sandbox)
- [ ] Üyelik sistemi
- [ ] Ödeme entegrasyonları
- [ ] Pazaryeri entegrasyonları

## Lisans

MIT
