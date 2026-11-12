<?php
/**
 * api/booking.php — réservation simulée.
 * POST { name, phone, email, date, time, guests, view, occasion?, notes?, consent }
 * Réponse : { ok, code: "LMN-T-XXXX", booking: {...}, ics: "..." }
 */
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';

require_post();

$body = read_body();

$name     = clean($body['name'] ?? '', 80);
$phone    = clean($body['phone'] ?? '', 30);
$email    = clean($body['email'] ?? '', 120);
$dateRaw  = clean($body['date'] ?? '', 10);
$time     = clean($body['time'] ?? '', 5);
$guests   = (int) ($body['guests'] ?? 0);
$view     = clean($body['view'] ?? '', 20);
$occasion = clean($body['occasion'] ?? 'nessuna', 20);
$notes    = clean($body['notes'] ?? '', 400);
$consent  = (bool) ($body['consent'] ?? false);

$errors = [];
if (mb_strlen($name) < 2) {
    $errors['name'] = 'Ci serve un nome per il tavolo.';
}
if (!is_valid_phone($phone)) {
    $errors['phone'] = 'Ci serve un numero valido per chiamarti.';
}
if (!is_valid_email($email)) {
    $errors['email'] = 'Controlla l\'email: ti mandiamo la conferma.';
}
if ($guests < 1 || $guests > $config['booking']['max_guests']) {
    $errors['guests'] = 'Per gruppi più grandi chiamaci: troviamo una soluzione.';
}
if (!$consent) {
    $errors['consent'] = 'Ci vuole il tuo consenso per trattare i dati.';
}

$viewKeys = array_column($config['booking']['views'], 'key');
if (!in_array($view, $viewKeys, true)) {
    $errors['view'] = 'Scegli una delle tre viste.';
}
if (!array_key_exists($occasion, $config['booking']['occasions'])) {
    $occasion = 'nessuna';
}

$tz = new DateTimeZone($config['contact']['timezone']);
$date = DateTimeImmutable::createFromFormat('!Y-m-d', $dateRaw, $tz);
if (!$date || $date->format('Y-m-d') !== $dateRaw) {
    $errors['date'] = 'Scegli una data valida.';
} elseif ($date < new DateTimeImmutable('today', $tz)) {
    $errors['date'] = 'Quella data è passata.';
} elseif ((int) $date->format('N') === (int) $config['closed_weekday']) {
    $errors['date'] = 'Il lunedì siamo chiusi. Ti aspettiamo domani.';
}

if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time)) {
    $errors['time'] = 'Scegli un orario tra quelli disponibili.';
} elseif (!isset($errors['date'])) {
    /* Vérifie que le créneau fait partie du service (déjeuner / apéritif / dîner). */
    [$h, $m] = array_map('intval', explode(':', $time));
    $minutes = $h * 60 + $m;
    $month   = (int) $date->format('n');
    $sunset  = $config['sunset'][$month];
    [$sh, $sm] = array_map('intval', explode(':', $sunset));
    $sunsetMin = $sh * 60 + $sm;
    $inLunch = $minutes >= 720 && $minutes <= 870;                       /* 12:00–14:30 */
    $inAper  = $minutes >= $sunsetMin - $config['booking']['aperitivo_before'] && $minutes <= $sunsetMin + 30;
    $inDinner = $minutes >= 1170 && $minutes <= 1380;                    /* 19:30–23:00 */
    if (!$inLunch && !$inAper && !$inDinner) {
        $errors['time'] = 'Fuori orario di servizio. Scegli un altro orario.';
    }
}

if ($errors !== []) {
    json_out(['ok' => false, 'errors' => $errors], 422);
}

$code = sim_code('LMN-T-');
$monthName = ['gennaio','febbraio','marzo','aprile','maggio','giugno','luglio','agosto','settembre','ottobre','novembre','dicembre'][(int) $date->format('n') - 1];
$monthNameEn = ['January','February','March','April','May','June','July','August','September','October','November','December'][(int) $date->format('n') - 1];

$viewLabel = '';
foreach ($config['booking']['views'] as $v) {
    if ($v['key'] === $view) {
        $viewLabel = $v['name']['it'];
    }
}

$sunsetTime = $config['sunset'][(int) $date->format('n')];
$start = $date->setTime((int) substr($time, 0, 2), (int) substr($time, 3, 2));
$end   = $start->modify('+2 hours');

$summaryIt = sprintf('%s %d %s · %s · %d persone · %s', ['lun','mar','mer','gio','ven','sab','dom'][(int) $date->format('N') - 1],
    (int) $date->format('j'), $monthName, $time, $guests, mb_strtoupper($viewLabel));

/* Fichier .ics complet, téléchargeable */
$icsLines = [
    'BEGIN:VCALENDAR',
    'VERSION:2.0',
    'PRODID:-//LIMONAIA//Prenotazione//IT',
    'CALSCALE:GREGORIAN',
    'METHOD:PUBLISH',
    'BEGIN:VEVENT',
    'UID:' . $code . '@limonaia.it',
    'DTSTAMP:' . gmdate('Ymd\THis\Z'),
    'DTSTART;TZID=Europe/Rome:' . $start->format('Ymd\THis'),
    'DTEND;TZID=Europe/Rome:' . $end->format('Ymd\THis'),
    'SUMMARY:Tavolo da LIMONAIA · ' . $guests . ' persone',
    'DESCRIPTION:Codice ' . $code . ' · ' . $viewLabel . ' · Tramonto ' . $sunsetTime,
    'LOCATION:' . $config['contact']['street'] . ', ' . $config['contact']['cap'] . ' ' . $config['contact']['city'],
    'STATUS:CONFIRMED',
    'BEGIN:VALARM',
    'TRIGGER:-PT3H',
    'ACTION:DISPLAY',
    'DESCRIPTION:LIMONAIA stasera',
    'END:VALARM',
    'END:VEVENT',
    'END:VCALENDAR',
];

json_out([
    'ok'   => true,
    'code' => $code,
    'booking' => [
        'name'     => $name,
        'date'     => $dateRaw,
        'weekday'  => (int) $date->format('N'),
        'day'      => (int) $date->format('j'),
        'month'    => (int) $date->format('n'),
        'month_it' => $monthName,
        'month_en' => $monthNameEn,
        'time'     => $time,
        'guests'   => $guests,
        'view'     => $view,
        'view_it'  => $viewLabel,
        'occasion' => $occasion,
        'sunset'   => $sunsetTime,
        'summary'  => $summaryIt,
        'notes'    => $notes,
        'ics'      => implode("\r\n", $icsLines) . "\r\n",
        'simulated'=> true,
    ],
    'message' => 'Tavolo riservato. Ti aspettiamo con il sole.',
], 201);
