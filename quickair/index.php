<?php
// QuickAir landing page – shares the CRM, GTM container and dataLayer events with the Anest Iwata page.
require __DIR__ . '/../inc/app.php';
$pageName = 'quickair';
$err = isset($_GET['err']) ? mb_substr($_GET['err'], 0, 160) : '';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php include __DIR__ . '/../inc/gtag.php'; ?>
<meta name="robots" content="noindex, nofollow">
<title>Ahuja Modular Aluminium Compressed Air Piping System | Ahuja Group</title>
<meta name="description" content="QuickAir modular aluminium compressed air piping, designed, supplied and installed across India by Ahuja Group. Leak-proof, corrosion-free, 10-year warranty.">
<meta name="theme-color" content="#152A4A">
<link rel="icon" href="assets/img/favicon.png">


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<!-- fonts load without blocking first paint -->
<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=IBM+Plex+Sans:wght@500;600&family=Roboto:wght@400;500;700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=IBM+Plex+Sans:wght@500;600&family=Roboto:wght@400;500;700&display=swap"></noscript>
<link rel="preload" as="image" href="assets/img/hero.webp">
<link rel="stylesheet" href="assets/css/style.css?v=7">
</head>
<body>
<?php include __DIR__ . '/../inc/gtm-body.php'; ?>

<!-- Icon sprite -->
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <symbol id="i-phone" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></symbol>
    <symbol id="i-mail" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></symbol>
    <symbol id="i-wa" viewBox="0 0 24 24"><path d="M3 21l1.65-4.6A9 9 0 1 1 8 19.9L3 21z"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5l1.2-1.4-1.9-1-1 .8a4 4 0 0 1-2.2-2.2l.8-1-1-1.9z"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14M13 5l7 7-7 7"/></symbol>
    <symbol id="i-zap" viewBox="0 0 24 24"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></symbol>
    <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></symbol>
    <symbol id="i-lock" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></symbol>
    <symbol id="i-layers" viewBox="0 0 24 24"><path d="m12 2 10 5-10 5L2 7l10-5z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></symbol>
    <symbol id="i-down" viewBox="0 0 24 24"><path d="m22 17-8.5-8.5-5 5L2 7"/><path d="M16 17h6v-6"/></symbol>
    <symbol id="i-award" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.5 12.9 17 22l-5-3-5 3 1.5-9.1"/></symbol>
    <symbol id="i-wind" viewBox="0 0 24 24"><path d="M17.7 7.7a2.5 2.5 0 1 1 1.8 4.3H2"/><path d="M9.6 4.6A2 2 0 1 1 11 8H2"/><path d="M12.6 19.4A2 2 0 1 0 14 16H2"/></symbol>
    <symbol id="i-link" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></symbol>
    <symbol id="i-recycle" viewBox="0 0 24 24"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 16h5v5"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
    <symbol id="i-users" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></symbol>
    <symbol id="i-ban" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m4.9 4.9 14.2 14.2"/></symbol>
    <symbol id="i-pin" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></symbol>
    <symbol id="i-cal" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></symbol>
    <symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol>
    <symbol id="i-xc" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></symbol>
    <symbol id="i-up" viewBox="0 0 24 24"><path d="m18 15-6-6-6 6"/></symbol>
    <symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></symbol>
  </defs>
</svg>

<!-- ============ ANNOUNCEMENT BAR ============ -->
<div class="sticky-head"><!-- top bar + header stay visible while scrolling -->
<div class="topbar">
  <div class="container topbar-inner">
    <a href="tel:+919001848552" data-track="phone" data-place="topbar"><svg class="ic"><use href="#i-phone"/></svg><span>+91 90018 48552</span></a>
    <a href="mailto:projects1@ahujagroup.in" data-track="email" data-place="topbar"><svg class="ic"><use href="#i-mail"/></svg><span>projects1@ahujagroup.in</span></a>
  </div>
</div>

<!-- ============ HEADER ============ -->
<header class="site-header" id="top">
  <div class="container header-inner">
    <a href="#top" class="brand" aria-label="Ahuja Group home"><img src="assets/img/ahuja-logo.webp" alt="Ahuja Group" width="501" height="280"></a>
    <div class="header-actions">
      <a href="https://wa.me/919001848552?text=Hi%2C%20I%27m%20interested%20in%20aluminium%20compressed%20air%20piping" target="_blank" rel="noopener" class="wa-icon" aria-label="WhatsApp us" data-track="whatsapp" data-place="header"><svg class="ic"><use href="#i-wa"/></svg></a>
      <button class="menu-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="main-nav"><svg class="ic"><use href="#i-menu"/></svg></button>
    </div>
    <nav class="main-nav" id="main-nav" aria-label="Main">
      <a href="#products">Products</a>
      <a href="#specs">Specification</a>
      <a href="#compare">Compare</a>
      <a href="#benefits">Benefits</a>
      <a href="#projects">Past Projects</a>
      <a href="#clients">Clients</a>
      <a href="#process">Process</a>
      <a href="#faq">FAQ</a>
      <div class="nav-actions">
        <a href="https://wa.me/919001848552?text=Hi%2C%20I%27m%20interested%20in%20aluminium%20compressed%20air%20piping" target="_blank" rel="noopener" class="btn btn-wa" data-track="whatsapp" data-place="menu"><svg class="ic"><use href="#i-wa"/></svg> WhatsApp</a>
        <a href="#quote" class="btn btn-red" data-cta="Speak to an Engineer">Speak to an Engineer</a>
      </div>
    </nav>
  </div>
