<?php
/**
 * /giris, /kayit-ol, /sifremi-unuttum, /cikis
 * @var string $mode giris|kayit|sifre|cikis
 */

store_session();

if ($mode === 'cikis') {
    if (is_post()) {
        customer_logout();
        store_session();
        flash('success', 'Çıkış yaptınız. Yine bekleriz!');
    }
    go('giris');
}

if (current_customer() && $mode !== 'sifre') {
    go(safe_return($_GET['donus'] ?? null));
}

$return = safe_return($_GET['donus'] ?? null);
$errors = [];
$old = [];

if ($mode === 'giris' && is_post()) {
    $old['email'] = input('email');
    $error = customer_login($old['email'], (string) ($_POST['password'] ?? ''), isset($_POST['remember']));
    if ($error === null) {
        go($return);
    }
    $errors['form'] = $error;
}

if ($mode === 'kayit' && is_post()) {
    $old = [
        'first_name' => mb_substr(input('first_name'), 0, 80),
        'last_name' => mb_substr(input('last_name'), 0, 80),
        'email' => mb_strtolower(input('email')),
        'phone' => mb_substr(input('phone'), 0, 30),
    ];
    $password = (string) ($_POST['password'] ?? '');

    // Bot tuzağı: gerçek kullanıcı bu gizli alanı görmez, doldurmaz
    if (input('website') !== '') {
        go('kayit-ol');
    }
    // IP başına saatte en fazla 5 yeni hesap / 20 deneme (e-posta taraması ve toplu sahte kayıt engeli)
    [$created, $tried] = array_values(db_one("SELECT COALESCE(SUM(success), 0) AS s, COUNT(*) AS c FROM login_attempts
        WHERE scope = 'register' AND ip = ? AND attempted_at > NOW() - INTERVAL 60 MINUTE", [client_ip()]));
    if ($created >= CUSTOMER_REGISTER_MAX_PER_IP || $tried >= 20) {
        $errors['form'] = 'Bu ağdan kısa sürede çok fazla kayıt denemesi yapıldı. Lütfen daha sonra tekrar deneyin.';
    } else {
        if ($old['first_name'] === '') $errors['first_name'] = 'Adınızı yazın.';
        if ($old['last_name'] === '') $errors['last_name'] = 'Soyadınızı yazın.';
        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($old['email']) > 190) $errors['email'] = 'Geçerli bir e-posta adresi yazın.';
        if ($old['phone'] !== '' && !clean_phone($old['phone'])) $errors['phone'] = 'Telefon numarası geçersiz.';
        if ($err = password_problem($password, CUSTOMER_PASSWORD_MIN)) $errors['password'] = $err;
        elseif (!hash_equals($password, (string) ($_POST['password_confirm'] ?? ''))) $errors['password_confirm'] = 'Şifreler eşleşmiyor.';
        if (!isset($_POST['kvkk'])) $errors['kvkk'] = 'Devam etmek için aydınlatma metnini onaylayın.';

        if (!$errors && db_value('SELECT 1 FROM customers WHERE email = ?', [$old['email']])) {
            $errors['email'] = 'Bu e-posta ile kayıtlı bir hesap var. Giriş yapmayı deneyin.';
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_ARGON2ID);
        $id = db_insert('customers', ['phone' => $old['phone'] ?: null, 'password_hash' => $hash] + $old);
        record_attempt('register', $old['email'], true);
        flash('success', 'Hoş geldiniz ' . $old['first_name'] . '! Hesabınız oluşturuldu.');
        start_customer_session($id, $hash, false);
        go($return);
    }
    record_attempt('register', $old['email'], false);
}

render('auth', [
    'title' => ['giris' => 'Giriş Yap', 'kayit' => 'Kayıt Ol', 'sifre' => 'Şifremi Unuttum'][$mode],
    'mode' => $mode,
    'errors' => $errors,
    'old' => $old,
    'return' => $return,
]);
