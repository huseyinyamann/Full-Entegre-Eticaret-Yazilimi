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

PHP 8.1+ yeterli, ek bağımlılık yok.

```bash
# Geçici sunucu
php -S 127.0.0.1:8092 -t public public/router.php
```

Laragon/Apache ile kullanırken site kökünü `public/` klasörüne yönlendirin (`.htaccess` hazır).

## Klasör yapısı

```
app/
  bootstrap.php     Uygulama başlangıcı
  config.php        Site ayarları (ad, iletişim, duyurular…)
  helpers.php       Yardımcı fonksiyonlar (url, img, money, render…)
  data/demo.php     Demo veri — veritabanı katmanı gelince değiştirilecek
  partials/         Header, footer, ürün kartı vb. parçalar
  views/            Sayfa şablonları
public/
  index.php         Front controller / yönlendirme
  assets/           CSS, JS, görseller
  uploads/          Yüklenen görseller (img() önce buraya bakar)
```

## Görseller

`img()` yardımcı fonksiyonu, verilen yol `public/uploads/` altında varsa onu,
yoksa aynı boyutta geçici bir placeholder görsel döndürür. Kendi görsellerini
`uploads/` klasörüne koyup veri katmanında yolunu vermen yeterli.

## Yol haritası

- [ ] Veritabanı katmanı (MySQL)
- [ ] Admin paneli (ürün, kategori, sipariş, sayfa yönetimi)
- [ ] Üyelik sistemi
- [ ] Ödeme entegrasyonları
- [ ] Pazaryeri entegrasyonları

## Lisans

MIT
