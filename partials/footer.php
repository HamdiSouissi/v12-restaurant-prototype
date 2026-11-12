<?php
/** footer.php — le mot de la marque rempli d'eau, branches qui se balancent, newsletter, réseau. */
?>
<div class="frieze" data-frieze aria-hidden="true"></div>

<footer class="site-footer" id="footer" aria-labelledby="footer-title">
    <?php wave('var(--maiolica)', true, 'wave--top'); ?>

    <svg class="site-footer__branch site-footer__branch--left" viewBox="0 0 200 140" aria-hidden="true"><use href="#i-branch"/></svg>
    <svg class="site-footer__branch site-footer__branch--right" viewBox="0 0 200 140" aria-hidden="true"><use href="#i-branch"/></svg>

    <div class="site-footer__word" id="footer-word" aria-hidden="true">
        <span class="site-footer__word-fill"><?= e($brand['name']) ?></span>
        <span class="site-footer__word-line" aria-hidden="true"><?= e($brand['name']) ?></span>
    </div>
    <h2 class="visually-hidden" id="footer-title"><?= e($brand['name'] . ' · ' . $brand['subtitle']) ?></h2>

    <div class="wrap site-footer__grid">
        <div class="site-footer__news">
            <h3 class="site-footer__title"><?= t($config, 'footer.news_title') ?></h3>
            <p class="site-footer__sub"><?= t($config, 'footer.news_sub') ?></p>
            <form class="newsletter" id="newsletter" novalidate>
                <label class="visually-hidden" for="newsletter-email"><?= t($config, 'footer.email') ?></label>
                <input id="newsletter-email" type="email" name="email" placeholder="<?= t($config, 'footer.email') ?>"
                       autocomplete="email" required>
                <button class="btn btn--limone btn--sm" type="submit"><?= t($config, 'footer.subscribe') ?></button>
            </form>
            <p class="newsletter__msg" data-newsletter-msg role="status" aria-live="polite"></p>
        </div>

        <nav class="site-footer__nav" aria-label="<?= t($config, 'footer.follow') ?>">
            <ul>
                <?php foreach ($config['socials'] as $social): ?>
                <li>
                    <a href="<?= e($social['url']) ?>" target="_blank" rel="noopener">
                        <svg viewBox="0 0 100 100" aria-hidden="true"><use href="#i-<?= e($social['icon']) ?>"/></svg>
                        <?= e($social['label']) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="site-footer__touch">
            <p class="label"><?= t($config, 'info.contacts') ?></p>
            <a href="tel:<?= e($config['contact']['phone_raw']) ?>"><?= e($config['contact']['phone']) ?></a>
            <a href="mailto:<?= e($config['contact']['email']) ?>"><?= e($config['contact']['email']) ?></a>
            <address><?= e($config['contact']['street']) ?> · <?= e($config['contact']['cap']) ?> <?= e($config['contact']['city']) ?></address>
        </div>
    </div>

    <div class="wrap site-footer__legal">
        <p>© 2026 <?= e($brand['legal']) ?>. <?= e($brand['vat']) ?></p>
        <p class="site-footer__legal-links">
            <a href="#footer"><?= t($config, 'footer.privacy') ?></a>
            <span aria-hidden="true">·</span>
            <a href="#footer"><?= t($config, 'footer.cookie') ?></a>
        </p>
        <button class="to-top" type="button" data-to-top aria-label="<?= t($config, 'footer.top') ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><use href="#i-arrow-up"/></svg>
            <span><?= t($config, 'footer.top') ?></span>
        </button>
    </div>
</footer>
