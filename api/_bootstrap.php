<?php
/**
 * api/_bootstrap.php — helpers partagés par les endpoints JSON (simulation).
 * Ce n'est pas une page : uniquement du JSON.
 */
declare(strict_types=1);

$config = require dirname(__DIR__) . '/config.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

/** Réponse JSON normalisée puis fin de script. */
function json_out(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/** Erreur JSON avec code et message localisé. */
function json_error(string $field, string $message, int $status = 422): void
{
    json_out(['ok' => false, 'error' => ['field' => $field, 'message' => $message]], $status);
}

/** Refuse tout sauf POST. */
function require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        header('Allow: POST');
        json_error('method', 'Metodo non consentito.', 405);
    }
}

/** Lit le corps JSON (ou form-encoded) et le normalise. */
function read_body(): array
{
    $raw = file_get_contents('php://input') ?: '';
    if ($raw !== '' && str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'json')) {
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
    return is_array($_POST) ? $_POST : [];
}

/** Nettoie une chaîne utilisateur. */
function clean(mixed $value, int $max = 200): string
{
    $value = is_scalar($value) ? (string) $value : '';
    $value = strip_tags($value);
    $value = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $value) ?? '';
    return mb_substr(trim(preg_replace('/\s+/u', ' ', $value) ?? ''), 0, $max);
}

function is_valid_email(string $email): bool
{
    return $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function is_valid_phone(string $phone): bool
{
    $digits = preg_replace('/\D+/', '', $phone) ?? '';
    return strlen($digits) >= 8 && strlen($digits) <= 15;
}

/** Code simulation : LMN- + 4 chiffres (évite les collisions évidentes). */
function sim_code(string $prefix = 'LMN-'): string
{
    return $prefix . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
}
