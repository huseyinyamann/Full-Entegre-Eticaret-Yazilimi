<?php
/**
 * Demo veri: bin/install.php tarafından veritabanına yazılır.
 */

const DEMO_CUSTOMER_EMAIL = 'musteri@yamanticaret.com';
const DEMO_CUSTOMER_PASSWORD = 'Musteri.2026!';

function demo_categories(): array
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

function demo_products(): array
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

function demo_slides(): array
{
    return [
        ['title' => "Yeni Sezon\nKoleksiyonu", 'text' => 'Sonbahar-kış parçalarıyla gardırobunu yenile.', 'button' => 'Alışverişe Başla', 'link' => 'kategori/kadin', 'image' => 'demo/fashion-slideshow-02.jpg'],
        ['title' => "Çantada\nSezon Fırsatı", 'text' => 'Seçili çantalarda %30\'a varan indirim.', 'button' => 'Çantaları Keşfet', 'link' => 'kategori/canta', 'image' => 'demo/fashion-slideshow-03.jpg'],
        ['title' => "Adım Adım\nKonfor", 'text' => 'Sneaker, bot ve loafer modelleri tek yerde.', 'button' => 'Ayakkabılara Göz At', 'link' => 'kategori/ayakkabi', 'image' => 'demo/lookbook-4.jpg'],
    ];
}

function demo_testimonials(): array
{
    return [
        ['name' => 'Elif K.', 'city' => 'İstanbul', 'text' => 'Siparişim ertesi gün elimdeydi, paketleme çok özenliydi. Ürün fotoğraftakinin aynısı.', 'rating' => 5],
        ['name' => 'Murat D.', 'city' => 'Ankara', 'text' => 'Beden değişimi için iade sürecini çok kolay hallettim. Müşteri hizmetleri hızlı dönüş yaptı.', 'rating' => 5],
        ['name' => 'Zeynep A.', 'city' => 'İzmir', 'text' => 'Fiyat performans açısından gayet başarılı. Kampanyaları takip ediyorum.', 'rating' => 4],
        ['name' => 'Can Y.', 'city' => 'Bursa', 'text' => 'Mont kalitesi beklediğimden iyi çıktı, kışın rahatça kullanıyorum.', 'rating' => 5],
    ];
}

function demo_posts(): array
{
    return [
        ['title' => 'Sonbahar Kombinlerinde 5 Temel Parça', 'slug' => 'sonbahar-kombinlerinde-5-temel-parca', 'image' => 'demo/7-66e9c9ae571be.jpg', 'date' => '2026-09-28', 'excerpt' => 'Mevsim geçişlerinde gardırobunda mutlaka bulunması gereken parçaları derledik.'],
        ['title' => 'Doğru Sırt Çantası Nasıl Seçilir?', 'slug' => 'dogru-sirt-cantasi-nasil-secilir', 'image' => 'demo/8.jpg', 'date' => '2026-09-15', 'excerpt' => 'Hacim, bölme yapısı ve kumaş seçerken dikkat etmen gerekenler.'],
        ['title' => 'Deri Ürün Bakım Rehberi', 'slug' => 'deri-urun-bakim-rehberi', 'image' => 'demo/9.jpg', 'date' => '2026-08-30', 'excerpt' => 'Deri ayakkabı ve çantalarının ömrünü uzatacak basit öneriler.'],
    ];
}

function demo_pages(): array
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

