/* =========================================================================
   booking.js — stepper de citrons, choix de la vue, calendrier de carreaux
   (coucher de soleil, lundi fermé, clavier), créneaux /api/slots.php,
   validation douce, /api/booking.php → cartolina + .ics + faux QR.
   ========================================================================= */
(function () {
  'use strict';

  const App = window.LIMONAIA_APP || {};
  const DATA = window.LIMONAIA || {};
  const section = document.getElementById('prenota');
  const form = document.getElementById('booking-form');
  if (!section || !form) return;

  const t = function (k, v) { return App.t ? App.t(k, v) : k; };
  const reduce = App.reduceMotion || window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const hasGSAP = typeof window.gsap !== 'undefined';
  const BOOKING = DATA.booking || { maxGuests: 8, sunset: {} };
  const SUNSET = BOOKING.sunset || {};
  const MAX = BOOKING.maxGuests || 8;

  const state = {
    guests: 2,
    view: 'terrazza',
    date: null,       // Date (local)
    time: null,
    sunset: null,
    slots: null,
    loading: false
  };

  /* ------------------------------------------------------------ dates utilitaires */
  const MONTHS = {
    it: ['gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre'],
    en: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
  };
  const WEEKDAYS = {
    it: ['lun', 'mar', 'mer', 'gio', 'ven', 'sab', 'dom'],
    en: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
  };
  const CLOSED_WEEKDAY = 1; // lundi

  function lang() { return window.I18N ? window.I18N.lang : 'it'; }
  function pad(n) { return String(n).padStart(2, '0'); }
  function iso(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }
  function startOfToday() { const d = new Date(); d.setHours(0, 0, 0, 0); return d; }
  function sunsetFor(date) { return SUNSET[date.getMonth() + 1] || '19:30'; }

  /* ------------------------------------------------------------ 1. couverts */
  const guestsWrap = section.querySelector('[data-guests]');
  const guestsCount = section.querySelector('[data-guests-count]');
  const guestsLemons = section.querySelector('[data-guests-lemons]');
  const guestsMany = section.querySelector('[data-guests-many]');

  function renderGuests() {
    if (guestsCount) guestsCount.textContent = state.guests >= MAX ? MAX + '+' : String(state.guests);
    if (guestsLemons) {
      const shown = Math.min(state.guests, MAX);
      let html = '';
      for (let i = 0; i < shown; i++) html += '<i style="animation-delay:' + (i * 45) + 'ms"></i>';
      guestsLemons.innerHTML = html;
    }
    if (guestsMany) guestsMany.hidden = state.guests < MAX;
  }

  section.querySelector('[data-guests-up]').addEventListener('click', function () {
    state.guests = Math.min(MAX, state.guests + 1);
    renderGuests();
    if (state.date) loadSlots();
  });
  section.querySelector('[data-guests-down]').addEventListener('click', function () {
    state.guests = Math.max(1, state.guests - 1);
    renderGuests();
    if (state.date) loadSlots();
  });

  /* ------------------------------------------------------------ 2. vue */
  section.querySelectorAll('input[name="view"]').forEach(function (input) {
    input.addEventListener('change', function () {
      if (input.checked) state.view = input.value;
      updateSummary();
    });
  });

  /* ------------------------------------------------------------ 3. calendrier de carreaux */
  const cal = section.querySelector('[data-calendar]');
  const calGrid = cal.querySelector('[data-cal-grid]');
  const calMonth = cal.querySelector('[data-cal-month]');
  const calWeekdays = cal.querySelector('.calendar__weekdays');
  let viewYear, viewMonth, focusedDay = null;

  function tileFor(day) {
    // un motif de carreau différent par jour — vraie variété, jamais le même pattern
    const n = ((day * 7 + viewMonth * 3) % 6) + 1;
    return 'tile-' + n;
  }

  function renderCalendar() {
    const today = startOfToday();
    const first = new Date(viewYear, viewMonth, 1);
    const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
    const offset = (first.getDay() + 6) % 7; // lundi = 0

    calMonth.textContent = MONTHS[lang()][viewMonth] + ' ' + viewYear;
    calWeekdays.innerHTML = WEEKDAYS[lang()].map(function (d) { return '<span>' + d + '</span>'; }).join('');

    let html = '';
    for (let i = 0; i < offset; i++) {
      html += '<span class="cal-day" data-empty="true" aria-hidden="true"></span>';
    }
    for (let day = 1; day <= daysInMonth; day++) {
      const date = new Date(viewYear, viewMonth, day);
      const closed = date.getDay() === CLOSED_WEEKDAY;
      const past = date < today;
      const selected = state.date && iso(date) === iso(state.date);
      const isToday = iso(date) === iso(today);
      const disabled = closed || past;
      const quiet = [0, 2, 4, 6].indexOf(date.getDay()) !== -1;
      html += '<button type="button" class="cal-day' +
        (closed ? ' is-closed' : '') +
        (selected ? ' is-selected' : '') +
        (isToday ? ' is-today' : '') +
        (quiet ? ' is-sunny' : '') +
        '"' +
        ' data-day="' + day + '"' +
        ' data-iso="' + iso(date) + '"' +
        ' role="gridcell"' +
        (disabled ? ' disabled aria-disabled="true"' : '') +
        ' tabindex="' + (selected || (!state.date && isToday && !disabled) ? '0' : '-1') + '"' +
        ' aria-label="' + date.getDate() + ' ' + MONTHS[lang()][viewMonth] + (closed ? ', ' + t('book.closed') : '') + (past ? ', ' + t('book.past') : '') + '">' +
        '<span class="cal-day__tile" aria-hidden="true"><svg viewBox="0 0 100 100"><use href="#i-' + tileFor(day) + '"/></svg></span>' +
        '<span class="cal-day__num">' + day + '</span>' +
        '<svg class="cal-day__sun" viewBox="0 0 100 100" aria-hidden="true"><use href="#i-sun-rays"/></svg>' +
        '</button>';
    }
    calGrid.innerHTML = html;

    // navigation mois : désactiver si le mois précédent est entièrement passé
    const prevBtn = cal.querySelector('[data-cal-prev]');
    const nowMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    if (prevBtn) {
      const isCurrentOrPast = new Date(viewYear, viewMonth, 1) <= nowMonth;
      prevBtn.disabled = isCurrentOrPast;
      prevBtn.style.opacity = isCurrentOrPast ? '.35' : '';
    }
  }

  function chooseDay(btn) {
    const date = new Date(btn.getAttribute('data-iso') + 'T00:00:00');
    state.date = date;
    state.sunset = sunsetFor(date);
    state.time = null;

    // flip 3D du carreau
    if (hasGSAP && !reduce) {
      gsap.fromTo(btn, { rotateY: 0 }, {
        rotateY: 96, duration: 0.24, ease: 'power2.in',
        onComplete: function () {
          btn.classList.add('is-selected');
          gsap.to(btn, { rotateY: 0, duration: 0.26, ease: 'power2.out' });
        }
      });
    }

    renderCalendar();
    loadSlots();
    updateSummary();
  }

  calGrid.addEventListener('click', function (ev) {
    const btn = ev.target.closest('.cal-day[data-iso]');
    if (!btn || btn.disabled) return;
    chooseDay(btn);
  });

  calGrid.addEventListener('keydown', function (ev) {
    const keys = ['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'];
    if (keys.indexOf(ev.key) === -1) return;
    const all = Array.prototype.slice.call(calGrid.querySelectorAll('.cal-day[data-iso]:not([disabled])'));
    const active = document.activeElement;
    let index = all.indexOf(active);
    if (index === -1) index = 0;
    let next = index;
    if (ev.key === 'ArrowLeft') next = Math.max(0, index - 1);
    if (ev.key === 'ArrowRight') next = Math.min(all.length - 1, index + 1);
    if (ev.key === 'ArrowUp') next = Math.max(0, index - 7);
    if (ev.key === 'ArrowDown') next = Math.min(all.length - 1, index + 7);
    if (ev.key === 'Home') next = 0;
    if (ev.key === 'End') next = all.length - 1;
    ev.preventDefault();
    if (all[next]) { all[next].tabIndex = 0; all[next].focus(); }
  });

  calGrid.addEventListener('keydown', function (ev) {
    if (ev.key === 'Enter' || ev.key === ' ') {
      const btn = ev.target.closest('.cal-day[data-iso]');
      if (btn && !btn.disabled) { ev.preventDefault(); chooseDay(btn); }
    }
  });

  cal.querySelector('[data-cal-prev]').addEventListener('click', function () {
    viewMonth -= 1;
    if (viewMonth < 0) { viewMonth = 11; viewYear -= 1; }
    renderCalendar();
  });
  cal.querySelector('[data-cal-next]').addEventListener('click', function () {
    viewMonth += 1;
    if (viewMonth > 11) { viewMonth = 0; viewYear += 1; }
    renderCalendar();
  });

  /* ------------------------------------------------------------ 4. créneaux */
  const slotsEl = section.querySelector('[data-slots]');

  function renderSlots(data) {
    if (!slotsEl) return;
    if (!data || !data.groups || !data.groups.length) {
      slotsEl.innerHTML = '<p class="slots__placeholder">' + App.esc(t('book.summary_empty')) + '</p>';
      return;
    }
    const labels = { pranzo: 'book.lunch', aperitivo: 'book.aperitivo', cena: 'book.dinner' };
    let html = '';
    data.groups.forEach(function (group) {
      const featured = group.key === 'aperitivo';
      html += '<div class="slots__group' + (featured ? ' slots__group--featured' : '') + '">' +
        '<p class="slots__label">' +
          (featured ? '<svg class="slots__sun" viewBox="0 0 100 100" aria-hidden="true"><use href="#i-sun-rays"/></svg>' : '') +
          App.esc(t(labels[group.key] || 'book.lunch')) +
          (featured ? '<span class="slots__sunset">' + App.esc(t('book.sunset')) + ' ' + App.esc(data.sunset) + '</span>' : '') +
        '</p><div class="slots__row">';

      group.slots.forEach(function (slot) {
        const disabled = slot.status === 'full';
        const tag = slot.status === 'full' ? t('book.full') : (slot.status === 'last' ? t('book.last') : '');
        html += '<button type="button" class="slot"' +
          ' data-time="' + App.esc(slot.time) + '"' +
          ' aria-pressed="' + (state.time === slot.time ? 'true' : 'false') + '"' +
          (disabled ? ' disabled' : '') +
          '>' + App.esc(slot.time) +
          (tag ? '<span class="slot__tag">' + App.esc(tag) + '</span>' : '') +
          '</button>';
      });
      html += '</div></div>';
    });
    slotsEl.innerHTML = html;
  }

  slotsEl.addEventListener('click', function (ev) {
    const btn = ev.target.closest('.slot[data-time]');
    if (!btn || btn.disabled) return;
    state.time = btn.getAttribute('data-time');
    slotsEl.querySelectorAll('.slot').forEach(function (b) {
      b.setAttribute('aria-pressed', String(b === btn));
    });
    updateSummary();
  });

  function loadSlots() {
    if (!state.date || !slotsEl) return;
    const dateStr = iso(state.date);
    state.loading = true;
    slotsEl.setAttribute('aria-busy', 'true');
    slotsEl.innerHTML = '<p class="slots__placeholder">' + App.esc(t('book.time')) + '…</p>';

    fetch('api/slots.php?date=' + encodeURIComponent(dateStr) + '&guests=' + state.guests, {
      headers: { Accept: 'application/json' }
    })
      .then(function (r) { return r.json().then(function (body) { return { ok: r.ok, body: body }; }); })
      .then(function (res) {
        state.loading = false;
        slotsEl.removeAttribute('aria-busy');
        if (!res.body || res.body.ok === false) {
          slotsEl.innerHTML = '<p class="slots__placeholder">' + App.esc((res.body && res.body.error && res.body.error.message) || t('checkout.error')) + '</p>';
          return;
        }
        state.slots = res.body;
        renderSlots(res.body);
      })
      .catch(function () {
        state.loading = false;
        slotsEl.removeAttribute('aria-busy');
        slotsEl.innerHTML = '<p class="slots__placeholder">' + App.esc(t('checkout.error')) + '</p>';
      });
  }

  /* ------------------------------------------------------------ récap dynamique */
  const summaryEl = section.querySelector('[data-summary-text]');
  function viewLabel() {
    const views = (BOOKING.views || []).filter(function (v) { return v.key === state.view; });
    return views.length ? App.bi(views[0].name) : state.view;
  }

  function updateSummary() {
    if (!summaryEl) return;
    if (!state.date || !state.time) {
      summaryEl.textContent = t('book.summary_empty');
      return;
    }
    const d = state.date;
    const parts = [
      WEEKDAYS[lang()][(d.getDay() + 6) % 7].toUpperCase() + ' ' + d.getDate() + ' ' + MONTHS[lang()][d.getMonth()].slice(0, 3).toUpperCase(),
      state.time,
      state.guests + ' ' + t('book.persone').toUpperCase(),
      viewLabel().toUpperCase()
    ];
    summaryEl.textContent = parts.join(' · ');
  }

  /* ------------------------------------------------------------ validation douce */
  function setError(field, message) {
    const wrap = field.closest('.field') || field.closest('.check');
    if (!wrap) return;
    wrap.classList.toggle('is-invalid', Boolean(message));
    const slot = wrap.querySelector('[data-error-for="' + field.name + '"]');
    if (slot) slot.textContent = message || '';
  }

  function validators() {
    const name = form.querySelector('[name="name"]');
    const phone = form.querySelector('[name="phone"]');
    const email = form.querySelector('[name="email"]');
    const consent = form.querySelector('[name="consent"]');

    const rules = {
      name: function () { return name.value.trim().length >= 2 ? '' : t('book.required'); },
      phone: function () {
        const digits = phone.value.replace(/\D/g, '');
        return digits.length >= 8 && digits.length <= 15 ? '' : t('book.phone_invalid');
      },
      email: function () { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email.value.trim()) ? '' : t('book.email_invalid'); },
      consent: function () { return consent.checked ? '' : t('book.consent_required'); }
    };

    [name, phone, email].forEach(function (input) {
      input.addEventListener('blur', function () {
        const msg = rules[input.name]();
        if (input.value.trim() !== '' || input.dataset.touched) {
          input.dataset.touched = '1';
          setError(input, msg);
        }
      });
      input.addEventListener('input', function () {
        if (input.closest('.field.is-invalid')) setError(input, rules[input.name]());
      });
    });
    consent.addEventListener('change', function () { setError(consent, rules.consent()); });
    return rules;
  }
  const rules = validators();

  /* ------------------------------------------------------------ faux QR */
  function drawQr(target, seed) {
    if (!target) return;
    let s = 0;
    for (let i = 0; i < seed.length; i++) s = (s * 31 + seed.charCodeAt(i)) % 9973;
    function rnd() { s = (s * 1103515245 + 12345) % 2147483648; return s / 2147483648; }
    const size = 21;
    let rects = '';
    for (let y = 0; y < size; y++) {
      for (let x = 0; x < size; x++) {
        const finder = (x < 7 && y < 7) || (x > size - 8 && y < 7) || (x < 7 && y > size - 8);
        if (finder) continue;
        if (rnd() > 0.52) rects += '<rect x="' + x + '" y="' + y + '" width="1" height="1"/>';
      }
    }
    target.innerHTML = '<svg viewBox="0 0 ' + size + ' ' + size + '" shape-rendering="crispEdges" role="img" aria-label="QR">' +
      '<rect width="' + size + '" height="' + size + '" fill="var(--calce)"/>' +
      '<g fill="var(--notte)">' + rects + '</g>' +
      '<g fill="none" stroke="var(--notte)" stroke-width="1">' +
        '<rect x="0.5" y="0.5" width="6" height="6"/><rect x="2.5" y="2.5" width="2" height="2" fill="var(--notte)" stroke="none"/>' +
        '<rect x="' + (size - 7) + '.5" y="0.5" width="6" height="6"/><rect x="' + (size - 5) + '.5" y="2.5" width="2" height="2" fill="var(--notte)" stroke="none"/>' +
        '<rect x="0.5" y="' + (size - 7) + '.5" width="6" height="6"/><rect x="2.5" y="' + (size - 5) + '.5" width="2" height="2" fill="var(--notte)" stroke="none"/>' +
      '</g></svg>';
  }

  /* ------------------------------------------------------------ envoi */
  const submitBtn = document.getElementById('booking-submit');
  const errorEl = section.querySelector('[data-booking-error]');
  const postcard = document.getElementById('booking-postcard');

  function showError(message) {
    if (!errorEl) return;
    errorEl.hidden = !message;
    errorEl.textContent = message || '';
  }

  form.addEventListener('submit', function (ev) {
    ev.preventDefault();
    showError('');

    let firstInvalid = null;
    ['name', 'phone', 'email', 'consent'].forEach(function (key) {
      const msg = rules[key]();
      const field = form.querySelector('[name="' + key + '"]');
      setError(field, msg);
      if (msg && !firstInvalid) firstInvalid = field;
    });

    if (!state.date) { showError(t('book.summary_empty')); if (!firstInvalid) firstInvalid = cal; }
    else if (!state.time) { showError(t('book.slot_required')); if (!firstInvalid) firstInvalid = slotsEl; }

    if (firstInvalid) {
      if (firstInvalid.focus) firstInvalid.focus();
      return;
    }

    const payload = {
      name: form.querySelector('[name="name"]').value.trim(),
      phone: form.querySelector('[name="phone"]').value.trim(),
      email: form.querySelector('[name="email"]').value.trim(),
      occasion: form.querySelector('[name="occasion"]').value,
      notes: form.querySelector('[name="notes"]').value.trim(),
      consent: form.querySelector('[name="consent"]').checked,
      guests: state.guests,
      view: state.view,
      date: iso(state.date),
      time: state.time
    };

    submitBtn.disabled = true;
    submitBtn.style.opacity = '.6';
    const original = submitBtn.textContent;
    submitBtn.textContent = '…';

    fetch('api/booking.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(function (r) { return r.json().then(function (body) { return { ok: r.ok, body: body }; }); })
      .then(function (res) {
        submitBtn.disabled = false;
        submitBtn.style.opacity = '';
        submitBtn.textContent = original;

        const body = res.body || {};
        if (!body.ok) {
          if (body.errors) {
            Object.keys(body.errors).forEach(function (field) {
              const input = form.querySelector('[name="' + field + '"]');
              if (input) setError(input, body.errors[field]);
            });
            const firstKey = Object.keys(body.errors)[0];
            showError(body.errors[firstKey]);
          } else {
            showError(t('book.error'));
          }
          return;
        }
        showPostcard(body);
      })
      .catch(function () {
        submitBtn.disabled = false;
        submitBtn.style.opacity = '';
        submitBtn.textContent = original;
        showError(t('book.error'));
      });
  });

  /* ------------------------------------------------------------ cartolina */
  function showPostcard(body) {
    if (!postcard) return;
    const b = body.booking;
    postcard.hidden = false;
    postcard.querySelector('[data-booking-code]').textContent = body.code;

    const recap = postcard.querySelector('[data-booking-recap]');
    const dayLabel = b.day + ' ' + MONTHS[lang()][b.month - 1];
    recap.innerHTML = '' +
      '<div><dt>' + App.esc(dayLabel) + '</dt><dd>' + App.esc(b.time) + '</dd></div>' +
      '<div><dt>' + App.esc(t('book.guests')) + '</dt><dd>' + b.guests + ' ' + App.esc(t('book.persone')) + '</dd></div>' +
      '<div><dt>' + App.esc(t('book.where')) + '</dt><dd>' + App.esc(App.bi((BOOKING.views || []).filter(function (v) { return v.key === b.view; }).map(function (v) { return v.name; })[0] || b.view_it)) + '</dd></div>' +
      '<div><dt>' + App.esc(t('book.sunset')) + '</dt><dd>' + App.esc(b.sunset) + '</dd></div>';

    drawQr(postcard.querySelector('[data-booking-qr]'), body.code + b.date + b.time);

    // .ics réel
    const icsBtn = postcard.querySelector('[data-ics]');
    if (icsBtn && b.ics) {
      const blob = new Blob([b.ics], { type: 'text/calendar;charset=utf-8' });
      const url = URL.createObjectURL(blob);
      icsBtn.href = url;
      icsBtn.setAttribute('download', 'limonaia-' + body.code + '.ics');
    }

    App.toast(t('book.done_title'));
    postcard.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'center' });

    if (hasGSAP && !reduce) {
      gsap.fromTo(postcard, { rotateY: -18, opacity: 0 }, { rotateY: 0, opacity: 1, duration: 0.85, ease: 'power3.out' });
    }
  }

  section.querySelector('[data-book-again]').addEventListener('click', function () {
    const postcardEl = document.getElementById('booking-postcard');
    if (postcardEl) postcardEl.hidden = true;
    form.reset();
    state.time = null;
    state.date = null;
    state.guests = 2;
    renderGuests();
    renderCalendar();
    if (slotsEl) slotsEl.innerHTML = '<p class="slots__placeholder">' + App.esc(t('book.summary_empty')) + '</p>';
    updateSummary();
    showError('');
    form.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
    App.scrollTo(section);
  });

  /* ------------------------------------------------------------ réactions à la langue */
  document.addEventListener('limonaia:lang', function () {
    renderCalendar();
    renderGuests();
    updateSummary();
    if (state.slots) renderSlots(state.slots);
  });

  /* ------------------------------------------------------------ init */
  const today = startOfToday();
  viewYear = today.getFullYear();
  viewMonth = today.getMonth();
  renderCalendar();
  renderGuests();
  updateSummary();
})();
