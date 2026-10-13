<?php

if ($action !== 'index') {
    redirect('kayitlar');
}

$pager = paginate('SELECT COUNT(*) FROM admin_logs', [], 30);
$logs = db_all("SELECT l.*, a.name AS admin_name FROM admin_logs l LEFT JOIN admins a ON a.id = l.admin_id
    ORDER BY l.id DESC LIMIT {$pager['limit']} OFFSET {$pager['offset']}");
$failedLogins = db_all("SELECT ip, email, attempted_at FROM login_attempts WHERE scope = 'admin' AND success = 0 ORDER BY id DESC LIMIT 10");

admin_render('logs', ['title' => 'İşlem Kayıtları', 'active' => 'kayitlar', 'logs' => $logs, 'pager' => $pager, 'failedLogins' => $failedLogins]);
