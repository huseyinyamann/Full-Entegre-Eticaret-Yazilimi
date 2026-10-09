<?php partial('breadcrumb', ['heading' => 'İletişim', 'items' => ['İletişim' => null]]); ?>

<section class="section pt-4">
    <div class="container-xl">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="form-card form-card--soft h-100">
                    <h5>Bize Ulaşın</h5>
                    <ul class="contact-list">
                        <li><i class="bi bi-geo-alt"></i><div><strong>Adres</strong><?= e(config('address')) ?></div></li>
                        <li><i class="bi bi-telephone"></i><div><strong>Telefon</strong><?= e(config('phone')) ?></div></li>
                        <li><i class="bi bi-envelope"></i><div><strong>E-posta</strong><?= e(config('email')) ?></div></li>
                        <li><i class="bi bi-clock"></i><div><strong>Çalışma Saatleri</strong>Hafta içi 09:00 – 18:00</div></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-7">
                <form class="form-card" onsubmit="event.preventDefault(); this.reset(); App.toast('Mesajın alındı, en kısa sürede dönüş yapacağız.')">
                    <h5>Mesaj Gönder</h5>
                    <div class="row g-3">
                        <div class="col-md-6"><input class="form-control" placeholder="Adınız Soyadınız" required></div>
                        <div class="col-md-6"><input type="email" class="form-control" placeholder="E-posta" required></div>
                        <div class="col-12"><input class="form-control" placeholder="Konu"></div>
                        <div class="col-12"><textarea class="form-control" rows="5" placeholder="Mesajınız" required></textarea></div>
                        <div class="col-12"><button class="btn btn-dark-brand">Gönder</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