</header>
</div>

<main>

<!-- ============ HERO ============ -->
<section class="hero" data-section="hero">
  <div class="container hero-grid">
    <div class="hero-copy">
      <h1>Ahuja Modular Aluminium Compressed Air Piping System</h1>
      <p class="eyebrow-light">Designed, supplied &amp; installed across India</p>
      <p class="lead">Corrosion-resistant and leak-proof. Modular and easy to install. The professional choice for modern manufacturing plants.</p>
      <ul class="hero-specs">
        <li><svg class="ic"><use href="#i-check"/></svg><span><b>Working pressure</b> 16 bar (40 bar high-pressure option)</span></li>
        <li><svg class="ic"><use href="#i-check"/></svg><span><b>Size range</b> 20 mm (¾") to 290 mm (12")</span></li>
        <li><svg class="ic"><use href="#i-check"/></svg><span><b>25-year life</b> | 10-year warranty</span></li>
        <li><svg class="ic"><use href="#i-check"/></svg><span><b>Temperature</b> −20 °C to 90 °C</span></li>
        <li><svg class="ic"><use href="#i-check"/></svg><span><b>6063-T5 aluminium alloy</b> with blue powder coating</span></li>
      </ul>
      <div class="hero-ctas">
        <a href="#quote" class="btn btn-red btn-lg" data-cta="Speak to an Engineer">Speak to an Engineer <svg class="ic"><use href="#i-arrow"/></svg></a>
      </div>
      <p class="trust-micro"><span class="stars" aria-label="5 stars">★★★★★</span> Certified &amp; trusted across India <span class="sep">|</span> 10-year comprehensive warranty included</p>
    </div>
    <div class="hero-media">
      <img src="assets/img/hero.webp" alt="QuickAir blue aluminium compressed air piping installed in a compressor room" width="1100" height="825" fetchpriority="high">
      <img src="assets/img/warranty-badge.webp" alt="10 year warranty" class="hero-badge" width="160" height="160">
    </div>
    <ul class="trust-badges">
      <li><svg class="ic"><use href="#i-cal"/></svg><div><b>55+ Years</b><span>in operation</span></div></li>
      <li><svg class="ic"><use href="#i-pin"/></svg><div><b>11+ States</b><span>projects across India</span></div></li>
      <li><svg class="ic"><use href="#i-users"/></svg><div><b>33+ Engineers</b><span>in our team</span></div></li>
      <li><svg class="ic"><use href="#i-award"/></svg><div><b>ISO 9001:2015</b><span>certified</span></div></li>
    </ul>
  </div>
</section>

<!-- ============ CLIENT LOGO STRIP ============ -->
<section class="logo-strip" data-section="logo-strip" aria-labelledby="trusted-title">
  <div class="container">
    <p class="strip-title" id="trusted-title">Trusted by leading manufacturers across India</p>
    <div class="auto-row">
      <span class="auto-label">Automotive</span>
      <ul class="logo-grid logo-grid-auto">
        <li><img src="assets/img/logos/maruti-suzuki.webp" style="--h:30px" alt="Maruti Suzuki" loading="lazy"></li>
        <li><img src="assets/img/logos/tvs.svg" style="--h:34px" alt="TVS" loading="lazy"></li>
        <li><img src="assets/img/logos/ve-commercial.webp" style="--h:36px" alt="VE Commercial Vehicles" loading="lazy"></li>
        <li class="break" aria-hidden="true"></li>
        <li><img src="assets/img/logos/opmobility.webp" style="--h:46px" alt="OPmobility" loading="lazy"></li>
        <li><img src="assets/img/logos/eka.webp" style="--h:44px" alt="EKA Mobility" loading="lazy"></li>
      </ul>
    </div>
    <ul class="logo-grid logo-grid-main">
      <li><img src="assets/img/logos/bvg.webp" style="--h:60px" alt="BVG India" loading="lazy"></li>
      <li><img src="assets/img/logos/dcm-nouvelle.webp" style="--h:44px" alt="DCM Nouvelle" loading="lazy"></li>
      <li><img src="assets/img/logos/epack-durable.webp" style="--h:52px" alt="Epack Durable" loading="lazy"></li>
      <li><img src="assets/img/logos/rajasthan-patrika.webp" style="--h:34px" alt="Rajasthan Patrika" loading="lazy"></li>
      <li class="break" aria-hidden="true"></li>
      <li><img src="assets/img/logos/genus.webp" style="--h:50px" alt="Genus" loading="lazy"></li>
      <li><img src="assets/img/logos/uttam.webp" style="--h:40px" alt="Uttam" loading="lazy"></li>
      <li><img src="assets/img/logos/paras.webp" style="--h:40px" alt="Paras" loading="lazy"></li>
      <li><img src="assets/img/logos/oswal-cables.webp" style="--h:58px" alt="Oswal Cables" loading="lazy"></li>
    </ul>
  </div>
</section>

