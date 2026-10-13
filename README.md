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

Mevcut bir kurulumu güncellerken `database/migrations/` altındaki SQL dosyalarını tarih sırasıyla uygulayın:

```bash
mysql -u root yaman_eticaret < database/migrations/2026_10_13_customer_accounts.sql
```

## Müşteri hesabı

`/kayit-ol`, `/giris` ve `/hesabim` sayfaları. Demo kurulumda: `musteri@yamanticaret.com` / `Musteri.2026!`

- Hesabım: profil, e-posta değiştirme ve şifre değiştirme (ikisi de mevcut şifreyi ister)
- Siparişlerim: durum sekmeleri, sipariş detayı, kargo takip numarası, beklemedeki siparişi iptal (stok geri eklenir)
- Adreslerim: en fazla 10 adres, varsayılan adres; ödeme sayfasında tek tıkla doldurulur
- Ödeme sayfası gerçek sipariş oluşturur (üye veya misafir). Sepetten yalnızca ürün/adet/seçenek gelir;
  fiyat, kargo ve stok sunucuda, ürün satırları kilitlenerek hesaplanır

Güvenlik: Argon2id, e-posta + IP bazlı giriş kilidi, IP başına kayıt sınırı ve bot tuzağı, `HttpOnly` +
`SameSite=Lax` çerez (yalnızca gerektiğinde açılır), oturum sabitleme koruması, şifre değişince diğer
cihazlardaki oturumların düşmesi, tüm POST'larda CSRF, açık yönlendirme (open redirect) koruması ve
her sorgunun `customer_id` ile sınırlanması (başka müşterinin sipariş/adresine erişilemez).

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
  lib/              .env okuyucu, PDO yardımcıları, HTML temizleyici, güvenlik, müşteri oturumu, sipariş
  store/            Vitrin denetleyicileri (giriş/kayıt, hesabım, ödeme)
  data/repository.php  Vitrin veri katmanı (MySQL)
  admin/            Yönetim paneli (router, modüller, görünümler)
  partials/         Header, footer, ürün kartı vb. parçalar
  views/            Sayfa şablonları (account/ altında Hesabım paneli)
bin/install.php     Kurulum betiği
database/
  schema.sql        Veritabanı şeması
  migrations/       Mevcut kurulumlar için şema güncellemeleri
  seeds/demo.php    Demo veri
public/
  index.php         Front controller / yönlendirme
  assets/           CSS, JS, görseller (admin/ altında panel teması, css/account.css Hesabım teması)
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
- [x] Üyelik sistemi (kayıt, giriş, hesabım, siparişlerim, adreslerim)
- [ ] E-posta bildirimleri ve şifre sıfırlama
- [ ] Ödeme entegrasyonları
- [ ] Pazaryeri entegrasyonları

## Lisans

MIT
