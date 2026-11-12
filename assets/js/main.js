/* =========================================================================
   main.js — socle : Lenis, GSAP/ScrollTrigger, preloader, header, curseur,
   révélations, vagues réactives, footer à niveau d'eau, toasts, statut open.
   Expose window.LIMONAIA_APP (bus d'événements + helpers partagés).
   ========================================================================= */
(function () {
  'use strict';

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const isTouch = window.matchMedia('(hover: none)').matches;
  const App = window.LIMONAIA_APP = window.LIMONAIA_APP || {};
  App.reduceMotion = reduceMotion.matches;
  App.isTouch = isTouch;
  App.t = function (key, vars) { return window.I18N ? window.I18N.t(key, vars) : key; };
  App.bi = function (field) { return window.I18N ? window.I18N.bi(field) : (field && field.it) || ''; };
  App.dishName = function (slug, fallback) {
    const found = window.I18N && window.I18N.dishName ? window.I18N.dishName(slug) : '';
    return found || fallback || slug;
  };

  /* Relibelle les nœuds qui portent data-it / data-en (noms et descriptions de plats,
     libellés de catégories, filtres). Appelé à la bascule et une fois au démarrage. */
  App.relabel = function (root) {
    const scope = root || document;
    const lang = (window.I18N && window.I18N.lang) === 'en' ? 'en' : 'it';
    scope.querySelectorAll('[data-it][data-en]').forEach(function (el) {
      const next = el.getAttribute(lang === 'en' ? 'data-en' : 'data-it');
      if (next && el.textContent !== next) el.textContent = next;
    });
    /* le nom embarqué dans les boutons d'ajout et les choix du Giro suit la langue */
    scope.querySelectorAll('[data-add][data-name], [data-opt-name]').forEach(function (el) {
      const attr = el.hasAttribute('data-opt-name') ? 'data-opt-name' : 'data-name';
      el.setAttribute(attr, App.dishName(el.value || el.getAttribute('data-add'), el.getAttribute(attr)));
    });
  };

  const hasGSAP = typeof window.gsap !== 'undefined';
  if (hasGSAP) {
    gsap.registerPlugin(window.ScrollTrigger, window.Flip, window.SplitText, window.Draggable);
  }

  /* ------------------------------------------------------------ utilitaires */
  App.esc = function (str) {
    return String(str == null ? '' : str)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  };
  App.money = function (value) {
    const n = Number(value) || 0;
    return '€ ' + (Number.isInteger(n) ? n : n.toFixed(2).replace('.', ','));
  };
  App.focusables = function (root) {
    return Array.prototype.slice.call(root.querySelectorAll(
      'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
    )).filter(function (el) { return el.offsetParent !== null || el === document.activeElement; });
  };
  /** Piège de focus réutilisable (drawer, modales, lightbox). */
  App.trapFocus = function (root, ev) {
    if (ev.key !== 'Tab') return;
    const list = App.focusables(root);
    if (!list.length) return;
    const first = list[0], last = list[list.length - 1];
    if (ev.shiftKey && document.activeElement === first) { ev.preventDefault(); last.focus(); }
    else if (!ev.shiftKey && document.activeElement === last) { ev.preventDefault(); first.focus(); }
  };
  App.lockScroll = function (lock) {
    document.body.classList.toggle('is-locked', lock);
    if (App.lenis) lock ? App.lenis.stop() : App.lenis.start();
  };

  /* ------------------------------------------------------------ toasts */
  const zone = document.getElementById('toast-zone');
  const tpl = document.getElementById('toast-template');

  App.toast = function (message, opts) {
    if (!zone || !tpl) return;
    opts = opts || {};
    const node = tpl.content.firstElementChild.cloneNode(true);
    node.querySelector('.toast__text').textContent = message;

    const action = node.querySelector('.toast__action');
    let timer = null;

    function kill() {
      node.classList.add('is-out');
      window.setTimeout(function () { node.remove(); }, 260);
    }

    if (opts.actionLabel && typeof opts.onAction === 'function') {
      action.hidden = false;
      action.textContent = opts.actionLabel;
      action.addEventListener('click', function () {
        opts.onAction();
        if (timer) window.clearTimeout(timer);
        kill();
      });
    }

    node.querySelector('.toast__close').addEventListener('click', function () {
      if (timer) window.clearTimeout(timer);
      kill();
    });

    zone.appendChild(node);
    // limite la pile à 3
    while (zone.children.length > 3) zone.firstElementChild.remove();
    requestAnimationFrame(function () { node.classList.add('is-in'); });
    timer = window.setTimeout(kill, opts.duration || 4000);
  };

  /* ------------------------------------------------------------ Lenis */
  let lenis = null;
  if (hasGSAP && typeof window.Lenis !== 'undefined' && !reduceMotion.matches) {
    lenis = new window.Lenis({
      duration: 1.05,
      easing: function (x) { return Math.min(1, 1.001 - Math.pow(2, -10 * x)); },
      smoothWheel: true,
      wheelMultiplier: 0.95,
      touchMultiplier: 1.4,
    });
    App.lenis = lenis;

    lenis.on('scroll', function (e) {
      if (window.ScrollTrigger) window.ScrollTrigger.update();
      document.documentElement.style.setProperty('--vh-vel', String(Math.min(Math.abs(e.velocity || 0), 60)));
      App.velocity = e.velocity || 0;
    });

    gsap.ticker.add(function (time) { lenis.raf(time * 1000); });
    gsap.ticker.lagSmoothing(0);
    window.ScrollTrigger.config({ ignoreMobileResize: true });
  } else {
    App.velocity = 0;
  }

  App.scrollTo = function (target) {
    const el = typeof target === 'string' ? document.querySelector(target) : target;
    if (!el) return;
    const offset = (document.getElementById('site-header') || {}).offsetHeight || 0;
    if (lenis) {
      lenis.scrollTo(el, { offset: -offset + 8, duration: 1.2 });
    } else {
      const top = el.getBoundingClientRect().top + window.scrollY - offset + 8;
      window.scrollTo({ top: top, behavior: reduceMotion.matches ? 'auto' : 'smooth' });
    }
  };

  /* ------------------------------------------------------------ preloader */
  const preloader = document.getElementById('preloader');
  function runPreloader() {
    if (!preloader) { document.body.classList.remove('is-loading'); return; }

    let seen = false;
    try { seen = sessionStorage.getItem('limonaia:preloaded') === '1'; } catch (e) { seen = false; }

    if (seen || reduceMotion.matches) {
      preloader.classList.add('is-skipped');
      document.body.classList.remove('is-loading');
      document.documentElement.classList.remove('is-loading');
      return;
    }

    const pct = preloader.querySelector('[data-preloader-pct]');
    const bar = preloader.querySelector('[data-preloader-bar]');
    const started = performance.now();
    const DURATION = 1250;
    const CEILING = 87; // « …87% » : le dernier citron n'est jamais tout à fait pressé

    function tick(now) {
      const p = Math.min(1, (now - started) / DURATION);
      const eased = 1 - Math.pow(1 - p, 3);
      const value = Math.round(eased * CEILING);
      if (pct) pct.textContent = value + '%';
      if (bar) bar.style.width = value + '%';
      if (p < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);

    window.setTimeout(function () {
      preloader.classList.add('is-done');
      document.body.classList.remove('is-loading');
      document.documentElement.classList.remove('is-loading');
      try { sessionStorage.setItem('limonaia:preloaded', '1'); } catch (e) { /* noop */ }
      document.dispatchEvent(new Event('limonaia:loaded'));
      window.setTimeout(function () { preloader.classList.add('is-skipped'); }, 700);
    }, 1500);
  }
  runPreloader();

  /* ------------------------------------------------------------ header */
  const header = document.getElementById('site-header');
  let lastY = window.scrollY;
  let ticking = false;

  function onScrollHeader() {
    const y = window.scrollY;
    if (!header) return;
    const shrunk = y > 120;
    header.setAttribute('data-shrunk', String(shrunk));

    if (y > 320 && y > lastY + 6) {
      header.classList.add('is-hidden');
    } else if (y < lastY - 6 || y < 120) {
      header.classList.remove('is-hidden');
    }
    lastY = y;

    // barre mobile
    const bar = document.getElementById('mobile-bar');
    if (bar) {
      const show = y > window.innerHeight * 0.75;
      bar.classList.toggle('is-visible', show);
      bar.setAttribute('aria-hidden', String(!show));
    }

    // lien actif
    updateActiveNav();
    ticking = false;
  }

  const navLinks = Array.prototype.slice.call(document.querySelectorAll('.site-nav__link'));
  function updateActiveNav() {
    if (!navLinks.length) return;
    const probe = window.scrollY + window.innerHeight * 0.34;
    let current = null;
    navLinks.forEach(function (link) {
      const id = link.getAttribute('href');
      const section = document.querySelector(id);
      if (section && section.offsetTop <= probe) current = link;
    });
    navLinks.forEach(function (link) { link.classList.toggle('is-active', link === current); });
  }

  window.addEventListener('scroll', function () {
    if (!ticking) { ticking = true; requestAnimationFrame(onScrollHeader); }
  }, { passive: true });
  onScrollHeader();

  // ancres internes
  document.querySelectorAll('a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (ev) {
      const href = a.getAttribute('href');
      if (!href || href === '#' || href.length < 2) return;
      const el = document.querySelector(href);
      if (!el) return;
      ev.preventDefault();
      App.scrollTo(el);
      history.replaceState(null, '', href);
    });
  });

  /* ------------------------------------------------------------ statut ouvert/fermé (Europe/Rome) */
  const HOURS = (window.LIMONAIA && window.LIMONAIA.hours) || null;

  function romeNow() {
    try {
      const parts = new Intl.DateTimeFormat('en-GB', {
        timeZone: 'Europe/Rome', weekday: 'short', hour: '2-digit', minute: '2-digit', hour12: false
      }).formatToParts(new Date());
      const map = {};
      parts.forEach(function (p) { map[p.type] = p.value; });
      const days = { Mon: 1, Tue: 2, Wed: 3, Thu: 4, Fri: 5, Sat: 6, Sun: 7 };
      return { day: days[map.weekday] || 1, minutes: parseInt(map.hour, 10) * 60 + parseInt(map.minute, 10) };
    } catch (e) {
      const d = new Date();
      return { day: d.getDay() || 7, minutes: d.getHours() * 60 + d.getMinutes() };
    }
  }

  function toMinutes(str) { const p = str.split(':'); return parseInt(p[0], 10) * 60 + parseInt(p[1], 10); }

  function computeOpen() {
    if (!HOURS) return { open: true, nextOpening: '19:00' };
    const now = romeNow();
    const today = HOURS[now.day] || [];
    for (let i = 0; i < today.length; i++) {
      const from = toMinutes(today[i][0]);
      let to = toMinutes(today[i][1]);
      if (to < from) to += 24 * 60; // service qui passe minuit
      const m = now.minutes < from && i === 0 ? now.minutes + 24 * 60 : now.minutes;
      if (m >= from && m <= to) return { open: true };
    }
    // prochaine ouverture
    for (let d = 0; d < 3; d++) {
      const day = ((now.day - 1 + d) % 7) + 1;
      const ranges = HOURS[day] || [];
      for (let i = 0; i < ranges.length; i++) {
        const from = toMinutes(ranges[i][0]);
        if (d === 0 && from <= now.minutes) continue;
        return { open: false, nextOpening: ranges[i][0] };
      }
    }
    return { open: false, nextOpening: '19:00' };
  }

  function paintStatus() {
    const state = computeOpen();
    const pills = document.querySelectorAll('#open-status, [data-open-inline]');
    pills.forEach(function (pill) {
      pill.setAttribute('data-open', String(state.open));
      const text = pill.querySelector('[data-open-text]');
      if (!text) return;
      const key = state.open ? 'nav.open' : 'nav.closed';
      text.textContent = state.open ? App.t(key) : App.t(key).replace('19:00', state.nextOpening);
    });
  }
  paintStatus();
  window.setInterval(paintStatus, 60000);
  document.addEventListener('limonaia:lang', paintStatus);

  /* ------------------------------------------------------------ menu mobile */
  const burger = document.getElementById('burger');
  const mobileMenu = document.getElementById('mobile-menu');
  let lastFocused = null;

  function openMobile() {
    if (!mobileMenu) return;
    lastFocused = document.activeElement;
    mobileMenu.hidden = false;
    requestAnimationFrame(function () { mobileMenu.classList.add('is-open'); });
    burger.setAttribute('aria-expanded', 'true');
    App.lockScroll(true);
    const first = mobileMenu.querySelector('a, button');
    if (first) window.setTimeout(function () { first.focus(); }, 260);
    document.addEventListener('keydown', mobileKeydown);
  }
  function closeMobile() {
    if (!mobileMenu) return;
    mobileMenu.classList.remove('is-open');
    burger.setAttribute('aria-expanded', 'false');
    App.lockScroll(false);
    document.removeEventListener('keydown', mobileKeydown);
    window.setTimeout(function () { mobileMenu.hidden = true; }, 360);
    if (lastFocused) lastFocused.focus();
  }
  function mobileKeydown(ev) {
    if (ev.key === 'Escape') { ev.preventDefault(); closeMobile(); return; }
    App.trapFocus(mobileMenu, ev);
  }
  if (burger) burger.addEventListener('click', function () {
    if (burger.getAttribute('aria-expanded') === 'true') closeMobile(); else openMobile();
  });
  document.querySelectorAll('[data-close-mobile], [data-close-mobile-link]').forEach(function (el) {
    if (el.hasAttribute('data-close-mobile-link')) {
      el.addEventListener('click', function () { closeMobile(); });
    } else {
      el.addEventListener('click', closeMobile);
    }
  });

  /* ------------------------------------------------------------ curseur tranche de citron */
  if (!isTouch && !reduceMotion.matches) {
    const cursor = document.createElement('div');
    cursor.className = 'cursor';
    cursor.setAttribute('aria-hidden', 'true');
    cursor.innerHTML =
      '<span class="cursor__slice"><svg viewBox="0 0 100 100"><use href="#i-lemon-slice"/></svg></span>' +
      '<span class="cursor__label"></span>';
    document.body.appendChild(cursor);

    const label = cursor.querySelector('.cursor__label');
    /* Libellés pilotés par i18n.js quand elle est chargée, repli italien sinon. */
    const LABELS = {
      add: 'Aggiungi', drag: 'Trascina', pick: 'Scegli', view: 'Guarda',
      it: { add: 'Aggiungi', drag: 'Trascina', pick: 'Scegli', view: 'Guarda' },
      en: { add: 'Add', drag: 'Drag', pick: 'Choose', view: 'Look' }
    };
    function cursorLabels() {
      const lang = document.documentElement.getAttribute('lang') === 'en' ? 'en' : 'it';
      return LABELS[lang];
    }
    let cx = window.innerWidth / 2, cy = window.innerHeight / 2, tx = cx, ty = cy;

    window.addEventListener('pointermove', function (ev) {
      tx = ev.clientX; ty = ev.clientY;
      cursor.style.opacity = '1';
      const el = ev.target.closest ? ev.target.closest('[data-cursor], a, button, .dish, .piatti__card, .gallery__btn, .cal-day') : null;
      let state = '';
      if (el) {
        /* le plus proche d'abord : une vignette de galerie dans la grille reste « GUARDA » */
        const explicit = el.closest('[data-cursor]');
        if (explicit) state = explicit.getAttribute('data-cursor');
        else if (el.closest('.dish, .piatti__card, [data-add]')) state = 'add';
        else if (el.closest('.reviews__track, .reviews__marquee')) state = 'drag';
        else if (el.closest('.cal-day')) state = 'pick';
        else state = 'link';
      }
      cursor.setAttribute('data-state', state);
      const dict = cursorLabels();
      label.textContent = dict[state] || '';
    }, { passive: true });

    window.addEventListener('pointerdown', function () { cursor.classList.add('is-down'); });
    window.addEventListener('pointerup', function () { cursor.classList.remove('is-down'); });

    (function loop() {
      cx += (tx - cx) * 0.2;
      cy += (ty - cy) * 0.2;
      cursor.style.transform = 'translate3d(' + cx + 'px,' + cy + 'px,0) translate(-50%,-50%)';
      requestAnimationFrame(loop);
    })();

    document.documentElement.classList.add('has-custom-cursor');
  }

  /* ------------------------------------------------------------ langue */
  App.relabel();
  document.addEventListener('limonaia:lang', function () {
    App.relabel();
  });

  /* ------------------------------------------------------------ révélations */
  function initReveal() {
    const items = document.querySelectorAll('[data-reveal]');
    if (!items.length) return;
    if (reduceMotion.matches || !hasGSAP) {
      items.forEach(function (el) { el.classList.add('is-in'); });
      return;
    }
    items.forEach(function (el) {
      window.ScrollTrigger.create({
        trigger: el,
        start: 'top 88%',
        once: true,
        onEnter: function () { el.classList.add('is-in'); }
      });
    });
  }
  initReveal();

  /* ------------------------------------------------------------ vagues réactives à la vélocité */
  function initWaves() {
    const waves = document.querySelectorAll('[data-wave]');
    if (!waves.length || !hasGSAP || reduceMotion.matches) return;
    waves.forEach(function (wave) {
      const layers = wave.querySelectorAll('.wave__layer');
      window.ScrollTrigger.create({
        trigger: wave,
        start: 'top bottom',
        end: 'bottom top',
        onUpdate: function (self) {
          const v = App.velocity || 0;
          const p = self.progress;
          layers.forEach(function (layer, i) {
            const shift = (i - 1) * v * 0.5;
            layer.style.setProperty('--wave-shift', shift + 'px');
            layer.style.marginLeft = shift + 'px';
          });
          wave.style.setProperty('--wave-progress', p.toFixed(3));
        }
      });
    });
  }
  initWaves();

  /* ------------------------------------------------------------ vidéo / sections visibles : pause hors écran */
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        entry.target.classList.toggle('is-offscreen', !entry.isIntersecting);
      });
    }, { rootMargin: '120px' });
    document.querySelectorAll('section, footer').forEach(function (s) { io.observe(s); });
  }
  document.addEventListener('visibilitychange', function () {
    document.documentElement.style.setProperty('--page-hidden', document.hidden ? '1' : '0');
  });

  /* ------------------------------------------------------------ footer : niveau d'eau + ondulations */
  const footerWord = document.getElementById('footer-word');
  if (footerWord) {
    const fill = footerWord.querySelector('.site-footer__word-fill');
    if (hasGSAP && !reduceMotion.matches) {
      const st = window.ScrollTrigger.create({
        trigger: footerWord,
        start: 'top bottom',
        end: 'bottom bottom',
        onUpdate: function (self) {
          const p = Math.max(0, Math.min(1, (self.progress - 0.05) / 0.75));
          const inset = 100 - p * 100;
          fill.style.clipPath = 'inset(0 0 ' + inset.toFixed(2) + '% 0)';
        }
      });
      if (!st) fill.style.clipPath = 'none';
    } else {
      fill.style.clipPath = 'none';
    }

    // ondulations depuis le curseur au survol du mot
    footerWord.addEventListener('pointermove', function (ev) {
      if (reduceMotion.matches) return;
      const rect = footerWord.getBoundingClientRect();
      const x = ((ev.clientX - rect.left) / rect.width) * 100;
      const y = ((ev.clientY - rect.top) / rect.height) * 100;
      footerWord.style.setProperty('--ripple-x', x.toFixed(1) + '%');
      footerWord.style.setProperty('--ripple-y', y.toFixed(1) + '%');
      if (!footerWord.querySelector('.footer-ripple')) {
        const rip = document.createElement('span');
        rip.className = 'footer-ripple';
        rip.setAttribute('aria-hidden', 'true');
        footerWord.appendChild(rip);
      }
      const rip = footerWord.querySelector('.footer-ripple');
      rip.style.left = x + '%';
      rip.style.top = y + '%';
      rip.classList.remove('is-live');
      void rip.offsetWidth;
      rip.classList.add('is-live');
    });
  }

  /* ------------------------------------------------------------ retour en haut */
  document.querySelectorAll('[data-to-top]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (lenis) lenis.scrollTo(0, { duration: 1.4 });
      else window.scrollTo({ top: 0, behavior: reduceMotion.matches ? 'auto' : 'smooth' });
    });
  });

  /* ------------------------------------------------------------ newsletter simulée */
  const news = document.getElementById('newsletter');
  if (news) {
    news.addEventListener('submit', function (ev) {
      ev.preventDefault();
      const input = news.querySelector('input[type="email"]');
      const msg = document.querySelector('[data-newsletter-msg]');
      const value = (input.value || '').trim();
      const valid = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value);
      if (!valid) {
        if (msg) { msg.textContent = App.t('footer.news_invalid'); msg.style.color = 'var(--limone)'; }
        input.focus();
        return;
      }
      if (msg) msg.textContent = App.t('footer.subscribed');
      App.toast(App.t('footer.subscribed'));
      news.reset();
    });
  }

  /* ------------------------------------------------------------ frieze de maiolique (injecté) */
  const frieze = document.querySelector('[data-frieze]');
  if (frieze) {
    const tiles = ['tile-1', 'tile-2', 'tile-3', 'tile-4', 'tile-5', 'tile-6'];
    const track = document.createElement('div');
    track.className = 'frieze__track';
    let html = '';
    for (let pass = 0; pass < 4; pass++) {
      tiles.forEach(function (tile) {
        html += '<svg class="frieze__tile" viewBox="0 0 100 100" aria-hidden="true"><use href="#i-' + tile + '"/></svg>';
      });
    }
    track.innerHTML = html;
    frieze.appendChild(track);
  }
})();