<!-- ============ NUMBERS BAR ============ -->
<section class="numbers" data-section="numbers" aria-label="QuickAir by the numbers">
  <div class="container numbers-grid">
    <div class="num"><b>25+ Years</b><span>Expected pipeline lifespan</span></div>
    <div class="num"><b>70% Faster</b><span>Installation vs steel / copper</span></div>
    <div class="num"><b>10-Year</b><span>Full manufacturing warranty</span></div>
    <div class="num"><b>100%</b><span>Corrosion-free &amp; leak-proof</span></div>
  </div>
</section>

<!-- ============ QUOTATION FORM ============ -->
<section class="quote" id="quote" data-section="quote-form">
  <div class="container quote-grid">
    <div class="quote-intro">
      <h2 id="quote-title">Get a quotation</h2>
      <p>Share a few details. Our engineer will call you back to understand your plant.</p>
      <div class="assure">
        <div class="assure-item"><svg class="ic"><use href="#i-clock"/></svg><div><b>Guaranteed response within 1 working day</b></div></div>
        <div class="assure-item assure-badge"><svg class="ic"><use href="#i-ban"/></svg><div><b>Zero production downtime</b><span>No welding required. Most installations happen during active factory runtime with zero production loss.</span></div></div>
      </div>
    </div>
    <form class="quote-form" id="quote-form" action="../submit.php" method="post" novalidate>
      <input type="hidden" name="site" value="quickair">
      <input type="hidden" name="form" value="quickair">
      <input type="hidden" name="ts" value="<?= time() ?>">
      <input type="hidden" name="cta" value="Get a quotation">
      <input type="hidden" name="form_place" value="quote-section">
      <div class="hp" aria-hidden="true"><label>Leave empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
      <div class="f-row">
        <label class="f"><span>Name *</span><input type="text" name="name" required maxlength="100" autocomplete="name" placeholder="Your name"></label>
        <label class="f"><span>Company *</span><input type="text" name="company" required maxlength="150" autocomplete="organization" placeholder="Company name"></label>
      </div>
      <div class="f-row">
        <label class="f"><span>Phone *</span><input type="tel" name="phone" required inputmode="numeric" pattern="[6-9][0-9]{9}" maxlength="10" autocomplete="tel-national" placeholder="10-digit mobile"></label>
        <label class="f"><span>Email *</span><input type="email" name="email" required maxlength="150" autocomplete="email" placeholder="you@company.com"></label>
      </div>
      <label class="f"><span>What do you need *</span>
        <select name="product" required>
          <option>New pipeline for new plant</option>
          <option>Extending an existing pipeline</option>
          <option>Replacing an existing pipeline</option>
          <option>Design and sizing only</option>
          <option>Material supply only</option>
          <option>Not sure which material is right, need expert advice</option>
        </select>
      </label>
      <button type="submit" class="btn btn-red btn-lg btn-block">Request quotation</button>
      <p class="form-note"><svg class="ic"><use href="#i-clock"/></svg> We respond within one working day.</p>
      <p class="form-msg<?= $err ? ' error' : '' ?>" role="status" aria-live="polite"><?= h($err) ?></p>
    </form>
  </div>
</section>

<!-- ============ PRODUCTS ============ -->
<section class="products" id="products" data-section="products">
  <div class="container">
    <p class="eyebrow">Product range</p>
    <h2>Everything the network needs, from one system</h2>
    <div class="product-grid">
      <article class="product"><img src="assets/img/products/aluminium-pipes.webp" alt="Aluminium pipes" loading="lazy" width="600" height="600"><h3>Aluminium pipes</h3><p>20 – 290 mm OD, powder coated</p></article>
      <article class="product"><img src="assets/img/products/push-fit-fittings.webp" alt="Push fit fittings" loading="lazy" width="600" height="600"><h3>Push fit fittings</h3><p>20 – 63 mm OD, tool-free</p></article>
      <article class="product"><img src="assets/img/products/integrated-fittings.webp" alt="Integrated fittings" loading="lazy" width="600" height="600"><h3>Integrated fittings</h3><p>20 – 90 mm OD</p></article>
      <article class="product"><img src="assets/img/products/crimped-fittings.webp" alt="Crimped fittings" loading="lazy" width="600" height="600"><h3>Crimped fittings</h3><p>113 – 290 mm OD</p></article>
      <article class="product"><img src="assets/img/products/valves.webp" alt="Valves" loading="lazy" width="600" height="600"><h3>Valves</h3><p>Isolation &amp; zoning</p></article>
      <article class="product"><img src="assets/img/products/drops-manifolds.webp" alt="Drops and manifolds" loading="lazy" width="600" height="600"><h3>Drops &amp; manifolds</h3><p>Point of use distribution</p></article>
      <article class="product"><img src="assets/img/products/accessories.webp" alt="Accessories" loading="lazy" width="600" height="600"><h3>Accessories</h3><p>Brackets, supports, hardware</p></article>
      <article class="product"><img src="assets/img/products/installation-tools.webp" alt="Installation tools" loading="lazy" width="600" height="600"><h3>Installation tools</h3><p>Cutting &amp; assembly</p></article>
    </div>
  </div>
</section>

