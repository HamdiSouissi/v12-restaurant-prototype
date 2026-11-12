/* =========================================================================
   gallery.js — 3 colonnes en parallaxe, curseur GUARDA, lightbox Flip
   accessible (flèches clavier, swipe tactile, compteur, focus piégé).
   ========================================================================= */
(function () {
  'use strict';

  const App = window.LIMONAIA_APP || {};
  const DATA = window.LIMONAIA || {};
  const section = document.getElementById('galleria');
  const lightbox = document.getElementById('lightbox');
  if (!section || !lightbox) return;

  const PHOTOS = (DATA.gallery || []).length ? DATA.gallery : [];
  const hasGSAP = typeof window.gsap !== 'undefined';
  const reduce = App.reduceMotion || window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ------------------------------------------------------------ parallaxe des colonnes */
  if (hasGSAP && !reduce && window.ScrollTrigger && window.innerWidth > 700) {
    const cols = section.querySelectorAll('[data-gallery-col]');
    cols.forEach(function (col, i) {
      const speed = [0.9, -0.55, 1.25][i % 3];
      gsap.to(col, {
        y: function () { return (window.innerHeight * 0.12) * speed; },
        ease: 'none',
        scrollTrigger: {
          trigger: section,
          start: 'top bottom',
          end: 'bottom top',
          scrub: 0.6
        }
      });
    });
  }

  /* ------------------------------------------------------------ lightbox */
  const media = lightbox.querySelector('[data-lightbox-media]');
  const caption = lightbox.querySelector('[data-lightbox-caption]');
  const counter = lightbox.querySelector('[data-lightbox-count]');
  const buttons = Array.prototype.slice.call(section.querySelectorAll('[data-lightbox]'));

  let index = 0;
  let lastFocused = null;

  function photoData(i) {
    // Les métadonnées complètes sont dans le DOM des vignettes
    const btn = buttons[i];
    if (!btn) return null;
    const img = btn.querySelector('img');
    return {
      src: img ? img.getAttribute('src') : '',
      webp: img ? (btn.querySelector('source') ? btn.querySelector('source').getAttribute('srcset') : '') : '',
      alt: img ? img.getAttribute('alt') : '',
      caption: btn.getAttribute('aria-label') || (img ? img.getAttribute('alt') : ''),
      shape: btn.closest('.gallery__item') ? btn.closest('.gallery__item').className : ''
    };
  }

  function render(i) {
    const data = photoData(i);
    if (!data) return;
    const fallback = data.alt || '';
    media.innerHTML = '<picture>' +
      (data.webp ? '<source srcset="' + data.webp + '" type="image/webp">' : '') +
      '<img src="' + data.src + '" alt="' + App.esc(fallback) + '" width="1200" height="1500">' +
      '</picture>';
    const img = media.querySelector('img');
    img.addEventListener('error', function () {
      media.innerHTML = '<div class="lightbox__ph">' + App.esc(fallback) + '</div>';
    }, { once: true });
    caption.textContent = data.caption.replace(/\s+—\s+.*$/, '');
    counter.textContent = pad(i + 1) + ' / ' + pad(buttons.length);
  }

  function pad(n) { return String(n).padStart(2, '0'); }

  function open(i, trigger) {
    index = i;
    lastFocused = trigger || document.activeElement;

    const sourceImg = trigger ? trigger.querySelector('img') : null;
    const state = sourceImg && hasGSAP && !reduce ? window.Flip.getState(sourceImg, 'scale') : null;

    render(i);
    lightbox.hidden = false;
    lightbox.classList.remove('is-open');
    App.lockScroll(true);
    requestAnimationFrame(function () { lightbox.classList.add('is-open'); });

    if (state && media.querySelector('img')) {
      window.Flip.from(state, {
        duration: 0.6,
        ease: 'power2.out',
        absolute: true,
        scale: true,
        onComplete: function () { lightbox.classList.add('is-open'); }
      });
    } else if (hasGSAP && !reduce) {
      gsap.fromTo(lightbox.querySelector('.lightbox__figure'), { opacity: 0, y: 16 }, { opacity: 1, y: 0, duration: 0.4, ease: 'power2.out' });
    }

    const closeBtn = lightbox.querySelector('[data-lightbox-close]');
    window.setTimeout(function () { if (closeBtn) closeBtn.focus(); }, 80);
    document.addEventListener('keydown', onKeydown);
  }

  function close() {
    lightbox.classList.remove('is-open');
    App.lockScroll(false);
    document.removeEventListener('keydown', onKeydown);
    window.setTimeout(function () {
      lightbox.hidden = true;
      media.innerHTML = '';
      if (lastFocused) lastFocused.focus();
    }, 260);
  }

  function step(dir) {
    index = (index + dir + buttons.length) % buttons.length;
    if (hasGSAP && !reduce) {
      gsap.fromTo(media, { opacity: 0, x: dir * 26 }, { opacity: 1, x: 0, duration: 0.36, ease: 'power2.out' });
    }
    render(index);
  }

  function onKeydown(ev) {
    if (ev.key === 'Escape') { ev.preventDefault(); close(); return; }
    if (ev.key === 'ArrowRight') { ev.preventDefault(); step(1); return; }
    if (ev.key === 'ArrowLeft') { ev.preventDefault(); step(-1); return; }
    App.trapFocus(lightbox, ev);
  }

  buttons.forEach(function (btn, i) {
    btn.addEventListener('click', function () { open(i, btn); });
  });

  lightbox.querySelector('[data-lightbox-close]').addEventListener('click', close);
  lightbox.querySelector('[data-lightbox-prev]').addEventListener('click', function () { step(-1); });
  lightbox.querySelector('[data-lightbox-next]').addEventListener('click', function () { step(1); });

  lightbox.addEventListener('click', function (ev) {
    if (ev.target === lightbox || ev.target.classList.contains('lightbox__figure')) close();
  });

  /* balayage tactile */
  let touchStartX = null;
  let touchStartY = null;
  lightbox.addEventListener('touchstart', function (ev) {
    touchStartX = ev.touches[0].clientX;
    touchStartY = ev.touches[0].clientY;
  }, { passive: true });
  lightbox.addEventListener('touchend', function (ev) {
    if (touchStartX === null) return;
    const dx = ev.changedTouches[0].clientX - touchStartX;
    const dy = ev.changedTouches[0].clientY - touchStartY;
    if (Math.abs(dx) > 46 && Math.abs(dx) > Math.abs(dy)) step(dx < 0 ? 1 : -1);
    touchStartX = null;
  }, { passive: true });

  /* images manquantes en galerie */
  section.querySelectorAll('.gallery__btn img').forEach(function (img) {
    img.addEventListener('error', function () {
      const btn = img.closest('.gallery__btn');
      if (!btn) return;
      const label = img.getAttribute('alt') || '';
      btn.innerHTML = '<div class="img-fallback" style="min-height:220px;border:0;border-radius:inherit"><span>' + App.esc(label) + '</span></div>';
    }, { once: true });
  });

  /* compteur initial */
  if (counter) counter.textContent = '01 / ' + pad(Math.max(buttons.length, 1));
})();
