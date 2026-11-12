<?php
/**
 * head.php — :root depuis config.php, SEO, Open Graph, JSON-LD Restaurant,
 * preloads police, CSS critique. Aucune ressource externe (hors Google Fonts non requis).
 */
$pageTitle = $brand['name'] . ' · ' . $brand['subtitle'] . ' — ' . $brand['tagline'];
$pageDesc  = $config['i18n']['it']['hero.sub'];
$origin    = (($_SERVER['HTTPS'] ?? '') === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8000');
$ogImage   = $origin . '/assets/img/og-cover.jpg';

$jsonLd = [
    '@context'   => 'https://schema.org',
    '@type'      => 'Restaurant',
    'name'       => $brand['legal'],
    'alternateName'=> $brand['name'],
    'description'=> $pageDesc,
    'servesCuisine' => ['Cucina italiana', 'Frutti di mare', 'Cucina di Costiera'],
    'priceRange' => '€€',
    'telephone'  => $config['contact']['phone'],
    'email'      => $config['contact']['email'],
    'url'        => $origin . '/',
    'image'      => $ogImage,
    'acceptsReservations' => true,
    'hasMenu'    => $origin . '/#menu',
    'address'    => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => $config['contact']['street'],
        'postalCode'      => $config['contact']['cap'],
        'addressLocality' => $config['contact']['city'],
        'addressRegion'   => $config['contact']['province'],
        'addressCountry'  => $config['contact']['country'],
    ],
    'geo' => [
        '@type'     => 'GeoCoordinates',
        'latitude'  => $config['contact']['lat'],
        'longitude' => $config['contact']['lng'],
    ],
    'openingHoursSpecification' => array_values(array_filter(array_map(
        static function (int $day, array $ranges): ?array {
            if ($ranges === []) {
                return null;
            }
            $names = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
            return [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => $names[$day],
                'opens'     => $ranges[0][0],
                'closes'    => $ranges[count($ranges) - 1][1],
            ];
        },
        array_keys($config['hours']),
        $config['hours']
    ))),
    'aggregateRating' => [
        '@type'       => 'AggregateRating',
        'ratingValue' => 4.8,
        'reviewCount' => 912,
        'bestRating'  => 5,
    ],
    'sameAs' => array_column($config['socials'], 'url'),
];
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDesc) ?>">
<meta name="theme-color" content="<?= e($colors['calce']) ?>">
<link rel="canonical" href="<?= e($origin) ?>/">

<meta property="og:type" content="restaurant">
<meta property="og:locale" content="it_IT">
<meta property="og:locale:alternate" content="en_GB">
<meta property="og:site_name" content="<?= e($brand['legal']) ?>">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($pageDesc) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="<?= e($config['i18n']['it']['costiera.img_alt']) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($pageTitle) ?>">
<meta name="twitter:description" content="<?= e($pageDesc) ?>">
<meta name="twitter:image" content="<?= e($ogImage) ?>">

<link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">

<link rel="preload" href="assets/fonts/instrument-serif-400.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="assets/fonts/instrument-serif-400i.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="assets/fonts/figtree-400.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="assets/fonts/figtree-600.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" as="image" href="assets/img/hero-terrazza.webp" type="image/webp" fetchpriority="high">

<link rel="stylesheet" href="assets/vendor/swiper-bundle.min.css">
<link rel="stylesheet" href="assets/css/style.css">

<style>
:root{
  --calce: <?= e($colors['calce']) ?>;
  --limone: <?= e($colors['limone']) ?>;
  --maiolica: <?= e($colors['maiolica']) ?>;
  --notte: <?= e($colors['notte']) ?>;
  --cielo: <?= e($colors['cielo']) ?>;
  --foglia: <?= e($colors['foglia']) ?>;
  --cotto: <?= e($colors['cotto']) ?>;
  --crema: <?= e($colors['crema']) ?>;
}
</style>
<script>document.documentElement.classList.replace('no-js','js');</script>
<script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?></script>
