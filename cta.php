<?php
// Receives navigator.sendBeacon() for visitor actions (CTA_TYPES: call, whatsapp, get_quote, form_start, video_play, email) from every landing page (SITES) and logs them.
require __DIR__ . '/inc/app.php';

http_response_code(204);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$val = fn(string $k, int $max) => mb_substr(trim((string)($_POST[$k] ?? '')), 0, $max);

$type = $val('type', 20);
if (!isset(CTA_TYPES[$type])) exit;

$track = [];
foreach (TRACK_FIELDS as $k) $track[$k] = $val($k, $k === 'landing_url' || $k === 'referrer' ? 1000 : 255);

$ip = client_ip();
$pdo = db();

// Ignore floods: max 60 events per IP per 10 minutes.
$st = $pdo->prepare('SELECT COUNT(*) FROM clicks WHERE ip = ? AND created_at > ?');
$st->execute([$ip, date('Y-m-d H:i:s', time() - 600)]);
if ($st->fetchColumn() >= 60) exit;

$row = [
    'created_at' => date('Y-m-d H:i:s'),
    'type' => $type,
    'site' => isset(SITES[$_POST['site'] ?? '']) ? $_POST['site'] : 'anest-iwata',
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