<!-- ============ SPECIFICATION + CERTIFICATIONS ============ -->
<section class="specs" id="specs" data-section="specs">
  <div class="container">
    <p class="eyebrow">Technical specification</p>
    <h2>Suitable fluids, resistance &amp; approvals</h2>
    <div class="spec-grid">
      <div class="spec-card"><h3>Suitable fluids</h3><ul>
        <li>Compressed air: dry, wet, lubricated</li><li>Vacuum</li><li>Inert gases: N₂, argon, helium, CO₂ mixes</li><li>Instrument air: pharma, food, paint</li></ul></div>
      <div class="spec-card"><h3>Pressure &amp; vacuum</h3><ul>
        <li>Max. working pressure: 16 bar</li><li>40 bar high-pressure option</li><li>Vacuum: 1 mbar (absolute)</li></ul></div>
      <div class="spec-card"><h3>Resistance to</h3><ul>
        <li>Corrosion &amp; aggressive environments</li><li>UV &amp; outdoor installation</li><li>Thermal variation &amp; mechanical shocks</li><li>Compressor oil carry-over (mineral &amp; synthetic)</li></ul></div>
      <div class="spec-card"><h3>Approvals &amp; material</h3><ul>
        <li>CE certified, PED 2014/68/EU</li><li>Non-flammable, UL94HB rated</li><li>6063-T5 aluminium alloy</li><li>100% recyclable</li></ul></div>
    </div>

    <div class="cert-block">
      <div class="cert-group">
        <h3>Certifications</h3>
        <ul class="cert-grid">
          <li><img src="assets/img/certs/tuv.webp" alt="TÜV Rheinland" loading="lazy"></li>
          <li><img src="assets/img/certs/ce.webp" alt="CE" loading="lazy"></li>
          <li><img src="assets/img/certs/ul.webp" alt="UL" loading="lazy"></li>
          <li><img src="assets/img/certs/iso-8573.webp" alt="ISO 8573-1:2010" loading="lazy"></li>
          <li><img src="assets/img/certs/iso-9001.webp" alt="ISO 9001:2015" loading="lazy"></li>
          <li><img src="assets/img/certs/rohs.webp" alt="RoHS" loading="lazy"></li>
        </ul>
      </div>
      <div class="cert-group">
        <h3>Standards</h3>
        <ul class="cert-grid">
          <li><img src="assets/img/certs/asme-b31-3.webp" alt="ASME B31.3" loading="lazy"></li>
          <li><img src="assets/img/certs/astm-b85.webp" alt="ASTM B85" loading="lazy"></li>
          <li><img src="assets/img/certs/en-837-1.webp" alt="EN 837-1" loading="lazy"></li>
          <li><img src="assets/img/certs/ped.webp" alt="PED certified 2014/68/EU" loading="lazy"></li>
          <li><img src="assets/img/certs/iso-8030.webp" alt="ISO 8030" loading="lazy"></li>
          <li><img src="assets/img/certs/is-1608.webp" alt="IS 1608" loading="lazy"></li>
        </ul>
      </div>
      <div class="made-in-india"><img src="assets/img/certs/made-in-india.svg" alt="Made in India" loading="lazy" width="120" height="120"><p><b>Manufactured in India.</b> Exported to the USA, Australia, Germany and Serbia.</p></div>
    </div>
  </div>
</section>

<!-- ============ COMPARISON ============ -->
<section class="compare" id="compare" data-section="compare">
  <div class="container">
    <p class="eyebrow">Competitive edge</p>
    <h2>QuickAir vs other systems</h2>
    <div class="cmp-table" role="table" aria-label="QuickAir compared with other piping systems">
      <div class="cmp-head" role="row"><span role="columnheader">QuickAir</span><span role="columnheader">Others</span></div>
      <div class="cmp-row" role="row">
        <div class="cmp-q" role="cell"><img src="assets/img/compare/quickair-1.png" alt="" loading="lazy"><p><b>Push-in fittings (20–63 mm)</b> Zero thread, zero leakage.</p></div>
        <div class="cmp-o" role="cell"><svg class="ic"><use href="#i-xc"/></svg><p>Not available.</p></div>
      </div>
      <div class="cmp-row" role="row">
        <div class="cmp-q" role="cell"><img src="assets/img/compare/quickair-2.png" alt="" loading="lazy"><p><b>Integrated elbow &amp; tee design</b> Merged fittings cut parts and assembly time by 40%.</p></div>
        <div class="cmp-o" role="cell"><img src="assets/img/compare/others-2.png" alt="" loading="lazy"><p>Requires multiple couplers.</p></div>
      </div>
      <div class="cmp-row" role="row">
        <div class="cmp-q" role="cell"><img src="assets/img/compare/quickair-3.png" alt="" loading="lazy"><p><b>Zero thread, zero leakage</b> Patented wall brackets and fittings eliminate leaks.</p></div>
        <div class="cmp-o" role="cell"><img src="assets/img/compare/others-3.png" alt="" loading="lazy"><p>Threaded, prone to leakage.</p></div>
      </div>
      <div class="cmp-row" role="row">
        <div class="cmp-q" role="cell"><img src="assets/img/compare/quickair-4.png" alt="" loading="lazy"><p><b>Automated valves</b> Ball and butterfly valves with pneumatic or electric actuators, PLC control and SCADA monitoring.</p></div>
        <div class="cmp-o" role="cell"><svg class="ic"><use href="#i-xc"/></svg><p>Auto systems not available. Manual or basic valves risk leaks and delays.</p></div>
      </div>
      <div class="cmp-row" role="row">
        <div class="cmp-q" role="cell"><img src="assets/img/compare/quickair-5.png" alt="" loading="lazy"><p><b>Male-female coupler</b> Rubber support and insert nut snap together like puzzle pieces for a leak-proof seal.</p></div>
        <div class="cmp-o" role="cell"><img src="assets/img/compare/others-5.png" alt="" loading="lazy"><p>Couplers lack this design, harder to install and prone to leaks.</p></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ EXPERT CTA ============ -->
