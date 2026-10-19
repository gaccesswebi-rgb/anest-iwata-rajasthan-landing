<?php
require_once __DIR__ . '/../config.php';

// Tracking params captured from the ad click URL (see README for the Google Ads URL suffix).
const TRACK_FIELDS = [
    'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
    'campaignid', 'adgroupid', 'keyword', 'matchtype', 'device', 'network',
    'gclid', 'gbraid', 'wbraid', 'fbclid', 'msclkid',
    'landing_url', 'referrer',
];

// Sales pipeline, in order. Shown as the status dropdown in the CRM.
const LEAD_STATUSES = [
    'New', 'Contacted', 'Not Reachable', 'Following Up', 'Site Visit Scheduled',
    'Quotation Sent', 'Negotiation', 'Won', 'Lost', 'Not Interested', 'Wrong Lead', 'Duplicate / Spam',
];
// Closed without a sale (counted as "Lost / Wrong" in reports).
const BAD_STATUSES = ['Lost', 'Not Interested', 'Wrong Lead', 'Duplicate / Spam'];

/** CSS-safe slug for a status, e.g. "Following Up" -> "following-up". */
function status_slug(string $s): string
{
    return trim(preg_replace('/[^a-z]+/', '-', strtolower($s)), '-');
}

const PRODUCTS = [
    ['cat' => 'oilfree recip', 'name' => 'Oil Free Reciprocating Air Compressor',              'img' => 'p-oilfree.webp',       'specs' => ['1 HP to 15 HP', '8.5 Bar']],
    ['cat' => 'vacuum',        'name' => 'Rotary Vane Vacuum Pump',                            'img' => 'p-rotary-vane.webp',   'specs' => ['1 HP to 20 HP', '26 to 624 m³/h']],
    ['cat' => 'vacuum',        'name' => 'Reciprocating Vacuum Pump',                          'img' => 'p-recip-vacuum.webp',  'specs' => ['2 HP to 10 HP', '35 to 152.6 CFM']],
    ['cat' => 'recip',         'name' => 'Lubricated Reciprocating Air Compressor',            'img' => 'p-lubricated.webp',    'specs' => ['1 HP to 20 HP', '7 to 12 Bar']],
    ['cat' => 'recip',         'name' => 'High Pressure Reciprocating Lubricated Compressor',  'img' => 'p-high-pressure.webp', 'specs' => ['15 HP to 20 HP', '30 Bar']],
    ['cat' => 'recip',         'name' => 'Crown Series Reciprocating Compressor',              'img' => 'p-crown.webp',         'specs' => ['1 HP to 3 HP', '10 kg/cm² working pressure', '4.5 to 13.4 CFM', '40 to 160 Ltr tank']],
    ['cat' => 'screw',         'name' => 'LRST Series Compact Screw Air Compressor',           'img' => 'p-lrst.webp',          'specs' => ['10 HP to 20 HP', '8 to 10 Bar', '14 to 81 CFM']],
    ['cat' => 'screw',         'name' => 'Premium Efficiency EPM Series Screw Air Compressor', 'img' => 'p-epm.webp',           'specs' => ['25 HP to 220 HP', '7 to 10 Bar', '131 to 954 CFM']],
    ['cat' => 'screw',         'name' => 'High Efficiency APM Screw Air Compressor',           'img' => 'p-apm.webp',           'specs' => ['10 HP to 100 HP', '7 to 16 Bar', '41 to 471 CFM']],
    ['cat' => 'screw',         'name' => 'C-Air Screw Air Compressor',                         'img' => 'p-cair.webp',          'specs' => ['7.5 HP to 50 HP', '7 to 12.5 Bar', '23 to 235 CFM', '5-Year Extended Warranty (AIM5)']],
];

const EXTRA_REQUIREMENTS = ['AMC / Service', 'Spare Parts', 'Not sure – need expert advice'];

