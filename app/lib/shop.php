<?php
/**
 * Sipariş sabitleri ve sipariş oluşturma.
 * Tutarlar her zaman veritabanındaki fiyatlardan hesaplanır; tarayıcıdan gelen fiyat kullanılmaz.
 */

const ORDER_STATUSES = [
    'beklemede' => ['Beklemede', 'warning'],
    'hazirlaniyor' => ['Hazırlanıyor', 'info'],
    'kargoda' => ['Kargoda', 'primary'],
    'teslim-edildi' => ['Teslim Edildi', 'success'],
    'iptal' => ['İptal', 'danger'],
    'iade' => ['İade', 'secondary'],
];
const PAYMENT_METHODS = ['kredi-karti' => 'Kredi Kartı', 'havale' => 'Havale / EFT', 'kapida-odeme' => 'Kapıda Ödeme'];
const PAYMENT_STATUSES = ['bekliyor' => 'Ödeme Bekliyor', 'odendi' => 'Ödendi', 'iade' => 'İade Edildi'];

const CART_MAX_LINES = 50;
const CART_MAX_QTY = 20;

function shipping_cost(float $subtotal): float
{
    return $subtotal <= 0 || $subtotal >= (float) config('free_shipping_limit') ? 0.0 : (float) config('shipping_fee');
}

/**
 * Tarayıcıdaki sepet JSON'unu ([{id, qty, size, color}, …]) doğrulanmış satırlara çevirir.
 * Ad/fiyat/görsel gibi alanlar yok sayılır.
 */
function parse_cart(string $json): array
{
    $items = json_decode($json, true);
    if (!is_array($items) || !array_is_list($items)) return [];

    $lines = [];
    foreach (array_slice($items, 0, CART_MAX_LINES) as $i) {
        if (!is_array($i) || !isset($i['id']) || !is_numeric($i['id'])) continue;
        $id = (int) $i['id'];
        $qty = max(1, min(CART_MAX_QTY, (int) ($i['qty'] ?? 1)));
        $size = is_string($i['size'] ?? null) ? mb_substr(trim($i['size']), 0, 30) : '';
        $color = is_string($i['color'] ?? null) ? mb_substr(trim($i['color']), 0, 40) : '';
        $key = "$id|$size|$color";
        if (isset($lines[$key])) {
            $lines[$key]['qty'] = min(CART_MAX_QTY, $lines[$key]['qty'] + $qty);
        } elseif ($id > 0) {
            $lines[$key] = compact('id', 'qty', 'size', 'color');
        }
    }
    return array_values($lines);
}

function generate_order_no(): string
{
    do {
        $no = 'YT' . date('ymd') . random_int(10000, 99999);
    } while (db_value('SELECT 1 FROM orders WHERE order_no = ?', [$no]));
    return $no;
}

/**
 * Siparişi tek transaction içinde oluşturur: ürünleri kilitler, stok/seçenek kontrolü yapar,
 * tutarları sunucuda hesaplar ve stoğu düşer.
 *
 * @return array{0: ?string, 1: string[]} [sipariş no, hata mesajları]
 */