<section class="cta-band" data-section="cta-compare">
  <div class="container cta-inner">
    <h2>Comparing materials or brands for your compressed air pipeline? <span>Not sure which one is right?</span></h2>
    <div class="cta-btns">
      <a href="#quote" class="btn btn-red" data-cta="Speak to an Engineer">Speak to an Engineer</a>
      <a href="tel:+919001848552" class="btn btn-outline-light" data-track="phone" data-place="cta-compare"><svg class="ic"><use href="#i-phone"/></svg> Call now</a>
    </div>
  </div>
</section>

<!-- ============ BENEFITS / TECHNICAL ADVANTAGES ============ -->
<section class="benefits dark" id="benefits" data-section="benefits">
  <div class="container">
    <p class="eyebrow">Benefits &amp; technical advantages</p>
    <h2>Why plants switch to QuickAir</h2>
    <div class="benefit-grid">
      <article class="benefit"><svg class="ic"><use href="#i-zap"/></svg><h3>Ultra-fast installation</h3><p>Up to 70% faster than steel, copper or welded systems. Labour is around 20% of total installation cost.</p></article>
      <article class="benefit"><svg class="ic"><use href="#i-shield"/></svg><h3>Zero corrosion, clean air</h3><p>Smooth powder-coated interior gives minimal pressure drop, laminar flow and contaminant-free delivery. No rust like GI/MS pipes.</p></article>
      <article class="benefit"><svg class="ic"><use href="#i-lock"/></svg><h3>Leak-proof performance</h3><p>Full-bore design, reusable fittings and push-to-connect technology, rated to 16 bar and −20 °C to 90 °C.</p></article>
      <article class="benefit"><svg class="ic"><use href="#i-wind"/></svg><h3>High flow performance</h3><p>Smooth, full bore ensures maximum air flow through the pipes and lower compressor load.</p></article>
      <article class="benefit"><svg class="ic"><use href="#i-down"/></svg><h3>Energy &amp; cost savings</h3><p>Optimised layout, no leak points and minimised pressure drop cut power, maintenance and installation costs.</p></article>
      <article class="benefit"><svg class="ic"><use href="#i-layers"/></svg><h3>Modular flexibility</h3><p>Expand, modify or relocate as the plant evolves using the same set of materials, 20 mm to 290 mm (¾" – 12").</p></article>
      <article class="benefit"><svg class="ic"><use href="#i-award"/></svg><h3>International standards</h3><p>Complies with ISO 8573-1:2010, ASME B31.1 / B31.3 and PED 2014/68/EU for piping.</p></article>
      <article class="benefit"><svg class="ic"><use href="#i-link"/></svg><h3>Perfect compatibility</h3><p>Connects to female thread, male thread and flange joints. Transition fittings tie into existing steel, copper or PPR lines.</p></article>
      <article class="benefit"><svg class="ic"><use href="#i-shield"/></svg><h3>Strong &amp; long life</h3><p>Resists corrosion, vibration, heat and harsh environments, protecting air quality up to the point of use.</p></article>
      <article class="benefit"><svg class="ic"><use href="#i-ban"/></svg><h3>No hot work</h3><p>No welding, gluing or threading. Light, easy-to-cut pipes; most work is done while the plant keeps running.</p></article>
      <article class="benefit"><svg class="ic"><use href="#i-recycle"/></svg><h3>100% recyclable</h3><p>Aluminium pipes and fittings are fully recyclable at the end of their life.</p></article>
      <article class="benefit"><svg class="ic"><use href="#i-users"/></svg><h3>One team, start to finish</h3><p>Designed, supplied, installed, tested and commissioned by the same team, under one warranty.</p></article>
    </div>
  </div>
</section>

<!-- ============ INSTALLATIONS ============ -->
<section class="projects" id="projects" data-section="projects">
  <div class="container">
    <p class="eyebrow">Past projects</p>
    <h2>Systems running in manufacturing plants</h2>
  </div>
  <div class="gallery">
    <img src="assets/img/installations/one.webp" width="1200" height="554" alt="QuickAir piping installation in a manufacturing plant" loading="lazy">
    <img src="assets/img/installations/two.webp" width="1200" height="900" alt="QuickAir piping network along factory roof" loading="lazy">
    <img src="assets/img/installations/three.webp" width="1200" height="675" alt="QuickAir compressor room piping" loading="lazy">
    <img src="assets/img/installations/four.webp" width="1200" height="540" alt="QuickAir distribution header" loading="lazy">
    <img src="assets/img/installations/five.webp" width="900" height="1223" alt="QuickAir vertical drops installation" loading="lazy">
  </div>
</section>

<section class="cta-band" data-section="cta-projects">
  <div class="container cta-inner">
    <h2>Want a system like this in your plant?</h2>
    <div class="cta-btns">
      <a href="#quote" class="btn btn-red" data-cta="Speak to an Engineer">Speak to an Engineer</a>
      <a href="tel:+919001848552" class="btn btn-outline-light" data-track="phone" data-place="cta-projects"><svg class="ic"><use href="#i-phone"/></svg> Call now</a>
    </div>
  </div>
