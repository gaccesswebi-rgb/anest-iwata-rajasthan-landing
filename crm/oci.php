<?php
// Google Ads offline conversion feed (CSV in Google's upload format).
// Download it from the CRM, or let Google Ads fetch it daily: Uploads > Schedules > Source: HTTPS, with OCI_FEED_USER / OCI_FEED_PASSWORD.
require __DIR__ . '/auth.php';

if (empty($_SESSION['crm_user'])) {
    $u = $_SERVER['PHP_AUTH_USER'] ?? null;
    $pw = $_SERVER['PHP_AUTH_PW'] ?? '';
    $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if ($u === null && stripos($hdr, 'basic ') === 0) {
        [$u, $pw] = array_pad(explode(':', (string)base64_decode(substr($hdr, 6)), 2), 2, '');
    }
    $ok = strlen(OCI_FEED_PASSWORD) >= 16 && $u !== null
        && hash_equals(OCI_FEED_USER, (string)$u) && hash_equals(OCI_FEED_PASSWORD, (string)$pw);
    if (!$ok) {
        if ($u !== null) sleep(2); // slows down password guessing
        header('WWW-Authenticate: Basic realm="Google Ads feed"');
        http_response_code(401);
        exit('Login required');
    }
}

header('Content-Type: text/csv; charset=utf-8');
if (isset($_GET['download'])) header('Content-Disposition: attachment; filename="google-ads-offline-conversions-' . date('Y-m-d') . '.csv"');

$out = fopen('php://output', 'w');
fwrite($out, "Parameters:TimeZone=+0530\n");
fputcsv($out, ['Google Click ID', 'Conversion Name', 'Conversion Time', 'Conversion Value', 'Conversion Currency'], ',', '"', '');
foreach (oci_conversions(db()) as $r) {
    if ($r['gclid'] === '' || $r['gclid'] === null) continue; // only GCLID clicks can be matched by file upload
    $stage = OCI_STAGES[$r['status']];
    fputcsv($out, [$r['gclid'], $stage['name'], $r['conv_time'], $stage['value'] ?: '', $stage['value'] ? 'INR' : ''], ',', '"', '');
}
