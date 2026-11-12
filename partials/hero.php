<?php
/** hero.php — la terrazza : trois arches, titre derrière l'arche centrale, citrons suspendus. */
?>
<section class="hero" id="hero" aria-labelledby="hero-title" data-parallax-root>
    <div class="hero__sun" aria-hidden="true">
        <svg viewBox="0 0 100 100"><use href="#i-sun"/></svg>
    </div>

    <svg class="hero__branch hero__branch--left" viewBox="0 0 200 140" aria-hidden="true"><use href="#i-branch"/></svg>
    <svg class="hero__branch hero__branch--right" viewBox="0 0 200 140" aria-hidden="true"><use href="#i-branch"/></svg>

    <div class="hero__stage">
        <p class="hero__kicker label"><?= t($config, 'hero.kicker') ?></p>

        <div class="hero__arches">
            <figure class="hero__arch hero__arch--side hero__arch--left" data-depth="0.5">
                <picture>
                    <source srcset="<?= img('limoni') ?>.webp" type="image/webp">
                    <img src="<?= img('limoni') ?>.jpg" alt="Limoni di Costiera sul ramo"
                         width="1000" height="1400" loading="eager" decoding="async" fetchpriority="high">
                </picture>
            </figure>

            <figure class="hero__arch hero__arch--center" data-depth="1">
                <picture>
                    <source srcset="<?= img('hero-terrazza') ?>.webp" type="image/webp">
                    <img src="<?= img('hero-terrazza') ?>.jpg" alt="<?= t($config, 'costiera.img_alt') ?>"
                         width="1120" height="1400" loading="eager" decoding="async" fetchpriority="high">
                </picture>
                <span class="hero__arch-glare" aria-hidden="true"></span>
            </figure>

            <figure class="hero__arch hero__arch--side hero__arch--right" data-depth="0.5">
                <picture>
                    <source srcset="<?= img('hero-mare') ?>.webp" type="image/webp">
                    <img src="<?= img('hero-mare') ?>.jpg" alt="<?= e($config['i18n']['it']['hero.note']) ?>"
                         width="1000" height="1400" loading="eager" decoding="async">
                </picture>
            </figure>
        </div>

        <h1 class="hero__title" id="hero-title">
            <span class="hero__line hero__line--1"><span data-split><?= t($config, 'hero.line1') ?></span></span>
            <span class="hero__line hero__line--2"><span data-split><?= t($config, 'hero.line2') ?></span></span>
            <span class="hero__line hero__line--3"><span data-split class="ital"><?= t($config, 'hero.line3') ?></span></span>
        </h1>

        <!-- trois citrons suspendus : hover = pendule, clic = chute + toast -->
        <div class="hero__lemons" aria-hidden="true">
            <div class="hero__thread" style="--x:12%; --len:96px; --delay:0s">
                <span class="hero__wire"></span>
                <button class="hero__lemon" type="button" data-lemon aria-label="<?= t($config, 'hero.lemon_hint') ?>">
                    <svg viewBox="0 0 100 100"><use href="#i-lemon-solid"/></svg>
                </button>
            </div>
            <div class="hero__thread" style="--x:58%; --len:148px; --delay:-1.6s">
                <span class="hero__wire"></span>
                <button class="hero__lemon" type="button" data-lemon aria-label="<?= t($config, 'hero.lemon_hint') ?>">
                    <svg viewBox="0 0 100 100"><use href="#i-lemon-solid"/></svg>
                </button>
            </div>
            <div class="hero__thread" style="--x:86%; --len:64px; --delay:-3.1s">
                <span class="hero__wire"></span>
                <button class="hero__lemon" type="button" data-lemon aria-label="<?= t($config, 'hero.lemon_hint') ?>">
                    <svg viewBox="0 0 100 100"><use href="#i-lemon-solid"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div class="hero__foot">
        <p class="hero__sub"><?= t($config, 'hero.sub') ?></p>
        <div class="hero__ctas">
            <a class="btn btn--limone btn--lg" href="#menu"><?= t($config, 'hero.cta1') ?></a>
            <a class="btn btn--outline btn--lg" href="#prenota"><?= t($config, 'hero.cta2') ?></a>
        </div>
        <p class="hero__note">
            <svg viewBox="0 0 100 100" aria-hidden="true" class="hero__note-icon"><use href="#i-sun-rays"/></svg>
            <?= t($config, 'hero.note') ?>
        </p>
    </div>

    <?php wave('var(--calce)'); ?>
</section>
