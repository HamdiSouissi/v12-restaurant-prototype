/* =========================================================================
   checkout.js — 3 étapes (dati, pagamento, conferma), /api/order.php,
   biglietto di bordo perforé, tampon PAGATO, suivi gozzo en 4 ports.
   ========================================================================= */
(function () {
  'use strict';

  const App = window.LIMONAIA_APP || {};
  const DATA = window.LIMONAIA || {};
  const modal = document.getElementById('checkout');
  const form = document.getElementById('checkout-form');
  if (!modal || !form) return;

  const t = function (k, v) { return App.t ? App.t(k, v) : k; };
  const reduce = App.reduceMotion || window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const DELIVERY = DATA.delivery || { pickup_slots: 6, eta: '30–40 min', pickup_eta: '15–20 min' };

  let step = 1;
  let lastFocused = null;
  let lastOrder = null;
  let trackerTimer = null;

  const titleEl = document.getElementById('checkout-title');
  const dots = modal.querySelectorAll('[data-step-dot]');
  const steps = modal.querySelectorAll('.checkout__step');
  const nextBtn = modal.querySelector('[data-checkout-next]');
  const backBtn = modal.querySelector('[data-checkout-back]');
  const submitBtn = modal.querySelector('[data-checkout-submit]');
  const errorEl = modal.querySelector('[data-checkout-error]');
  const ticket = document.getElementById('ticket');
  const formWrap = form;

  /* ------------------------------------------------------------ étape visible */
  const TITLES = { 1: 'checkout.step1', 2: 'checkout.step2', 3: 'checkout.step3' };

  function paintSteps() {
    if (titleEl) titleEl.textContent = t(TITLES[step]);
    dots.forEach(function (dot) {
      const n = Number(dot.getAttribute('data-step-dot'));
      dot.classList.toggle('is-active', n === step);
      dot.classList.toggle('is-done', n < step);
    });
    steps.forEach(function (s) {
      s.classList.toggle('is-active', Number(s.getAttribute('data-step')) === step);
    });
    if (backBtn) backBtn.hidden = step === 1;
    if (nextBtn) nextBtn.hidden = step === 3;
    if (submitBtn) submitBtn.hidden = step !== 3;
  }

  /* ------------------------------------------------------------ mode conseil / asporto */
  function paintMode() {
    const mode = window.LIMONAIA_CART ? window.LIMONAIA_CART.mode() : 'delivery';
    modal.querySelectorAll('[data-delivery-only]').forEach(function (el) { el.hidden = mode !== 'delivery'; });
    modal.querySelectorAll('[data-pickup-only]').forEach(function (el) { el.hidden = mode === 'delivery'; });
    if (mode === 'pickup') renderPickupTimes();
  }

  function renderPickupTimes() {
    const wrap = modal.querySelector('[data-pickup-times]');
    if (!wrap || wrap.dataset.ready === '1') return;
    wrap.dataset.ready = '1';
    const now = new Date();
    let minutes = now.getHours() * 60 + now.getMinutes();
    minutes = Math.ceil((minutes + 20) / 15) * 15;
    let html = '<label><input type="radio" name="pickup" value="asap" checked><span>' + App.esc(t('checkout.asap')) + '</span></label>';
    for (let i = 0; i < (DELIVERY.pickup_slots || 6); i++) {
      const m = minutes + i * 15;
      const label = String(Math.floor(m / 60) % 24).padStart(2, '0') + ':' + String(m % 60).padStart(2, '0');
      html += '<label><input type="radio" name="pickup" value="' + label + '"><span>' + label + '</span></label>';
    }
    wrap.innerHTML = html;
  }

  /* ------------------------------------------------------------ récap */
  function paintRecap() {
    const cart = window.LIMONAIA_CART;
    if (!cart) return;
    const recap = modal.querySelector('[data-checkout-recap]');
    if (recap) {
      recap.innerHTML = cart.items().map(function (item) {
        return '<li><span>' + item.qty + ' × ' + App.esc(App.dishName(item.slug, item.name)) + '</span><span>' + App.esc(App.money(item.price * item.qty)) + '</span></li>';
      }).join('');
    }
    const setText = function (sel, value) {
      const el = modal.querySelector(sel);
      if (el) el.textContent = value;
    };
    setText('[data-checkout-subtotal]', App.money(cart.subtotal()));
    setText('[data-checkout-fee]', cart.fee() === 0 ? t('cart.free') : App.money(cart.fee()));
    setText('[data-checkout-total]', App.money(cart.total()));
  }

  /* ------------------------------------------------------------ ouverture */
  function open() {
    const cart = window.LIMONAIA_CART;
    if (!cart) return;
    if (cart.belowMinimum()) {
      App.toast(t('checkout.minimum_error'));
      return;
    }
    if (cart.count() === 0) {
      App.toast(t('cart.empty_title'));
      return;
    }
    lastFocused = document.activeElement;
    if (cart.close) cart.close();
    modal.hidden = false;
    requestAnimationFrame(function () { modal.classList.add('is-open'); });
    App.lockScroll(true);
    step = 1;
    ticket.hidden = true;
    formWrap.hidden = false;
    paintSteps();
    paintMode();
    paintRecap();
    showError('');
    window.setTimeout(function () {
      const field = form.querySelector('[name="name"]');
      if (field) field.focus();
    }, 260);
    document.addEventListener('keydown', onKeydown);
  }

  function close() {
    modal.classList.remove('is-open');
    App.lockScroll(false);
    document.removeEventListener('keydown', onKeydown);
    if (trackerTimer) { window.clearInterval(trackerTimer); trackerTimer = null; }
    window.setTimeout(function () {
      modal.hidden = true;
      if (lastFocused) lastFocused.focus();
    }, 280);
  }

  function onKeydown(ev) {
    if (ev.key === 'Escape') { ev.preventDefault(); close(); return; }
    App.trapFocus(modal, ev);
  }

  document.querySelectorAll('[data-checkout]').forEach(function (btn) {
    btn.addEventListener('click', open);
  });
  document.addEventListener('limonaia:checkout', open);
  modal.querySelectorAll('[data-close-checkout]').forEach(function (btn) {
    btn.addEventListener('click', close);
  });
  modal.addEventListener('click', function (ev) {
    if (ev.target === modal) close();
  });

  /* ------------------------------------------------------------ validation */
  function setError(field, message) {
    const wrap = field.closest('.field');
    if (!wrap) return;
    wrap.classList.toggle('is-invalid', Boolean(message));
    const slot = wrap.querySelector('[data-error-for="' + field.name + '"]');
    if (slot) slot.textContent = message || '';
  }

  function showError(message) {
    if (!errorEl) return;
    errorEl.hidden = !message;
    errorEl.textContent = message || '';
  }

  function validateStep1() {
    const mode = window.LIMONAIA_CART ? window.LIMONAIA_CART.mode() : 'delivery';
    const name = form.querySelector('[name="name"]');
    const phone = form.querySelector('[name="phone"]');
    const email = form.querySelector('[name="email"]');
    const address = form.querySelector('[name="address"]');

    const checks = [
      [name, name.value.trim().length >= 2 ? '' : t('checkout.required')],
      [phone, /^\+?[\d\s().-]{8,}$/.test(phone.value.trim()) ? '' : t('checkout.phone_invalid')],
      [email, /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email.value.trim()) ? '' : t('checkout.email_invalid')]
    ];
    if (mode === 'delivery') {
      checks.push([address, address.value.trim().length >= 5 ? '' : t('checkout.address_required')]);
    }

    let firstBad = null;
    checks.forEach(function (pair) {
      setError(pair[0], pair[1]);
      if (pair[1] && !firstBad) firstBad = pair[0];
    });
    if (firstBad) { firstBad.focus(); return false; }
    return true;
  }

  form.querySelectorAll('[data-validate]').forEach(function (input) {
    input.addEventListener('blur', function () {
      if (input.value.trim() === '') return; // pas d'agression à la sortie d'un champ vide
      const value = input.value.trim();
      if (input.dataset.validate === 'phone') {
        setError(input, /^\+?[\d\s().-]{8,}$/.test(value) ? '' : t('checkout.phone_invalid'));
      } else if (input.dataset.validate === 'email') {
        setError(input, /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value) ? '' : t('checkout.email_invalid'));
      } else if (input.dataset.validate === 'address') {
        setError(input, value.length >= 5 ? '' : t('checkout.address_required'));
      }
    });
  });

  /* ------------------------------------------------------------ navigation */
  nextBtn.addEventListener('click', function () {
    showError('');
    if (step === 1 && !validateStep1()) return;
    if (step < 3) {
      step += 1;
      paintSteps();
      if (step === 3) paintRecap();
      if (step === 2) {
        const firstPay = modal.querySelector('input[name="payment"]');
        if (firstPay) firstPay.focus();
      }
    }
  });

  if (backBtn) {
    backBtn.addEventListener('click', function () {
      showError('');
      if (step > 1) { step -= 1; paintSteps(); }
    });
  }

  dots.forEach(function (dot) {
    dot.addEventListener('click', function () {
      const n = Number(dot.getAttribute('data-step-dot'));
      if (n > step) return;
      step = n;
      paintSteps();
      if (step === 3) paintRecap();
    });
  });

  modal.querySelectorAll('input[name="payment"]').forEach(function (input) {
    input.addEventListener('change', function () {
      const cardFields = modal.querySelector('[data-card-fields]');
      if (cardFields) cardFields.style.display = input.value === 'carta' ? '' : 'none';
    });
  });

  /* ------------------------------------------------------------ envoi */
  form.addEventListener('submit', function (ev) {
    ev.preventDefault();
    showError('');
    if (!validateStep1()) { step = 1; paintSteps(); return; }

    const cart = window.LIMONAIA_CART;
    if (!cart) return;
    if (cart.belowMinimum()) { showError(t('checkout.minimum_error')); return; }

    const paymentInput = form.querySelector('input[name="payment"]:checked');
    const pickup = form.querySelector('input[name="pickup"]:checked');
    const payload = {
      mode: cart.mode(),
      payment: paymentInput ? paymentInput.value : 'carta',
      items: cart.items().map(function (i) { return { slug: i.slug, qty: i.qty }; }),
      customer: {
        name: form.querySelector('[name="name"]').value.trim(),
        phone: form.querySelector('[name="phone"]').value.trim(),
        email: form.querySelector('[name="email"]').value.trim(),
        address: (form.querySelector('[name="address"]') || {}).value || '',
        notes: (form.querySelector('[name="notes"]') || {}).value || '',
        pickup: pickup ? pickup.value : 'asap'
      }
    };

    submitBtn.disabled = true;
    nextBtn.disabled = true;
    submitBtn.textContent = '…';

    fetch('api/order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(function (r) { return r.json().then(function (body) { return { ok: r.ok, body: body }; }); })
      .then(function (res) {
        submitBtn.disabled = false;
        nextBtn.disabled = false;
        submitBtn.textContent = t('checkout.submit');
        const body = res.body || {};
        if (!body.ok) {
          if (body.errors) {
            const keys = Object.keys(body.errors);
            showError(body.errors[keys[0]]);
          } else {
            showError(t('checkout.error'));
          }
          return;
        }
        showTicket(body);
      })
      .catch(function () {
        submitBtn.disabled = false;
        nextBtn.disabled = false;
        submitBtn.textContent = t('checkout.submit');
        showError(t('checkout.error'));
      });
  });

  /* ------------------------------------------------------------ biglietto */
  function barcode(seed, target) {
    let s = 0;
    for (let i = 0; i < seed.length; i++) s = (s * 37 + seed.charCodeAt(i)) % 997;
    let x = 0;
    let rects = '';
    while (x < 100) {
      s = (s * 1103515245 + 12345) % 2147483648;
      const w = 0.6 + (s % 180) / 100;
      if ((s >> 4) % 2 === 0) rects += '<rect x="' + x.toFixed(1) + '" y="0" width="' + w.toFixed(1) + '" height="100%"/>';
      x += w + 0.7;
    }
    target.innerHTML = '<svg viewBox="0 0 100 40" preserveAspectRatio="none" aria-hidden="true">' +
      '<g fill="var(--notte)">' + rects + '</g></svg>';
  }

  function showTicket(order) {
    lastOrder = order;
    formWrap.hidden = true;
    ticket.hidden = false;

    const codeEl = ticket.querySelector('[data-ticket-code]');
    if (codeEl) codeEl.textContent = order.code;

    const itemsEl = ticket.querySelector('[data-ticket-items]');
    if (itemsEl) {
      itemsEl.innerHTML = order.items.map(function (item) {
        return '<li><span>' + item.qty + ' × ' + App.esc(item.name) + '</span><span>' + App.esc(App.money(item.line)) + '</span></li>';
      }).join('');
    }

    const totalsEl = ticket.querySelector('[data-ticket-totals]');
    if (totalsEl) {
      totalsEl.innerHTML =
        '<div><dt>' + App.esc(t('cart.subtotal')) + '</dt><dd>' + App.esc(App.money(order.totals.subtotal)) + '</dd></div>' +
        '<div><dt>' + App.esc(t('cart.fee')) + '</dt><dd>' + App.esc(order.totals.fee === 0 ? t('cart.free') : App.money(order.totals.fee)) + '</dd></div>' +
        '<div class="is-total"><dt>' + App.esc(t('cart.total')) + '</dt><dd>' + App.esc(App.money(order.totals.total)) + '</dd></div>';
    }

    const barcodeEl = ticket.querySelector('[data-ticket-barcode]');
    if (barcodeEl) barcode(order.code, barcodeEl);

    const etaEl = ticket.querySelector('[data-ticket-eta]');
    if (etaEl) etaEl.textContent = order.eta;

    // ports du suivi : « Pronto al molo » en asporto
    const ports = ticket.querySelectorAll('.tracker__ports li');
    if (ports.length === 4 && order.steps) {
      ports.forEach(function (li, i) { li.textContent = order.steps[i]; });
    }

    // vide le panier une fois la commande confirmée
    if (window.LIMONAIA_CART) window.LIMONAIA_CART.clear();

    // tampon + suivi
    if (!reduce && typeof window.gsap !== 'undefined') {
      const stamp = ticket.querySelector('.ticket__stamp');
      if (stamp) {
        gsap.fromTo(stamp, { scale: 1.9, opacity: 0, rotate: -28 }, { scale: 1, opacity: 0.9, rotate: -12, duration: 0.45, delay: 0.35, ease: 'back.out(2)' });
      }
    }
    runTracker();
    App.toast(order.code + ' · ' + order.message);
  }

  /* ------------------------------------------------------------ gozzo en 4 ports */
  function runTracker() {
    const boat = ticket.querySelector('[data-tracker-boat]');
    const ports = ticket.querySelectorAll('.tracker__ports li');
    if (!boat) return;
    if (trackerTimer) { window.clearInterval(trackerTimer); trackerTimer = null; }

    const positions = ['0%', '27%', '54%', '78%'];
    let stepIndex = 0;

    function paint() {
      boat.style.left = positions[stepIndex];
      ports.forEach(function (li, i) { li.classList.toggle('is-done', i <= stepIndex); });
    }
    paint();

    if (reduce) {
      stepIndex = 3;
      paint();
      return;
    }

    trackerTimer = window.setInterval(function () {
      if (!ticket.hidden && stepIndex < 3) {
        stepIndex += 1;
        paint();
      } else if (stepIndex >= 3) {
        window.clearInterval(trackerTimer);
        trackerTimer = null;
      }
    }, 1500);
  }

  ticket.querySelector('[data-new-order]').addEventListener('click', function () {
    if (trackerTimer) { window.clearInterval(trackerTimer); trackerTimer = null; }
    ticket.hidden = true;
    formWrap.hidden = false;
    form.reset();
    modal.querySelectorAll('.is-invalid').forEach(function (el) { el.classList.remove('is-invalid'); });
    step = 1;
    paintSteps();
    close();
  });

  /* ------------------------------------------------------------ langue */
  document.addEventListener('limonaia:lang', function () {
    paintSteps();
    paintRecap();
    const mode = window.LIMONAIA_CART ? window.LIMONAIA_CART.mode() : 'delivery';
    modal.querySelectorAll('[data-delivery-only]').forEach(function (el) { el.hidden = mode !== 'delivery'; });
  });

  // pré-remplissage du mode à l'ouverture du checkout
  document.addEventListener('limonaia:cart', function () {
    if (!modal.hidden) paintMode();
  });
})();
