<?php /** @var array $addresses */ ?>
<div class="acc-head acc-head--row">
    <div>
        <h1>Adreslerim</h1>
        <p>Kayıtlı adresleriniz ödeme sırasında tek tıkla doldurulur</p>
    </div>
    <a href="<?= url('hesabim/adresler/yeni') ?>" class="acc-btn"><i class="bi bi-plus-lg"></i> Yeni Adres</a>
</div>

<?php if (!$addresses): ?>
    <section class="acc-card">
        <div class="acc-empty">
            <span class="acc-empty__icon"><i class="bi bi-geo-alt"></i></span>
            <h2>Kayıtlı adresiniz yok</h2>
            <p>Adres ekleyin; bir sonraki siparişinizde bilgileri yeniden yazmanız gerekmesin.</p>
            <a href="<?= url('hesabim/adresler/yeni') ?>" class="acc-btn"><i class="bi bi-plus-lg"></i> Adres Ekle</a>
        </div>
    </section>
<?php else: ?>
    <div class="acc-address-grid">
        <?php foreach ($addresses as $a): ?>
            <article class="acc-card acc-address<?= $a['is_default'] ? ' is-default' : '' ?>">
                <header class="acc-address__head">
                    <span class="acc-card__icon acc-tone--<?= $a['is_default'] ? 'orange' : 'gray' ?>"><i class="bi <?= mb_strtolower($a['title']) === 'iş' ? 'bi-briefcase' : 'bi-house' ?>"></i></span>
                    <h2><?= e($a['title']) ?></h2>
                    <?php if ($a['is_default']): ?><span class="acc-pill">Varsayılan</span><?php endif; ?>
                </header>
                <div class="acc-address__body acc-info">
                    <strong><?= e($a['full_name']) ?></strong>
                    <p><?= e($a['address']) ?><br><?= e($a['district'] . ' / ' . $a['city']) ?></p>
                    <small><i class="bi bi-telephone"></i> <?= e($a['phone']) ?></small>
                </div>
                <footer class="acc-address__foot">
                    <a href="<?= url('hesabim/adresler/' . (int) $a['id']) ?>" class="acc-link"><i class="bi bi-pencil"></i> Düzenle</a>
                    <?php if (!$a['is_default']): ?>
                        <form method="post" action="<?= url('hesabim/adresler/' . (int) $a['id'] . '/varsayilan') ?>">
                            <?= csrf_field() ?>
                            <button class="acc-link"><i class="bi bi-star"></i> Varsayılan yap</button>
                        </form>
                    <?php endif; ?>
                    <form method="post" action="<?= url('hesabim/adresler/' . (int) $a['id'] . '/sil') ?>" class="ms-auto" data-confirm="“<?= e($a['title']) ?>” adresi silinsin mi?">
                        <?= csrf_field() ?>
                        <button class="acc-link acc-link--danger"><i class="bi bi-trash"></i> Sil</button>
                    </form>
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
