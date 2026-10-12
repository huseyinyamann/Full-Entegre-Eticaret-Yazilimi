<?php
// Siparişler mağazadan oluşur; panelden yalnızca görüntülenir ve durumu güncellenir.

if ($action === 'index') {
    $where = ['1 = 1'];
    $params = [];
    if (($q = query('q')) !== '') {
        $where[] = '(order_no LIKE ? OR customer_name LIKE ? OR email LIKE ?)';
        $like = '%' . addcslashes($q, '%_\\') . '%';
        array_push($params, $like, $like, $like);
    }
    if (isset(ORDER_STATUSES[$status = query('durum')])) {
        $where[] = 'status = ?';
        $params[] = $status;
    }
    $whereSql = implode(' AND ', $where);
    $pager = paginate("SELECT COUNT(*) FROM orders WHERE $whereSql", $params);
    $orders = db_all("SELECT o.*, (SELECT SUM(quantity) FROM order_items WHERE order_id = o.id) AS item_count
        FROM orders o WHERE $whereSql ORDER BY created_at DESC LIMIT {$pager['limit']} OFFSET {$pager['offset']}", $params);
    $counts = array_column(db_all('SELECT status, COUNT(*) AS c FROM orders GROUP BY status'), 'c', 'status');

    admin_render('orders/index', ['title' => 'Siparişler', 'active' => 'siparisler', 'orders' => $orders, 'pager' => $pager, 'counts' => $counts]);
    return;
}

if ($action !== 'edit') {
    redirect('siparisler');
}

$order = db_one('SELECT * FROM orders WHERE id = ?', [$id]);
if (!$order) redirect('siparisler');

if (is_post()) {
    $data = [
        'status' => isset(ORDER_STATUSES[input('status')]) ? input('status') : $order['status'],
        'payment_status' => isset(PAYMENT_STATUSES[input('payment_status')]) ? input('payment_status') : $order['payment_status'],
        'cargo_company' => mb_substr(input('cargo_company'), 0, 60) ?: null,
        'tracking_no' => mb_substr(input('tracking_no'), 0, 60) ?: null,
        'admin_note' => mb_substr(input('admin_note'), 0, 2000) ?: null,
    ];
    db_update('orders', $data, $id);
    $summary = $data['status'] !== $order['status']
        ? '#' . $order['order_no'] . ': ' . ORDER_STATUSES[$order['status']][0] . ' → ' . ORDER_STATUSES[$data['status']][0]
        : '#' . $order['order_no'] . ' güncellendi';
    audit('update', 'order', $id, $summary);
    flash('success', 'Sipariş güncellendi.');
    redirect('siparisler/' . $id);
}

$items = db_all('SELECT oi.*, (SELECT path FROM product_images WHERE product_id = oi.product_id ORDER BY sort_order LIMIT 1) AS image
    FROM order_items oi WHERE order_id = ? ORDER BY id', [$id]);
$history = db_all("SELECT l.summary, l.created_at, a.name FROM admin_logs l LEFT JOIN admins a ON a.id = l.admin_id
    WHERE l.entity = 'order' AND l.entity_id = ? ORDER BY l.created_at DESC LIMIT 20", [$id]);

admin_render('orders/show', [
    'title' => 'Sipariş #' . $order['order_no'],
    'active' => 'siparisler',
    'order' => $order,
    'items' => $items,
    'history' => $history,
]);
