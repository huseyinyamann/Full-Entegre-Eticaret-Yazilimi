<?php if ($p['pages'] > 1): ?>
    <nav class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 py-3 border-top">
        <small class="text-muted">Toplam <?= (int) $p['total'] ?> kayıt · Sayfa <?= $p['page'] ?> / <?= $p['pages'] ?></small>
        <ul class="pagination pagination-sm mb-0">
            <li class="page-item<?= $p['page'] <= 1 ? ' disabled' : '' ?>"><a class="page-link" href="<?= e(qs(['sayfa' => $p['page'] - 1])) ?>"><i class="bi bi-chevron-left"></i></a></li>
            <?php for ($i = max(1, $p['page'] - 2); $i <= min($p['pages'], $p['page'] + 2); $i++): ?>
                <li class="page-item<?= $i === $p['page'] ? ' active' : '' ?>"><a class="page-link" href="<?= e(qs(['sayfa' => $i])) ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <li class="page-item<?= $p['page'] >= $p['pages'] ? ' disabled' : '' ?>"><a class="page-link" href="<?= e(qs(['sayfa' => $p['page'] + 1])) ?>"><i class="bi bi-chevron-right"></i></a></li>
        </ul>
    </nav>
<?php endif; ?>
