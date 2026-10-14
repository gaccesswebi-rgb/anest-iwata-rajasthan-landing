<?php
require __DIR__ . '/inc/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./');
    exit;
}

function back_with_error(string $msg): void
{
    $f = ($_POST['form'] ?? '') === 'hero' ? 'hero' : 'footer';
    header('Location: ./?err=' . rawurlencode($msg) . "&f=$f#$f");
    exit;
}

function field(string $k, int $max = 200): string
{
    return mb_substr(trim((string)($_POST[$k] ?? '')), 0, $max);
}

// Spam guards: hidden honeypot must stay empty, and humans take more than 3s to fill the form.
if (field('website') !== '' || (time() - (int)field('ts')) < 3) {
    header('Location: thank-you.php');
    exit;
}

$name    = field('name', 80);
$phone   = preg_replace('/\D/', '', field('phone', 20));
$email   = field('email', 120);
$city    = field('city', 60);
$product = field('product', 120);
$message = field('message', 1000);
$form    = field('form', 20);

if (strlen($phone) === 12 && strpos($phone, '91') === 0) $phone = substr($phone, 2);
if (strlen($phone) === 11 && $phone[0] === '0') $phone = substr($phone, 1);

if (mb_strlen($name) < 2) back_with_error('Please enter your name.');
if (!preg_match('/^[6-9]\d{9}$/', $phone)) back_with_error('Please enter a valid 10-digit mobile number.');
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) back_with_error('Please enter a valid email address.');

$track = [];
foreach (TRACK_FIELDS as $k) $track[$k] = field($k, $k === 'landing_url' || $k === 'referrer' ? 1000 : 255);

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$pdo = db();

// Rate limit: max 5 enquiries per IP per 10 minutes.
$st = $pdo->prepare('SELECT COUNT(*) FROM leads WHERE ip = ? AND created_at > ?');
$st->execute([$ip, date('Y-m-d H:i:s', time() - 600)]);
if ($st->fetchColumn() >= 5) back_with_error('Too many enquiries. Please call us instead.');

$row = [
    'created_at' => date('Y-m-d H:i:s'),
    'name' => $name, 'phone' => $phone, 'email' => $email, 'city' => $city,
    'product' => $product, 'message' => $message, 'form' => $form,
    'status' => 'New',
    'platform' => detect_platform($track),
] + $track + [
    'ip' => $ip,
    'user_agent' => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 300),
];

$cols = array_keys($row);
$pdo->prepare('INSERT INTO leads (' . implode(',', $cols) . ') VALUES (' . rtrim(str_repeat('?,', count($cols)), ',') . ')')
    ->execute(array_values($row));

// lead id doubles as Google Ads transaction_id so a page refresh doesn't count twice.
header('Location: thank-you.php?lead=' . $pdo->lastInsertId());
