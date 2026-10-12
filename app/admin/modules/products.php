<?php

$categoryOptions = db_all('SELECT c.id, c.name, p.name AS parent_name FROM categories c LEFT JOIN categories p ON p.id = c.parent_id
    ORDER BY COALESCE(p.sort_order, c.sort_order), COALESCE(p.id, c.id), c.parent_id IS NOT NULL, c.sort_order, c.name');

if ($action === 'index') {
    $where = ['1 = 1'];
    $params = [];
    if (($q = query('q')) !== '') {
        $where[] = '(p.name LIKE ? OR p.sku LIKE ?)';
        $like = '%' . addcslashes($q, '%_\\') . '%';
        array_push($params, $like, $like);
    }
    if (($cat = (int) query('kategori')) > 0) {
        $where[] = '(p.category_id = ? OR p.category_id IN (SELECT id FROM categories WHERE parent_id = ?))';
        array_push($params, $cat, $cat);
    }
    match (query('durum')) {
        'aktif' => $where[] = 'p.is_active = 1',
        'pasif' => $where[] = 'p.is_active = 0',
        'stoksuz' => $where[] = 'p.stock = 0',
        default => null,
    };
    $whereSql = implode(' AND ', $where);

    $pager = paginate("SELECT COUNT(*) FROM products p WHERE $whereSql", $params);
    $products = db_all("SELECT p.id, p.name, p.sku, p.price, p.old_price, p.stock, p.is_active, p.badge, c.name AS category_name,
            (SELECT path FROM product_images WHERE product_id = p.id ORDER BY sort_order, id LIMIT 1) AS image
        FROM products p LEFT JOIN categories c ON c.id = p.category_id
        WHERE $whereSql ORDER BY p.id DESC LIMIT {$pager['limit']} OFFSET {$pager['offset']}", $params);

    admin_render('products/index', ['title' => 'Ürünler', 'active' => 'urunler', 'products' => $products, 'pager' => $pager, 'categoryOptions' => $categoryOptions]);
    return;
}

$product = null;
if ($id !== null) {
    $product = db_one('SELECT * FROM products WHERE id = ?', [$id]);
    if (!$product) redirect('urunler');
}

if ($action === 'delete') {
    $paths = array_column(db_all('SELECT path FROM product_images WHERE product_id = ?', [$id]), 'path');
    db_run('DELETE FROM products WHERE id = ?', [$id]);
    array_map('delete_upload', $paths);
    audit('delete', 'product', $id, $product['name']);
    flash('success', '“' . $product['name'] . '” silindi.');
    redirect('urunler');
}

$errors = [];
$images = $id ? db_all('SELECT id, path FROM product_images WHERE product_id = ? ORDER BY sort_order, id', [$id]) : [];

if (is_post()) {
    $colors = [];
    foreach ((array) ($_POST['color_name'] ?? []) as $i => $name) {
        $name = is_string($name) ? trim($name) : '';
        $hex = (string) ($_POST['color_hex'][$i] ?? '');
        if ($name !== '' && preg_match('/^#[0-9a-f]{6}$/i', $hex)) {
            $colors[] = ['name' => mb_substr($name, 0, 40), 'hex' => strtolower($hex)];
        }
    }
    $sizes = array_values(array_unique(array_filter(array_map(fn($s) => mb_substr(trim($s), 0, 20), explode(',', input('sizes'))))));

    $data = [
        'name' => mb_substr(input('name'), 0, 190),
        'sku' => mb_substr(input('sku'), 0, 64),
        'category_id' => input_int('category_id'),
        'price' => input_money('price'),
        'old_price' => input_money('old_price'),
        'stock' => input_int('stock', 0),
        'badge' => array_key_exists(input('badge'), BADGES) ? input('badge') : null,
        'summary' => mb_substr(input('summary'), 0, 1000),
        'description' => sanitize_html(input('description')),
        'colors' => json_encode($colors, JSON_UNESCAPED_UNICODE),
        'sizes' => json_encode($sizes, JSON_UNESCAPED_UNICODE),
        'is_active' => input_bool('is_active'),
        'is_featured' => input_bool('is_featured'),
        'meta_title' => mb_substr(input('meta_title'), 0, 190) ?: null,
        'meta_description' => mb_substr(input('meta_description'), 0, 300) ?: null,
    ];

    if ($data['name'] === '') $errors['name'] = 'Ürün adı zorunludur.';
    if ($data['price'] === null) $errors['price'] = 'Geçerli bir fiyat girin.';
    if ($data['old_price'] !== null && $data['price'] !== null && $data['old_price'] <= $data['price']) {
        $errors['old_price'] = 'Eski fiyat, satış fiyatından büyük olmalı (boş bırakabilirsiniz).';
    }
    if ($data['category_id'] !== null && !in_array($data['category_id'], array_column($categoryOptions, 'id'))) {
        $errors['category_id'] = 'Geçersiz kategori.';
    }
    if ($data['stock'] < 0) $errors['stock'] = 'Stok negatif olamaz.';
    if ($data['sku'] === '') {
        $data['sku'] = 'YT-' . strtoupper(bin2hex(random_bytes(3)));
    } elseif (db_value('SELECT id FROM products WHERE sku = ? AND id <> ?', [$data['sku'], $id ?? 0])) {
        $errors['sku'] = 'Bu stok kodu başka bir üründe kullanılıyor.';
    }
    $data['slug'] = unique_slug('products', input('slug') !== '' ? input('slug') : $data['name'], $id);

    // Yeni görseller
    $uploaded = [];
    $files = $_FILES['images'] ?? null;
    if ($files && is_array($files['name'])) {
        foreach (array_keys($files['name']) as $k) {
            $err = null;
            $path = upload_image(['name' => $files['name'][$k], 'type' => $files['type'][$k], 'tmp_name' => $files['tmp_name'][$k], 'error' => $files['error'][$k], 'size' => $files['size'][$k]], 'products', $err);
            if ($err) $errors['images'] = $files['name'][$k] . ': ' . $err;
            if ($path) $uploaded[] = $path;
        }
    }

    if ($errors) {
        array_map('delete_upload', $uploaded);
        $product = array_merge($product ?? [], $data, ['slug' => input('slug')]);
    } else {
        if ($id) {
            db_update('products', $data, $id);
            audit('update', 'product', $id, $data['name']);
        } else {
            $id = db_insert('products', $data);
            audit('create', 'product', $id, $data['name']);
        }

        // Silinmek üzere işaretlenen görseller
        $removeIds = array_map('intval', (array) ($_POST['remove_images'] ?? []));
        foreach ($images as $img) {
            if (in_array((int) $img['id'], $removeIds, true)) {
                db_run('DELETE FROM product_images WHERE id = ? AND product_id = ?', [$img['id'], $id]);
                delete_upload($img['path']);
            }
        }
        // Kapak seçimi: seçilen görsel başa alınır
        $cover = (int) input('cover_image');
        $order = 0;
        foreach (db_all('SELECT id FROM product_images WHERE product_id = ? ORDER BY id = ? DESC, sort_order, id', [$id, $cover]) as $img) {
            db_run('UPDATE product_images SET sort_order = ? WHERE id = ?', [$order++, $img['id']]);
        }
        foreach ($uploaded as $path) {
            db_insert('product_images', ['product_id' => $id, 'path' => $path, 'sort_order' => $order++]);
        }

        flash('success', 'Ürün kaydedildi.');
        redirect('urunler/' . $id);
    }
}

$product ??= ['name' => '', 'slug' => '', 'sku' => '', 'category_id' => null, 'price' => '', 'old_price' => '', 'stock' => 0, 'badge' => null,
    'summary' => '', 'description' => '', 'colors' => '[]', 'sizes' => '[]', 'is_active' => 1, 'is_featured' => 0, 'meta_title' => '', 'meta_description' => ''];

admin_render('products/form', [
    'title' => $id ? 'Ürünü Düzenle' : 'Yeni Ürün',
    'active' => 'urunler',
    'id' => $id,
    'product' => $product,
    'images' => $images,
    'errors' => $errors,
    'categoryOptions' => $categoryOptions,
]);
