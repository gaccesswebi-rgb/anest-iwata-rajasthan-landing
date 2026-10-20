<?php
// QuickAir thank-you page: fires generate_lead (no personal data) for GTM after a lead is saved by ../submit.php.
require __DIR__ . '/../inc/app.php';
$lead = (int)($_GET['lead'] ?? 0);
$pageType = 'thank_you';
$pageName = 'quickair';
$dlEvents = [];
if ($lead) {
    $st = db()->prepare("SELECT form, product, platform FROM leads WHERE id = ? AND site = 'quickair'");
    $st->execute([$lead]);
    if ($row = $st->fetch()) {
        $dlEvents[] = [
            'event' => 'generate_lead',
            'lead_id' => 'lead-' . $lead,           // use as Google Ads transaction ID (no double counting on refresh)
            'form_id' => $row['form'],
            'form_section' => 'Quote Section',
            'lead_product' => (string)$row['product'],
            'traffic_platform' => (string)$row['platform'],
        ];
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php include __DIR__ . '/../inc/gtag.php'; ?>
<title>Thank You | QuickAir Aluminium Piping | Ahuja Group</title>
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#152A4A">
<link rel="icon" href="assets/img/favicon.png">


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<!-- fonts load without blocking first paint -->
<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=IBM+Plex+Sans:wght@500;600&family=Roboto:wght@400;500;700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=IBM+Plex+Sans:wght@500;600&family=Roboto:wght@400;500;700&display=swap"></noscript>
<link rel="stylesheet" href="assets/css/style.css?v=6">
</head>
<body class="ty-page">
<?php include __DIR__ . '/../inc/gtm-body.php'; ?>

<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <symbol id="i-phone" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></symbol>
    <symbol id="i-mail" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></symbol>
    <symbol id="i-wa" viewBox="0 0 24 24"><path d="M3 21l1.65-4.6A9 9 0 1 1 8 19.9L3 21z"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5l1.2-1.4-1.9-1-1 .8a4 4 0 0 1-2.2-2.2l.8-1-1-1.9z"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14M13 5l7 7-7 7"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
  </defs>
</svg>

<!-- ============ ANNOUNCEMENT BAR ============ -->
<div class="topbar">
  <div class="container topbar-inner">
    <a href="tel:+919001848552" data-track="phone" data-place="topbar"><svg class="ic"><use href="#i-phone"/></svg><span>+91 90018 48552</span></a>
    <a href="mailto:projects1@ahujagroup.in" data-track="email" data-place="topbar"><svg class="ic"><use href="#i-mail"/></svg><span>projects1@ahujagroup.in</span></a>
  </div>
</div>

<header class="site-header">
  <div class="container header-inner">
    <a href="./" class="brand" aria-label="Ahuja Group home"><img src="assets/img/ahuja-logo.webp" alt="Ahuja Group" width="501" height="280"></a>
    <div class="header-actions">
      <a href="https://wa.me/919001848552?text=Hi%2C%20I%20just%20submitted%20a%20quotation%20request%20for%20aluminium%20compressed%20air%20piping" target="_blank" rel="noopener" class="wa-icon" aria-label="WhatsApp us" data-track="whatsapp" data-place="thankyou-header"><svg class="ic"><use href="#i-wa"/></svg></a>
    </div>
  </div>
</header>

<main class="ty" data-section="thank-you">
  <div class="container ty-card">
    <div class="ty-tick" aria-hidden="true"><svg class="ic"><use href="#i-check"/></svg></div>
    <h1>Thank you!</h1>
    <p class="ty-lead">Your request has been received. Our engineer will call you <b>within 1 working day</b> to understand your plant.</p>

    <div class="ty-next">
      <h2>What happens next</h2>
      <ol>
        <li><span>1</span><div><b>We call you</b>An engineer reviews your requirement and calls you back.</div></li>
        <li><span>2</span><div><b>Share your layout</b>A drawing, sketch or Excel list of machines helps us size the system. Send it on WhatsApp or email.</div></li>
        <li><span>3</span><div><b>Get your quotation</b>You receive a sized design and a detailed quotation.</div></li>
      </ol>
    </div>

    <p class="ty-urgent">Need to talk sooner?</p>
    <div class="ty-actions">
      <a href="tel:+919001848552" class="btn btn-red btn-lg" data-track="phone" data-place="thankyou"><svg class="ic"><use href="#i-phone"/></svg> Call +91 90018 48552</a>
      <a href="https://wa.me/919001848552?text=Hi%2C%20I%20just%20submitted%20a%20quotation%20request%20for%20aluminium%20compressed%20air%20piping" target="_blank" rel="noopener" class="btn btn-wa btn-lg" data-track="whatsapp" data-place="thankyou"><svg class="ic"><use href="#i-wa"/></svg> Send drawing on WhatsApp</a>
    </div>
    <a href="mailto:projects1@ahujagroup.in" class="ty-mail" data-track="email" data-place="thankyou"><svg class="ic"><use href="#i-mail"/></svg> projects1@ahujagroup.in</a>

    <a href="./" class="ty-back"><svg class="ic" style="transform:rotate(180deg)"><use href="#i-arrow"/></svg> Back to home</a>
  </div>
</main>

<footer class="site-footer">
  <div class="container footer-inner">
    <div>
      <a href="tel:+919001848552" data-track="phone" data-place="footer">+91 90018 48552</a>
      <p>© <span id="year">2026</span> · All rights reserved.</p>
      <p class="muted">QuickAir aluminium piping systems supplied and installed by Ahuja Group.</p>
      <p class="muted footer-legal"><a href="https://ahujagroup.in/privacy-policy/" target="_blank" rel="noopener">Privacy Policy</a> · <a href="https://ahujagroup.in/terms-of-use/" target="_blank" rel="noopener">Terms of Use</a></p>
    </div>
    <a href="mailto:projects1@ahujagroup.in" data-track="email" data-place="footer">projects1@ahujagroup.in</a>
  </div>
</footer>

<script src="assets/js/main.js?v=4" defer></script>
</body>
</html>
