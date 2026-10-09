<?php
require dirname(__DIR__) . '/app/bootstrap.php';

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$base = trim(config('base_url'), '/');
if ($base !== '' && str_starts_with($path, $base)) {
    $path = trim(substr($path, strlen($base)), '/');
}
$seg = $path === '' ? [] : explode('/', rawurldecode($path));

$listFilter = fn(array $extra = []) => array_merge([
    'q'    => $_GET['q'] ?? '',
    'sort' => $_GET['sirala'] ?? '',
    'min'  => $_GET['min'] ?? '',
    'max'  => $_GET['max'] ?? '',
], $extra);

switch ($seg[0] ?? '') {
    case '':
        render('home', ['bodyClass' => 'page-home']);
        break;

    case 'kategoriler':
        render('categories', ['title' => 'Kategoriler']);
        break;

    case 'kategori':
        $category = categories()[$seg[1] ?? ''] ?? null;
        if (!$category) not_found();
        $filter = $listFilter(['category' => $category['slug']]);
        render('listing', [
            'title' => $category['name'],
            'heading' => $category['name'],
            'crumbs' => ['Kategoriler' => 'kategoriler', $category['name'] => null],
            'items' => products_by($filter),
            'filter' => $filter,
            'category' => $category,
        ]);
        break;

    case 'arama':
        $filter = $listFilter();
        $heading = $filter['q'] !== '' ? '“' . $filter['q'] . '” için sonuçlar' : 'Tüm Ürünler';
        render('listing', [
            'title' => $heading,
            'heading' => $heading,
            'crumbs' => ['Arama' => null],
            'items' => products_by($filter),
            'filter' => $filter,
            'category' => null,
        ]);
        break;

    case 'urun':
        $p = product($seg[1] ?? '');
        if (!$p) not_found();
        render('product', ['title' => $p['name'], 'description' => $p['summary'], 'p' => $p, 'bodyClass' => 'page-product']);
        break;

    case 'hizli-bakis':
        $p = product($seg[1] ?? '');
        if (!$p) { http_response_code(404); exit; }
        require APP_PATH . '/views/quick-view.php';
        break;

    case 'sepet':
        render('cart', ['title' => 'Sepetim']);
        break;

    case 'odeme':
        render('checkout', ['title' => 'Ödeme']);
        break;

    case 'favorilerim':
        render('wishlist', ['title' => 'Favorilerim']);
        break;

    case 'giris':
        render('auth', ['title' => 'Giriş Yap', 'mode' => 'giris']);
        break;

    case 'kayit-ol':
        render('auth', ['title' => 'Kayıt Ol', 'mode' => 'kayit']);
        break;

    case 'sifremi-unuttum':
        render('auth', ['title' => 'Şifremi Unuttum', 'mode' => 'sifre']);
        break;

    case 'iletisim':
        render('contact', ['title' => 'İletişim']);
        break;

    case 'blog':
        if (isset($seg[1])) {
            $post = current(array_filter(posts(), fn($x) => $x['slug'] === $seg[1]));
            if (!$post) not_found();
            render('simple', [
                'title' => $post['title'],
                'heading' => $post['title'],
                'crumbs' => ['Blog' => 'blog', $post['title'] => null],
                'content' => '<p class="text-muted">' . e(date('d.m.Y', strtotime($post['date']))) . '</p><p class="lead">' . e($post['excerpt']) . '</p><p>Yazı içeriği admin panelden eklenecek.</p>',
            ]);
        } else {
            render('blog', ['title' => 'Blog']);
        }
        break;

    case 'sayfa':
        $label = pages()[$seg[1] ?? ''] ?? null;
        if (!$label) not_found();
        render('simple', [
            'title' => $label,
            'heading' => $label,
            'crumbs' => [$label => null],
            'content' => '<p>' . e($label) . ' içeriği admin panelden düzenlenecek.</p>',
        ]);
        break;

    case 'banka-hesaplari':
        render('simple', [
            'title' => 'Banka Hesapları',
            'heading' => 'Banka Hesapları',
            'crumbs' => ['Banka Hesapları' => null],
            'content' => '<div class="table-responsive"><table class="table"><thead><tr><th>Banka</th><th>Hesap Sahibi</th><th>IBAN</th></tr></thead><tbody><tr><td>Örnek Bank</td><td>' . e(config('site_name')) . '</td><td>TR00 0000 0000 0000 0000 0000 00</td></tr></tbody></table></div>',
        ]);
        break;

    case 'siparis-takip':
        render('simple', [
            'title' => 'Sipariş Takibi',
            'heading' => 'Sipariş Takibi',
            'crumbs' => ['Sipariş Takibi' => null],
            'content' => '<form class="form-card" style="max-width:480px" onsubmit="event.preventDefault(); App.toast(\'Sipariş bulunamadı\', \'danger\')"><input class="form-control mb-3" placeholder="Sipariş numarası" required><input type="email" class="form-control mb-3" placeholder="E-posta" required><button class="btn btn-dark-brand w-100">Sorgula</button></form>',
        ]);
        break;

    default:
        not_found();
}
