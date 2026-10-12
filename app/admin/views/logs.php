<?php
$actions = [
    'create' => ['Ekledi', 'success', 'bi-plus-circle'],
    'update' => ['Güncelledi', 'info', 'bi-pencil'],
    'delete' => ['Sildi', 'danger', 'bi-trash'],
    'login' => ['Giriş', 'primary', 'bi-box-arrow-in-right'],
    'logout' => ['Çıkış', 'secondary', 'bi-box-arrow-right'],
];
$entities = ['product' => 'Ürün', 'category' => 'Kategori', 'order' => 'Sipariş', 'customer' => 'Müşteri', 'page' => 'Sayfa', 'post' => 'Blog',
    'slide' => 'Slayt', 'testimonial' => 'Yorum', 'settings' => 'Ayarlar', 'admin' => 'Yönetici'];
?>
<div class="page-head">
    <div>
        <h1>İşlem Kayıtları</h1>
        <p>Panelde yapılan tüm değişiklikler kimin, ne zaman, hangi IP'den yaptığıyla birlikte saklanır.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <section class="card-x">
            <div class="table-responsive">
                <table class="table table-x align-middle mb-0">
                    <thead><tr><th>İşlem</th><th>Kayıt</th><th>Kim</th><th>Zaman</th></tr></thead>
                    <tbody>
                    <?php foreach ($logs as $l): [$label, $class, $icon] = $actions[$l['action']] ?? [$l['action'], 'secondary', 'bi-dot']; ?>
                        <tr>
                            <td><span class="status status--<?= $class ?>"><i class="bi <?= $icon ?> me-1"></i><?= e($label) ?></span></td>
                            <td>
                                <span class="text-muted small"><?= e($entities[$l['entity']] ?? $l['entity']) ?></span>
                                <div><?= e($l['summary'] ?? '—') ?></div>
                            </td>
                            <td><?= e($l['admin_name'] ?? 'Silinmiş hesap') ?><small class="d-block text-muted"><?= e($l['ip']) ?></small></td>
                            <td class="text-muted text-nowrap"><?= fmt_date($l['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php admin_partial('pagination', ['p' => $pager]); ?>
        </section>
    </div>
    <div class="col-xl-4">
        <section class="card-x card-x--pad">
            <h2 class="card-x__title">Son başarısız girişler</h2>
            <?php if (!$failedLogins): ?>
                <p class="text-muted mb-0">Başarısız giriş denemesi yok.</p>
            <?php else: ?>
                <ul class="timeline">
                    <?php foreach ($failedLogins as $f): ?>
                        <li><strong><?= e($f['email']) ?></strong><small><?= e($f['ip']) ?> · <?= fmt_date($f['attempted_at']) ?></small></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </div>
</div>