</section>

<!-- ============ INDUSTRIES ============ -->
<section class="industries" id="clients" data-section="industries">
  <div class="container">
    <p class="eyebrow">Trusted by</p>
    <h2>Customers across India, in every major industry</h2>
    <div class="ind-layout">
      <img class="ind-map" src="assets/img/india-map.webp" alt="Map of Indian states where QuickAir systems are installed" loading="lazy" width="800" height="906">
      <div class="ind-grid">
        <div class="ind ind-wide"><h3>Automotive</h3><div class="ind-logos"><img src="assets/img/logos/maruti-suzuki.webp" style="--h:30px" alt="Maruti Suzuki" loading="lazy"><img src="assets/img/logos/tvs.svg" style="--h:34px" alt="TVS" loading="lazy"><img src="assets/img/logos/ve-commercial.webp" style="--h:36px" alt="VE Commercial Vehicles" loading="lazy"><img src="assets/img/logos/opmobility.webp" style="--h:46px" alt="OPmobility" loading="lazy"><img src="assets/img/logos/eka.webp" style="--h:44px" alt="EKA Mobility" loading="lazy"></div></div>
        <div class="ind"><h3>Power</h3><div class="ind-logos"><img src="assets/img/logos/genus.webp" style="--h:50px" alt="Genus" loading="lazy"><img src="assets/img/logos/uttam.webp" style="--h:40px" alt="Uttam" loading="lazy"></div></div>
        <div class="ind"><h3>Solar</h3><div class="ind-logos"><img src="assets/img/logos/bvg.webp" style="--h:60px" alt="BVG India" loading="lazy"></div></div>
        <div class="ind"><h3>Textiles</h3><div class="ind-logos"><img src="assets/img/logos/dcm-nouvelle.webp" style="--h:44px" alt="DCM Nouvelle" loading="lazy"></div></div>
        <div class="ind"><h3>Stone</h3><div class="ind-logos"><img src="assets/img/logos/shree-ram.webp" style="--h:56px" alt="Shree Ram Mega Structure" loading="lazy"></div></div>
        <div class="ind"><h3>Printing</h3><div class="ind-logos"><img src="assets/img/logos/rajasthan-patrika.webp" style="--h:34px" alt="Rajasthan Patrika" loading="lazy"></div></div>
        <div class="ind"><h3>Plastic</h3><div class="ind-logos"><img src="assets/img/logos/paras.webp" style="--h:40px" alt="Paras" loading="lazy"><img src="assets/img/logos/safari.webp" style="--h:40px" alt="Safari" loading="lazy"></div></div>
        <div class="ind"><h3>Ceramics</h3><div class="ind-logos"><img src="assets/img/logos/clay-craft.webp" style="--h:70px" alt="Clay Craft" loading="lazy"></div></div>
        <div class="ind"><h3>Food</h3><div class="ind-logos"><img src="assets/img/logos/radhey-agro.webp" style="--h:70px" alt="Radhey Agro (Kanha Group)" loading="lazy"></div></div>
        <div class="ind"><h3>Engineering</h3><div class="ind-logos"><img src="assets/img/logos/epack-prefab.webp" style="--h:56px" alt="Epack Prefab" loading="lazy"></div></div>
        <div class="ind"><h3>Cables</h3><div class="ind-logos"><img src="assets/img/logos/dynamic-cables.webp" style="--h:44px" alt="Dynamic Cables" loading="lazy"><img src="assets/img/logos/oswal-cables.webp" style="--h:58px" alt="Oswal Cables" loading="lazy"></div></div>
        <div class="ind ind-span2"><h3>Consumer durables</h3><div class="ind-logos"><img src="assets/img/logos/epack-durable.webp" style="--h:52px" alt="Epack Durable" loading="lazy"></div></div>
        <div class="ind ind-wide"><h3>Furniture</h3><div class="ind-logos"><img src="assets/img/logos/laxmi-ideal.webp" style="--h:60px" alt="Laxmi Ideal Interiors" loading="lazy"><img src="assets/img/logos/modi-furniture.webp" style="--h:50px" alt="Modi Furniture" loading="lazy"><img src="assets/img/logos/aked.webp" style="--h:60px" alt="AKED" loading="lazy"><img src="assets/img/logos/haus-luxuriant.webp" style="--h:44px" alt="Haus Luxuriant" loading="lazy"></div></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ PROCESS ============ -->
