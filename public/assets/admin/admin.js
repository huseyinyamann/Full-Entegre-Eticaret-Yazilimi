/* Yaman Ticaret — Yönetim paneli */
(function () {
    'use strict';

    var $ = function (sel, ctx) { return (ctx || document).querySelector(sel); };
    var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); };

    // ---------- Yan menü (mobil) ----------
    var sidebar = $('#sidebar');
    $$('[data-sidebar-open]').forEach(function (btn) {
        btn.addEventListener('click', function () { sidebar.classList.add('is-open'); });
    });
    $$('[data-sidebar-close]').forEach(function (el) {
        el.addEventListener('click', function () { sidebar.classList.remove('is-open'); });
    });

    // ---------- Açık / koyu tema ----------
    function setThemeIcon() {
        var dark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        $$('[data-theme-toggle] i').forEach(function (i) { i.className = dark ? 'bi bi-sun' : 'bi bi-moon-stars'; });
    }
    setThemeIcon();
    $$('[data-theme-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var next = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', next);
            try { localStorage.setItem('admin-theme', next); } catch (e) {}
            setThemeIcon();
            if (window.__salesChart) drawSalesChart();
        });
    });

    // ---------- Şifre göster/gizle ----------
    $$('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = $(btn.getAttribute('data-toggle-password'));
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

    // ---------- Silme onayı ----------
    $$('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.confirm(form.getAttribute('data-confirm'))) e.preventDefault();
            else form.dataset.submitting = '1';
        });
    });

    // ---------- Tıklanabilir tablo satırı ----------
    $$('tr[data-href]').forEach(function (row) {
        row.addEventListener('click', function (e) {
            if (e.target.closest('a, button, input, form, label')) return;
            window.location.href = row.getAttribute('data-href');
        });
    });

    // ---------- Filtre seçince gönder ----------
    $$('[data-autosubmit]').forEach(function (el) {
        el.addEventListener('change', function () { el.form.submit(); });
    });

    // ---------- Başlıktan URL önerisi ----------
    var trMap = { 'ç': 'c', 'ğ': 'g', 'ı': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u', 'Ç': 'c', 'Ğ': 'g', 'İ': 'i', 'Ö': 'o', 'Ş': 's', 'Ü': 'u' };
    function slugify(text) {
        return text.replace(/[çğıöşüÇĞİÖŞÜ]/g, function (c) { return trMap[c]; })
            .toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }
    $$('[data-slug-source]').forEach(function (src) {
        var target = $(src.getAttribute('data-slug-source'));
        if (!target) return;
        var update = function () { target.placeholder = slugify(src.value) || 'otomatik oluşturulur'; };
        src.addEventListener('input', update);
        update();
    });

    // ---------- Renk satırları ----------
    var colorRows = $('#colorRows');
    if (colorRows) {
        $('[data-add-color]').addEventListener('click', function () {
            var first = colorRows.querySelector('.color-row');
            var row = first.cloneNode(true);
            row.querySelector('input[type=text]').value = '';
            row.querySelector('input[type=color]').value = '#111111';
            colorRows.appendChild(row);
            row.querySelector('input[type=text]').focus();
        });
        colorRows.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-remove-row]');
            if (!btn) return;
            var rows = colorRows.querySelectorAll('.color-row');
            var row = btn.closest('.color-row');
            if (rows.length > 1) row.remove();
            else row.querySelector('input[type=text]').value = '';
        });
    }

    // ---------- Yeni görsel önizleme ----------
    $$('input[type=file][data-preview]').forEach(function (input) {
        var box = $(input.getAttribute('data-preview'));
        var zone = input.closest('.dropzone');
        input.addEventListener('change', function () {
            box.innerHTML = '';
            Array.prototype.forEach.call(input.files, function (file) {
                if (!/^image\/(jpeg|png|webp)$/.test(file.type)) return;
                var tile = document.createElement('div');
                tile.className = 'image-tile image-tile--new';
                var img = document.createElement('img');
                img.alt = '';
                img.src = URL.createObjectURL(file);
                tile.appendChild(img);
                box.appendChild(tile);
            });
        });
        if (zone) {
            ['dragenter', 'dragover'].forEach(function (ev) { zone.addEventListener(ev, function () { zone.classList.add('is-drag'); }); });
            ['dragleave', 'drop'].forEach(function (ev) { zone.addEventListener(ev, function () { zone.classList.remove('is-drag'); }); });
        }
    });

    // ---------- Kaydedilmemiş değişiklik uyarısı ----------
    $$('form[data-dirty-check]').forEach(function (form) {
        var dirty = false;
        form.addEventListener('input', function () { dirty = true; });
        form.addEventListener('change', function () { dirty = true; });
        form.addEventListener('submit', function () { dirty = false; });
        window.addEventListener('beforeunload', function (e) {
            if (dirty) { e.preventDefault(); e.returnValue = ''; }
        });
    });

    // ---------- Satış grafiği ----------
    var dataEl = $('#salesChartData');
    var canvas = $('#salesChart');
    function cssVar(name) { return getComputedStyle(document.documentElement).getPropertyValue(name).trim(); }
    var tl = new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY', maximumFractionDigits: 0 });
    var tlCompact = new Intl.NumberFormat('tr-TR', { notation: 'compact', maximumFractionDigits: 1 });

    function drawSalesChart() {
        if (!dataEl || !canvas || !window.Chart) return;
        var rows = JSON.parse(dataEl.textContent);
        var ink = cssVar('--muted');
        if (window.__salesChart) window.__salesChart.destroy();
        window.__salesChart = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: rows.map(function (r) { return r.label; }),
                datasets: [{
                    label: 'Ciro',
                    data: rows.map(function (r) { return r.total; }),
                    backgroundColor: cssVar('--chart-bar'),
                    hoverBackgroundColor: cssVar('--brand-hover'),
                    borderRadius: { topLeft: 4, topRight: 4 },
                    borderSkipped: 'bottom',
                    maxBarThickness: 28,
                    categoryPercentage: .7,
                    barPercentage: .9
                }]
            },
            options: {
                maintainAspectRatio: false,
                animation: { duration: 400 },
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: cssVar('--ink'),
                        titleColor: cssVar('--surface'),
                        bodyColor: cssVar('--surface'),
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function (ctx) {
                                var r = rows[ctx.dataIndex];
                                return tl.format(r.total) + ' · ' + r.count + ' sipariş';
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, border: { color: cssVar('--line') }, ticks: { color: ink, font: { family: 'Albert Sans', size: 11 }, maxRotation: 0, autoSkipPadding: 8 } },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: cssVar('--chart-grid') },
                        ticks: { color: ink, font: { family: 'Albert Sans', size: 11 }, maxTicksLimit: 5, callback: function (v) { return '₺' + tlCompact.format(v); } }
                    }
                }
            }
        });
    }
    drawSalesChart();
})();
