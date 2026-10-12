<div class="page-head">
    <div>
        <h1><?= e($def['plural']) ?></h1>
        <p><?= e($def['description']) ?></p>
    </div>
    <a href="<?= admin_url($def['route'] . '/yeni') ?>" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Yeni <?= e($def['singular']) ?></a>
</div>

<section class="card-x">
    <?php if (!$rows): ?>
        <div class="empty-state">
            <i class="bi <?= $def['icon'] ?>"></i>
            <h2>Henüz kayıt yok</h2>
            <p>İlk <?= e(mb_strtolower($def['singular'])) ?> kaydını oluşturun.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-x align-middle mb-0">
                <thead>
                <tr>
                    <?php foreach ($def['columns'] as $label): ?><th><?= e($label) ?></th><?php endforeach; ?>
                    <th>Durum</th><th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r): $editUrl = admin_url($def['route'] . '/' . $r['id']); ?>
                    <tr>
                        <?php foreach ($def['columns'] as $col => $label): ?>
                            <td<?= $col === 'image' ? ' class="col-thumb"' : '' ?>>
                                <?php if ($col === 'image'): ?>
                                    <img src="<?= e(img($r['image'], 160, 100, $def['route'] . $r['id'])) ?>" alt="" class="thumb-wide">
                                <?php elseif ($col === $def['title_field']): ?>
                                    <a href="<?= $editUrl ?>" class="fw-semibold"><?= e(str_replace("\n", ' ', (string) $r[$col])) ?></a>
                                <?php elseif ($col === 'rating'): ?>
                                    <span class="stars"><?= str_repeat('★', (int) $r[$col]) ?><span><?= str_repeat('★', 5 - (int) $r[$col]) ?></span></span>
                                <?php elseif ($col === 'published_at'): ?>
                                    <span class="text-muted text-nowrap"><?= fmt_date($r[$col], false) ?></span>
                                    <?php if ($r[$col] > date('Y-m-d')): ?><span class="status status--info ms-1">Planlandı</span><?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted text-truncate-2"><?= e((string) $r[$col]) ?></span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <td><?= $r['is_active'] ? '<span class="status status--success">Yayında</span>' : '<span class="status status--secondary">Pasif</span>' ?></td>
                        <td class="text-end text-nowrap">
                            <?php if (isset($def['public_url']) && $r['is_active']): ?>
                                <a href="<?= url($def['public_url']($r)) ?>" target="_blank" rel="noopener" class="btn btn-icon" title="Mağazada gör"><i class="bi bi-eye"></i></a>
                            <?php endif; ?>
                            <a href="<?= $editUrl ?>" class="btn btn-icon" title="Düzenle"><i class="bi bi-pencil"></i></a>
                            <form method="post" action="<?= admin_url($def['route'] . '/' . $r['id'] . '/sil') ?>" class="d-inline" data-confirm="Bu kayıt silinsin mi? Bu işlem geri alınamaz.">
                                <?= csrf_field() ?>
                                <button class="btn btn-icon btn-icon--danger" title="Sil"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
