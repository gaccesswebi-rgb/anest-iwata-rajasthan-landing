<?php
// Receives navigator.sendBeacon() from Call / WhatsApp buttons and logs the click.
require __DIR__ . '/inc/app.php';

http_response_code(204);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$val = fn(string $k, int $max) => mb_substr(trim((string)($_POST[$k] ?? '')), 0, $max);

$type = $val('type', 20);
if (!in_array($type, ['call', 'whatsapp'], true)) exit;

$track = [];
foreach (TRACK_FIELDS as $k) $track[$k] = $val($k, $k === 'landing_url' || $k === 'referrer' ? 1000 : 255);

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$pdo = db();

// Ignore floods: max 30 clicks per IP per 10 minutes.
$st = $pdo->prepare('SELECT COUNT(*) FROM clicks WHERE ip = ? AND created_at > ?');
$st->execute([$ip, date('Y-m-d H:i:s', time() - 600)]);
if ($st->fetchColumn() >= 30) exit;

$row = [
    'created_at' => date('Y-m-d H:i:s'),
    'type' => $type,
    'section' => $val('section', 40),
    'product' => $val('product', 120),
    'platform' => detect_platform($track),
] + $track + [
    'ip' => $ip,
    'user_agent' => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 300),
];
$cols = array_keys($row);
$pdo->prepare('INSERT INTO clicks (' . implode(',', $cols) . ') VALUES (' . rtrim(str_repeat('?,', count($cols)), ',') . ')')
    ->execute(array_values($row));
