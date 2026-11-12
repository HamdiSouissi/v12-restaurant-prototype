<?php
/** cart-drawer.php — cassette à citrons : tiroir, toggle, frais, minimum, annulation, état vide. */
?>
<div class="cart-overlay" id="cart-overlay" hidden></div>

<aside class="cart-drawer" id="cart-drawer" role="dialog" aria-modal="true" aria-labelledby="cart-title" hidden>
    <div class="cart-drawer__frieze" aria-hidden="true"></div>

    <header class="cart-drawer__head">
        <h2 class="cart-drawer__title" id="cart-title"><?= t($config, 'cart.title') ?></h2>
        <button class="round-btn round-btn--sm" type="button" data-close-cart aria-label="<?= t($config, 'cart.close') ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-close"/></svg>
        </button>
    </header>

    <div class="cart-drawer__mode" role="group" aria-label="<?= t($config, 'cart.title') ?>">
        <button class="mode-btn is-active" type="button" data-mode="delivery" aria-pressed="true"><?= t($config, 'cart.delivery') ?></button>
        <button class="mode-btn" type="button" data-mode="pickup" aria-pressed="false"><?= t($config, 'cart.pickup') ?></button>
    </div>

    <div class="cart-drawer__body" data-cart-body>
        <ul class="cart-list" data-cart-list></ul>

        <div class="cart-empty" data-cart-empty hidden>
            <svg class="cart-empty__art" viewBox="0 0 200 150" aria-hidden="true">
                <g style="color:var(--maiolica)">
                    <use href="#i-crate" x="40" y="18" width="120" height="120"/>
                </g>
                <g class="cart-empty__lemon">
                    <circle cx="0" cy="0" r="15" fill="var(--limone)" stroke="var(--notte)" stroke-width="2.5"/>
                </g>
                <path d="M30 128h140" stroke="var(--maiolica)" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="4 8"/>
            </svg>
            <p class="cart-empty__title"><?= t($config, 'cart.empty_title') ?></p>
            <p class="cart-empty__text"><?= t($config, 'cart.empty_text') ?></p>
            <a class="btn btn--limone btn--sm" href="#menu" data-close-cart-goto><?= t($config, 'cart.empty_cta') ?></a>
        </div>
    </div>

    <footer class="cart-drawer__foot" data-cart-foot hidden>
        <div class="cart-progress" data-cart-progress>
            <p class="cart-progress__text" data-cart-progress-text></p>
            <div class="cart-progress__bar" aria-hidden="true"><span data-cart-progress-bar></span></div>
        </div>

        <dl class="cart-totals">
            <div><dt><?= t($config, 'cart.subtotal') ?></dt><dd data-cart-subtotal>€ 0</dd></div>
            <div><dt><?= t($config, 'cart.fee') ?></dt><dd data-cart-fee>€ 3</dd></div>
            <div class="cart-totals__total"><dt><?= t($config, 'cart.total') ?></dt><dd data-cart-total>€ 0</dd></div>
        </dl>

        <p class="cart-minimum" data-cart-minimum hidden><?= t($config, 'cart.minimum') ?></p>

        <button class="btn btn--limone btn--lg btn--block" type="button" data-checkout>
            <?= t($config, 'cart.checkout') ?>
        </button>
    </footer>
</aside>
