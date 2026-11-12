/* =========================================================================
   giro.js — sélection des 4 étapes, gozzo qui navigue le long de la route,
   prix en direct (barré € 45 → € 38), CTA bloquée avec « Manca ancora… ».
   ========================================================================= */
(function () {
  'use strict';

  const App = window.LIMONAIA_APP || {};
  const section = document.getElementById('giro');
  if (!section) return;

  const config = (window.LIMONAIA && window.LIMONAIA.giro) || null;
  if (!config) return;

  const hasGSAP = typeof window.gsap !== 'undefined';
  const reduce = App.reduceMotion || window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const t = function (k, v) { return App.t ? App.t(k, v) : k; };

  const boat = document.getElementById('giro-boat');
  const cta = document.getElementById('giro-cta');
  const missingEl = section.querySelector('[data-giro-missing]');
  const routeList = document.getElementById('giro-route-list');

  /* positions des étapes dans le viewBox 1000×520 */
  const stops = {};
  config.stops.forEach(function (stop) {
    stops[stop.key] = { x: stop.x * 10, y: stop.y * 5.2, el: section.querySelector('[data-stop="' + stop.key + '"]'), name: stop.name, course: stop.course };
  });

  const chosen = {}; // key -> {slug, name, price}

  function stopKeys() { return config.stops.map(function (s) { return s.key; }); }

  /* ------------------------------------------------------------ gozzo */
  function sailTo(key, instant) {
    const target = stops[key];
    if (!boat || !target) return;
    if (!hasGSAP || reduce || instant) {
      boat.setAttribute('transform', 'translate(' + target.x + ' ' + target.y + ')');
      return;
    }
    gsap.killTweensOf(boat);
    const state = { x: 0, y: 0, rot: -2 };
    const current = boat.getAttribute('transform');
    const m = /translate\(\s*([-\d.]+)[ ,]+([-\d.]+)/.exec(current || '');
    if (m) { state.x = parseFloat(m[1]); state.y = parseFloat(m[2]); }

    gsap.to(state, {
      x: target.x,
      y: target.y,
      duration: 1.35,
      ease: 'power2.inOut',
      onUpdate: function () {
        // petit roulis de bateau
        const rot = Math.sin(state.x / 26) * 3.4;
        boat.setAttribute('transform', 'translate(' + state.x.toFixed(1) + ' ' + state.y.toFixed(1) + ') rotate(' + rot.toFixed(1) + ' 0 0)');
      }
    });
  }

  /* ------------------------------------------------------------ état visuel */
  function paint() {
    const keys = stopKeys();
    let doneCount = 0;
    let lastDone = null;

    keys.forEach(function (key, i) {
      const stop = stops[key];
      const isDone = Boolean(chosen[key]);
      if (isDone) { doneCount++; lastDone = key; }
      if (!stop.el) return;
      stop.el.classList.toggle('is-done', isDone);
      stop.el.classList.toggle('is-active', isDone && key === lastDone);
      stop.el.setAttribute('opacity', isDone ? '1' : '1');

      const step = section.querySelector('[data-step="' + key + '"]');
      if (step) {
        step.classList.toggle('is-done', isDone);
        step.classList.toggle('is-active', isDone && key === lastDone);
      }

      const route = routeList ? routeList.querySelector('[data-route="' + key + '"]') : null;
      if (route) {
        route.classList.toggle('is-done', isDone);
        const dish = route.querySelector('[data-route-dish]');
        if (dish) dish.textContent = isDone ? chosen[key].name : '—';
      }
    });

    /* bateau : va à la dernière étape choisie, sinon au large */
    if (lastDone) sailTo(lastDone);
    else sailTo('positano', true);

    /* CTA */
    if (cta) {
      const missing = config.stops.filter(function (s) { return !chosen[s.key]; });
      const complete = missing.length === 0;
      cta.disabled = !complete;
      if (missingEl) {
        if (complete) {
          missingEl.textContent = '';
          missingEl.hidden = true;
        } else {
          missingEl.hidden = false;
          const first = missing[0];
          missingEl.textContent = t('giro.missing') + ' ' + App.bi(first.course);
        }
      }
      cta.classList.toggle('is-ready', complete);
    }

    const priceEl = section.querySelector('[data-giro-price]');
    if (priceEl) priceEl.textContent = String(config.bundle_price);
  }

  /* ------------------------------------------------------------ sélection */
  section.querySelectorAll('[data-giro-pick]').forEach(function (input) {
    input.addEventListener('change', function () {
      const key = input.getAttribute('data-stop');
      if (!input.checked) return;
      chosen[key] = {
        slug: input.value,
        name: input.getAttribute('data-opt-name'),
        price: Number(input.getAttribute('data-opt-price')) || 0
      };
      paint();
      if (hasGSAP && !reduce) {
        const stopEl = stops[key] && stops[key].el;
        const halo = stopEl && stopEl.querySelector('.giro__halo');
        if (halo) {
          gsap.fromTo(halo, { attr: { r: 16 }, opacity: 0.7 }, { attr: { r: 42 }, opacity: 0, duration: 0.75, ease: 'power2.out' });
        }
        const step = section.querySelector('[data-step="' + key + '"]');
        if (step) gsap.fromTo(step, { y: 0 }, { y: -4, duration: 0.22, yoyo: true, repeat: 1, ease: 'power2.out' });
      }
    });
  });

  /* ------------------------------------------------------------ ajout au panier (1 seul article) */
  if (cta) {
    cta.addEventListener('click', function () {
      if (cta.disabled) return;
      if (!window.LIMONAIA_CART) return;
      const picks = config.stops.map(function (s) {
        return chosen[s.key] ? chosen[s.key].name : '';
      }).filter(Boolean);

      window.LIMONAIA_CART.add({
        slug: 'giro-della-costiera',
        name: 'Il Giro della Costiera',
        price: config.bundle_price,
        single: true,
        meta: picks.join(' · ')
      });

      App.toast(t('giro.added'));
      cta.disabled = true;
      if (missingEl) { missingEl.hidden = true; }
      // remise à zéro : on peut composer une nouvelle route
      window.setTimeout(function () {
        section.querySelectorAll('[data-giro-pick]').forEach(function (i) { i.checked = false; });
        Object.keys(chosen).forEach(function (k) { delete chosen[k]; });
        paint();
      }, 700);
    });
  }

  /* ------------------------------------------------------------ traduction des arrêts */
  function relabel() {
    routeList && routeList.querySelectorAll('[data-route]').forEach(function (li) {
      const key = li.getAttribute('data-route');
      const stop = config.stops.filter(function (s) { return s.key === key; })[0];
      if (!stop) return;
      const em = li.querySelector('.giro__route-stop em');
      if (em) em.textContent = '(' + App.bi(stop.course) + ')';
      const dish = li.querySelector('[data-route-dish]');
      if (dish && chosen[key]) {
        chosen[key].name = App.dishName(chosen[key].slug, chosen[key].name);
        dish.textContent = chosen[key].name;
      }
    });
    paint();
  }
  document.addEventListener('limonaia:lang', relabel);

  paint();
  // bateau posé au départ dès l'apparition de la section
  window.setTimeout(function () { if (!Object.keys(chosen).length) sailTo('positano', true); }, 60);

  // navigation douce du bateau quand la section entre à l'écran
  if (hasGSAP && !reduce && window.ScrollTrigger) {
    window.ScrollTrigger.create({
      trigger: section,
      start: 'top 65%',
      once: true,
      onEnter: function () {
        if (!Object.keys(chosen).length && boat) {
          gsap.fromTo(boat, { y: -6 }, { y: 0, duration: 1.6, ease: 'sine.inOut', yoyo: true, repeat: 5 });
        }
      }
    });
  }
})();
