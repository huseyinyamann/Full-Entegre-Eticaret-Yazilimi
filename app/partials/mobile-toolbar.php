<nav class="mobile-toolbar d-lg-none">
    <a href="<?= url('kategoriler') ?>"><i class="bi bi-grid"></i><span>Kategoriler</span></a>
    <button type="button" data-bs-toggle="offcanvas" data-bs-target="#searchPanel"><i class="bi bi-search"></i><span>Ara</span></button>
    <a href="<?= account_url() ?>"><i class="bi bi-person<?= current_customer() ? '-check' : '' ?>"></i><span>Hesabım</span></a>
    <a href="<?= url('favorilerim') ?>"><i class="bi bi-heart"></i><span>Favoriler</span></a>
    <button type="button" data-bs-toggle="offcanvas" data-bs-target="#miniCart"><i class="bi bi-bag"></i><span>Sepet</span><em class="count" data-cart-count>0</em></button>
</nav>
