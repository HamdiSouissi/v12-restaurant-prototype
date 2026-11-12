/* =========================================================================
   i18n.js — bascule IT / EN sans rechargement.
   Source unique : window.LIMONAIA.i18n (config.php).
   Expose window.I18N : t(), bi(), lang, onChange()
   ========================================================================= */
(function () {
  'use strict';

  const DATA = window.LIMONAIA || {};
  const DICT = DATA.i18n || { it: {}, en: {} };
  const STORAGE = 'limonaia:lang';
  const listeners = [];

  function detect() {
    try {
      const saved = localStorage.getItem(STORAGE);
      if (saved === 'it' || saved === 'en') return saved;
    } catch (e) { /* mode privé */ }
    return 'it'; // italien par défaut, toujours
  }

  const state = { lang: detect() };

  /** t('cart.title') → texte brut (jamais injecté en innerHTML). */
  function t(key, vars) {
    const dict = DICT[state.lang] || DICT.it;
    let str = dict[key];
    if (str == null) str = (DICT.it || {})[key];
    if (str == null) return key;
    if (vars) {
      Object.keys(vars).forEach(function (k) {
        str = str.replace(new RegExp('\\{' + k + '\\}', 'g'), vars[k]);
      });
    }
    return str;
  }

  /** Valeur bilingue d'un champ config : {it, en} */
  function bi(field) {
    if (!field) return '';
    if (typeof field === 'string') return field;
    return field[state.lang] || field.it || '';
  }

  /** Applique la langue au document + aux nœuds data-i18n. */
  function apply(lang) {
    state.lang = lang === 'en' ? 'en' : 'it';
    document.documentElement.lang = state.lang;
    document.documentElement.setAttribute('data-lang', state.lang);

    document.querySelectorAll('[data-i18n]').forEach(function (el) {
      el.textContent = t(el.getAttribute('data-i18n'));
    });
    document.querySelectorAll('[data-i18n-html]').forEach(function (el) {
      el.innerHTML = '';
      el.textContent = t(el.getAttribute('data-i18n-html'));
    });
    document.querySelectorAll('[data-i18n-placeholder]').forEach(function (el) {
      el.setAttribute('placeholder', t(el.getAttribute('data-i18n-placeholder')));
    });
    document.querySelectorAll('[data-i18n-label]').forEach(function (el) {
      el.setAttribute('aria-label', t(el.getAttribute('data-i18n-label')));
    });

    document.querySelectorAll('[data-lang]').forEach(function (btn) {
      const active = btn.getAttribute('data-lang') === state.lang;
      btn.classList.toggle('is-active', active);
      btn.setAttribute('aria-pressed', String(active));
    });

    try { localStorage.setItem(STORAGE, state.lang); } catch (e) { /* noop */ }

    listeners.forEach(function (fn) {
      try { fn(state.lang); } catch (e) { console.warn('i18n listener', e); }
    });
    document.dispatchEvent(new CustomEvent('limonaia:lang', { detail: { lang: state.lang } }));
  }

  function onChange(fn) {
    if (typeof fn === 'function') listeners.push(fn);
  }

  /** Nom d'un plat dans la langue courante, depuis window.LIMONAIA.menu. */
  const MENU_DATA = DATA.menu || {};
  function dishName(slug) {
    for (const key in MENU_DATA) {
      if (!Object.prototype.hasOwnProperty.call(MENU_DATA, key)) continue;
      const items = MENU_DATA[key].items || [];
      for (let i = 0; i < items.length; i++) {
        if (items[i].slug === slug) {
          const n = items[i].name || {};
          return n[state.lang] || n.it || '';
        }
      }
    }
    return '';
  }

  /** Formate un prix : « € 16 » */
  function price(value) {
    return '€ ' + Number(value).toLocaleString(state.lang === 'en' ? 'en-GB' : 'it-IT', {
      minimumFractionDigits: 0, maximumFractionDigits: 2
    });
  }

  document.querySelectorAll('[data-lang]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const next = btn.getAttribute('data-lang');
      if (next !== state.lang) apply(next);
    });
  });

  window.I18N = { t: t, bi: bi, price: price, dishName: dishName, apply: apply, onChange: onChange };
  Object.defineProperty(window.I18N, 'lang', { get: function () { return state.lang; } });
  Object.defineProperty(window.I18N, 'dict', { get: function () { return DICT; } });

  // Langue détectée avant même le DOMContentLoaded du reste
  document.documentElement.lang = state.lang;
  document.documentElement.setAttribute('data-lang', state.lang);
})();
