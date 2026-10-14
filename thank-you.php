<?php
require __DIR__ . '/inc/app.php';
$lead = (int)($_GET['lead'] ?? 0);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Thank You | Ahuja Group – Anest Iwata Rajasthan</title>
<link rel="icon" href="assets/img/aim-logo.webp">
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=8">
<?php if ($lead): ?>
<script>
  // For GTM: trigger on Custom Event "generate_lead"; use lead_id as the Google Ads transaction ID (no double counting on refresh).
  window.dataLayer = window.dataLayer || [];
  dataLayer.push({event: 'generate_lead', lead_id: 'lead-<?= $lead ?>'});
</script>
<?php endif; ?>
<?php include __DIR__ . '/inc/gtag.php'; ?>
<?php if (GADS_ID && GADS_LEAD_LABEL && $lead): ?>
<script>gtag('event', 'conversion', {send_to: '<?= h(GADS_ID . '/' . GADS_LEAD_LABEL) ?>', transaction_id: 'lead-<?= $lead ?>'});</script>
<?php endif; ?>
<style>
  .ty { min-height: 100vh; display: grid; place-items: center; text-align: center; padding: 40px 16px; background: var(--soft); }
  .ty-card { max-width: 520px; background: #fff; border: 1px solid var(--line); border-radius: var(--radius); box-shadow: var(--shadow); padding: 40px 28px; }
  .ty-tick { width: 64px; height: 64px; margin: 0 auto 18px; border-radius: 50%; background: var(--wa); color: #fff; display: grid; place-items: center; font-size: 34px; }
  .ty h1 { font-size: 40px; text-transform: uppercase; margin-bottom: 10px; }
  .ty p { color: var(--steel); }
  .ty .cta-btns { justify-content: center; }
</style>
</head>
<body>
<main class="ty">
    <div class="ty-card">
        <div class="ty-tick" aria-hidden="true">✓</div>
        <h1>Thank You!</h1>
        <p>We've received your enquiry. Our engineer will call you within 2 hours.</p>
        <div class="cta-btns">
            <a class="btn btn-red" href="tel:+<?= SITE_PHONE_RAW ?>" data-track="call" data-section="Thank-you Page">Call <?= SITE_PHONE ?></a>
            <a class="btn btn-wa" href="https://wa.me/<?= SITE_PHONE_RAW ?>" target="_blank" rel="noopener" data-track="wa" data-section="Thank-you Page">WhatsApp</a>
        </div>
        <p style="margin-top:22px"><a href="./">← Back to products</a></p>
    </div>
</main>
<script src="assets/js/main.js?v=5"></script>
</body>
</html>
