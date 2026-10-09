/* Yaman Ticaret — mağaza ön yüz scriptleri
   Sepet ve favoriler şimdilik localStorage'da tutulur; backend yazıldığında
   Cart.save / Cart.load fonksiyonları API çağrılarıyla değiştirilecek. */
(function () {
    'use strict';

    const $ = (s, el = document) => el.querySelector(s);
    const $$ = (s, el = document) => [...el.querySelectorAll(s)];
    const money = n => n.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + APP.currency;
    const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    const store = {
        get(key, def) { try { return JSON.parse(localStorage.getItem(key)) ?? def; } catch (e) { return def; } },
        set(key, val) { try { localStorage.setItem(key, JSON.stringify(val)); } catch (e) { /* gizli sekme vb. */ } }
    };

    /* ---------- Toast ---------- */
    function toast(message, type = 'success') {
        const icons = { success: 'check-circle', danger: 'x-circle', info: 'info-circle' };
        const el = document.createElement('div');
        el.className = `toast align-items-center text-bg-${type === 'info' ? 'dark' : type} border-0`;
        el.innerHTML = `<div class="d-flex"><div class="toast-body"><i class="bi bi-${icons[type] || icons.info} me-2"></i>${esc(message)}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
        $('#toastBox').appendChild(el);
        const t = new bootstrap.Toast(el, { delay: 2500 });
        el.addEventListener('hidden.bs.toast', () => el.remove());
        t.show();
    }

    /* ---------- Sepet ---------- */
    const Cart = {
        items: store.get('yt_cart', []),
        key: i => i.id + '|' + (i.size || '') + '|' + (i.color || ''),
        save() { store.set('yt_cart', this.items); this.render(); },
        add(item, qty = 1) {
            const found = this.items.find(i => this.key(i) === this.key(item));
            found ? found.qty += qty : this.items.push({ ...item, qty });
            this.save();
        },
        update(key, qty) {
            const it = this.items.find(i => this.key(i) === key);
            if (!it) return;
            it.qty = Math.max(1, qty);
            this.save();
        },
        remove(key) { this.items = this.items.filter(i => this.key(i) !== key); this.save(); },
        count() { return this.items.reduce((s, i) => s + i.qty, 0); },
        total() { return this.items.reduce((s, i) => s + i.qty * i.price, 0); },
        shipping() { const t = this.total(); return t === 0 || t >= APP.freeShipping ? 0 : 59.9; },

        itemHtml(i, big) {
            const k = esc(this.key(i));
            const variant = [i.color, i.size].filter(Boolean).join(' / ');
            return `<div class="cart-item" data-key="${k}">
                <a href="${esc(i.url)}"><img src="${esc(i.image)}" alt=""></a>
                <div class="cart-item__info">
                    <a href="${esc(i.url)}">${esc(i.name)}</a>
                    ${variant ? `<small>${esc(variant)}</small>` : ''}
                    <small>${money(i.price)}</small>
                    <div class="cart-item__row">
                        <div class="qty qty--sm">
                            <button type="button" data-cart-qty="-1">−</button>
                            <input type="number" value="${i.qty}" min="1" data-cart-input>
                            <button type="button" data-cart-qty="1">+</button>
                        </div>
                        <button type="button" class="cart-item__remove" data-cart-remove>Kaldır</button>
                    </div>
                </div>
                ${big ? `<div class="cart-item__line">${money(i.qty * i.price)}</div>` : ''}
            </div>`;
        },

        render() {
            const count = this.count(), total = this.total(), ship = this.shipping(), empty = count === 0;
            $$('[data-cart-count]').forEach(el => el.textContent = count);
            $$('[data-cart-total]').forEach(el => el.textContent = money(total));
            $$('[data-cart-shipping]').forEach(el => el.textContent = ship ? money(ship) : 'Ücretsiz');
            $$('[data-cart-grand]').forEach(el => el.textContent = money(total + ship));
            $$('[data-cart-empty]').forEach(el => el.style.display = empty ? '' : 'none');
            $$('[data-cart-footer]').forEach(el => el.style.display = empty ? 'none' : '');
            $$('[data-cart-items]').forEach(el => el.innerHTML = this.items.map(i => this.itemHtml(i)).join(''));
            $$('[data-cart-rows]').forEach(el => el.innerHTML = this.items.map(i => this.itemHtml(i, true)).join(''));
            $$('[data-checkout-items]').forEach(el => el.innerHTML = this.items.map(i =>
                `<div class="cart-item"><img src="${esc(i.image)}" alt=""><div class="cart-item__info"><a href="${esc(i.url)}">${esc(i.name)}</a><small>${i.qty} × ${money(i.price)}</small></div></div>`).join(''));

            const left = APP.freeShipping - total;
            $$('[data-ship-bar]').forEach(el => el.style.width = Math.min(100, total / APP.freeShipping * 100) + '%');
            $$('[data-ship-text]').forEach(el => el.innerHTML = left > 0
                ? `Ücretsiz kargo için <strong>${money(left)}</strong> daha ekle.`
                : '<i class="bi bi-check-circle-fill text-success"></i> Kargo bedava!');
        }
    };

    document.addEventListener('click', e => {
        const row = e.target.closest('.cart-item[data-key]');
        if (!row) return;
        const key = row.dataset.key;
        if (e.target.closest('[data-cart-remove]')) Cart.remove(key);
        const btn = e.target.closest('[data-cart-qty]');
        if (btn) {
            const it = Cart.items.find(i => Cart.key(i) === key);
            if (it) Cart.update(key, it.qty + Number(btn.dataset.cartQty));
        }
    });
    document.addEventListener('change', e => {
        if (!e.target.matches('[data-cart-input]')) return;
        Cart.update(e.target.closest('.cart-item').dataset.key, parseInt(e.target.value, 10) || 1);
    });

    function openCart() {
        const el = $('#miniCart');
        bootstrap.Modal.getInstance($('#quickView'))?.hide();
        bootstrap.Offcanvas.getOrCreateInstance(el).show();
    }

    // Ürün kartındaki hızlı ekle
    document.addEventListener('click', e => {
        const btn = e.target.closest('[data-add-cart]');
        if (!btn) return;
        Cart.add(JSON.parse(btn.dataset.addCart));
        toast('Ürün sepete eklendi');
        openCart();
    });

    // Ürün detay / hızlı bakış: varyant seçimi + adet
    document.addEventListener('click', e => {
        const q = e.target.closest('[data-qty]');
        if (q) {
            const input = $('[data-qty-input]', q.closest('.qty'));
            input.value = Math.max(1, (parseInt(input.value, 10) || 1) + Number(q.dataset.qty));
            return;
        }
        const add = e.target.closest('[data-add-detail], [data-buy-now]');
        if (!add) return;
        const box = $('.product-info[data-product]', add.closest('.modal-body') || document);
        if (!box) return;
        const sizeOpt = $('.size-options', box);
        const size = $('input[type=radio]:checked', sizeOpt || document.createElement('div'));
        if (sizeOpt && !size) {
            e.preventDefault();
            sizeOpt.closest('.product-option').classList.add('is-invalid');
            toast('Lütfen beden seçiniz', 'danger');
            sizeOpt.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        const color = $('.color-options input:checked', box);
        const qty = parseInt($('[data-qty-input]', box)?.value, 10) || 1;
        Cart.add({ ...JSON.parse(box.dataset.product), size: size?.value || '', color: color?.value || '' }, qty);
        if (add.matches('[data-buy-now]')) return; // link ödeme sayfasına gider
        toast('Ürün sepete eklendi');
        openCart();
    });

    document.addEventListener('change', e => {
        if (e.target.matches('.size-options input')) {
            const box = e.target.closest('.product-info');
            box.querySelector('.size-options').closest('.product-option').classList.remove('is-invalid');
            const l = $('[data-size-label]', box); if (l) l.textContent = e.target.value;
        }
        if (e.target.matches('.color-options input')) {
            const l = $('[data-color-label]', e.target.closest('.product-info')); if (l) l.textContent = e.target.value;
        }
    });

    /* ---------- Favoriler ---------- */
    const Wish = {
        ids: store.get('yt_wish', []),
        toggle(id) {
            const i = this.ids.indexOf(id);
            i > -1 ? this.ids.splice(i, 1) : this.ids.push(id);
            store.set('yt_wish', this.ids);
            toast(i > -1 ? 'Favorilerden çıkarıldı' : 'Favorilere eklendi', i > -1 ? 'info' : 'success');
            this.render();
        },
        render() {
            $$('[data-wishlist-count]').forEach(el => el.textContent = this.ids.length);
            $$('[data-wishlist]').forEach(btn => {
                const on = this.ids.includes(Number(btn.dataset.wishlist));
                btn.classList.toggle('active', on);
                const i = $('i', btn); if (i) i.className = on ? 'bi bi-heart-fill' : 'bi bi-heart';
            });
            const grid = $('[data-wishlist-grid]');
            if (grid) {
                $$('[data-wishlist-item]', grid).forEach(el => el.classList.toggle('d-none', !this.ids.includes(Number(el.dataset.wishlistItem))));
                $('[data-wishlist-empty]').classList.toggle('d-none', this.ids.length > 0);
            }
        }
    };
    document.addEventListener('click', e => {
        const btn = e.target.closest('[data-wishlist]');
        if (btn) Wish.toggle(Number(btn.dataset.wishlist));
    });

    /* ---------- Hızlı bakış ---------- */
    document.addEventListener('click', async e => {
        const btn = e.target.closest('[data-quick-view]');
        if (!btn) return;
        const modal = $('#quickView'), body = $('[data-quick-body]', modal);
        body.innerHTML = '<div class="text-center p-5"><span class="loader-spin"></span></div>';
        bootstrap.Modal.getOrCreateInstance(modal).show();
        try {
            const res = await fetch(`${APP.baseUrl}/hizli-bakis/${encodeURIComponent(btn.dataset.quickView)}`);
            if (!res.ok) throw new Error(res.status);
            body.innerHTML = await res.text();
        } catch (err) {
            body.innerHTML = '<p class="p-5 text-center">Ürün yüklenemedi.</p>';
        }
    });

    /* ---------- Slider'lar ---------- */
    function initSwipers() {
        if (typeof Swiper === 'undefined') return;

        new Swiper('.top-bar__ticker', { loop: true, autoplay: { delay: 3500 }, speed: 600, direction: 'vertical', allowTouchMove: false, autoHeight: true });

        new Swiper('.hero-swiper', {
            loop: true, speed: 900, effect: 'fade', fadeEffect: { crossFade: true },
            autoplay: { delay: 5500, disableOnInteraction: false },
            pagination: { el: '.hero-swiper .swiper-pagination', clickable: true }
        });

        const withArrows = (sel, opts) => $$(sel).forEach(el => {
            const head = el.closest('.section')?.querySelector('.section-head');
            new Swiper(el, {
                ...opts,
                navigation: head ? { prevEl: $('[data-prev]', head), nextEl: $('[data-next]', head) } : undefined
            });
        });

        withArrows('.product-swiper', {
            slidesPerView: 2, spaceBetween: 14, speed: 500,
            breakpoints: { 768: { slidesPerView: 3, spaceBetween: 20 }, 1200: { slidesPerView: 4, spaceBetween: 28 } }
        });
        withArrows('.category-swiper', {
            slidesPerView: 2, spaceBetween: 14,
            breakpoints: { 768: { slidesPerView: 3, spaceBetween: 20 }, 1200: { slidesPerView: 5, spaceBetween: 24 } }
        });

        new Swiper('.testimonial-swiper', {
            slidesPerView: 1, spaceBetween: 20, autoplay: { delay: 5000 },
            pagination: { el: '.testimonial-swiper .swiper-pagination', clickable: true },
            breakpoints: { 768: { slidesPerView: 2 }, 1200: { slidesPerView: 3, spaceBetween: 28 } }
        });

        const thumbsEl = $('.gallery-thumbs');
        if (thumbsEl) {
            const thumbs = new Swiper(thumbsEl, {
                slidesPerView: 'auto', spaceBetween: 10, watchSlidesProgress: true,
                direction: 'horizontal',
                breakpoints: { 768: { direction: 'vertical', slidesPerView: 5 } }
            });
            new Swiper('.gallery-main', {
                spaceBetween: 10, thumbs: { swiper: thumbs },
                navigation: { prevEl: '.gallery-main .swiper-button-prev', nextEl: '.gallery-main .swiper-button-next' }
            });
        }
    }

    /* ---------- Diğer UI ---------- */
    function initUi() {
        const header = $('#siteHeader'), toTop = $('#backToTop'), sticky = $('[data-sticky-buy]');
        const buyBtn = $('.product-buy');
        const onScroll = () => {
            const y = window.scrollY;
            header.classList.toggle('is-scrolled', y > 10);
            toTop.classList.toggle('show', y > 600);
            if (sticky && buyBtn) sticky.classList.toggle('show', buyBtn.getBoundingClientRect().bottom < 0);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
        toTop.addEventListener('click', () => window.scrollTo({ top: 0 }));

        // Footer akordeon (mobil)
        $$('.footer-col [data-collapse]').forEach(h => h.addEventListener('click', () => {
            if (window.innerWidth < 768) h.parentElement.classList.toggle('open');
        }));

        // Sıralama
        $('[data-sort-select]')?.addEventListener('change', e => location.search = e.target.value);

        // Grid sütun değiştirme
        $$('[data-grid]').forEach(btn => btn.addEventListener('click', () => {
            $$('[data-grid]').forEach(b => b.classList.toggle('active', b === btn));
            const grid = $('[data-product-grid]');
            if (grid) grid.style.setProperty('--cols', btn.dataset.grid);
            store.set('yt_grid', btn.dataset.grid);
        }));
        const savedGrid = store.get('yt_grid', null);
        if (savedGrid) $(`[data-grid="${savedGrid}"]`)?.click();

        // Bülten
        $$('[data-newsletter]').forEach(f => f.addEventListener('submit', e => {
            e.preventDefault(); f.reset(); toast('Bültene kaydoldun, teşekkürler!');
        }));

        // Ödeme formu (backend gelene kadar)
        $('[data-checkout]')?.addEventListener('submit', e => {
            e.preventDefault();
            if (!Cart.count()) return toast('Sepetin boş', 'danger');
            toast('Ödeme altyapısı henüz bağlanmadı (demo).', 'info');
        });

        // Geri sayım
        $$('[data-countdown]').forEach(el => {
            const end = new Date(el.dataset.countdown).getTime();
            const pad = n => String(n).padStart(2, '0');
            const tick = () => {
                const d = Math.max(0, end - Date.now()) / 1000;
                $('[data-d]', el).textContent = pad(Math.floor(d / 86400));
                $('[data-h]', el).textContent = pad(Math.floor(d % 86400 / 3600));
                $('[data-m]', el).textContent = pad(Math.floor(d % 3600 / 60));
                $('[data-s]', el).textContent = pad(Math.floor(d % 60));
            };
            tick(); setInterval(tick, 1000);
        });

        // Bölümleri kaydırınca göster
        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver(entries => entries.forEach(en => {
                if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
            }), { rootMargin: '0px 0px -60px 0px' });
            $$('main .section .section-head, main .section .row, main .features, main .deal-box').forEach(el => {
                el.classList.add('reveal'); io.observe(el);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        initSwipers();
        initUi();
        Cart.render();
        Wish.render();
    });
    window.addEventListener('load', () => $('#pageLoader')?.classList.add('is-hidden'));
    setTimeout(() => $('#pageLoader')?.classList.add('is-hidden'), 2500);

    window.App = { toast, Cart, Wish };
})();
