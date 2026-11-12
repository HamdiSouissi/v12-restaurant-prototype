<?php
/** header.php — pilule flottante crème, statut live Europe/Rome, IT/EN, cassette, menu mobile. */
$navItems = [
    ['href' => '#menu',    'key' => 'nav.menu'],
    ['href' => '#piatti',  'key' => 'nav.piatti'],
    ['href' => '#giro',    'key' => 'nav.giro'],
    ['href' => '#prenota', 'key' => 'nav.prenota'],
    ['href' => '#dove',    'key' => 'nav.dove'],
];
?>
<header class="site-header" id="site-header" data-open="false">
    <div class="site-header__pill">
        <a class="logo" href="#hero" aria-label="<?= e($brand['name'] . ' — ' . $brand['subtitle']) ?>">
            <svg class="logo__lemon" viewBox="0 0 100 100" aria-hidden="true"><use href="#i-lemon-solid"/></svg>
            <span class="logo__word"><?= e($brand['name']) ?></span>
            <span class="logo__sub"><?= e($brand['subtitle']) ?></span>
        </a>

        <nav class="site-nav" aria-label="Navigazione principale">
            <ul>
                <?php foreach ($navItems as $item): ?>
                <li><a class="site-nav__link" href="<?= e($item['href']) ?>" data-nav-key="<?= e($item['key']) ?>"><?= t($config, $item['key']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="site-header__actions">
            <p class="status-pill" id="open-status" data-open="true" aria-live="polite">
                <span class="status-pill__dot" aria-hidden="true"></span>
                <span class="status-pill__text" data-open-text><?= t($config, 'nav.open') ?></span>
            </p>

            <div class="lang-switch" role="group" aria-label="Lingua / Language">
                <button type="button" class="lang-switch__btn is-active" data-lang="it" aria-pressed="true">IT</button>
                <span aria-hidden="true">/</span>
                <button type="button" class="lang-switch__btn" data-lang="en" aria-pressed="false">EN</button>
            </div>

            <a class="btn btn--limone btn--sm header-order" href="#menu" data-scroll-order><?= t($config, 'nav.ordina') ?></a>

            <button class="cart-btn" type="button" data-open-cart aria-label="<?= t($config, 'nav.cart') ?>">
                <svg viewBox="0 0 100 100" aria-hidden="true"><use href="#i-crate"/></svg>
                <span class="cart-btn__badge" data-cart-badge data-empty="true">0</span>
            </button>

            <button class="burger" type="button" id="burger" aria-expanded="false" aria-controls="mobile-menu" aria-label="<?= t($config, 'nav.mobile') ?>">
                <span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span>
            </button>
        </div>
    </div>
</header>

<div class="mobile-menu" id="mobile-menu" role="dialog" aria-modal="true" aria-label="<?= e($brand['name']) ?>" hidden>
    <div class="mobile-menu__tiles" aria-hidden="true"></div>
    <button class="mobile-menu__close" type="button" data-close-mobile aria-label="<?= t($config, 'nav.close') ?>">
        <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-close"/></svg>
    </button>
    <svg class="mobile-menu__branch" viewBox="0 0 200 140" aria-hidden="true"><use href="#i-branch"/></svg>
    <nav class="mobile-menu__nav" aria-label="Navigazione mobile">
        <ul>
            <?php foreach ($navItems as $i => $item): ?>
            <li style="--i:<?= $i ?>">
                <a href="<?= e($item['href']) ?>" data-close-mobile-link>
                    <span class="mobile-menu__num" aria-hidden="true">0<?= $i + 1 ?></span>
                    <span data-nav-key="<?= e($item['key']) ?>"><?= t($config, $item['key']) ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div class="mobile-menu__foot">
        <a class="btn btn--limone" href="#menu" data-close-mobile-link data-scroll-order><?= t($config, 'nav.ordina') ?></a>
        <div class="lang-switch lang-switch--light" role="group" aria-label="Lingua / Language">
            <button type="button" class="lang-switch__btn is-active" data-lang="it" aria-pressed="true">IT</button>
            <span aria-hidden="true">/</span>
            <button type="button" class="lang-switch__btn" data-lang="en" aria-pressed="false">EN</button>
        </div>
        <p class="mobile-menu__contact">
            <a href="tel:<?= e($config['contact']['phone_raw']) ?>"><?= e($config['contact']['phone']) ?></a>
            <span aria-hidden="true">·</span>
            <a href="mailto:<?= e($config['contact']['email']) ?>"><?= e($config['contact']['email']) ?></a>
        </p>
    </div>
</div>