<section class="process" id="process" data-section="process">
  <div class="container">
    <p class="eyebrow">How it works</p>
    <h2>Our 6-step turnkey process</h2>
    <ol class="steps">
      <li><span class="step-ic" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4M12 16h4M8 11h.01M8 16h.01"/></svg></span><span>Step 1</span><h3>Site survey &amp; assessment</h3></li>
      <li><span class="step-ic" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M21.17 6.81a1 1 0 0 0-3.99-3.99L3.84 16.17a2 2 0 0 0-.5.83l-1.32 4.35a.5.5 0 0 0 .62.62l4.35-1.32a2 2 0 0 0 .83-.5z"/><path d="m15 5 4 4"/><path d="M13 7 8.7 2.7a2.41 2.41 0 0 0-3.4 0L2.7 5.3a2.41 2.41 0 0 0 0 3.4L7 13"/><path d="m17 11 4.3 4.3c.94.94.94 2.46 0 3.4l-2.6 2.6c-.94.94-2.46.94-3.4 0L11 17"/></svg></span><span>Step 2</span><h3>System design</h3></li>
      <li><span class="step-ic" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg></span><span>Step 3</span><h3>Supply</h3></li>
      <li><span class="step-ic" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></span><span>Step 4</span><h3>Installation</h3></li>
      <li><span class="step-ic" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3.34 19a10 10 0 1 1 17.32 0"/><path d="m12 14 4-4"/><circle cx="12" cy="14" r="1.3"/><path d="M6 13h.01M8 8.5h.01M12 6.5h.01M18 13h.01"/></svg></span><span>Step 5</span><h3>Testing</h3></li>
      <li><span class="step-ic" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg></span><span>Step 6</span><h3>Commissioning &amp; handover</h3></li>
    </ol>
  </div>
</section>

<!-- ============ PIPE, PIPELINE & FITTINGS (search keywords: fittings / pipeline / air line / suppliers) ============ -->
<section class="benefits dark pipe-range" id="pipe-fittings" data-section="pipe-fittings">
  <div class="container">
    <p class="eyebrow">Aluminium air pipe &amp; fittings</p>
    <h2>Aluminium compressed air pipe, pipeline &amp; fittings</h2>
    <p class="pipe-range-intro">Ahuja Group is an authorised supplier and installer of QuickAir modular aluminium (aluminum) compressed air piping. One team designs, supplies, installs, tests and commissions your aluminium air pipeline across India.</p>
    <div class="benefit-grid">
      <article class="benefit"><svg class="ic"><use href="#i-link"/></svg><h3>Aluminium air pipe fittings</h3><p>Push-fit fittings 20–63 mm OD (tool-free, zero thread), integrated elbows and tees 20–90 mm, crimped fittings 113–290 mm, ball and butterfly valves, drops &amp; manifolds, brackets and accessories. Transition fittings join existing steel, copper or PPR lines.</p></article>
      <article class="benefit"><svg class="ic"><use href="#i-layers"/></svg><h3>Aluminium air pipeline &amp; air line sizes</h3><p>20 mm (¾") to 290 mm (12"). 16 bar working pressure with a 40 bar high-pressure option, −20 °C to 90 °C, vacuum down to 1 mbar (absolute). For compressed air (dry, wet, lubricated), vacuum, inert gases and instrument air.</p></article>
      <article class="benefit"><svg class="ic"><use href="#i-shield"/></svg><h3>Blue powder-coated aluminium pipe</h3><p>6063-T5 aluminium alloy with blue powder coating. Smooth interior for minimal pressure drop and clean air; resists corrosion, UV and outdoor installation. 25+ years expected life and a 10-year warranty.</p></article>
      <article class="benefit"><svg class="ic"><use href="#i-award"/></svg><h3>Authorised supplier &amp; installer</h3><p>Designed, supplied and installed across India, with projects in 11+ states, a team of 33+ engineers and ISO 9001:2015 certification. A 6-step turnkey process from site survey to commissioning and handover.</p></article>
    </div>
    <div class="pipe-range-cta">
      <a href="#quote" class="btn btn-red btn-lg" data-cta="Speak to an Engineer">Speak to an Engineer</a>
      <a href="tel:+919001848552" class="btn btn-outline-light btn-lg" data-track="phone"><svg class="ic"><use href="#i-phone"/></svg> Call now</a>
    </div>
  </div>
</section>

