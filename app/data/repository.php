<?php
/**
 * Vitrin veri katmanı. Fonksiyon adları ve dönen dizi yapıları eski demo
 * katmanıyla aynıdır; view'lar değişmeden veritabanından beslenir.
 */

function settings_all(): array
{
    $out = [];
    foreach (db_all('SELECT `key`, `value` FROM settings') as $row) {
        $out[$row['key']] = json_decode((string) $row['value'], true);
    }
    return $out;
}

function setting_save(string $key, mixed $value): void
{
    db_run('INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
        [$key, json_encode($value, JSON_UNESCAPED_UNICODE)]);
}

/** Aktif ana kategoriler (alt kategorileriyle), slug anahtarlı. */
function categories(): array
{
    static $cats = null;
    if ($cats !== null) return $cats;

    $rows = db_all('SELECT id, parent_id, name, slug, image, description FROM categories WHERE is_active = 1 ORDER BY sort_order, id');
    $cats = [];
    foreach ($rows as $r) {
        if ($r['parent_id'] === null) {
            $cats[$r['slug']] = $r + ['children' => []];
        }
    }
    $bySlugId = array_column($cats, 'slug', 'id');
    foreach ($rows as $r) {
        if ($r['parent_id'] !== null && isset($bySlugId[$r['parent_id']])) {
            $cats[$bySlugId[$r['parent_id']]]['children'][] = ['id' => $r['id'], 'name' => $r['name'], 'slug' => $r['slug']];
        }
    }
    return $cats;
}

/** Kategori (ve isteğe bağlı alt kategori) için ürün sorgusunda kullanılacak kategori id'leri. */
function category_ids(string $slug, string $child = ''): array
{
    $cat = categories()[$slug] ?? null;
    if (!$cat) return [0];
    if ($child !== '') {
        foreach ($cat['children'] as $c) {
            if ($c['slug'] === $child) return [$c['id']];
        }
    }
    return [$cat['id'], ...array_column($cat['children'], 'id')];
}

const PRODUCT_SELECT = '
    SELECT p.*, COALESCE(parent.slug, c.slug) AS category_slug,
           CASE WHEN parent.id IS NULL THEN NULL ELSE c.slug END AS subcategory_slug
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    LEFT JOIN categories parent ON parent.id = c.parent_id';

function products_by(array $filter = [], int $limit = 0): array
{
    $where = ['p.is_active = 1'];
    $params = [];

    if (!empty($filter['category'])) {
        $ids = category_ids($filter['category'], (string) ($filter['alt'] ?? ''));
        $where[] = 'p.category_id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')';
        array_push($params, ...$ids);
    }
    if (!empty($filter['badge'])) {
        $where[] = 'p.badge = ?';
        $params[] = $filter['badge'];
    }
    if (!empty($filter['q'])) {
        $where[] = '(p.name LIKE ? OR p.sku LIKE ?)';
        $like = '%' . addcslashes((string) $filter['q'], '%_\\') . '%';
        array_push($params, $like, $like);
    }
    if (is_numeric($filter['min'] ?? null)) {
        $where[] = 'p.price >= ?';
        $params[] = (float) $filter['min'];
    }
    if (is_numeric($filter['max'] ?? null)) {
        $where[] = 'p.price <= ?';
        $params[] = (float) $filter['max'];
    }

    $order = match ($filter['sort'] ?? '') {
        'fiyat-artan'  => 'p.price ASC',
        'fiyat-azalan' => 'p.price DESC',
        'yeni'         => 'p.created_at DESC',
        'indirim'      => '(CASE WHEN p.old_price > 0 THEN 1 - p.price / p.old_price ELSE 0 END) DESC',
        default        => 'p.is_featured DESC, p.id ASC',
    };

    $sql = PRODUCT_SELECT . ' WHERE ' . implode(' AND ', $where) . " ORDER BY $order, p.id";
    if ($limit > 0) $sql .= ' LIMIT ' . (int) $limit;

    return hydrate_products(db_all($sql, $params));
}

function product(string $slug): ?array
{
    $row = db_one(PRODUCT_SELECT . ' WHERE p.slug = ? AND p.is_active = 1', [$slug]);
    return $row ? hydrate_products([$row])[0] : null;
}

/** Veritabanı satırlarını view'ların beklediği ürün dizisine çevirir; görselleri tek sorguda çeker. */
function hydrate_products(array $rows): array
{
    if (!$rows) return [];
    $ids = array_column($rows, 'id');
    $images = [];
    $imgRows = db_all('SELECT product_id, path FROM product_images WHERE product_id IN (' . implode(',', array_fill(0, count($ids), '?')) . ') ORDER BY sort_order, id', $ids);
    foreach ($imgRows as $img) {
        $images[$img['product_id']][] = $img['path'];
    }

    return array_map(fn($r) => [
        'id'          => (int) $r['id'],
        'name'        => $r['name'],
        'slug'        => $r['slug'],
        'category'    => $r['category_slug'],
        'subcategory' => $r['subcategory_slug'],
        'price'       => (float) $r['price'],
        'old_price'   => $r['old_price'] !== null ? (float) $r['old_price'] : null,
        'badge'       => $r['badge'],
        'rating'      => (float) $r['rating'],
        'reviews'     => (int) $r['review_count'],
        'stock'       => (int) $r['stock'],
        'colors'      => array_map(fn($c) => [$c['name'], $c['hex']], json_decode((string) $r['colors'], true) ?: []),
        'sizes'       => json_decode((string) $r['sizes'], true) ?: [],
        'images'      => $images[$r['id']] ?? [],
        'sku'         => $r['sku'],
        'summary'     => (string) $r['summary'],
        'description' => (string) $r['description'],
        'created'     => substr($r['created_at'], 0, 10),
    ], $rows);
}

function slides(): array
{
    return db_all('SELECT title, text, button_text AS button, link, image FROM slides WHERE is_active = 1 ORDER BY sort_order, id');
}

function testimonials(): array
{
    return db_all('SELECT name, city, text, rating FROM testimonials WHERE is_active = 1 ORDER BY sort_order, id');
}

function posts(int $limit = 0): array
{
    $sql = 'SELECT title, slug, image, excerpt, content, published_at AS date FROM posts WHERE is_active = 1 AND published_at <= CURDATE() ORDER BY published_at DESC, id DESC';
    return db_all($limit > 0 ? $sql . ' LIMIT ' . (int) $limit : $sql);
}

function post(string $slug): ?array
{
    return db_one('SELECT title, slug, image, excerpt, content, published_at AS date FROM posts WHERE slug = ? AND is_active = 1 AND published_at <= CURDATE()', [$slug]);
}

/** Footer menüsü için: slug => başlık */
function pages(): array
{
    static $pages = null;
    return $pages ??= array_column(db_all('SELECT slug, title FROM pages WHERE is_active = 1 ORDER BY sort_order, id'), 'title', 'slug');
}

function page(string $slug): ?array
{
    return db_one('SELECT * FROM pages WHERE slug = ? AND is_active = 1', [$slug]);
}
