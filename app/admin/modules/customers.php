<?php

if ($action === 'index') {
    $where = '1 = 1';
    $params = [];
    if (($q = query('q')) !== '') {
        $where = "(CONCAT(first_name, ' ', last_name) LIKE ? OR email LIKE ? OR phone LIKE ?)";
        $like = '%' . addcslashes($q, '%_\\') . '%';
        $params = [$like, $like, $like];
    }
    $pager = paginate("SELECT COUNT(*) FROM customers WHERE $where", $params);
    $customers = db_all("SELECT c.*,
            (SELECT COUNT(*) FROM orders WHERE customer_id = c.id) AS order_count,
            (SELECT COALESCE(SUM(grand_total), 0) FROM orders WHERE customer_id = c.id AND status NOT IN ('iptal', 'iade')) AS total_spent,
            (SELECT MAX(created_at) FROM orders WHERE customer_id = c.id) AS last_order_at
        FROM customers c WHERE $where ORDER BY c.created_at DESC LIMIT {$pager['limit']} OFFSET {$pager['offset']}", $params);

    admin_render('customers/index', ['title' => 'Müşteriler', 'active' => 'musteriler', 'customers' => $customers, 'pager' => $pager]);
    return;
}

if ($action !== 'edit') {
    redirect('musteriler');
}

$customer = db_one('SELECT * FROM customers WHERE id = ?', [$id]);
if (!$customer) redirect('musteriler');

if (is_post()) {
    $active = input_bool('is_active');
    db_update('customers', ['is_active' => $active], $id);
    audit('update', 'customer', $id, $customer['first_name'] . ' ' . $customer['last_name'] . ($active ? ' aktifleştirildi' : ' engellendi'));
    flash('success', 'Müşteri hesabı güncellendi.');
    redirect('musteriler/' . $id);
}

$orders = db_all('SELECT id, order_no, grand_total, status, created_at FROM orders WHERE customer_id = ? ORDER BY created_at DESC', [$id]);
$spent = array_sum(array_map(fn($o) => in_array($o['status'], ['iptal', 'iade'], true) ? 0 : (float) $o['grand_total'], $orders));

admin_render('customers/show', [
    'title' => $customer['first_name'] . ' ' . $customer['last_name'],
    'active' => 'musteriler',
    'customer' => $customer,
    'orders' => $orders,
    'spent' => $spent,
]);
