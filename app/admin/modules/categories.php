<?php

if ($action === 'index') {
    $rows = db_all('SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) AS product_count
        FROM categories c ORDER BY c.sort_order, c.name');
    $tree = [];
    foreach ($rows as $r) {
        if ($r['parent_id'] === null) $tree[$r['id']] = $r + ['children' => []];
    }
    foreach ($rows as $r) {
        if ($r['parent_id'] !== null && isset($tree[$r['parent_id']])) $tree[$r['parent_id']]['children'][] = $r;
    }
    admin_render('categories/index', ['title' => 'Kategoriler', 'active' => 'kategoriler', 'tree' => $tree]);
    return;
}

$category = null;
if ($id !== null) {
    $category = db_one('SELECT * FROM categories WHERE id = ?', [$id]);
    if (!$category) redirect('kategoriler');
}
$hasChildren = $id && db_value('SELECT COUNT(*) FROM categories WHERE parent_id = ?', [$id]) > 0;

if ($action === 'delete') {
    if ($hasChildren) {
        flash('danger', 'Alt kategorisi olan bir kategori silinemez. Önce alt kategorileri taşıyın veya silin.');
        redirect('kategoriler/' . $id);
    }
    db_run('DELETE FROM categories WHERE id = ?', [$id]);
    delete_upload($category['image']);
    audit('delete', 'category', $id, $category['name']);
    flash('success', '“' . $category['name'] . '” silindi. Bu kategorideki ürünler kategorisiz kaldı.');
    redirect('kategoriler');
}

// Yalnızca iki seviye: üst kategori olarak sadece ana kategoriler seçilebilir
$parents = db_all('SELECT id, name FROM categories WHERE parent_id IS NULL AND id <> ? ORDER BY sort_order, name', [$id ?? 0]);
$errors = [];

if (is_post()) {
    $data = [
        'name' => mb_substr(input('name'), 0, 120),
        'parent_id' => input_int('parent_id'),
        'description' => mb_substr(input('description'), 0, 2000) ?: null,
        'sort_order' => input_int('sort_order', 0),
        'is_active' => input_bool('is_active'),
        'meta_title' => mb_substr(input('meta_title'), 0, 190) ?: null,
        'meta_description' => mb_substr(input('meta_description'), 0, 300) ?: null,
    ];
    if ($data['name'] === '') $errors['name'] = 'Kategori adı zorunludur.';
    if ($data['parent_id'] !== null) {
        if (!in_array($data['parent_id'], array_map('intval', array_column($parents, 'id')), true)) {
            $errors['parent_id'] = 'Geçersiz üst kategori.';
        } elseif ($hasChildren) {
            $errors['parent_id'] = 'Alt kategorisi olan bir kategori başka bir kategorinin altına taşınamaz.';
        }
    }
    $data['slug'] = unique_slug('categories', input('slug') !== '' ? input('slug') : $data['name'], $id);

    $uploadError = null;
    $newImage = upload_image($_FILES['image'] ?? [], 'categories', $uploadError);
    if ($uploadError) $errors['image'] = $uploadError;

    if ($errors) {
        delete_upload($newImage);
        $category = array_merge($category ?? ['image' => null], $data);
    } else {
        if ($newImage) {
            $data['image'] = $newImage;
            if ($category) delete_upload($category['image']);
        } elseif (input_bool('remove_image') && $category) {
            $data['image'] = null;
            delete_upload($category['image']);
        }
        if ($id) {
            db_update('categories', $data, $id);
            audit('update', 'category', $id, $data['name']);
        } else {
            $id = db_insert('categories', $data);
            audit('create', 'category', $id, $data['name']);
        }
        flash('success', 'Kategori kaydedildi.');
        redirect('kategoriler/' . $id);
    }
}

$category ??= ['name' => '', 'slug' => '', 'parent_id' => input_int('parent_id') ?? (int) query('ust') ?: null, 'image' => null, 'description' => '',
    'sort_order' => 0, 'is_active' => 1, 'meta_title' => '', 'meta_description' => ''];

admin_render('categories/form', [
    'title' => $id ? 'Kategoriyi Düzenle' : 'Yeni Kategori',
    'active' => 'kategoriler',
    'id' => $id,
    'category' => $category,
    'parents' => $parents,
    'hasChildren' => $hasChildren,
    'errors' => $errors,
]);
