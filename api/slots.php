<?php
/**
 * api/slots.php — créneaux de réservation simulés.
 * GET ?date=YYYY-MM-DD&guests=2
 * Groupes : Pranzo, Aperitivo al tramonto (autour du coucher du soleil), Cena.
 */
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    header('Allow: GET');
    json_error('method', 'Metodo non consentito.', 405);
}

$tz      = new DateTimeZone($config['contact']['timezone']);
$dateRaw = (string) ($_GET['date'] ?? '');
$guests  = (int) ($_GET['guests'] ?? 2);

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateRaw)) {
    json_error('date', 'Scegli una data valida.', 422);
}
$date = DateTimeImmutable::createFromFormat('!Y-m-d', $dateRaw, $tz);
if (!$date || $date->format('Y-m-d') !== $dateRaw) {
    json_error('date', 'Scegli una data valida.', 422);
}

$today = new DateTimeImmutable('today', $tz);
if ($date < $today) {
    json_error('date', 'Quella data è passata.', 422);
}
if ($date->format('N') === (string) $config['closed_weekday']) {
    json_error('date', 'Il lunedì siamo chiusi. Ti aspettiamo domani.', 409);
}

$guests = max(1, min($guests, $config['booking']['max_guests']));
$month  = (int) $date->format('n');
$sunset = $config['sunset'][$month] ?? '19:30';
[$sh, $sm] = array_map('intval', explode(':', $sunset));
$sunsetMinutes = $sh * 60 + $sm;

/** Crée les créneaux entre deux heures, pas de 30 min. */
$makeSlots = static function (string $from, string $to, int $step): array {
    $slots = [];
    [$fh, $fm] = array_map('intval', explode(':', $from));
    [$th, $tm] = array_map('intval', explode(':', $to));
    $cur = $fh * 60 + $fm;
    $end = $th * 60 + $tm;
    while ($cur <= $end) {
        $slots[] = sprintf('%02d:%02d', intdiv($cur, 60) % 24, $cur % 60);
        $cur += $step;
    }
    return $slots;
};

/* Aperitivo : de sunset-90 min à sunset+30 min, arrondi au pas de 30. */
$aperFrom = max(0, $sunsetMinutes - $config['booking']['aperitivo_before']);
$aperFrom = (int) (ceil($aperFrom / $config['booking']['slot_step']) * $config['booking']['slot_step']);
$aperTo   = $sunsetMinutes + 30;

$groups = [
    [
        'key'    => 'pranzo',
        'label'  => 'Pranzo',
        'slots'  => [],
        'from'   => $config['booking']['lunch'][0],
        'to'     => $config['booking']['lunch'][1],
    ],
    [
        'key'    => 'aperitivo',
        'label'  => 'Aperitivo al tramonto',
        'slots'  => [],
        'from'   => sprintf('%02d:%02d', intdiv($aperFrom, 60) % 24, $aperFrom % 60),
        'to'     => sprintf('%02d:%02d', intdiv($aperTo, 60) % 24, $aperTo % 60),
        'featured' => true,
    ],
    [
        'key'    => 'cena',
        'label'  => 'Cena',
        'slots'  => [],
        'from'   => $config['booking']['dinner'][0],
        'to'     => $config['booking']['dinner'][1],
    ],
];

$seen = [];

foreach ($groups as $gi => $group) {
    foreach ($makeSlots($group['from'], $group['to'], $config['booking']['slot_step']) as $time) {
        if (isset($seen[$time])) {
            continue;
        }
        $seen[$time] = true;

        /* Pseudo-aléatoire déterministe : même date + heure + couverts = même état. */
        $seed = crc32($dateRaw . '|' . $time . '|' . $guests);
        $roll = $seed % 100;

        $status = 'free';
        if ($roll < 12) {
            $status = 'full';
        } elseif ($roll < 28) {
            $status = 'last';
        }
        /* Le créneau pile du coucher du soleil est toujours le plus demandé. */
        if ((int) substr($time, 0, 2) * 60 + (int) substr($time, 3, 2) === $sunsetMinutes && $status === 'free') {
            $status = 'last';
        }

        $groups[$gi]['slots'][] = [
            'time'   => $time,
            'status' => $status,
            'sunset' => $time === sprintf('%02d:%02d', intdiv($sunsetMinutes, 60), $sunsetMinutes % 60),
        ];
    }
}

/* Supprime les groupes vides */
$groups = array_values(array_filter($groups, static fn (array $g): bool => $g['slots'] !== []));

json_out([
    'ok'       => true,
    'date'     => $dateRaw,
    'weekday'  => (int) $date->format('N'),
    'guests'   => $guests,
    'sunset'   => $sunset,
    'timezone' => $config['contact']['timezone'],
    'groups'   => $groups,
]);
