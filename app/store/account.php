<?php
/**
 * /hesabim                          profil, e-posta ve şifre
 * /hesabim/siparisler               sipariş geçmişi
 * /hesabim/siparisler/{no}          sipariş detayı
 * /hesabim/siparisler/{no}/iptal    beklemedeki siparişi iptal (POST)
 * /hesabim/adresler                 adres defteri
 * /hesabim/adresler/yeni|{id}       adres formu
 * /hesabim/adresler/{id}/sil|varsayilan  (POST)
 *
 * Her sorgu customer_id ile sınırlandırılır; başka müşterinin kaydına id/no tahminiyle erişilemez.
 */

store_session();
$me = require_customer();
header('Cache-Control: no-store, max-age=0');

$section = $seg[1] ?? '';
$errors = [];

const ADDRESS_LIMIT = 10;

switch ($section) {
    case '':
        $old = $me;
        if (is_post()) {
            $form = input('form');
            if ($form === 'profile') {
                $old = array_merge($me, [
                    'first_name' => mb_substr(input('first_name'), 0, 80),
                    'last_name' => mb_substr(input('last_name'), 0, 80),
                    'phone' => mb_substr(input('phone'), 0, 30),
                ]);
                if ($old['first_name'] === '') $errors['first_name'] = 'Adınızı yazın.';
                if ($old['last_name'] === '') $errors['last_name'] = 'Soyadınızı yazın.';
                if ($old['phone'] !== '' && !clean_phone($old['phone'])) $errors['phone'] = 'Telefon numarası geçersiz.';
                if (!$errors) {
                    db_update('customers', ['first_name' => $old['first_name'], 'last_name' => $old['last_name'], 'phone' => $old['phone'] ?: null], (int) $me['id']);
                    flash('success', 'Profil bilgileriniz güncellendi.');
                    go('hesabim');
                }
            } elseif ($form === 'email' || $form === 'password') {
                // Hassas değişiklikler mevcut şifreyi ister; tahmin denemeleri giriş sınırına dahildir
                $current = (string) ($_POST['current_password'] ?? '');
                $hash = (string) db_value('SELECT password_hash FROM customers WHERE id = ?', [$me['id']]);
                if (too_many_attempts('password', $me['email'], 5, 30)) {
                    $errors[$form . '_current'] = 'Çok fazla hatalı deneme. 15 dakika sonra tekrar deneyin.';
                } elseif (!password_verify($current, $hash)) {
                    record_attempt('password', $me['email'], false);
                    $errors[$form . '_current'] = 'Mevcut şifreniz hatalı.';
                }

                if ($form === 'email') {
                    $old['new_email'] = mb_strtolower(input('new_email'));
                    if (!filter_var($old['new_email'], FILTER_VALIDATE_EMAIL) || mb_strlen($old['new_email']) > 190) {
                        $errors['new_email'] = 'Geçerli bir e-posta adresi yazın.';
                    } elseif ($old['new_email'] === $me['email']) {
                        $errors['new_email'] = 'Yeni adres mevcut adresinizle aynı.';
                    } elseif (!$errors && db_value('SELECT 1 FROM customers WHERE email = ? AND id <> ?', [$old['new_email'], $me['id']])) {
                        $errors['new_email'] = 'Bu e-posta başka bir hesapta kullanılıyor.';
                    }
                    if (!$errors) {
                        db_update('customers', ['email' => $old['new_email']], (int) $me['id']);
                        flash('success', 'E-posta adresiniz güncellendi.');
                        go('hesabim');
                    }
                } else {
                    $new = (string) ($_POST['new_password'] ?? '');
                    if ($err = password_problem($new, CUSTOMER_PASSWORD_MIN)) $errors['new_password'] = $err;
                    elseif (!hash_equals($new, (string) ($_POST['new_password_confirm'] ?? ''))) $errors['new_password_confirm'] = 'Şifreler eşleşmiyor.';
                    elseif (password_verify($new, $hash)) $errors['new_password'] = 'Yeni şifre eskisiyle aynı olamaz.';
                    if (!$errors) {
                        $newHash = password_hash($new, PASSWORD_ARGON2ID);
                        db_update('customers', ['password_hash' => $newHash], (int) $me['id']);
                        // Bu cihazda oturum sürer, diğer cihazlardaki oturumlar düşer (pv değişti)
                        flash('success', 'Şifreniz değiştirildi. Diğer cihazlardaki oturumlarınız kapatıldı.');
                        start_customer_session((int) $me['id'], $newHash, !empty($_SESSION['remember']));
                        go('hesabim');
                    }
                }
            }
        }
        $stats = db_one("SELECT COUNT(*) AS total,
                SUM(status IN ('beklemede', 'hazirlaniyor', 'kargoda')) AS active,
                COALESCE(SUM(CASE WHEN status NOT IN ('iptal', 'iade') THEN grand_total END), 0) AS spent
            FROM orders WHERE customer_id = ?", [$me['id']]);
        account_render('profile', ['title' => 'Hesabım', 'active' => '', 'me' => $me, 'old' => $old, 'errors' => $errors, 'stats' => $stats]);
        break;

    case 'siparisler':
        $no = $seg[2] ?? null;
        if ($no === null) {
            $filters = [
                '' => ['Tümü', null],
                'aktif' => ['Devam eden', "status IN ('beklemede', 'hazirlaniyor', 'kargoda')"],
                'tamamlanan' => ['Teslim edilen', "status = 'teslim-edildi'"],
                'iptal' => ['İptal / İade', "status IN ('iptal', 'iade')"],
            ];
            $filter = isset($filters[$_GET['durum'] ?? '']) ? (string) ($_GET['durum'] ?? '') : '';
            $where = 'customer_id = ?' . ($filters[$filter][1] ? ' AND ' . $filters[$filter][1] : '');
            $orders = db_all("SELECT id, order_no, grand_total, status, payment_method, created_at FROM orders WHERE $where ORDER BY created_at DESC LIMIT 100", [$me['id']]);
            $counts = db_one("SELECT COUNT(*) AS t, SUM(status IN ('beklemede', 'hazirlaniyor', 'kargoda')) AS a,
                SUM(status = 'teslim-edildi') AS d, SUM(status IN ('iptal', 'iade')) AS c FROM orders WHERE customer_id = ?", [$me['id']]);
            account_render('orders', [
                'title' => 'Siparişlerim', 'active' => 'siparisler', 'me' => $me,
                'orders' => $orders, 'items' => order_items_for(array_column($orders, 'id')),
                'filters' => $filters, 'filter' => $filter,
                'counts' => ['' => $counts['t'], 'aktif' => $counts['a'], 'tamamlanan' => $counts['d'], 'iptal' => $counts['c']],
            ]);
            break;
        }

        $order = db_one('SELECT * FROM orders WHERE order_no = ? AND customer_id = ?', [$no, $me['id']]);
        if (!$order) account_not_found($me);

        if (($seg[3] ?? '') === 'iptal') {
            if (!is_post()) go('hesabim/siparisler/' . rawurlencode($no));
            if (in_array($order['status'], ['iptal', 'iade'], true)) {
                flash('info', 'Bu sipariş zaten iptal edilmiş.');
            } elseif (cancel_order((int) $order['id'])) {
                flash('success', "#$no numaralı siparişiniz iptal edildi.");
            } else {
                flash('danger', 'Bu sipariş hazırlanmaya başladığı için iptal edilemiyor. Lütfen bizimle iletişime geçin.');
            }
            go('hesabim/siparisler/' . rawurlencode($no));
        }
        if (isset($seg[3])) account_not_found($me);

        account_render('order', [
            'title' => 'Sipariş #' . $order['order_no'], 'active' => 'siparisler', 'me' => $me,
            'order' => $order, 'items' => order_items_for([$order['id']])[$order['id']] ?? [],
        ]);
        break;

    case 'adresler':
        $param = $seg[2] ?? null;
        $address = null;
        if ($param !== null && $param !== 'yeni') {
            $address = ctype_digit($param) ? db_one('SELECT * FROM customer_addresses WHERE id = ? AND customer_id = ?', [(int) $param, $me['id']]) : null;
            if (!$address) account_not_found($me);
        }
        $action = $seg[3] ?? null;

        if ($address && $action !== null) {
            if (!is_post() || !in_array($action, ['sil', 'varsayilan'], true)) account_not_found($me);
            if ($action === 'sil') {
                db_run('DELETE FROM customer_addresses WHERE id = ? AND customer_id = ?', [$address['id'], $me['id']]);
                // Varsayılan adres silindiyse en yeni adres varsayılan olsun
                if ($address['is_default']) {
                    db_run('UPDATE customer_addresses SET is_default = 1 WHERE customer_id = ? ORDER BY id DESC LIMIT 1', [$me['id']]);
                }
                flash('success', '“' . $address['title'] . '” adresi silindi.');
            } else {
                set_default_address((int) $me['id'], (int) $address['id']);
                flash('success', '“' . $address['title'] . '” varsayılan teslimat adresiniz oldu.');
            }
            go('hesabim/adresler');
        }

        if ($param === null) {
            $addresses = db_all('SELECT * FROM customer_addresses WHERE customer_id = ? ORDER BY is_default DESC, id DESC', [$me['id']]);
            account_render('addresses', ['title' => 'Adreslerim', 'active' => 'adresler', 'me' => $me, 'addresses' => $addresses]);
            break;
        }

        $count = (int) db_value('SELECT COUNT(*) FROM customer_addresses WHERE customer_id = ?', [$me['id']]);
        if (!$address && $count >= ADDRESS_LIMIT) {
            flash('danger', 'En fazla ' . ADDRESS_LIMIT . ' adres kaydedebilirsiniz. Yeni adres için birini silin.');
            go('hesabim/adresler');
        }

        $old = $address ?? ['title' => $count ? '' : 'Ev', 'full_name' => $me['name'], 'phone' => (string) $me['phone'],
            'city' => '', 'district' => '', 'address' => '', 'is_default' => $count === 0];
        if (is_post()) {
            [$data, $errors] = address_input();
            $old = $data + ['is_default' => isset($_POST['is_default'])];
            if (!$errors) {
                if ($address) {
                    db_update('customer_addresses', $data, (int) $address['id']);
                    $id = (int) $address['id'];
                } else {
                    $id = db_insert('customer_addresses', $data + ['customer_id' => $me['id']]);
                }
                if ($old['is_default'] || $count === 0) set_default_address((int) $me['id'], $id);
                flash('success', $address ? 'Adres güncellendi.' : 'Adres eklendi.');
                go('hesabim/adresler');
            }
        }
        account_render('address-form', [
            'title' => $address ? 'Adresi Düzenle' : 'Yeni Adres', 'active' => 'adresler', 'me' => $me,
            'address' => $address, 'old' => $old, 'errors' => $errors,
        ]);
        break;

    default:
        account_not_found($me);
}

// ---------------------------------------------------------------------------

/** Adres formunu doğrular; [veri, hatalar] döner */
function address_input(): array
{
    $data = [
        'title' => mb_substr(input('title'), 0, 60),
        'full_name' => mb_substr(input('full_name'), 0, 160),
        'phone' => mb_substr(input('phone'), 0, 30),
        'city' => mb_substr(input('city'), 0, 60),
        'district' => mb_substr(input('district'), 0, 60),
        'address' => mb_substr(input('address'), 0, 500),
    ];
    $errors = [];
    if ($data['title'] === '') $errors['title'] = 'Adrese bir ad verin (Ev, İş …).';
    if ($data['full_name'] === '') $errors['full_name'] = 'Alıcı adını yazın.';
    if (!clean_phone($data['phone'])) $errors['phone'] = 'Geçerli bir telefon numarası yazın.';
    if ($data['city'] === '') $errors['city'] = 'İl seçin.';
    if ($data['district'] === '') $errors['district'] = 'İlçe yazın.';
    if (mb_strlen($data['address']) < 10) $errors['address'] = 'Açık adresi mahalle, sokak ve numarayla yazın.';
    return [$data, $errors];
}

function set_default_address(int $customerId, int $addressId): void
{
    db_run('UPDATE customer_addresses SET is_default = (id = ?) WHERE customer_id = ?', [$addressId, $customerId]);
}

function account_not_found(array $me): never
{
    http_response_code(404);
    account_render('not-found', ['title' => 'Bulunamadı', 'active' => '', 'me' => $me]);
    exit;
}
