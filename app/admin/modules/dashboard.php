<?php

$valid = "status NOT IN ('iptal', 'iade')";

$stats = db_one("SELECT
    COALESCE(SUM(CASE WHEN created_at >= CURDATE() - INTERVAL 29 DAY THEN grand_total END), 0) AS revenue30,
    COALESCE(SUM(CASE WHEN created_at >= CURDATE() - INTERVAL 59 DAY AND created_at < CURDATE() - INTERVAL 29 DAY THEN grand_total END), 0) AS revenuePrev30,
    COUNT(CASE WHEN created_at >= CURDATE() - INTERVAL 29 DAY THEN 1 END) AS orders30,
    COUNT(CASE WHEN created_at >= CURDATE() THEN 1 END) AS ordersToday
    FROM orders WHERE $valid");
$stats['avgBasket'] = $stats['orders30'] ? $stats['revenue30'] / $stats['orders30'] : 0;
$stats['pending'] = (int) db_value("SELECT COUNT(*) FROM orders WHERE status IN ('beklemede', 'hazirlaniyor')");
$stats['customers'] = (int) db_value('SELECT COUNT(*) FROM customers');
$stats['products'] = (int) db_value('SELECT COUNT(*) FROM products WHERE is_active = 1');

// Son 14 günün günlük cirosu (sipariş olmayan günler 0)
$daily = array_column(db_all("SELECT DATE(created_at) AS d, SUM(grand_total) AS total, COUNT(*) AS cnt
    FROM orders WHERE $valid AND created_at >= CURDATE() - INTERVAL 13 DAY GROUP BY DATE(created_at)"), null, 'd');
$months = ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'];
$chartData = [];
for ($i = 13; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $chartData[] = [
        'label' => (int) date('j', strtotime($d)) . ' ' . $months[(int) date('n', strtotime($d)) - 1],
        'total' => round((float) ($daily[$d]['total'] ?? 0), 2),
        'count' => (int) ($daily[$d]['cnt'] ?? 0),
    ];
}

$recentOrders = can('orders') ? db_all('SELECT id, order_no, customer_name, grand_total, status, created_at FROM orders ORDER BY created_at DESC LIMIT 7') : [];
$lowStock = db_all('SELECT p.id, p.name, p.sku, p.stock, (SELECT path FROM product_images WHERE product_id = p.id ORDER BY sort_order LIMIT 1) AS image
    FROM products p WHERE p.is_active = 1 AND p.stock <= 10 ORDER BY p.stock, p.name LIMIT 6');
$topProducts = db_all("SELECT oi.product_name, SUM(oi.quantity) AS qty, SUM(oi.line_total) AS total
    FROM order_items oi JOIN orders o ON o.id = oi.order_id
    WHERE o.status NOT IN ('iptal', 'iade') AND o.created_at >= CURDATE() - INTERVAL 29 DAY
    GROUP BY oi.product_name ORDER BY qty DESC, total DESC LIMIT 5");

admin_render('dashboard', [
    'title' => 'Gösterge Paneli',
    'active' => '',
    'chart' => true,
    'stats' => $stats,
    'chartData' => $chartData,
    'recentOrders' => $recentOrders,
    'lowStock' => $lowStock,
    'topProducts' => $topProducts,
]);
