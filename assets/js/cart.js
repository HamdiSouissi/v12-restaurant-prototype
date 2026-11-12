/* =========================================================================
   cart.js — cassette à citrons : état, localStorage, toast d'annulation,
   toggle conseil/asporto, frais et minimum, ouverture du checkout.
   Expose window.LIMONAIA_CART : add, removeOne, remove, qtyOf, items, ...
   ========================================================================= */
(function () {
  'use strict';

  const App = window.LIMONAIA_APP || {};
  const DATA = window.LIMONAIA || {};
  const DELIVERY = DATA.delivery || { fee: 3, free_from: 30, minimum: 15, eta: '30–40 min', pickup_eta: '15–20 min' };
  const STORE_KEY = 'limonaia:cart:v1';
  const MODE_KEY = 'limonaia:cart:mode';

  const drawer = document.getElementById('cart-drawer');
  const overlay = document.getElementById('cart-overlay');
  const listEl = document.querySelector('[data-cart-list]');
  const emptyEl = document.querySelector('[data-cart-empty]');
  const footEl = document.querySelector('[data-cart-foot]');
  const t = function (k, v) { return App.t ? App.t(k, v) : k; };

  let state = { items: [], mode: 'delivery' };
  let lastFocused = null;

  /* ------------------------------------------------------------ persistance */
  function load() {
    try {
      const raw = localStorage.getItem(STORE_KEY);
      if (raw) {
        const parsed = JSON.parse(raw);
        if (parsed && Array.isArray(parsed.items)) {
          state.items = parsed.items.filter(function (i) {
            return i && typeof i.slug === 'string' && i.qty > 0;
          }).map(function (i) {
            return {
              slug: i.slug,
              name: String(i.name || ''),
              price: Number(i.price) || 0,
              qty: Math.max(1, Math.min(20, Number(i.qty) || 1)),
              single: Boolean(i.single),
              meta: i.meta || ''
            };
          });
          state.mode = parsed.mode === 'pickup' ? 'pickup' : 'delivery';
        }
      }
    } catch (e) { /* stockage indisponible */ }
  }

  function save() {
    try { localStorage.setItem(STORE_KEY, JSON.stringify(state)); } catch (e) { /* noop */ }
  }

  /* ------------------------------------------------------------ calculs */
  function subtotal() {
    return state.items.reduce(function (sum, i) { return sum + i.price * i.qty; }, 0);
  }
  function fee() {
    if (state.mode === 'pickup') return 0;
    return subtotal() >= DELIVERY.free_from ? 0 : DELIVERY.fee;
  }
  function total() { return subtotal() + fee(); }
  function count() {
    return state.items.reduce(function (sum, i) { return sum + i.qty; }, 0);
  }
  function missingForFree() {
    return Math.max(0, DELIVERY.free_from - subtotal());
  }
  function belowMinimum() {
    return state.mode === 'delivery' && subtotal() > 0 && subtotal() < DELIVERY.minimum;
  }

  /* ------------------------------------------------------------ rendu */
  function imageFor(item) {
    if (item.slug === 'giro-della-costiera') return '';
    return 'assets/img/piatti/' + item.slug;
  }

  function render() {
    if (listEl) {
      listEl.innerHTML = state.items.map(function (item) {
        const img = imageFor(item);
        const thumb = item.slug === 'giro-della-costiera'
          ? '<span class="cart-item__thumb cart-item__thumb--giro" aria-hidden="true">' + App.esc('G') + '</span>'
          : '<span class="cart-item__thumb">' +
              '<picture>' +
                '<source srcset="' + img + '.webp" type="image/webp">' +
                '<img src="' + img + '.jpg" alt="" width="52" height="52" loading="lazy">' +
              '</picture>' +
            '</span>';
        return '' +
          '<li class="cart-item" data-slug="' + App.esc(item.slug) + '">' +
            thumb +
            '<div class="cart-item__body">' +
              '<p class="cart-item__name">' + App.esc(App.dishName(item.slug, item.name)) + '</p>' +
              (item.slug === 'giro-della-costiera'
                ? '<p class="cart-item__meta">' + App.esc(t('cart.single')) + (item.meta ? ' · ' + App.esc(item.meta) : '') + '</p>'
                : '<p class="cart-item__meta">' + App.esc(App.money(item.price)) + '</p>') +
              (item.single ? '' :
                '<span class="cart-item__qty">' +
                  '<button type="button" data-cart-dec="' + App.esc(item.slug) + '" aria-label="' + App.esc(t('cart.qty')) + ' −">−</button>' +
                  '<span aria-label="' + App.esc(t('cart.qty')) + '">' + item.qty + '</span>' +
                  '<button type="button" data-cart-inc="' + App.esc(item.slug) + '" aria-label="' + App.esc(t('cart.qty')) + ' +">+</button>' +
                '</span>') +
            '</div>' +
            '<p class="cart-item__price">' + App.esc(App.money(item.price * item.qty)) + '</p>' +
            '<button class="cart-item__remove" type="button" data-cart-remove="' + App.esc(item.slug) + '">' +
              App.esc(t('menu.remove')) +
            '</button>' +
          '</li>';
      }).join('');
    }

    const isEmpty = state.items.length === 0;
    if (emptyEl) emptyEl.hidden = !isEmpty;
    if (footEl) footEl.hidden = isEmpty;
    if (listEl) listEl.hidden = isEmpty;

    const subEl = document.querySelector('[data-cart-subtotal]');
    const feeEl = document.querySelector('[data-cart-fee]');
    const totEl = document.querySelector('[data-cart-total]');
    if (subEl) subEl.textContent = App.money(subtotal());
    if (feeEl) feeEl.textContent = fee() === 0 ? (isEmpty ? App.money(0) : t('cart.free')) : App.money(fee());
    if (totEl) totEl.textContent = App.money(total());

    const progress = document.querySelector('[data-cart-progress]');
    const progressText = document.querySelector('[data-cart-progress-text]');
    const progressBar = document.querySelector('[data-cart-progress-bar]');
    if (progress && progressText && progressBar) {
      const missing = missingForFree();
      if (state.mode === 'pickup') {
        progress.hidden = true;
      } else {
        progress.hidden = false;
        progress.classList.toggle('is-done', missing === 0);
        progressText.textContent = missing === 0
          ? t('cart.progress_done')
          : t('cart.progress', { x: App.money(missing) });
        progressBar.style.width = Math.min(100, (subtotal() / DELIVERY.free_from) * 100) + '%';
      }
    }

    const minEl = document.querySelector('[data-cart-minimum]');
    if (minEl) minEl.hidden = !belowMinimum();

    /* badges */
    document.querySelectorAll('[data-cart-badge]').forEach(function (badge) {
      badge.textContent = String(count());
      badge.setAttribute('data-empty', count() === 0 ? 'true' : 'false');
    });
    document.querySelectorAll('[data-cart-count]').forEach(function (el) {
      el.textContent = String(count());
    });

    /* toggles de mode */
    document.querySelectorAll('[data-mode]').forEach(function (btn) {
      const on = btn.getAttribute('data-mode') === state.mode;
      btn.classList.toggle('is-active', on);
      btn.setAttribute('aria-pressed', String(on));
    });

    /* checkout bloqué si sous le minimum */
    const checkoutBtn = document.querySelector('[data-checkout]');
    if (checkoutBtn) {
      checkoutBtn.disabled = isEmpty || belowMinimum();
      checkoutBtn.style.opacity = checkoutBtn.disabled ? '.55' : '';
    }

    save();
    document.dispatchEvent(new CustomEvent('limonaia:cart', { detail: { items: state.items, mode: state.mode } }));
  }

  /* ------------------------------------------------------------ actions */
  function popBadge() {
    document.querySelectorAll('[data-cart-badge]').forEach(function (badge) {
      badge.classList.remove('is-pop');
      void badge.offsetWidth;
      badge.classList.add('is-pop');
    });
  }

  function add(item) {
    if (!item || !item.slug) return;
    const existing = state.items.find(function (i) { return i.slug === item.slug; });
    if (existing) {
      if (existing.single) {
        App.toast(App.t('cart.added') + ': ' + App.dishName(item.slug, item.name));
        popBadge();
        return;
      }
      existing.qty = Math.min(20, existing.qty + (item.qty || 1));
    } else {
      state.items.push({
        slug: item.slug,
        name: item.name || item.slug,
        price: Number(item.price) || 0,
        qty: item.single ? 1 : (item.qty || 1),
        single: Boolean(item.single),
        meta: item.meta || ''
      });
    }
    render();
    popBadge();
    if (!item.silent) App.toast(App.dishName(item.slug, item.name) + ' ' + App.t('cart.added'));
  }

  function removeOne(slug) {
    const item = state.items.find(function (i) { return i.slug === slug; });
    if (!item) return;
    if (item.single || item.qty <= 1) { remove(slug); return; }
    item.qty -= 1;
    render();
  }

  function remove(slug) {
    const index = state.items.findIndex(function (i) { return i.slug === slug; });
    if (index === -1) return;
    const removed = state.items[index];
    state.items.splice(index, 1);
    render();
    App.toast(App.t('cart.remove'), {
      actionLabel: App.t('cart.undo'),
      duration: 5000,
      onAction: function () {
        state.items.splice(Math.min(index, state.items.length), 0, removed);
        render();
      }
    });
  }

  function clear() { state.items = []; render(); }

  function setMode(mode) {
    state.mode = mode === 'pickup' ? 'pickup' : 'delivery';
    try { localStorage.setItem(MODE_KEY, state.mode); } catch (e) { /* noop */ }
    render();
  }

  /* ------------------------------------------------------------ ouverture du tiroir */
  function openCart() {
    if (!drawer) return;
    lastFocused = document.activeElement;
    drawer.hidden = false;
    if (overlay) overlay.hidden = false;
    requestAnimationFrame(function () {
      drawer.classList.add('is-open');
      if (overlay) overlay.classList.add('is-open');
    });
    drawer.setAttribute('aria-hidden', 'false');
    App.lockScroll(true);
    window.setTimeout(function () {
      const focusable = App.focusables(drawer)[0];
      if (focusable) focusable.focus();
    }, 260);
    document.addEventListener('keydown', cartKeydown);
  }

  function closeCart() {
    if (!drawer) return;
    drawer.classList.remove('is-open');
    if (overlay) overlay.classList.remove('is-open');
    App.lockScroll(false);
    document.removeEventListener('keydown', cartKeydown);
    window.setTimeout(function () {
      drawer.hidden = true;
      if (overlay) overlay.hidden = true;
      drawer.setAttribute('aria-hidden', 'true');
    }, 420);
    if (lastFocused) lastFocused.focus();
  }

  function cartKeydown(ev) {
    if (ev.key === 'Escape') { ev.preventDefault(); closeCart(); return; }
    if (drawer) App.trapFocus(drawer, ev);
  }

  /* ------------------------------------------------------------ écouteurs */
  document.querySelectorAll('[data-open-cart]').forEach(function (btn) {
    btn.addEventListener('click', function (ev) { ev.preventDefault(); openCart(); });
  });
  document.querySelectorAll('[data-close-cart]').forEach(function (btn) {
    btn.addEventListener('click', function (ev) { ev.preventDefault(); closeCart(); });
  });
  document.querySelectorAll('[data-close-cart-goto]').forEach(function (btn) {
    btn.addEventListener('click', function () { closeCart(); });
  });
  if (overlay) overlay.addEventListener('click', closeCart);

  if (drawer) {
    drawer.addEventListener('click', function (ev) {
      const inc = ev.target.closest('[data-cart-inc]');
      const dec = ev.target.closest('[data-cart-dec]');
      const rm = ev.target.closest('[data-cart-remove]');
      if (inc) {
        const item = state.items.find(function (i) { return i.slug === inc.getAttribute('data-cart-inc'); });
        if (item) { item.qty = Math.min(20, item.qty + 1); render(); }
      }
      if (dec) {
        const slug = dec.getAttribute('data-cart-dec');
        const item = state.items.find(function (i) { return i.slug === slug; });
        if (item && item.qty > 1) { item.qty -= 1; render(); } else if (item) { remove(slug); }
      }
      if (rm) remove(rm.getAttribute('data-cart-remove'));
    });
  }

  document.querySelectorAll('[data-mode]').forEach(function (btn) {
    btn.addEventListener('click', function () { setMode(btn.getAttribute('data-mode')); });
  });

  const checkoutBtn = document.querySelector('[data-checkout]');
  if (checkoutBtn) {
    checkoutBtn.addEventListener('click', function () {
      document.dispatchEvent(new CustomEvent('limonaia:checkout'));
    });
  }

  document.querySelectorAll('[data-scroll-order]').forEach(function (link) {
    link.addEventListener('click', function () {
      // petit indice visuel : on ouvre la carte du menu
      const tabs = document.querySelector('[data-tab="antipasti"]');
      if (tabs) tabs.click();
    });
  });

  /* changement de langue : re-render (noms de plats stockés en IT) */
  document.addEventListener('limonaia:lang', render);

  /* ------------------------------------------------------------ API publique */
  window.LIMONAIA_CART = {
    add: add,
    remove: remove,
    removeOne: removeOne,
    clear: clear,
    setMode: setMode,
    open: openCart,
    close: closeCart,
    qtyOf: function (slug) {
      const item = state.items.find(function (i) { return i.slug === slug; });
      return item ? item.qty : 0;
    },
    items: function () { return state.items.slice(); },
    mode: function () { return state.mode; },
    subtotal: subtotal,
    fee: fee,
    total: total,
    count: count,
    belowMinimum: belowMinimum,
    render: render
  };

  load();
  render();
})();