function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function db(): PDO
{
    static $pdo;
    if ($pdo) return $pdo;
    if (!is_dir(dirname(DB_PATH))) mkdir(dirname(DB_PATH), 0750, true);
    $pdo = new PDO('sqlite:' . DB_PATH, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $track = implode(' TEXT, ', TRACK_FIELDS) . ' TEXT';
    $pdo->exec("CREATE TABLE IF NOT EXISTS leads (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        created_at TEXT NOT NULL,
        updated_at TEXT,
        name TEXT NOT NULL, phone TEXT NOT NULL, email TEXT, city TEXT,
        product TEXT, message TEXT, form TEXT,
        status TEXT NOT NULL DEFAULT 'New', notes TEXT,
        platform TEXT, $track,
        ip TEXT, user_agent TEXT
    )");
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_leads_created ON leads(created_at)');
    // Call / WhatsApp button clicks (we can't see the caller's number, so we count clicks + where they happened).
    $pdo->exec("CREATE TABLE IF NOT EXISTS clicks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        created_at TEXT NOT NULL,
        type TEXT NOT NULL, section TEXT, product TEXT,
        platform TEXT, $track,
        ip TEXT, user_agent TEXT
    )");
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_clicks_created ON clicks(created_at)');
    // Status history: when each lead reached each status (conversion time for Google Ads offline import).
    $pdo->exec('CREATE TABLE IF NOT EXISTS lead_events (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        lead_id INTEGER NOT NULL, status TEXT NOT NULL, created_at TEXT NOT NULL
    )');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_events_lead ON lead_events(lead_id)');
    return $pdo;
}

/** Human label for where the lead came from: "Google Ads", "Meta Ads", "Organic Search (google)", "Direct"... */
function detect_platform(array $t): string
{
    if (!empty($t['gclid']) || !empty($t['gbraid']) || !empty($t['wbraid'])) return 'Google Ads';
    if (!empty($t['msclkid'])) return 'Microsoft Ads';

    $src = strtolower(trim($t['utm_source'] ?? ''));
    $med = strtolower(trim($t['utm_medium'] ?? ''));
    $paid = in_array($med, ['cpc', 'ppc', 'paid', 'paidsocial', 'paid_social', 'paid-social', 'cpm', 'display', 'ads', 'ad'], true);
    $names = [
        'google' => 'Google', 'adwords' => 'Google', 'youtube' => 'YouTube',
        'facebook' => 'Meta', 'fb' => 'Meta', 'instagram' => 'Meta', 'ig' => 'Meta', 'meta' => 'Meta',
        'bing' => 'Microsoft', 'microsoft' => 'Microsoft', 'linkedin' => 'LinkedIn',
        'indiamart' => 'IndiaMART', 'justdial' => 'Justdial',
    ];
    if ($src !== '') {
        $name = $names[$src] ?? ucfirst($src);
        return $paid ? "$name Ads" : $name . ' (' . ($med ?: 'referral') . ')';
    }
    if (!empty($t['fbclid'])) return 'Meta (organic/social)';

    $host = strtolower((string)parse_url($t['referrer'] ?? '', PHP_URL_HOST));
    $host = preg_replace('/^www\./', '', $host);
    if ($host === '') return 'Direct';
    if (preg_match('/(^|\.)(google|bing|yahoo|duckduckgo|ecosia)\./', $host, $m)) return "Organic Search ({$m[2]})";
    if (preg_match('/(facebook|instagram)\.com$/', $host)) return 'Meta (organic/social)';
    return "Referral ($host)";
}

/** Campaign label shown in CRM: utm_campaign, else Google Ads campaign ID. */
function campaign_label(array $lead): string
{
    if (!empty($lead['utm_campaign'])) return $lead['utm_campaign'];
    if (!empty($lead['campaignid'])) return 'Campaign ID ' . $lead['campaignid'];
    return '';
}

/** Leads that reached an OCI_STAGES status in the last $days days (first time each stage was reached). */
function oci_conversions(PDO $pdo, int $days = 90): array
{
    $stages = array_keys(OCI_STAGES);
    $in = rtrim(str_repeat('?,', count($stages)), ',');
    $st = $pdo->prepare("SELECT l.id AS lead_id, l.name, l.platform, l.gclid, l.gbraid, l.wbraid, e.status, MIN(e.created_at) AS conv_time
        FROM lead_events e JOIN leads l ON l.id = e.lead_id
        WHERE e.status IN ($in) AND e.created_at >= ?
        GROUP BY e.lead_id, e.status ORDER BY conv_time DESC");
    $st->execute(array_merge($stages, [date('Y-m-d H:i:s', time() - $days * 86400)]));
    return $st->fetchAll();
}

/** Requirement dropdown: LRST, EPM and APM are offered as one "Screw Air Compressor" choice. */
function form_requirements(): array
{
    $out = [];
    foreach (PRODUCTS as $p) {
        if (preg_match('/^(LRST|Premium Efficiency EPM|High Efficiency APM)\b/', $p['name'])) {
            if (!in_array('Screw Air Compressor', $out, true)) $out[] = 'Screw Air Compressor';
            continue;
        }
        $out[] = $p['name'];
    }
    return $out;
}
