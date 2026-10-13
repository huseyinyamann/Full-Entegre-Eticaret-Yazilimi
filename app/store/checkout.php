<?php
/**
 * /odeme                     ödeme formu ve sipariş oluşturma
 * /siparis-tamamlandi        onay sayfası (yalnızca bu oturumda verilen son sipariş)
 */

store_session();
$me = current_customer();
header('Cache-Control: no-store, max-age=0');

if (($seg[0] ?? '') === 'siparis-tamamlandi') {
    $no = $_SESSION['last_order'] ?? null;
    $order = $no ? db_one('SELECT order_no, email, grand_total, payment_method, customer_id FROM orders WHERE order_no = ?', [$no]) : null;
    if (!$order) go('');
    render('order-complete', ['title' => 'Siparişiniz alındı', 'order' => $order, 'me' => $me]);
    return;
}

$addresses = $me ? db_all('SELECT * FROM customer_addresses WHERE customer_id = ? ORDER BY is_default DESC, id DESC', [$me['id']]) : [];
$default = $addresses[0] ?? null;
[$defFirst, $defLast] = $default ? split_name($default['full_name']) : [$me['first_name'] ?? '', $me['last_name'] ?? ''];
$old = [
    'first_name' => $defFirst, 'last_name' => $defLast,
    'email' => $me['email'] ?? '', 'phone' => $default['phone'] ?? ($me['phone'] ?? ''),
    'city' => $default['city'] ?? '', 'district' => $default['district'] ?? '', 'address' => $default['address'] ?? '',
    'payment' => 'kredi-karti', 'note' => '', 'save_address' => !$addresses,
];
$errors = [];

if (is_post()) {
    $old = [
        'first_name' => mb_substr(input('first_name'), 0, 80),
        'last_name' => mb_substr(input('last_name'), 0, 80),
        'email' => mb_strtolower(mb_substr(input('email'), 0, 190)),
        'phone' => mb_substr(input('phone'), 0, 30),
        'city' => mb_substr(input('city'), 0, 60),
        'district' => mb_substr(input('district'), 0, 60),
        'address' => mb_substr(input('address'), 0, 500),
        'payment' => input('payment'),
        'note' => mb_substr(input('note'), 0, 500),
        'save_address' => isset($_POST['save_address']),
    ];
    foreach (['first_name' => 'Adınızı', 'last_name' => 'Soyadınızı', 'city' => 'İli', 'district' => 'İlçeyi'] as $k => $label) {
        if ($old[$k] === '') $errors[$k] = "$label yazın.";
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Geçerli bir e-posta adresi yazın.';
    if (!clean_phone($old['phone'])) $errors['phone'] = 'Geçerli bir telefon numarası yazın.';
    if (mb_strlen($old['address']) < 10) $errors['address'] = 'Açık adresi mahalle, sokak ve numarayla yazın.';
    if (!isset(PAYMENT_METHODS[$old['payment']])) $errors['payment'] = 'Ödeme yöntemi seçin.';
    if (!isset($_POST['agree'])) $errors['agree'] = 'Mesafeli satış sözleşmesini onaylayın.';

    $lines = parse_cart(input('cart'));
    if (!$lines) $errors['form'] = 'Sepetiniz boş görünüyor.';

    if (!$errors) {
        [$orderNo, $orderErrors] = place_order($lines, $old, $me ? (int) $me['id'] : null);
        if ($orderNo) {
            if ($me && $old['save_address']
                && (int) db_value('SELECT COUNT(*) FROM customer_addresses WHERE customer_id = ?', [$me['id']]) < 10) {
                $aid = db_insert('customer_addresses', [
                    'customer_id' => $me['id'], 'title' => $addresses ? 'Adres ' . (count($addresses) + 1) : 'Ev',
                    'full_name' => $old['first_name'] . ' ' . $old['last_name'], 'phone' => $old['phone'],
                    'city' => $old['city'], 'district' => $old['district'], 'address' => $old['address'],
                ]);
                if (!$addresses) db_run('UPDATE customer_addresses SET is_default = 1 WHERE id = ?', [$aid]);
            }
            $_SESSION['last_order'] = $orderNo;
            go('siparis-tamamlandi');
        }
        $errors['form'] = implode(' ', $orderErrors);
    }
}

render('checkout', ['title' => 'Ödeme', 'me' => $me, 'addresses' => $addresses, 'old' => $old, 'errors' => $errors]);

function split_name(string $full): array
{
    $parts = preg_split('/\s+/', trim($full));
    $last = count($parts) > 1 ? array_pop($parts) : '';
    return [implode(' ', $parts), $last];
}
