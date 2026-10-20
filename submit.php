<?php
require __DIR__ . '/inc/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./');
    exit;
}

// Which landing page sent the form (each has its own error return and thank-you page).
$site = isset(SITES[$_POST['site'] ?? '']) ? $_POST['site'] : 'anest-iwata';

function back_with_error(string $msg): void
{
    global $site;
    if ($site === 'quickair') {
        header('Location: quickair/?err=' . rawurlencode($msg) . '#quote');
        exit;
    }
    $f = ($_POST['form'] ?? '') === 'hero' ? 'hero' : 'footer';
    header('Location: ./?err=' . rawurlencode($msg) . "&f=$f#$f");
    exit;
}

/** After-form destination: QuickAir's own thank-you page, or the main site's thank-you page (tagged) / local thank-you.php for Anest Iwata. */
function thank_you_url(int $leadId): string
{
    global $site;
    if ($site === 'quickair') return "quickair/thank-you.php?lead=$leadId";
    if (THANK_YOU_URL === '') return "thank-you.php?lead=$leadId";
    $qs = http_build_query(['source' => 'anest-iwata-lp', 'lead' => $leadId]);
    return THANK_YOU_URL . (strpos(THANK_YOU_URL, '?') === false ? '?' : '&') . $qs;
}

function field(string $k, int $max = 200): string
{
    return mb_substr(trim((string)($_POST[$k] ?? '')), 0, $max);
}

// Spam guards: hidden honeypot must stay empty, and humans take more than 3s to fill the form.
// Bots get the plain page (no ?source / lead tag) so they are never counted as a conversion.
if (field('website') !== '' || (time() - (int)field('ts')) < 3) {
    header('Location: ' . ($site === 'quickair' ? 'quickair/thank-you.php' : (THANK_YOU_URL ?: 'thank-you.php')));
    exit;
}

$name    = field('name', 80);
$phone   = preg_replace('/\D/', '', field('phone', 20));
$email   = field('email', 120);
$city    = field('city', 60);
$product = field('product', 120);
$message = field('message', 1000);
$form    = field('form', 20);
$company = field('company', 150);
if ($message === '' && field('cta') !== '') $message = 'Enquiry via: ' . field('cta', 100); // QuickAir CTA that opened the form

if (strlen($phone) === 12 && strpos($phone, '91') === 0) $phone = substr($phone, 2);
if (strlen($phone) === 11 && $phone[0] === '0') $phone = substr($phone, 1);

if (mb_strlen($name) < 2) back_with_error('Please enter your name.');
if (!preg_match('/^[6-9]\d{9}$/', $phone)) back_with_error('Please enter a valid 10-digit mobile number.');
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) back_with_error('Please enter a valid email address.');
if ($site === 'quickair' && ($company === '' || $email === '')) back_with_error('Please fill all fields correctly (company and a valid email are required).');

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
    'site' => $site, 'company' => $company,
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
header('Location: ' . thank_you_url((int)$pdo->lastInsertId()));
