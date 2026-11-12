<?php
/**
 * api/order.php — commande simulée.
 * POST { mode: "delivery"|"pickup", items: [{slug, name, price, qty}], customer:{...}, payment }
 * Réponse : { ok, code: "LMN-XXXX", totals, eta, steps }
 */
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';

require_post();

$body    = read_body();
$mode    = (string) ($body['mode'] ?? 'delivery');
$items   = $body['items'] ?? [];
$customer = is_array($body['customer'] ?? null) ? $body['customer'] : [];
$payment = (string) ($body['payment'] ?? 'carta');

if (!in_array($mode, ['delivery', 'pickup'], true)) {
    json_error('mode', 'Scegli consegna o asporto.');
}
if (!is_array($items) || $items === []) {
    json_error('items', 'La cassetta è vuota.');
}
if (count($items) > 40) {
    json_error('items', 'Troppi articoli: chiamaci, ci organizziamo.');
}
if (!in_array($payment, ['carta', 'satispay', 'contanti'], true)) {
    $payment = 'carta';
}

/* Reconstruit les prix côté serveur depuis config.php : le client ne fait pas le prix. */
$catalog = [];
foreach ($config['menu'] as $category) {
    foreach ($category['items'] as $item) {
        $catalog[$item['slug']] = ['name' => $item['name']['it'], 'price' => $item['price']];
    }
}
$giroSlug = 'giro-della-costiera';
$giroName = 'Il Giro della Costiera';

$cleanItems = [];
$subtotal = 0;

foreach ($items as $raw) {
    if (!is_array($raw)) {
        continue;
    }
    $slug = clean($raw['slug'] ?? '', 60);
    $qty  = (int) ($raw['qty'] ?? 0);

    if ($slug === $giroSlug) {
        $qty = 1; // menu dégustation : un seul article, jamais dupliqué
        $price = $config['giro']['bundle_price'];
        $name = $giroName;
    } elseif (isset($catalog[$slug])) {
        $price = $catalog[$slug]['price'];
        $name = $catalog[$slug]['name'];
    } else {
        continue;
    }

    $qty = max(1, min($qty, 20));
    $line = $price * $qty;
    $subtotal += $line;
    $cleanItems[] = ['slug' => $slug, 'name' => $name, 'price' => $price, 'qty' => $qty, 'line' => $line];
}

if ($cleanItems === []) {
    json_error('items', 'Nessun articolo valido nella cassetta.');
}

/* Coordonnées */
$name  = clean($customer['name'] ?? '', 80);
$phone = clean($customer['phone'] ?? '', 30);
$email = clean($customer['email'] ?? '', 120);
$address = clean($customer['address'] ?? '', 160);

$errors = [];
if (mb_strlen($name) < 2) {
    $errors['name'] = 'Ci serve un nome per l\'ordine.';
}
if (!is_valid_phone($phone)) {
    $errors['phone'] = 'Ci serve un numero valido per chiamarti.';
}
if (!is_valid_email($email)) {
    $errors['email'] = 'Controlla l\'email: ci mandiamo la conferma.';
}
if ($mode === 'delivery' && mb_strlen($address) < 5) {
    $errors['address'] = 'Ci serve un indirizzo per portarti il sole.';
}

$fee = 0;
if ($mode === 'delivery') {
    if ($subtotal < $config['delivery']['minimum']) {
        $errors['minimum'] = 'Per la consegna servono almeno € ' . $config['delivery']['minimum'] . '.';
    }
    $fee = $subtotal >= $config['delivery']['free_from'] ? 0 : $config['delivery']['fee'];
}

if ($errors !== []) {
    json_out(['ok' => false, 'errors' => $errors], 422);
}

$total = $subtotal + $fee;
$code  = sim_code('LMN-');

json_out([
    'ok'   => true,
    'code' => $code,
    'mode' => $mode,
    'items' => array_map(static function (array $i) use ($config): array {
        return [
            'slug'  => $i['slug'],
            'name'  => $i['name'],
            'price' => $i['price'],
            'qty'   => $i['qty'],
            'line'  => $i['line'],
            // Le Giro est un menu composé : pas de photo, le client dessine la carte.
            'img'   => $i['slug'] === 'giro-della-costiera' ? null : 'assets/img/piatti/' . $i['slug'],
        ];
    }, $cleanItems),
    'totals' => [
        'subtotal' => $subtotal,
        'fee'      => $fee,
        'total'    => $total,
    ],
    'payment' => $payment,
    'eta'     => $mode === 'delivery' ? $config['delivery']['eta'] : $config['delivery']['pickup_eta'],
    'steps'   => $mode === 'delivery'
        ? ['Ricevuto', 'In cucina', 'Impacchettato', 'In rotta']
        : ['Ricevuto', 'In cucina', 'Impacchettato', 'Pronto al molo'],
    'simulated' => true,
    'message' => 'Ordine ricevuto. Il gozzo è già in acqua.',
], 201);
