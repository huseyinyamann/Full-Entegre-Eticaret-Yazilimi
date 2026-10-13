<?php if (session_status() === PHP_SESSION_ACTIVE): foreach (take_flashes() as [$type, $msg]): ?>
    <div class="alert alert-<?= e($type) ?> d-flex align-items-center gap-2" role="alert">
        <i class="bi <?= $type === 'success' ? 'bi-check-circle' : ($type === 'info' ? 'bi-info-circle' : 'bi-exclamation-triangle') ?>"></i>
        <span><?= e($msg) ?></span>
    </div>
<?php endforeach; endif; ?>
