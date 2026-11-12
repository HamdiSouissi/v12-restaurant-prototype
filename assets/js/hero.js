/* =========================================================================
   hero.js — arches en cascade, titre masqué, branches/citrons pendule,
   soleil rotatif, parallaxe souris, chute + rebond des citrons.
   ========================================================================= */
(function () {
  'use strict';

  const App = window.LIMONAIA_APP || {};
  const hero = document.getElementById('hero');
  if (!hero) return;

  const hasGSAP = typeof window.gsap !== 'undefined';
  const reduce = App.reduceMotion || window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ------------------------------------------------------------ entrée en cascade */
  function intro() {
    const arches = hero.querySelectorAll('.hero__arch');
    const lines = hero.querySelectorAll('.hero__line > span');
    const kicker = hero.querySelector('.hero__kicker');
    const foot = hero.querySelector('.hero__foot');
    const branch = hero.querySelectorAll('.hero__branch');
    const sun = hero.querySelector('.hero__sun');

    if (!hasGSAP || reduce) {
      arches.forEach(function (a) { a.style.opacity = '1'; });
      return;
    }

    const tl = gsap.timeline({ defaults: { ease: 'power2.out' }, delay: 0.08 });

    gsap.set(arches, { clipPath: 'inset(100% 0 0 0)', scale: 1.04 });
    gsap.set(lines, { yPercent: 112 });
    gsap.set([kicker, foot], { opacity: 0, y: 18 });
    gsap.set(branch, { opacity: 0, y: -24 });
    if (sun) gsap.set(sun, { opacity: 0, scale: 0.8 });

    tl.to(arches, {
      clipPath: 'inset(0% 0 0 0)',
      scale: 1,
      duration: 1.1,
      stagger: { each: 0.11, from: 'center' }
    })
    .to(lines, { yPercent: 0, duration: 0.95, stagger: 0.09 }, '-=0.75')
    .to([kicker, foot], { opacity: 1, y: 0, duration: 0.7, stagger: 0.08 }, '-=0.55')
    .to(branch, { opacity: 1, y: 0, duration: 0.9, stagger: 0.1 }, '-=0.9')
    .to(sun, { opacity: 0.95, scale: 1, duration: 0.9 }, '-=0.8');
  }

  // Attendre la fin du preloader si présent
  if (document.body.classList.contains('is-loading')) {
    document.addEventListener('limonaia:loaded', intro, { once: true });
    // filet de sécurité si l'événement n'arrive pas
    window.setTimeout(intro, 1800);
  } else {
    intro();
  }

  /* ------------------------------------------------------------ parallaxe souris */
  if (hasGSAP && !reduce && !App.isTouch) {
    const layers = hero.querySelectorAll('[data-depth]');
    const branches = hero.querySelectorAll('.hero__branch');
    const sun = hero.querySelector('.hero__sun');
    const xTo = [], yTo = [];

    layers.forEach(function (layer) {
      const depth = parseFloat(layer.getAttribute('data-depth')) || 0.4;
      xTo.push(gsap.quickTo(layer, 'x', { duration: 1.1, ease: 'power2.out' }));
      yTo.push(gsap.quickTo(layer, 'y', { duration: 1.1, ease: 'power2.out' }));
      layer.dataset.depth = depth;
    });

    const bx = branches.length ? gsap.quickTo(branches, 'x', { duration: 1.4, ease: 'power2.out' }) : null;
    const by = branches.length ? gsap.quickTo(branches, 'y', { duration: 1.4, ease: 'power2.out' }) : null;
    const sx = sun ? gsap.quickTo(sun, 'x', { duration: 1.6, ease: 'power2.out' }) : null;

    hero.addEventListener('pointermove', function (ev) {
      const rect = hero.getBoundingClientRect();
      const nx = (ev.clientX - rect.left) / rect.width - 0.5;
      const ny = (ev.clientY - rect.top) / rect.height - 0.5;
      layers.forEach(function (layer, i) {
        const d = parseFloat(layer.dataset.depth) || 0.4;
        xTo[i](nx * 26 * d);
        yTo[i](ny * 18 * d);
      });
      if (bx) { bx(nx * -34); by(ny * -16); }
      if (sx) sx(nx * 22);
    }, { passive: true });

    hero.addEventListener('pointerleave', function () {
      layers.forEach(function (layer, i) { xTo[i](0); yTo[i](0); });
      if (bx) { bx(0); by(0); }
      if (sx) sx(0);
    });
  }

  /* ------------------------------------------------------------ citrons : pendule + chute */
  const lemons = hero.querySelectorAll('[data-lemon]');
  lemons.forEach(function (lemon) {
    const thread = lemon.closest('.hero__thread');
    if (!thread) return;

    lemon.addEventListener('pointerenter', function () {
      if (reduce || !hasGSAP || thread.classList.contains('is-plucked')) return;
      gsap.fromTo(lemon, { rotate: -6 }, {
        rotate: 6, duration: 1.4, ease: 'sine.inOut', yoyo: true, repeat: -1
      });
    });
    lemon.addEventListener('pointerleave', function () {
      if (!hasGSAP) return;
      gsap.killTweensOf(lemon);
      if (!thread.classList.contains('is-plucked')) {
        gsap.to(lemon, { rotate: 0, duration: 0.6, ease: 'power2.out' });
      }
    });

    lemon.addEventListener('click', function () {
      if (thread.classList.contains('is-plucked')) {
        // remise en place
        thread.classList.remove('is-plucked');
        if (hasGSAP) gsap.to(thread, { y: 0, duration: 0.5, ease: 'power2.out' });
        return;
      }
      thread.classList.add('is-plucked');
      App.toast(App.t('hero.squeezed'));

      if (!hasGSAP || reduce) {
        thread.style.opacity = '0.35';
        return;
      }

      gsap.killTweensOf(lemon);
      const wire = thread.querySelector('.hero__wire');
      const fallDistance = window.innerHeight * 0.7;

      const tl = gsap.timeline();
      if (wire) tl.to(wire, { scaleY: 0, transformOrigin: 'top center', duration: 0.16, ease: 'power2.in' }, 0);
      tl.to(lemon, {
        y: fallDistance,
        rotate: 380,
        duration: 0.95,
        ease: 'power1.in'
      }, 0)
        .to(lemon, { y: fallDistance - 34, duration: 0.22, ease: 'power2.out' }, '-=0.16')
        .to(lemon, { y: fallDistance, duration: 0.18, ease: 'power2.in' })
        .to(lemon, { opacity: 0, scale: 0.85, duration: 0.4, ease: 'power2.out' }, '-=0.05');

      // Le citron revient après quelques secondes pour pouvoir rejouer
      window.setTimeout(function () {
        thread.classList.remove('is-plucked');
        gsap.set(lemon, { y: 0, rotate: 0, opacity: 1, scale: 1 });
        if (wire) gsap.set(wire, { scaleY: 1 });
      }, 3600);
    });
  });

  /* ------------------------------------------------------------ lueur qui suit sur les arches */
  if (!App.isTouch && !reduce) {
    hero.querySelectorAll('.hero__arch').forEach(function (arch) {
      arch.addEventListener('pointermove', function (ev) {
        const r = arch.getBoundingClientRect();
        const gx = ((ev.clientX - r.left) / r.width) * 100;
        const gy = ((ev.clientY - r.top) / r.height) * 100;
        const glare = arch.querySelector('.hero__arch-glare');
        if (glare) {
          glare.style.background = 'radial-gradient(120px 180px at ' + gx + '% ' + gy + '%, rgba(255,255,255,.55), transparent 70%)';
          glare.style.opacity = '.9';
        }
      });
      arch.addEventListener('pointerleave', function () {
        const glare = arch.querySelector('.hero__arch-glare');
        if (glare) glare.style.opacity = '0';
      });
    });
  }

  /* ------------------------------------------------------------ arch image fallback */
  hero.querySelectorAll('img').forEach(function (img) {
    img.addEventListener('error', function () {
      const fig = img.closest('.hero__arch') || img.parentElement;
      img.style.display = 'none';
      if (fig && !fig.querySelector('.img-fallback')) {
        const box = document.createElement('div');
        box.className = 'img-fallback';
        box.style.height = '100%';
        box.innerHTML = '<span>' + App.esc('LIMONAIA') + '</span>';
        fig.appendChild(box);
      }
    }, { once: true });
  });
})();
