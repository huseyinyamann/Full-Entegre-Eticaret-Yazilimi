<div class="page-head">
    <div>
        <h1>Yöneticiler</h1>
        <p>Panele erişebilen hesaplar. Editörler katalog ve içeriği, yöneticiler ek olarak sipariş, müşteri ve ayarları yönetir.</p>
    </div>
    <a href="<?= admin_url('yoneticiler/yeni') ?>" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Yeni Yönetici</a>
</div>

<section class="card-x">
    <div class="table-responsive">
        <table class="table table-x align-middle mb-0">
            <thead><tr><th>Ad</th><th>Rol</th><th>Son giriş</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($admins as $a): ?>
                <tr>
                    <td>
                        <div class="product-cell">
                            <span class="avatar avatar--soft"><?= e(mb_strtoupper(mb_substr($a['name'], 0, 1))) ?></span>
                            <div>
                                <a href="<?= admin_url('yoneticiler/' . $a['id']) ?>" class="fw-semibold"><?= e($a['name']) ?></a>
                                <?php if ((int) $a['id'] === (int) current_admin()['id']): ?><span class="status status--info ms-1">Siz</span><?php endif; ?>
                                <small><?= e($a['email']) ?></small>
                            </div>
                        </div>
                    </td>
                    <td><?= e(ROLE_LABELS[$a['role']]) ?></td>
                    <td class="text-muted text-nowrap"><?= fmt_date($a['last_login_at']) ?><?php if ($a['last_login_ip']): ?><small class="d-block"><?= e($a['last_login_ip']) ?></small><?php endif; ?></td>
                    <td><?= $a['is_active'] ? '<span class="status status--success">Aktif</span>' : '<span class="status status--secondary">Pasif</span>' ?></td>
                    <td class="text-end"><a href="<?= admin_url('yoneticiler/' . $a['id']) ?>" class="btn btn-icon" title="Düzenle"><i class="bi bi-pencil"></i></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
