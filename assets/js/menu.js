/* =========================================================================
   menu.js — onglets en carreaux avec indicateur jaune glissant, filtres,
   entrée des cartes en vague, synchronisation des steppers avec le panier.
   ========================================================================= */
(function () {
  'use strict';

  const App = window.LIMONAIA_APP || {};
  const section = document.getElementById('menu');
  if (!section) return;
  const reduce = App.reduceMotion || window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const hasGSAP = typeof window.gsap !== 'undefined';
  const t = function (k, v) { return App.t ? App.t(k, v) : k; };

  const tabs = Array.prototype.slice.call(section.querySelectorAll('[data-tab]'));
  const indicator = section.querySelector('[data-tab-indicator]');
  const panels = Array.prototype.slice.call(section.querySelectorAll('[data-panel]'));
  const chips = Array.prototype.slice.call(section.querySelectorAll('[data-filter]'));

  let activeTab = tabs.length ? tabs[0].getAttribute('data-tab') : null;
  let activeFilter = 'tutti';

  /* ------------------------------------------------------------ indicateur glissant */
  function moveIndicator(tab) {
    if (!indicator || !tab) return;
    const parent = tab.parentElement.getBoundingClientRect();
    const rect = tab.getBoundingClientRect();
    indicator.style.width = rect.width + 'px';
    indicator.style.height = rect.height + 'px';
    indicator.style.transform = 'translate(' + (rect.left - parent.left) + 'px,' + (rect.top - parent.top) + 'px)';
    indicator.classList.add('is-ready');
  }

  function paintIndicator() {
    const tab = tabs.find(function (t2) { return t2.getAttribute('data-tab') === activeTab; });
    moveIndicator(tab);
  }

  /* ------------------------------------------------------------ cartes en vague */
  function animateCards(panel) {
    if (!panel) return;
    const cards = panel.querySelectorAll('.dish:not(.is-filtered)');
    if (!hasGSAP || reduce) {
      cards.forEach(function (c) { c.style.opacity = '1'; c.style.transform = 'none'; });
      return;
    }
    gsap.killTweensOf(cards);
    gsap.fromTo(cards,
      { opacity: 0, y: 26, x: -10 },
      {
        opacity: 1, y: 0, x: 0,
        duration: 0.62,
        ease: 'power2.out',
        stagger: { each: 0.05, from: 'start' },
        clearProps: 'transform'
      }
    );
  }

  /* ------------------------------------------------------------ application des filtres */
  function applyFilter(filter) {
    activeFilter = filter;
    chips.forEach(function (chip) {
      const on = chip.getAttribute('data-filter') === filter;
      chip.classList.toggle('is-active', on);
      chip.setAttribute('aria-pressed', String(on));
    });

    panels.forEach(function (panel) {
      const visible = panel.getAttribute('data-panel') === activeTab;
      if (!visible) return;
      let count = 0;
      panel.querySelectorAll('.dish').forEach(function (dish) {
        const tags = (dish.getAttribute('data-tags') || '').split(/\s+/);
        const show = filter === 'tutti' ||
          (filter === 'veg' && tags.indexOf('veg') !== -1) ||
          (filter === 'senza-glutine' && tags.indexOf('gf') !== -1) ||
          (filter === 'senza-lattosio' && tags.indexOf('lf') !== -1);
        dish.classList.toggle('is-filtered', !show);
        if (show) count++;
      });
      const empty = panel.querySelector('.menu-empty');
      if (empty) empty.hidden = count !== 0;
      animateCards(panel);
    });
  }

  /* ------------------------------------------------------------ changement d'onglet */
  function selectTab(key, focus) {
    activeTab = key;
    tabs.forEach(function (tab) {
      const on = tab.getAttribute('data-tab') === key;
      tab.setAttribute('aria-selected', String(on));
      tab.setAttribute('tabindex', on ? '0' : '-1');
      if (on && focus) tab.focus();
    });
    panels.forEach(function (panel) {
      panel.hidden = panel.getAttribute('data-panel') !== key;
    });
    paintIndicator();
    applyFilter(activeFilter);
  }

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () { selectTab(tab.getAttribute('data-tab'), false); });
    tab.addEventListener('keydown', function (ev) {
      const i = tabs.indexOf(tab);
      let nextIndex = null;
      if (ev.key === 'ArrowRight') nextIndex = (i + 1) % tabs.length;
      if (ev.key === 'ArrowLeft') nextIndex = (i - 1 + tabs.length) % tabs.length;
      if (ev.key === 'Home') nextIndex = 0;
      if (ev.key === 'End') nextIndex = tabs.length - 1;
      if (nextIndex === null) return;
      ev.preventDefault();
      selectTab(tabs[nextIndex].getAttribute('data-tab'), true);
    });
  });

  chips.forEach(function (chip) {
    chip.addEventListener('click', function () { applyFilter(chip.getAttribute('data-filter')); });
  });

  /* ------------------------------------------------------------ steppers + / – */
  function stepperFor(slug) {
    return section.querySelector('.stepper[data-stepper] [data-add="' + slug + '"]');
  }

  function syncSteppers() {
    if (!window.LIMONAIA_CART) return;
    section.querySelectorAll('[data-stepper]').forEach(function (stepper) {
      const addBtn = stepper.querySelector('[data-add]');
      if (!addBtn) return;
      const slug = addBtn.getAttribute('data-add');
      const qty = window.LIMONAIA_CART.qtyOf(slug);
      const qtyEl = stepper.querySelector('.stepper__qty');
      if (qtyEl) qtyEl.textContent = String(qty);
      stepper.classList.toggle('is-active', qty > 0);
      addBtn.setAttribute('aria-label', (qty > 0 ? t('menu.add') : t('menu.add')) + ': ' + addBtn.getAttribute('data-name'));
    });

    // mêmes produits dans le carrousel des plats du soleil
    document.querySelectorAll('.piatti__add[data-add]').forEach(function (btn) {
      const qty = window.LIMONAIA_CART.qtyOf(btn.getAttribute('data-add'));
      btn.classList.toggle('is-in-cart', qty > 0);
      const label = btn.querySelector('span');
      if (label) label.textContent = qty > 0 ? t('menu.add') + ' · ' + qty : t('piatti.add');
    });
  }

  section.addEventListener('click', function (ev) {
    const addBtn = ev.target.closest('[data-add]');
    const rmBtn = ev.target.closest('[data-remove]');
    if (addBtn) {
      const slug = addBtn.getAttribute('data-add');
      if (window.LIMONAIA_CART) {
        window.LIMONAIA_CART.add({
          slug: slug,
          name: addBtn.getAttribute('data-name'),
          price: Number(addBtn.getAttribute('data-price')) || 0
        });
      }
      return;
    }
    if (rmBtn) {
      if (window.LIMONAIA_CART) window.LIMONAIA_CART.removeOne(rmBtn.getAttribute('data-remove'));
    }
  });

  document.addEventListener('limonaia:cart', syncSteppers);
  document.addEventListener('limonaia:lang', syncSteppers);

  /* ------------------------------------------------------------ init */
  window.setTimeout(function () {
    if (tabs.length) selectTab(activeTab, false);
    syncSteppers();
  }, 40);

  let resizeTimer = null;
  window.addEventListener('resize', function () {
    window.clearTimeout(resizeTimer);
    resizeTimer = window.setTimeout(paintIndicator, 140);
  });

  // après chargement des polices, l'indicateur doit se replacer
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(function () { window.setTimeout(paintIndicator, 60); });
  }

  document.addEventListener('limonaia:lang', function () {
    window.setTimeout(paintIndicator, 40);
    // libellés de catégories
    section.querySelectorAll('[data-i18n-cat]').forEach(function (el) {
      const key = el.getAttribute('data-i18n-cat');
      const cat = (window.LIMONAIA.menu || {})[key];
      if (cat) el.textContent = App.bi(cat.label);
    });
  });
})();
