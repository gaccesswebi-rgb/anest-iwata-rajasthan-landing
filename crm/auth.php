<?php
require_once __DIR__ . '/../inc/app.php';

session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict', 'secure' => !empty($_SERVER['HTTPS'])]);
session_name('aw_crm');
session_start();
header('X-Frame-Options: DENY');
header('X-Robots-Tag: noindex, nofollow');

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}

function check_csrf(): void
{
    if (!hash_equals($_SESSION['csrf'], (string)($_POST['csrf'] ?? ''))) {
        http_response_code(400);
        exit('Session expired. Go back and reload the page.');
    }
}

function require_login(): void
{
    if (empty($_SESSION['crm_user'])) {
        header('Location: login.php');
        exit;
    }
}

function crm_header(string $title): void
{ ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= h($title) ?> · Leads CRM</title>
<link rel="stylesheet" href="crm.css?v=5">
</head>
<body>
<?php if (!empty($_SESSION['crm_user'])): ?>
<header class="bar">
    <a class="logo" href="./">Leads CRM <small>Anest Iwata · Rajasthan</small></a>
    <nav><a href="./">Leads</a><a href="./#campaigns">Campaigns</a><a href="clicks.php">Call &amp; WhatsApp Clicks</a><a href="google-ads.php">Google Ads Sync</a><a href="logout.php">Logout</a></nav>
</header>
<?php endif; ?>
<main class="page">
<?php }

function crm_footer(): void
{
    echo "</main>\n</body>\n</html>";
}

// SQL twin of campaign_label() so we can filter and group by it.
const CAMPAIGN_SQL = "COALESCE(NULLIF(utm_campaign, ''), CASE WHEN campaignid <> '' THEN 'Campaign ID ' || campaignid END, '')";

/** WHERE clause + params built from the filter query string (shared by list and CSV export). */
function lead_filters(array $q): array
{
    $w = [];
    $p = [];
    if (($s = trim($q['q'] ?? '')) !== '') {
        $w[] = '(name LIKE ? OR phone LIKE ? OR city LIKE ? OR email LIKE ? OR keyword LIKE ? OR utm_term LIKE ?)';
        array_push($p, ...array_fill(0, 6, "%$s%"));
    }
    if (!empty($q['status']))   { $w[] = 'status = ?';   $p[] = $q['status']; }
    if (!empty($q['platform'])) { $w[] = 'platform = ?'; $p[] = $q['platform']; }
    if (isset($q['campaign']) && $q['campaign'] !== '') { $w[] = CAMPAIGN_SQL . ' = ?'; $p[] = $q['campaign']; }
    if (!empty($q['from']))     { $w[] = 'created_at >= ?'; $p[] = $q['from'] . ' 00:00:00'; }
    if (!empty($q['to']))       { $w[] = 'created_at <= ?'; $p[] = $q['to'] . ' 23:59:59'; }
    return [$w ? 'WHERE ' . implode(' AND ', $w) : '', $p];
}

function badge(string $status): string
{
    return '<span class="badge st-' . status_slug($status) . '">' . h($status) . '</span>';
}

function platform_badge(string $p): string
{
    $cls = stripos($p, 'google') !== false ? 'p-google' : (stripos($p, 'meta') !== false ? 'p-meta' : (stripos($p, 'ads') !== false ? 'p-ads' : 'p-other'));
    return '<span class="plat ' . $cls . '">' . h($p) . '</span>';
}
