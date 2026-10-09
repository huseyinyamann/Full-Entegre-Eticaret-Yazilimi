<?php
/**
 * Demo veri. Admin panel ve veritabanı yazıldığında bu dosyadaki
 * fonksiyonlar repository/model katmanıyla değiştirilecek.
 */

function categories(): array
{
    static $cats = null;
    if ($cats !== null) return $cats;

    $list = [
        ['Kadın',      ['Elbise', 'Abiye', 'Takım']],
        ['Erkek',      ['Gömlek', 'Tişört']],
        ['Çanta',      ['El Çantası', 'Omuz Çantası', 'Sırt Çantası']],
        ['Ayakkabı',   ['Sneaker', 'Topuklu', 'Terlik']],
        ['Aksesuar',   ['Saat', 'Gözlük', 'Takı']],
        ['Ev & Yaşam', ['Dekorasyon', 'Mutfak']],
    ];
    $cats = [];
    foreach ($list as $i => [$name, $children]) {
        $slug = slugify($name);
        $cats[$slug] = [
            'id' => $i + 1,
            'name' => $name,
            'slug' => $slug,
            'image' => 'demo/' . ['26.jpg', '26-2.jpg', '26-3.jpg', '26-4.jpg', '26-5.jpg', '26-6.jpg'][$i],
            'children' => array_map(fn($c) => ['name' => $c, 'slug' => slugify($c)], $children),
        ];
    }
    return $cats;
}

function products(): array
{
    static $products = null;
    if ($products !== null) return $products;

    // [ürün adı, görsel klasör no (uploads/demo/products/{no}-{1..4}.webp), görsel adedi]
    $names = [
        'kadin' => [
            ['Siyah Uzun Abiye Elbise', 177, 4], ['Deri Korse Etekli Takım', 178, 4], ['Korse Detaylı Siyah Takım', 179, 4],
            ['Puantiyeli Yazlık Elbise', 180, 4], ['Kırmızı Siyah Kadın Takım', 181, 4], ['Mavi Kloş Elbise', 162, 4],
            ['Çiçekli Yazlık Elbise', 163, 4], ['Gri Midi Elbise', 164, 4], ['Kısa Kloş Elbise', 165, 4], ['Ekose Desenli Elbise', 166, 4],
        ],
        'erkek' => [
            ['Mavi Siyah Kareli Gömlek', 83, 4], ['Baskılı Erkek Tişört', 84, 4], ['Ekose Oduncu Gömlek', 85, 4],
            ['Kısa Kollu Erkek Gömlek', 86, 4], ['Kareli Slim Fit Gömlek', 87, 4],
        ],
        'canta' => [
            ['Mavi Kadın El Çantası', 172, 3], ['Hakiki Deri Omuz Çantası', 173, 3], ['Klasik Kadın Çanta', 174, 3],
            ['Beyaz Suni Deri Sırt Çantası', 175, 3], ['Siyah Kadın El Çantası', 176, 3],
        ],
        'ayakkabi' => [
            ['Kırmızı Siyah Basketbol Ayakkabısı', 88, 4], ['Retro Koşu Ayakkabısı', 90, 4], ['Beyaz Kırmızı Spor Ayakkabı', 91, 4],
            ['Topuklu Kadın Ayakkabı', 186, 4], ['Altın Rengi Topuklu Ayakkabı', 187, 4], ['Kırmızı Stiletto', 189, 4],
            ['Siyah Kahve Ev Terliği', 185, 4],
        ],
        'aksesuar' => [
            ['Deri Kordon Kol Saati', 93, 3], ['Klasik Çelik Erkek Saati', 94, 3], ['Dalgıç Tipi Çelik Saat', 98, 3],
            ['Altın Rengi Kadın Saati', 193, 3], ['İnce Kordon Kadın Saati', 194, 3], ['Siyah Güneş Gözlüğü', 154, 3],
            ['Klasik Güneş Gözlüğü', 155, 3], ['Yeşil Kristal Küpe', 182, 3],
        ],
        'ev-yasam' => [
            ['Dekoratif Saksı', 46, 4], ['Yapay Bitki Dekor', 45, 3], ['Dekoratif Salıncak Biblo', 43, 3],
            ['Kutulu Blender', 51, 4], ['Mikrodalga Fırın', 66, 4], ['Elektrikli Ocak', 56, 4],
        ],
    ];
    $colors = [['Siyah', '#111111'], ['Lacivert', '#1c355e'], ['Bej', '#d8c3a5'], ['Beyaz', '#f5f5f5'], ['Kahve', '#6b4a2f']];
    $badges = [null, 'yeni', 'indirim', null, 'cok-satan', null];

    $products = [];
    $id = 1;
    foreach ($names as $cat => $items) {
        foreach ($items as [$name, $imgNo, $imgCount]) {
            $price = 149 + (($id * 137) % 1850);
            $hasDiscount = $id % 3 !== 0;
            $slug = slugify($name);
            $products[$slug] = [
                'id' => $id,
                'name' => $name,
                'slug' => $slug,
                'category' => $cat,
                'price' => (float) $price,
                'old_price' => $hasDiscount ? (float) round($price * 1.35) : null,
                'badge' => $badges[$id % count($badges)],
                'rating' => 3.5 + ($id % 4) * 0.5,
                'reviews' => ($id * 7) % 120,
                'stock' => ($id % 11 === 0) ? 0 : 5 + $id,
                'colors' => array_slice($colors, $id % 3, 3),
                'sizes' => in_array($cat, ['kadin', 'erkek'], true) ? ['XS', 'S', 'M', 'L', 'XL'] : ($cat === 'ayakkabi' ? ['37', '38', '39', '40', '41', '42'] : []),
                'images' => array_map(fn($n) => "demo/products/{$imgNo}-{$n}.webp", range(1, $imgCount)),
                'sku' => 'YT-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT),
                'summary' => 'Günlük kullanıma uygun, kaliteli malzemeden üretilmiş ' . mb_strtolower($name) . '. Rahat kalıbı ve zamansız tasarımıyla her kombine uyum sağlar.',
                'created' => date('Y-m-d', strtotime("-{$id} days")),
            ];
            $id++;
        }
    }
    return $products;
}

