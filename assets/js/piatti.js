/* =========================================================================
   piatti.js — carrousel Swiper 11 centré, cartes sur une ligne ondulée,
   carte centrale agrandie, navigation flèches + glisser + clavier.
   ========================================================================= */
(function () {
  'use strict';

  const App = window.LIMONAIA_APP || {};
  const root = document.getElementById('piatti-swiper');
  if (!root || typeof window.Swiper === 'undefined') return;

  const reduce = App.reduceMotion || window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const pagination = document.querySelector('[data-piatti-pagination]');

  const swiper = new window.Swiper(root, {
    slidesPerView: 'auto',
    centeredSlides: true,
    spaceBetween: 22,
    loop: true,
    grabCursor: true,
    speed: reduce ? 0 : 680,
    keyboard: { enabled: true, onlyInViewport: true },
    autoplay: false,
    pagination: {
      el: pagination,
      clickable: true,
      bulletClass: 'dot-btn',
      bulletActiveClass: 'is-active',
      renderBullet: function (index, className) {
        return '<button class="' + className + '" type="button" aria-label="' + (index + 1) + '"></button>';
      }
    },
    on: {
      /* décalage vertical sinusoïdal : chaque carte flotte sur la vague */
      setTranslate: function (sw) {
        sw.slides.forEach(function (slide, i) {
          const offset = i - sw.activeIndex;
          const wave = Math.sin((i % 6) * 1.05) * 14;
          slide.style.setProperty('--wave-y', wave.toFixed(1) + 'px');
          slide.classList.toggle('is-active', offset === 0);
        });
      },
      slideChangeTransitionStart: function (sw) {
        sw.slides.forEach(function (slide) {
          const card = slide.querySelector('.piatti__card');
          if (!card) return;
          card.style.transition = 'transform 620ms cubic-bezier(.22,.65,.3,1)';
        });
      }
    },
    breakpoints: {
      0: { spaceBetween: 14 },
      700: { spaceBetween: 20 },
      1200: { spaceBetween: 30 }
    }
  });

  const prev = document.querySelector('[data-piatti-prev]');
  const next = document.querySelector('[data-piatti-next]');
  if (prev) prev.addEventListener('click', function () { swiper.slidePrev(); });
  if (next) next.addEventListener('click', function () { swiper.slideNext(); });

  /* carte centrale légèrement agrandie */
  function sizeActive() {
    swiper.slides.forEach(function (slide) {
      const card = slide.querySelector('.piatti__card');
      if (!card) return;
      const isActive = slide.classList.contains('swiper-slide-active');
      card.style.scale = isActive ? '1' : '0.94';
      card.style.opacity = isActive ? '1' : '0.92';
    });
  }
  swiper.on('slideChangeTransitionEnd', sizeActive);
  swiper.on('resize', sizeActive);
  window.setTimeout(sizeActive, 60);

  /* lueur de soleil au survol de la photo */
  if (!App.isTouch && !reduce) {
    document.querySelectorAll('.piatti__card').forEach(function (card) {
      const glare = card.querySelector('.piatti__glare');
      const photo = card.querySelector('.piatti__photo');
      if (!glare || !photo) return;
      photo.addEventListener('pointermove', function (ev) {
        const r = photo.getBoundingClientRect();
        const gx = ((ev.clientX - r.left) / r.width) * 100;
        const gy = ((ev.clientY - r.top) / r.height) * 100;
        glare.style.background = 'radial-gradient(90px 130px at ' + gx + '% ' + gy + '%, rgba(255,255,255,.65), transparent 72%)';
        glare.style.opacity = '1';
      });
      photo.addEventListener('pointerleave', function () { glare.style.opacity = '0'; });
    });
  }

  /* image manquante → bloc crème + motif de carreau + nom du plat */
  document.querySelectorAll('.piatti__photo img').forEach(function (img) {
    img.addEventListener('error', function () {
      const wrap = img.closest('.piatti__photo');
      const name = img.getAttribute('data-fallback') || '';
      img.style.display = 'none';
      if (wrap && !wrap.querySelector('.img-fallback')) {
        const box = document.createElement('div');
        box.className = 'img-fallback';
        box.style.cssText = 'height:100%;border:0;border-radius:50%';
        box.innerHTML = '<span>' + App.esc(name) + '</span>';
        wrap.appendChild(box);
      }
    }, { once: true });
  });
})();
