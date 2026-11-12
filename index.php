<?php
/**
 * LIMONAIA · Cucina di Costiera — one-page, PHP 8 natif.
 * `php -S localhost:8000` depuis la racine du projet.
 */
declare(strict_types=1);

$config = require __DIR__ . '/config.php';

/** Échappement systématique de toute sortie. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Traduit une clé i18n (italien par défaut) et échappe. */
function t(array $config, string $key, string $lang = 'it'): string
{
    return e($config['i18n'][$lang][$key] ?? $config['i18n']['it'][$key] ?? $key);
}

/** Retourne la valeur bilingue d'un champ config. */
function bi(array $field, string $lang = 'it'): string
{
    return e($field[$lang] ?? $field['it'] ?? '');
}

/** URL d'une image optimisée, sans extension (picture source gère webp/jpg). */
function img(string $slug, string $sub = ''): string
{
    return 'assets/img/' . ($sub !== '' ? $sub . '/' : '') . $slug;
}

/**
 * Séparateur de section en vagues superposées (2 ou 3 vagues qui glissent en boucle).
 * Le décalage est piloté par la vélocité Lenis (main.js).
 */
function wave(string $fill, bool $flip = false, string $extraClass = ''): void
{
    $cls = 'wave' . ($flip ? ' wave--flip' : '') . ($extraClass !== '' ? ' ' . $extraClass : '');
    ?>
    <div class="<?= $cls ?>" aria-hidden="true" data-wave data-velocity="1">
        <svg class="wave__layer wave__layer--back" viewBox="0 0 1440 120" preserveAspectRatio="none" style="color:<?= $fill ?>">
            <path d="M0 64c120 0 180-38 300-38s180 38 300 38 180-38 300-38 180 38 300 38 120-20 240-20v92H0Z" fill="currentColor" opacity=".38"/>
        </svg>
        <svg class="wave__layer wave__layer--mid" viewBox="0 0 1440 120" preserveAspectRatio="none" style="color:<?= $fill ?>">
            <path d="M0 78c120 0 190-30 310-30s180 30 300 30 190-30 310-30 180 30 300 30 100-14 220-14v56H0Z" fill="currentColor" opacity=".62"/>
        </svg>
        <svg class="wave__layer wave__layer--front" viewBox="0 0 1440 120" preserveAspectRatio="none" style="color:<?= $fill ?>">
            <path d="M0 92c120 0 200-22 320-22s180 22 300 22 200-22 320-22 180 22 300 22 100-8 200-8v36H0Z" fill="currentColor"/>
        </svg>
    </div>
    <?php
}

$lang   = 'it';
$brand  = $config['brand'];
$colors = $config['colors'];
$year   = (int) date('Y');

/* JSON exposé aux modules JS (menu, config, i18n). */
$payload = [
    'menu'      => $config['menu'],
    'signature' => $config['signature'],
    'giro'      => $config['giro'],
    'booking'   => [
        'maxGuests' => $config['booking']['max_guests'],
        'step'      => $config['booking']['slot_step'],
        'sunset'    => $config['sunset'],
        'views'     => array_map(
            static fn (array $v): array => ['key' => $v['key'], 'name' => $v['name']],
            $config['booking']['views']
        ),
    ],
    'delivery'  => $config['delivery'],
    'colors'    => $colors,
    'i18n'      => $config['i18n'],
    'hours'     => $config['hours'],
];
?>
<!DOCTYPE html>
<html lang="it" class="no-js">
<head>
<?php require __DIR__ . '/partials/head.php'; ?>
</head>
<body class="is-loading">
<?php require __DIR__ . '/partials/icons-svg.php'; ?>

<a class="skip-link" href="#hero"><?= t($config, 'skip') ?></a>

<?php require __DIR__ . '/partials/preloader.php'; ?>

<?php require __DIR__ . '/partials/header.php'; ?>

<main id="main">
    <?php require __DIR__ . '/partials/hero.php'; ?>
    <?php require __DIR__ . '/partials/costiera.php'; ?>
    <?php require __DIR__ . '/partials/piatti.php'; ?>
    <?php require __DIR__ . '/partials/menu.php'; ?>
    <?php require __DIR__ . '/partials/giro.php'; ?>
    <?php require __DIR__ . '/partials/booking.php'; ?>
    <?php require __DIR__ . '/partials/gallery.php'; ?>
    <?php require __DIR__ . '/partials/reviews.php'; ?>
    <?php require __DIR__ . '/partials/info.php'; ?>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>

<?php require __DIR__ . '/partials/cart-drawer.php'; ?>
<?php require __DIR__ . '/partials/checkout-modal.php'; ?>
<?php require __DIR__ . '/partials/toast.php'; ?>

<div class="mobile-bar" id="mobile-bar" aria-hidden="true">
    <a class="btn btn--outline" href="#prenota"><?= t($config, 'nav.prenota') ?></a>
    <button class="btn btn--limone" type="button" data-open-cart>
        <?= t($config, 'nav.ordina') ?> <span aria-hidden="true">·</span> <span data-cart-count>0</span>
    </button>
</div>

<?php if ($config['demo_badge']): ?>
<p class="demo-badge" title="Prototipo commerciale — dati fittizi">
    <span class="demo-badge__dot" aria-hidden="true"></span><?= t($config, 'demo.badge') ?>
</p>
<?php endif; ?>

<script>window.LIMONAIA = <?= json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?>;</script>
<script src="assets/vendor/gsap.min.js" defer></script>
<script src="assets/vendor/ScrollTrigger.min.js" defer></script>
<script src="assets/vendor/SplitText.min.js" defer></script>
<script src="assets/vendor/Flip.min.js" defer></script>
<script src="assets/vendor/Draggable.min.js" defer></script>
<script src="assets/vendor/lenis.min.js" defer></script>
<script src="assets/vendor/swiper-bundle.min.js" defer></script>
<script src="assets/js/i18n.js" defer></script>
<script src="assets/js/main.js" defer></script>
<script src="assets/js/hero.js" defer></script>
<script src="assets/js/piatti.js" defer></script>
<script src="assets/js/menu.js" defer></script>
<script src="assets/js/giro.js" defer></script>
<script src="assets/js/cart.js" defer></script>
<script src="assets/js/checkout.js" defer></script>
<script src="assets/js/booking.js" defer></script>
<script src="assets/js/gallery.js" defer></script>
</body>
</html>