function seed_demo(): void
{
    mt_srand(2026);
    $pdo = db();
    $pdo->beginTransaction();

    // Kategoriler
    $catIds = [];   // ana slug => id
    $childIds = []; // ana slug => [alt ad => id]
    foreach (array_values(demo_categories()) as $i => $cat) {
        $catIds[$cat['slug']] = db_insert('categories', [
            'name' => $cat['name'], 'slug' => $cat['slug'], 'image' => $cat['image'], 'sort_order' => $i + 1,
        ]);
        foreach ($cat['children'] as $j => $child) {
            $childIds[$cat['slug']][$child['name']] = db_insert('categories', [
                'parent_id' => $catIds[$cat['slug']], 'name' => $child['name'], 'slug' => $child['slug'], 'sort_order' => $j + 1,
            ]);
        }
    }

    // Ürünler — adında alt kategori geçiyorsa (Elbise, Saat, Topuklu…) o alt kategoriye bağlanır
    $productRows = [];
    foreach (demo_products() as $p) {
        $categoryId = $catIds[$p['category']];
        $bestPos = PHP_INT_MAX;
        foreach ($childIds[$p['category']] ?? [] as $childName => $childId) {
            $stem = mb_strtolower(mb_strlen($childName) > 4 ? mb_substr($childName, 0, -1) : $childName);
            $pos = mb_strpos(mb_strtolower($p['name']), $stem);
            if ($pos !== false && $pos < $bestPos) {
                $bestPos = $pos;
                $categoryId = $childId;
            }
        }
        $id = db_insert('products', [
            'category_id' => $categoryId,
            'name' => $p['name'],
            'slug' => $p['slug'],
            'sku' => $p['sku'],
            'summary' => $p['summary'],
            'description' => '<p>' . e($p['summary']) . '</p><ul><li>Nefes alabilen, cilt dostu malzeme</li><li>Günlük kullanıma uygun dayanıklı yapı</li><li>Türkiye\'de üretilmiştir</li></ul>',
            'price' => $p['price'],
            'old_price' => $p['old_price'],
            'stock' => $p['stock'],
            'badge' => $p['badge'],
            'colors' => json_encode(array_map(fn($c) => ['name' => $c[0], 'hex' => $c[1]], $p['colors']), JSON_UNESCAPED_UNICODE),
            'sizes' => json_encode($p['sizes']),
            'rating' => $p['rating'],
            'review_count' => $p['reviews'],
            'is_featured' => $p['badge'] === 'cok-satan' ? 1 : 0,
            'created_at' => $p['created'] . ' 10:00:00',
        ]);
        foreach ($p['images'] as $k => $path) {
            db_insert('product_images', ['product_id' => $id, 'path' => $path, 'sort_order' => $k]);
        }
        $productRows[] = ['id' => $id] + $p;
    }

    foreach (demo_slides() as $i => $s) {
        db_insert('slides', ['title' => $s['title'], 'text' => $s['text'], 'button_text' => $s['button'], 'link' => $s['link'], 'image' => $s['image'], 'sort_order' => $i + 1]);
    }
    foreach (demo_testimonials() as $i => $t) {
        db_insert('testimonials', $t + ['sort_order' => $i + 1]);
    }
    foreach (demo_posts() as $post) {
        db_insert('posts', [
            'title' => $post['title'], 'slug' => $post['slug'], 'image' => $post['image'], 'excerpt' => $post['excerpt'],
            'content' => '<p>' . e($post['excerpt']) . '</p><h3>Öne çıkanlar</h3><p>Bu yazının içeriğini admin panel &gt; Blog bölümünden düzenleyebilirsiniz.</p>',
            'published_at' => $post['date'],
        ]);
    }
    $i = 0;
    foreach (demo_pages() as $slug => $title) {
        db_insert('pages', [
            'title' => $title, 'slug' => $slug, 'sort_order' => ++$i,
            'content' => '<p>' . e($title) . ' içeriğini admin panel &gt; Sayfalar bölümünden düzenleyebilirsiniz.</p>',
        ]);
    }

    // Demo müşteriler ve son 30 günün siparişleri
    $people = [['Elif', 'Kaya', 'İstanbul'], ['Murat', 'Demir', 'Ankara'], ['Zeynep', 'Aydın', 'İzmir'], ['Can', 'Yılmaz', 'Bursa'],
        ['Ayşe', 'Çelik', 'Antalya'], ['Emre', 'Şahin', 'Konya'], ['Selin', 'Arslan', 'Eskişehir'], ['Burak', 'Koç', 'Adana'],
        ['Deniz', 'Öztürk', 'Kocaeli'], ['Gizem', 'Kurt', 'Samsun'], ['Oğuz', 'Aksoy', 'Trabzon'], ['Merve', 'Polat', 'Mersin']];
    $customers = [];
    foreach ($people as $k => [$first, $last, $city]) {
        $email = slugify($first) . '.' . slugify($last) . '@example.com';
        $customers[] = ['id' => db_insert('customers', [
            'first_name' => $first, 'last_name' => $last, 'email' => $email,
            'phone' => '05' . (30 + $k) . ' 000 00 ' . str_pad((string) $k, 2, '0', STR_PAD_LEFT),
            'created_at' => date('Y-m-d H:i:s', strtotime('-' . (40 + $k * 3) . ' days')),
        ]), 'name' => "$first $last", 'email' => $email, 'city' => $city];
    }

    // Giriş yapılabilen demo müşteri hesabı (mağaza > Hesabım sayfasını denemek için)
    $demo = ['name' => 'Demo Müşteri', 'email' => DEMO_CUSTOMER_EMAIL, 'city' => 'İstanbul'];
    $demo['id'] = db_insert('customers', [
        'first_name' => 'Demo', 'last_name' => 'Müşteri', 'email' => DEMO_CUSTOMER_EMAIL, 'phone' => '0555 000 00 00',
        'password_hash' => password_hash(DEMO_CUSTOMER_PASSWORD, PASSWORD_ARGON2ID),
        'created_at' => date('Y-m-d H:i:s', strtotime('-60 days')),
    ]);
    db_insert('customer_addresses', [
        'customer_id' => $demo['id'], 'title' => 'Ev', 'full_name' => 'Demo Müşteri', 'phone' => '0555 000 00 00',
        'city' => 'İstanbul', 'district' => 'Kadıköy', 'address' => 'Caferağa Mah. Moda Cad. No:12 D:3', 'is_default' => 1,
    ]);

    $statuses = ['teslim-edildi', 'teslim-edildi', 'teslim-edildi', 'kargoda', 'kargoda', 'hazirlaniyor', 'beklemede', 'iptal'];
    $payments = ['kredi-karti', 'kredi-karti', 'kredi-karti', 'havale', 'kapida-odeme'];
    for ($n = 1; $n <= 48; $n++) {
        $daysAgo = (int) floor(($n - 1) * 30 / 48);
        $created = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days") - mt_rand(0, 8 * 3600));
        $status = $daysAgo <= 1 ? ['beklemede', 'hazirlaniyor'][$n % 2] : $statuses[mt_rand(0, count($statuses) - 1)];
        $cust = $n % 6 === 0 ? $demo : $customers[mt_rand(0, count($customers) - 1)];
        $payment = $payments[mt_rand(0, count($payments) - 1)];

        $items = [];
        $subtotal = 0;
        for ($k = 0, $count = mt_rand(1, 3); $k < $count; $k++) {
            $p = $productRows[mt_rand(0, count($productRows) - 1)];
            $qty = mt_rand(1, 2);
            $items[] = ['product_id' => $p['id'], 'product_name' => $p['name'], 'sku' => $p['sku'],
                'options' => $p['sizes'] ? 'Beden: ' . $p['sizes'][mt_rand(0, count($p['sizes']) - 1)] : null,
                'unit_price' => $p['price'], 'quantity' => $qty, 'line_total' => $p['price'] * $qty];
            $subtotal += $p['price'] * $qty;
        }
        $shipping = $subtotal >= 750 ? 0 : 49.90;
        $shipped = in_array($status, ['kargoda', 'teslim-edildi'], true);
        $unpaid = ($payment === 'kapida-odeme' && $status !== 'teslim-edildi') || ($payment === 'havale' && $status === 'beklemede');
        $orderId = db_insert('orders', [
            'order_no' => 'YT' . date('ymd', strtotime($created)) . str_pad((string) $n, 4, '0', STR_PAD_LEFT),
            'customer_id' => $cust['id'], 'customer_name' => $cust['name'], 'email' => $cust['email'],
            'phone' => '0530 000 00 00', 'city' => $cust['city'], 'district' => 'Merkez', 'address' => 'Örnek Mah. Demo Sok. No:' . $n,
            'subtotal' => $subtotal, 'shipping_total' => $shipping, 'grand_total' => $subtotal + $shipping,
            'payment_method' => $payment,
            'payment_status' => $status === 'iptal' ? 'iade' : ($unpaid ? 'bekliyor' : 'odendi'),
            'status' => $status,
            'cargo_company' => $shipped ? 'Yurtiçi Kargo' : null,
            'tracking_no' => $shipped ? (string) mt_rand(100000000, 999999999) : null,
            'created_at' => $created,
        ]);
        foreach ($items as $item) {
            db_insert('order_items', ['order_id' => $orderId] + $item);
        }
    }

    $pdo->commit();
    echo '• Demo veri eklendi (' . count($productRows) . " ürün, 48 sipariş)\n";
    echo "• Demo müşteri: " . DEMO_CUSTOMER_EMAIL . " / " . DEMO_CUSTOMER_PASSWORD . "\n";
}
