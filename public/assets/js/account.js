/* Yaman Ticaret — Hesabım paneli */
(function () {
    'use strict';

    // Silme / iptal onayı
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.confirm(form.getAttribute('data-confirm'))) e.preventDefault();
        });
    });

    // Bildirimi kapat
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-dismiss-alert]');
        if (btn) btn.closest('.acc-alert').remove();
    });

    // Aktif menü öğesi mobilde görünür olsun
    var active = document.querySelector('.acc-nav .is-active');
    if (active && window.matchMedia('(max-width: 991.98px)').matches) {
        active.scrollIntoView({ block: 'nearest', inline: 'center' });
    }

    // Formu iki kez göndermeyi engelle
    document.querySelectorAll('.acc-main form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (e.defaultPrevented) return;
            var btn = form.querySelector('button:not([type=button])');
            if (btn) setTimeout(function () { btn.disabled = true; });
        });
    });
})();