function product(string $slug): ?array
{
    return products()[$slug] ?? null;
}

function products_by(array $filter = [], int $limit = 0): array
{
    $list = array_values(products());
    if (!empty($filter['category'])) {
        $list = array_filter($list, fn($p) => $p['category'] === $filter['category']);
    }
    if (!empty($filter['badge'])) {
        $list = array_filter($list, fn($p) => $p['badge'] === $filter['badge']);
    }
    if (!empty($filter['q'])) {
        $q = mb_strtolower($filter['q']);
        $list = array_filter($list, fn($p) => str_contains(mb_strtolower($p['name']), $q));
    }
    if (!empty($filter['min'])) $list = array_filter($list, fn($p) => $p['price'] >= (float) $filter['min']);
    if (!empty($filter['max'])) $list = array_filter($list, fn($p) => $p['price'] <= (float) $filter['max']);

    $list = array_values($list);
    switch ($filter['sort'] ?? '') {
        case 'fiyat-artan':  usort($list, fn($a, $b) => $a['price'] <=> $b['price']); break;
        case 'fiyat-azalan': usort($list, fn($a, $b) => $b['price'] <=> $a['price']); break;
        case 'yeni':         usort($list, fn($a, $b) => strcmp($b['created'], $a['created'])); break;
        case 'indirim':      usort($list, fn($a, $b) => discount_rate($b) <=> discount_rate($a)); break;
    }
    return $limit ? array_slice($list, 0, $limit) : $list;
}

function slides(): array
{
    return [
        ['title' => "Yeni Sezon\nKoleksiyonu", 'text' => 'Sonbahar-kış parçalarıyla gardırobunu yenile.', 'button' => 'Alışverişe Başla', 'link' => 'kategori/kadin', 'image' => 'demo/fashion-slideshow-02.jpg'],
        ['title' => "Çantada\nSezon Fırsatı", 'text' => 'Seçili çantalarda %30\'a varan indirim.', 'button' => 'Çantaları Keşfet', 'link' => 'kategori/canta', 'image' => 'demo/fashion-slideshow-03.jpg'],
        ['title' => "Adım Adım\nKonfor", 'text' => 'Sneaker, bot ve loafer modelleri tek yerde.', 'button' => 'Ayakkabılara Göz At', 'link' => 'kategori/ayakkabi', 'image' => 'demo/lookbook-4.jpg'],
    ];
}

function testimonials(): array
{
    return [
        ['name' => 'Elif K.', 'city' => 'İstanbul', 'text' => 'Siparişim ertesi gün elimdeydi, paketleme çok özenliydi. Ürün fotoğraftakinin aynısı.', 'rating' => 5],
        ['name' => 'Murat D.', 'city' => 'Ankara', 'text' => 'Beden değişimi için iade sürecini çok kolay hallettim. Müşteri hizmetleri hızlı dönüş yaptı.', 'rating' => 5],
        ['name' => 'Zeynep A.', 'city' => 'İzmir', 'text' => 'Fiyat performans açısından gayet başarılı. Kampanyaları takip ediyorum.', 'rating' => 4],
        ['name' => 'Can Y.', 'city' => 'Bursa', 'text' => 'Mont kalitesi beklediğimden iyi çıktı, kışın rahatça kullanıyorum.', 'rating' => 5],
    ];
}

function posts(): array
{
    return [
        ['title' => 'Sonbahar Kombinlerinde 5 Temel Parça', 'slug' => 'sonbahar-kombinlerinde-5-temel-parca', 'image' => 'demo/7-66e9c9ae571be.jpg', 'date' => '2026-09-28', 'excerpt' => 'Mevsim geçişlerinde gardırobunda mutlaka bulunması gereken parçaları derledik.'],
        ['title' => 'Doğru Sırt Çantası Nasıl Seçilir?', 'slug' => 'dogru-sirt-cantasi-nasil-secilir', 'image' => 'demo/8.jpg', 'date' => '2026-09-15', 'excerpt' => 'Hacim, bölme yapısı ve kumaş seçerken dikkat etmen gerekenler.'],
        ['title' => 'Deri Ürün Bakım Rehberi', 'slug' => 'deri-urun-bakim-rehberi', 'image' => 'demo/9.jpg', 'date' => '2026-08-30', 'excerpt' => 'Deri ayakkabı ve çantalarının ömrünü uzatacak basit öneriler.'],
    ];
}

function pages(): array
{
    return [
        'hakkimizda'             => 'Hakkımızda',
        'gizlilik-politikasi'    => 'Gizlilik Politikası',
        'mesafeli-satis'         => 'Mesafeli Satış Sözleşmesi',
        'iade-ve-degisim'        => 'İade ve Değişim',
        'kargo-ve-teslimat'      => 'Kargo ve Teslimat',
        'sikca-sorulan-sorular'  => 'Sıkça Sorulan Sorular',
    ];
}