<!-- ============ FAQ ============ -->
<section class="faq" id="faq" data-section="faq">
  <div class="container narrow">
    <p class="eyebrow">Before you enquire</p>
    <h2>Common questions</h2>
    <div class="faq-list">
      <details open><summary>What do you need from me to quote?</summary><p>Your compressor capacity in CFM or m³/min, approximate factory dimensions, and the number of machines to be connected. A layout drawing makes it faster and more accurate, but is not essential to start.</p></details>
      <details><summary>Can this connect to my existing pipeline?</summary><p>Yes. Transition fittings connect the aluminium network to existing steel, copper or PPR lines, so a plant can be converted section by section rather than all at once.</p></details>
      <details><summary>Who does the installation?</summary><p>Our own certified installation team. The same team that produces the design carries out the erection, pressure testing and commissioning, so responsibility does not split between a supplier and a contractor.</p></details>
      <details><summary>What does the warranty cover?</summary><p>Ten years against manufacturing defects on pipe and fittings, valid when the system is installed by authorised technicians. Installation workmanship is covered separately under our own warranty.</p></details>
      <details><summary>Can work be done while the plant is running?</summary><p>In most cases yes. The system requires no welding or hot work, so new lines and drops can usually be installed alongside production, with a short tie-in shutdown only at changeover.</p></details>
      <details><summary>Which aluminium air pipe fittings do you supply?</summary><p>The QuickAir range covers push-fit fittings (20–63 mm OD, tool-free), integrated fittings with a merged elbow and tee design (20–90 mm OD), crimped fittings (113–290 mm OD), ball and butterfly valves for isolation and zoning, drops and manifolds for point-of-use distribution, and brackets, supports, hardware and installation tools.</p></details>
      <details><summary>Push-fit, integrated or crimped fittings: which one is right?</summary><p>It mainly depends on pipe size. Push-fit fittings cover 20–63 mm OD and connect tool-free with zero threads. Integrated fittings (20–90 mm OD) merge the elbow and tee, cutting parts and assembly time by 40%. Crimped fittings are used on large pipes from 113 to 290 mm OD. Our engineer selects the right type during system design.</p></details>
      <details><summary>Can aluminium compressed air pipe fittings connect to my machines and pneumatic equipment?</summary><p>Yes. The system connects to female thread, male thread and flange joints, and drops and manifolds deliver air at each point of use. Transition fittings also tie into existing steel, copper or PPR lines.</p></details>
      <details><summary>Can the aluminium air line be extended or changed later?</summary><p>Yes. The system is modular and the fittings are reusable, so you can expand, modify or relocate lines as the plant evolves using the same set of materials, from 20 mm to 290 mm (¾" – 12").</p></details>
      <details><summary>Are you a manufacturer or a supplier of aluminium air pipes?</summary><p>Ahuja Group is an authorised supplier and installer of QuickAir aluminium compressed air piping. QuickAir is manufactured in India and exported to the USA, Australia, Germany and Serbia. We design, supply, install, test and commission the system with one team, under one warranty.</p></details>
      <details><summary>Do you supply and install aluminium air pipelines across India?</summary><p>Yes. QuickAir systems are designed, supplied and installed across India, with projects in 11+ states. Share your plant details in the form or call +91 90018 48552, and our engineer will respond within one working day.</p></details>
      <details><summary>What is an aluminium air pipeline, and why is it better than GI or MS pipe?</summary><p>It is a modular network of powder-coated aluminium pipes and fittings that carries compressed air from the compressor to every machine. Unlike GI or MS pipe it does not rust, the smooth full bore gives minimal pressure drop and contaminant-free air, zero-thread joints are leak-proof, and installation is up to 70% faster with no welding.</p></details>
      <details><summary>What sizes and pressure ratings are available?</summary><p>Pipes and fittings from 20 mm (¾") to 290 mm (12"), rated for 16 bar working pressure with a 40 bar high-pressure option, vacuum down to 1 mbar (absolute), and temperatures from −20 °C to 90 °C.</p></details>
      <details><summary>What is the blue aluminium compressed air pipe made of?</summary><p>The pipes are made from 6063-T5 aluminium alloy with a blue powder coating. The powder-coated finish gives a smooth interior for minimal pressure drop and clean air, and resists corrosion, UV and outdoor installation. The material is non-flammable (UL94HB rated) and 100% recyclable.</p></details>
      <details><summary>How fast is installation compared with GI, steel or copper pipe?</summary><p>Up to 70% faster than steel, copper or welded systems. The pipes are light and easy to cut, and there is no welding, gluing or threading, so most installation work is done while the plant keeps running.</p></details>
      <details><summary>What is the expected life of an aluminium compressed air pipeline?</summary><p>The expected pipeline lifespan is 25+ years, backed by a 10-year warranty against manufacturing defects on pipe and fittings. The system resists corrosion, vibration, heat and harsh environments.</p></details>
    </div>
  </div>
</section>

<!-- ============ CONTACT ============ -->
<section class="contact" id="contact" data-section="contact">
  <div class="container">
    <h2>Talk to us about your plant</h2>
    <p class="sub">A drawing, a sketch, or just your compressor capacity and factory size. That is enough to start.</p>
    <div class="contact-grid">
      <a class="contact-card" href="tel:+919001848552" data-track="phone" data-place="contact"><svg class="ic"><use href="#i-phone"/></svg><span>Call</span><b>+91 90018 48552</b></a>
      <a class="contact-card" href="https://wa.me/919001848552?text=Hi%2C%20I%27m%20interested%20in%20aluminium%20compressed%20air%20piping" target="_blank" rel="noopener" data-track="whatsapp" data-place="contact"><svg class="ic"><use href="#i-wa"/></svg><span>WhatsApp</span><b>+91 90018 48552</b></a>
      <a class="contact-card" href="mailto:projects1@ahujagroup.in" data-track="email" data-place="contact"><svg class="ic"><use href="#i-mail"/></svg><span>Email</span><b>projects1@ahujagroup.in</b></a>
    </div>
    <div class="contact-cta"><a href="#quote" class="btn btn-red btn-lg" data-cta="Speak to an Engineer">Speak to an Engineer</a></div>
  </div>
</section>

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

<a href="#top" class="to-top" aria-label="Back to top"><svg class="ic"><use href="#i-up"/></svg></a>

<!-- Mobile sticky action bar -->
<nav class="sticky-bar" aria-label="Quick contact">
  <a href="tel:+919001848552" data-track="phone" data-place="sticky-bar"><svg class="ic"><use href="#i-phone"/></svg>Call</a>
  <a href="https://wa.me/919001848552?text=Hi%2C%20I%27m%20interested%20in%20aluminium%20compressed%20air%20piping" target="_blank" rel="noopener" class="sb-wa" data-track="whatsapp" data-place="sticky-bar"><svg class="ic"><use href="#i-wa"/></svg>WhatsApp</a>
  <a href="#quote" class="sb-cta" data-cta="Speak to an Engineer">Speak to an Engineer</a>
</nav>

<script src="assets/js/main.js?v=4" defer></script>
</body>
</html>