function place_order(array $lines, array $buyer, ?int $customerId): array
{
    if (!$lines) return [null, ['Sepetin boş görünüyor.']];

    $pdo = db();
    $pdo->beginTransaction();
    try {
        $ids = array_values(array_unique(array_column($lines, 'id')));
        $rows = db_all('SELECT id, name, sku, price, stock, sizes, colors, is_active FROM products WHERE id IN ('
            . implode(',', array_fill(0, count($ids), '?')) . ') FOR UPDATE', $ids);
        $products = array_column($rows, null, 'id');

        $errors = [];
        $needed = [];
        $items = [];
        $subtotal = 0.0;
        foreach ($lines as $l) {
            $p = $products[$l['id']] ?? null;
            if (!$p || !$p['is_active']) {
                $errors[] = 'Sepetindeki bir ürün artık satışta değil; sepetini güncelleyip tekrar dene.';
                continue;
            }
            $sizes = json_decode((string) $p['sizes'], true) ?: [];
            $colors = array_column(json_decode((string) $p['colors'], true) ?: [], 'name');
            if ($l['size'] !== '' && !in_array($l['size'], $sizes, true)) {
                $errors[] = $p['name'] . ': seçilen beden bulunamadı.';
                continue;
            }
            if ($l['color'] !== '' && !in_array($l['color'], $colors, true)) {
                $errors[] = $p['name'] . ': seçilen renk bulunamadı.';
                continue;
            }
            $needed[$p['id']] = ($needed[$p['id']] ?? 0) + $l['qty'];

            $options = array_filter(['Renk: ' . $l['color'] => $l['color'] !== '', 'Beden: ' . $l['size'] => $l['size'] !== '']);
            $price = (float) $p['price'];
            $items[] = [
                'product_id' => (int) $p['id'],
                'product_name' => $p['name'],
                'sku' => $p['sku'],
                'options' => $options ? implode(', ', array_keys($options)) : null,
                'unit_price' => $price,
                'quantity' => $l['qty'],
                'line_total' => round($price * $l['qty'], 2),
            ];
            $subtotal += $price * $l['qty'];
        }
        foreach ($needed as $pid => $qty) {
            if ($products[$pid]['stock'] < $qty) {
                $left = max(0, (int) $products[$pid]['stock']);
                $errors[] = $products[$pid]['name'] . ($left ? ": stokta yalnızca $left adet kaldı." : ': stokta kalmadı.');
            }
        }
        if ($errors) {
            $pdo->rollBack();
            return [null, array_values(array_unique($errors))];
        }

        $subtotal = round($subtotal, 2);
        $shipping = shipping_cost($subtotal);
        $orderNo = generate_order_no();
        $orderId = db_insert('orders', [
            'order_no' => $orderNo,
            'customer_id' => $customerId,
            'customer_name' => $buyer['first_name'] . ' ' . $buyer['last_name'],
            'email' => $buyer['email'],
            'phone' => $buyer['phone'],
            'city' => $buyer['city'],
            'district' => $buyer['district'],
            'address' => $buyer['address'],
            'subtotal' => $subtotal,
            'shipping_total' => $shipping,
            'grand_total' => $subtotal + $shipping,
            'payment_method' => $buyer['payment'],
            'customer_note' => $buyer['note'] !== '' ? $buyer['note'] : null,
        ]);
        foreach ($items as $item) {
            db_insert('order_items', ['order_id' => $orderId] + $item);
        }
        foreach ($needed as $pid => $qty) {
            db_run('UPDATE products SET stock = stock - ? WHERE id = ?', [$qty, $pid]);
        }
        $pdo->commit();
        return [$orderNo, []];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
}

/** Müşteri iptali: yalnızca "beklemede" siparişler; stok geri eklenir */
function cancel_order(int $orderId): bool
{
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $status = db_value('SELECT status FROM orders WHERE id = ? FOR UPDATE', [$orderId]);
        if ($status !== 'beklemede') {
            $pdo->rollBack();
            return false;
        }
        foreach (db_all('SELECT product_id, quantity FROM order_items WHERE order_id = ? AND product_id IS NOT NULL', [$orderId]) as $i) {
            db_run('UPDATE products SET stock = stock + ? WHERE id = ?', [$i['quantity'], $i['product_id']]);
        }
        db_run("UPDATE orders SET status = 'iptal', payment_status = IF(payment_status = 'odendi', 'iade', payment_status) WHERE id = ?", [$orderId]);
        $pdo->commit();
        return true;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
}

/** Sipariş kalemlerini ürünün ilk görseli ve güncel adresiyle birlikte getirir (order_id => kalemler) */
function order_items_for(array $orderIds): array
{
    if (!$orderIds) return [];
    $rows = db_all('SELECT oi.*, p.slug,
            (SELECT pi.path FROM product_images pi WHERE pi.product_id = oi.product_id ORDER BY pi.sort_order, pi.id LIMIT 1) AS image
        FROM order_items oi LEFT JOIN products p ON p.id = oi.product_id AND p.is_active = 1
        WHERE oi.order_id IN (' . implode(',', array_fill(0, count($orderIds), '?')) . ') ORDER BY oi.id', $orderIds);
    $out = [];
    foreach ($rows as $r) {
        $out[$r['order_id']][] = $r;
    }
    return $out;
}
