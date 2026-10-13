<?php
require dirname(__DIR__) . '/app/bootstrap.php';

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$base = trim(config('base_url'), '/');
if ($base !== '' && str_starts_with($path, $base)) {
    $path = trim(substr($path, strlen($base)), '/');
}
$seg = $path === '' ? [] : explode('/', rawurldecode($path));

if (($seg[0] ?? '') === 'admin') {
    require APP_PATH . '/admin/router.php';
    exit;
}

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Vitrindeki tüm form gönderimleri oturum + CSRF token ister
if (is_post()) {
    store_session();
    verify_csrf();
}
// Çerezi olan ziyaretçinin oturumu çıktı başlamadan çözülür (header'daki "Hesabım" simgesi için)
current_customer();

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
        $filter = $listFilter(['category' => $category['slug'], 'alt' => $_GET['alt'] ?? '']);
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
        if (($seg[1] ?? '') === 'fiyatlar') {
            // Sepetteki ürünlerin güncel fiyatı (yalnızca aktif ürünler; en fazla 50 id)
            $ids = array_slice(array_filter(array_map('intval', explode(',', (string) ($_GET['ids'] ?? '')))), 0, CART_MAX_LINES);
            $rows = $ids ? db_all('SELECT id, price, stock FROM products WHERE is_active = 1 AND id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')', $ids) : [];
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode((object) array_column(array_map(fn($r) => ['id' => (int) $r['id'], 'price' => (float) $r['price'], 'stock' => (int) $r['stock']], $rows), null, 'id'));
            break;
        }
        render('cart', ['title' => 'Sepetim']);
        break;

    case 'odeme':
    case 'siparis-tamamlandi':
        require APP_PATH . '/store/checkout.php';
        break;

    case 'hesabim':
        require APP_PATH . '/store/account.php';
        break;

    case 'favorilerim':
        render('wishlist', ['title' => 'Favorilerim']);
        break;

    case 'giris':
    case 'kayit-ol':
    case 'sifremi-unuttum':
    case 'cikis':
        $mode = ['giris' => 'giris', 'kayit-ol' => 'kayit', 'sifremi-unuttum' => 'sifre', 'cikis' => 'cikis'][$seg[0]];
        require APP_PATH . '/store/auth.php';
        break;

    case 'iletisim':
        render('contact', ['title' => 'İletişim']);
        break;

    case 'blog':
        if (isset($seg[1])) {
            $post = post($seg[1]);
            if (!$post) not_found();
            render('simple', [
                'title' => $post['title'],
                'description' => $post['excerpt'],
                'heading' => $post['title'],
                'crumbs' => ['Blog' => 'blog', $post['title'] => null],
                // İçerik kaydedilirken admin panelde sanitize edilir (app/lib/html_sanitizer.php)
                'content' => '<p class="text-muted">' . e(date('d.m.Y', strtotime($post['date']))) . '</p><p class="lead">' . e($post['excerpt']) . '</p>' . $post['content'],
            ]);
        } else {
            render('blog', ['title' => 'Blog']);
        }
        break;

    case 'sayfa':
        $page = page($seg[1] ?? '');
        if (!$page) not_found();
        render('simple', [
            'title' => $page['meta_title'] ?: $page['title'],
            'description' => $page['meta_description'] ?: null,
            'heading' => $page['title'],
            'crumbs' => [$page['title'] => null],
            'content' => $page['content'],
        ]);
        break;

    case 'banka-hesaplari':
        $rows = '';
        foreach (config('bank_accounts', []) as $acc) {
            $rows .= '<tr><td>' . e($acc['bank']) . '</td><td>' . e($acc['holder']) . '</td><td>' . e($acc['iban']) . '</td></tr>';
        }
        render('simple', [
            'title' => 'Banka Hesapları',
            'heading' => 'Banka Hesapları',
            'crumbs' => ['Banka Hesapları' => null],
            'content' => '<div class="table-responsive"><table class="table"><thead><tr><th>Banka</th><th>Hesap Sahibi</th><th>IBAN</th></tr></thead><tbody>' . $rows . '</tbody></table></div>',
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
