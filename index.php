<?php
require __DIR__ . '/inc/app.php';

$err = isset($_GET['err']) ? mb_substr($_GET['err'], 0, 120) : '';
$errForm = ($_GET['f'] ?? '') === 'hero' ? 'hero' : 'footer'; // show the error on the form that was submitted

function icon(string $n): string
{
    // Inline SVG from assets/icons (Lucide + Simple Icons WhatsApp) so icons inherit text colour.
    static $cache = [];
    if (!isset($cache[$n])) {
        $svg = file_get_contents(__DIR__ . "/assets/icons/$n.svg");
        $svg = preg_replace('/<!--.*?-->|<title>.*?<\/title>/s', '', $svg);
        // Drop size/class only from the root <svg> tag; inner shapes (e.g. <rect width>) must keep theirs.
        $svg = preg_replace_callback('/<svg\b[^>]*>/', function ($m) {
            $tag = preg_replace('/\s(class|width|height|role)="[^"]*"/', '', $m[0]);
            $attrs = ' class="ic" aria-hidden="true" focusable="false"' . (strpos($tag, 'stroke=') === false ? ' fill="currentColor"' : '');
            return '<svg' . $attrs . substr($tag, 4);
        }, $svg, 1);
        $cache[$n] = trim($svg);
    }
    return $cache[$n];
}

function wa_link(string $text): string
{
    return 'https://wa.me/' . SITE_PHONE_RAW . '?text=' . rawurlencode($text);
}

function render_form(string $id, string $err): void
{ ?>
    <form class="lead-form" id="<?= $id ?>" method="post" action="submit.php" novalidate>
        <?php if ($err): ?><p class="form-error" role="alert"><?= h($err) ?></p><?php endif; ?>
        <div class="f-row">
            <label for="<?= $id ?>-name">Your Name *</label>
            <input id="<?= $id ?>-name" name="name" required minlength="2" maxlength="80" autocomplete="name" placeholder="Full name">
        </div>
        <div class="f-row">
            <label for="<?= $id ?>-phone">Mobile Number *</label>
            <input id="<?= $id ?>-phone" name="phone" type="tel" required inputmode="numeric" pattern="(\+?91)?[6-9][0-9]{9}" maxlength="13" autocomplete="tel" placeholder="10-digit mobile">
        </div>
        <div class="f-grid">
            <div class="f-row">
                <label for="<?= $id ?>-city">City</label>
                <input id="<?= $id ?>-city" name="city" maxlength="60" autocomplete="address-level2" placeholder="e.g. Jaipur">
            </div>
            <div class="f-row">
                <label for="<?= $id ?>-email">Email <span class="opt">(optional)</span></label>
                <input id="<?= $id ?>-email" name="email" type="email" maxlength="120" autocomplete="email" placeholder="you@company.com">
            </div>
        </div>
        <div class="f-row">
            <label for="<?= $id ?>-product">Requirement</label>
            <select id="<?= $id ?>-product" name="product">
                <option value="">Select product / service</option>
                <?php foreach (form_requirements() as $name): ?><option><?= h($name) ?></option><?php endforeach; ?>
                <?php foreach (EXTRA_REQUIREMENTS as $x): ?><option><?= h($x) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="f-row">
            <label for="<?= $id ?>-msg">Details <span class="opt">(optional)</span></label>
            <textarea id="<?= $id ?>-msg" name="message" rows="2" maxlength="1000" placeholder="HP / CFM / application, if known"></textarea>
        </div>
        <div class="hp" aria-hidden="true"><input name="website" tabindex="-1" autocomplete="off"></div>
        <input type="hidden" name="ts" value="<?= time() ?>">
        <input type="hidden" name="form" value="<?= $id ?>">
        <?php foreach (TRACK_FIELDS as $k): ?><input type="hidden" name="<?= $k ?>"><?php endforeach; ?>
        <button class="btn btn-red btn-block" type="submit">Get Best Price <?= icon('arrow-right') ?></button>
        <p class="f-note"><?= icon('clock') ?> We respond within 2 hours</p>
    </form>
<?php }

$tel = 'tel:+' . SITE_PHONE_RAW;
$waDefault = wa_link('Hi, I need details of Anest Iwata compressors.');

$industries = ['Textile & Garment', 'Automotive & Engineering', 'Food & Beverage Processing', 'Pharmaceuticals', 'Packaging',
    'Marble & Stone Processing', 'Mineral Processing', 'Furniture & Handicrafts', 'Jewellery', 'Fabrication',
    'Automobile Workshops', 'Printing & Packaging', 'Hospitals'];

$services = [
    ['calendar-check', 'Annual Maintenance Contracts (AMC)', 'Customized AMC plans, covering scheduled servicing, genuine spare parts, on-site support, and performance tracking—ensuring peace of mind and equipment longevity.'],
    ['wrench', 'Breakdown & Preventive Maintenance', 'Rapid-response troubleshooting for breakdowns and routine preventive maintenance to reduce unexpected failures and extend machine life.'],
    ['battery-charging', 'Backup / Standby Equipment', 'Stay production-ready with our standby equipment, available for backup during overhauls, breakdowns, or project delays—ideal for AMC and critical-use clients.'],
    ['lightbulb', 'Consultancy & Expert Advice', 'With decades of cross-industry experience, our experts offer guidance on system design, equipment selection, energy optimization, and troubleshooting. Ideal for new setups, expansions, or performance improvement projects.'],
];

$awards = [
    ['award-1.webp', 'Anest Iwata Rising Star FY 23-24'],
    ['award-2.webp', 'Anest Iwata Certificate of Excellence – Sales Superstar Award 2024-2025'],
    ['award-4.webp', 'Anest Iwata Sales Achievement Award 2024'],
    ['award-3.webp', 'Anest Iwata Sales Achievement Award 2025'],
];

$partner = [
    ['award', 'Trusted Legacy of 50+ Years'],
    ['users', 'Diverse Customer Base Across India'],
    ['handshake', 'Expert, Honest Advice with Customer First Approach'],
    ['badge-percent', 'Competitive Pricing Through Volume Advantage'],
    ['headset', 'Strong After-Sales Support & Backup Readiness'],
    ['package', 'Large Inventories for Fast Deliveries'],
];

$anest = [
    ['cog', 'Japanese Engineering Excellence', 'ISO‑certified manufacturing and continuous R&D'],
    ['sparkles', 'Industry‑First Innovations', 'World’s first oil‑free scroll & seize‑free reciprocating technology'],
    ['shield-check', 'Proven Reliability', 'Across Thousands of Indian Installations'],
    ['leaf', 'Energy & Sustainability Focus', 'High‑efficiency PM motors and VSD for lower carbon footprint'],
];

$offices = [
    ['office-head.webp', 'Ahuja Group, Head Office', 'A-185, Prahladpura Industrial Area, 302022 – Jaipur'],
    ['office-city.webp', 'Ahuja Group, City Office', 'B-85, Bais Godam Industrial Area, 302006 – Jaipur'],
];

$faqs = [
    ['Are you the authorised dealer for Anest Iwata in Rajasthan?', 'Yes. Ahuja Group is the authorised dealer for Anest Iwata Motherson (AIM) compressors and vacuum pumps across Rajasthan.'],
    ['Which AIM products do you supply?', 'We supply the full AIM compressor and vacuum pump range: C-Air, LRST, EPM and APM screw compressors, Crown and other lubricated reciprocating compressors, oil-free reciprocating and scroll compressors, high pressure compressors, and rotary vane and reciprocating vacuum pumps.'],
    ['What is the C-Air screw compressor range?', 'C-Air is Anest Iwata Motherson’s reliable screw air compressor series from 7.5 HP to 50 HP (5.5 to 37 kW), delivering 23 to 235 CFM at 7 to 12.5 bar. It uses Siemens electrical components, an IP55 TEFC motor with SKF bearings and a 30% oversized cooler for hot Indian conditions.'],
    ['Does the C-Air series come with an extended warranty?', 'Yes. C-Air compressors come with AIM5 – Anest Iwata Motherson’s Five-Year Care extended warranty. Ask our team for the warranty terms for your model.'],
    ['Which compressor suits a small workshop or garage?', 'The Crown series lubricated reciprocating compressor is ideal: 1 HP to 3 HP, 4.5 to 13.4 CFM at 10 kg/cm², with 40 to 160 litre tanks and single or three phase motors. It is built for low noise, low vibration, low oil consumption and low maintenance.'],
    ['Should I choose an oil-free or a lubricated compressor?', 'Choose oil-free when the air touches your product or patients – pharma, food & beverage, hospitals and electronics. Lubricated reciprocating and screw compressors are the economical choice for general manufacturing, fabrication and workshops. Our engineers will recommend the right one for your application.'],
    ['How do I select the right compressor capacity?', 'Share your air tools or machines, required working pressure and daily running hours. Our engineer will calculate the air demand (CFM) and suggest the right model and tank size. For existing plants we also offer energy audits.'],
    ['Are Anest Iwata compressors made in India?', 'Anest Iwata Motherson is a joint venture of Anest Iwata Corporation, Japan and the Motherson Group, India, with two manufacturing plants in India. Performance is rated as per Japanese Industrial Standards (JIS) and free air delivery is measured as per ISO 1217.'],
    ['Do you handle installation and piping?', 'Yes. We provide a complete turnkey solution: site assessment, compressed air piping system design and installation, compressor commissioning, and handover. You deal with one team from start to finish.'],
    ['What is the delivery and lead time in Rajasthan?', 'Standard models are available ex-stock or on short lead times. Contact us with your requirement and we will confirm availability and delivery schedule for your location.'],
    ['Do you offer after-sales service and spare parts?', 'Yes. We maintain AIM spare parts inventory at our Jaipur facility and have a trained service team available across Rajasthan. Call ' . SITE_PHONE . ' for service enquiries.'],
];

$nav = ['products' => 'Products', 'industries' => 'Industries', 'location' => 'Location', 'services' => 'Services',
    'awards' => 'Awards', 'why-choose' => 'Why Choose', 'faq' => 'FAQs', 'contact' => 'Contact Us'];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Anest Iwata Air Compressor Dealer in Rajasthan | Ahuja Group, Jaipur</title>
<meta name="description" content="Authorised Anest Iwata Motherson dealer in Rajasthan. Screw, oil-free & reciprocating air compressors and vacuum pumps. Ex-stock delivery, installation, AMC & spares. Call <?= SITE_PHONE ?>.">
<meta name="theme-color" content="#0E1621">
<link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
<link rel="icon" type="image/png" sizes="192x192" href="assets/favicon/icon-192.png">
<link rel="apple-touch-icon" href="assets/favicon/apple-touch-icon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<!-- fonts load without blocking first paint -->
<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Barlow:wght@400;500;600&display=swap" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Barlow:wght@400;500;600&display=swap"></noscript>
<link rel="stylesheet" href="assets/css/style.css?v=10">
<link rel="preload" as="image" href="assets/img/hero-bg.webp" fetchpriority="high">
<link rel="preload" as="image" href="assets/img/hero-compressors.webp">
<?php include __DIR__ . '/inc/gtag.php'; ?>
</head>
<body>

<div class="utility">
    <div class="wrap utility-in">
        <span class="utility-tag">Authorised Dealer · Anest Iwata Motherson · Rajasthan</span>
        <div class="utility-links">
            <a href="<?= $tel ?>" data-track="call" data-section="Top Bar"><?= icon('phone') ?> <?= SITE_PHONE ?></a>
            <a href="mailto:<?= SITE_EMAIL ?>"><?= icon('mail') ?> <?= SITE_EMAIL ?></a>
        </div>
    </div>
</div>

<header class="topbar">
    <div class="wrap topbar-in">
        <a class="brand" href="#top" aria-label="Anest Iwata Motherson – home">
            <img src="assets/img/aim-logo.webp" alt="Anest Iwata Motherson" width="140" height="52">
        </a>
        <nav class="mainnav" aria-label="Sections">
            <?php foreach ($nav as $id => $label): ?><a href="#<?= $id ?>"><?= $label ?></a><?php endforeach; ?>
        </nav>
        <div class="topbar-cta">
            <a class="btn btn-wa btn-sm" href="<?= $waDefault ?>" target="_blank" rel="noopener" data-track="wa" data-section="Header" aria-label="WhatsApp"><?= icon('whatsapp') ?> <span class="hide-sm">WhatsApp</span></a>
            <a class="btn btn-red btn-sm" href="#enquiry">Get Quote</a>
        </div>
    </div>
</header>

<main id="top">
<section class="hero">
    <div class="wrap hero-in">
        <div class="hero-copy">
            <p class="eyebrow"><span class="dot"></span> Authorised Dealer in Rajasthan</p>
            <h1><span class="h1-brand">Anest Iwata Motherson</span> Compressors &amp; Vacuum Pumps</h1>
            <p class="lead">Genuine, warranted Anest Iwata Motherson machines — supplied, installed and serviced by Ahuja Group, Jaipur.</p>
            <div class="cat-chips">
                <a href="#products" data-goto="screw">Screw Compressor</a>
                <a href="#products" data-goto="oilfree">Oil-Free Compressors</a>
                <a href="#products" data-goto="recip">Lubricated Reciprocating Compressors</a>
                <a href="#products" data-goto="vacuum">Vacuum Pumps</a>
            </div>
            <div class="hero-btns">
                <a class="btn btn-red" href="<?= $tel ?>" data-track="call" data-section="Hero"><?= icon('phone') ?> Call Now</a>
                <a class="btn btn-wa" href="<?= $waDefault ?>" target="_blank" rel="noopener" data-track="wa" data-section="Hero"><?= icon('whatsapp') ?> WhatsApp</a>
            </div>
            <ul class="hero-facts">
                <li><strong>1,00,000+</strong> Customers Satisfied Across India</li>
                <li><strong>Since 1926</strong> Serving Industry</li>
                <li><strong>Sales, Service</strong> &amp; Spare Parts</li>
                <li><strong>Site Visit</strong> &amp; Installation Across Rajasthan</li>
            </ul>
        </div>
        <div class="hero-visual">
            <img src="assets/img/hero-compressors.webp" alt="Anest Iwata reciprocating air compressors" width="308" height="389" fetchpriority="high">
        </div>
    </div>
</section>

<section class="sec quote-sec" id="enquiry">
    <div class="wrap quote-in">
        <div class="quote-copy">
            <p class="kicker">Get a Quote</p>
            <h2>Tell us your requirement – get the right model and best price</h2>
            <p>Share your application, HP / CFM (if known) and city. Our engineer calls you back within 2 hours with the right model options and price.</p>
            <ul class="quote-ticks">
                <li><?= icon('check') ?> Model selection by application engineers</li>
                <li><?= icon('check') ?> Genuine Anest Iwata Motherson machines with warranty</li>
                <li><?= icon('check') ?> Ex-stock popular models &amp; spares in Jaipur</li>
                <li><?= icon('check') ?> Installation, AMC &amp; service across Rajasthan</li>
            </ul>
        </div>
        <div class="form-card">
            <div class="form-head">
                <h2>Get a Quote Today</h2>
                <p>Mobile number is required – we respond within 2 hours.</p>
            </div>
            <?php render_form('hero', $errForm === 'hero' ? $err : ''); ?>
        </div>
    </div>
</section>

<section class="sec" id="products">
    <div class="wrap">
        <div class="sec-head center">
            <p class="kicker">Our Products</p>
            <h2>Product Range</h2>
            <p>Complete range of air compressors and vacuum pumps available through Ahuja Group — genuine, warranted, backed by expert technical support across Rajasthan.</p>
        </div>
        <div class="tabs" role="tablist" aria-label="Filter products">
            <button class="tab is-on" data-filter="all" role="tab" aria-selected="true">All</button>
            <button class="tab" data-filter="oilfree" role="tab" aria-selected="false">Oil-Free Compressors</button>
            <button class="tab" data-filter="vacuum" role="tab" aria-selected="false">Vacuum Pumps</button>
            <button class="tab" data-filter="recip" role="tab" aria-selected="false">Reciprocating Compressors</button>
            <button class="tab" data-filter="screw" role="tab" aria-selected="false">Screw Compressors</button>
        </div>
        <div class="products">
            <?php foreach (PRODUCTS as $p): ?>
            <article class="product" data-cat="<?= $p['cat'] ?>">
                <div class="product-img"><img src="assets/img/<?= $p['img'] ?>" alt="<?= h($p['name']) ?>" loading="lazy" width="340" height="192"></div>
                <div class="product-body">
                    <h3><?= h($p['name']) ?></h3>
                    <ul class="specs"><?php foreach ($p['specs'] as $s): ?><li><?= h($s) ?></li><?php endforeach; ?></ul>
                    <div class="product-cta">
                        <a class="btn btn-red" href="<?= $tel ?>" data-track="call" data-section="Product Range" data-product="<?= h($p['name']) ?>"><?= icon('phone') ?> Call Now</a>
                        <a class="btn btn-wa" href="<?= wa_link('Hi, I need price for ' . $p['name']) ?>" target="_blank" rel="noopener" data-track="wa" data-section="Product Range" data-product="<?= h($p['name']) ?>"><?= icon('whatsapp') ?> WhatsApp</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="sec sec-soft" id="industries">
    <div class="wrap">
        <div class="sec-head center">
            <p class="kicker">Sectors We Serve</p>
            <h2>Industries We Serve Across Rajasthan</h2>
            <p>From Jaipur’s jewellery workshops to Jodhpur and Bhilwara’s textile mills — every major industrial sector across Rajasthan.</p>
        </div>
        <div class="industries">
            <?php foreach ($industries as $i => $name): ?>
            <figure class="ind"><img src="assets/img/ind-<?= $i + 1 ?>.webp" alt="<?= h($name) ?>" loading="lazy" width="360" height="299"><figcaption><?= h($name) ?></figcaption></figure>
            <?php endforeach; ?>
            <a class="ind ind-cta" href="#enquiry"><span>Your industry?</span><strong>Get the right compressor</strong><?= icon('arrow-right') ?></a>
        </div>
    </div>
</section>

<section class="sec" id="location">
    <div class="wrap">
        <div class="footprint">
            <div class="footprint-copy">
                <p class="kicker">Our Presence</p>
                <h2>Our Footprint Across Rajasthan</h2>
                <p>Strategically operating from our corporate hubs in Jaipur, we deliver top-tier industrial solutions, prompt sales assistance, and expert technical support to every major city and industrial cluster across Rajasthan.</p>
                <img class="map" src="assets/img/rajasthan-map.webp" alt="Ahuja Group coverage across Rajasthan" loading="lazy" width="840" height="782">
            </div>
            <div class="offices">
                <?php foreach ($offices as [$img, $title, $addr]): ?>
                <article class="office">
                    <img src="assets/img/<?= $img ?>" alt="<?= h($title) ?>" loading="lazy" width="389" height="257">
                    <div class="office-body">
                        <h3><?= h($title) ?></h3>
                        <p><?= icon('map-pin') ?> <?= h($addr) ?></p>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="sec sec-soft" id="services">
    <div class="wrap">
        <div class="sec-head center">
            <p class="kicker">Our Services</p>
            <h2>Comprehensive Industrial Services</h2>
            <p>At Ahuja Group, we don’t just supply products—we ensure they work flawlessly throughout their lifecycle. Our comprehensive range of industrial services is designed to maximize uptime, reduce operational costs, and ensure system reliability.</p>
        </div>
        <div class="services">
            <?php foreach ($services as $i => [$ic, $title, $text]): ?>
            <article class="svc">
                <span class="badge-ic"><?= icon($ic) ?></span>
                <span class="svc-n">0<?= $i + 1 ?></span>
                <h3><?= h($title) ?></h3>
                <p><?= h($text) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="sec" id="awards">
    <div class="wrap">
        <div class="sec-head center">
            <p class="kicker">Our Awards</p>
            <h2>Recognised by Industry Leaders</h2>
            <p>For over 50 years, Ahuja Group has been a trusted name in the industrial ecosystem — delivering high-performance solutions in air compressors, turnkey pipeline systems, pneumatics, and automation. Our relentless focus on quality, reliability, and service excellence has earned us recognition from some of the most respected names in the industry.</p>
        </div>
        <div class="awards">
            <?php foreach ($awards as [$img, $cap]): ?>
            <figure>
                <a href="assets/img/<?= $img ?>" data-lightbox><img src="assets/img/<?= $img ?>" alt="<?= h($cap) ?>" loading="lazy" width="560" height="596"></a>
                <figcaption><?= h($cap) ?></figcaption>
            </figure>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="sec sec-dark" id="why-choose">
    <div class="wrap">
        <div class="sec-head center">
            <p class="kicker">Why Choose Ahuja Group</p>
            <h2>Why Choose Ahuja Group as Your Anest Iwata Partner</h2>
            <p>Trusted by industries across India for reliable products, expert guidance, and strong after-sales support.</p>
        </div>
        <div class="partner">
            <div class="video">
                <button class="video-btn" type="button" data-yt="CpEE-Qftl14" aria-label="Play Ahuja Group video">
                    <img src="assets/img/video-thumb.webp" alt="" loading="lazy" width="480" height="360">
                    <span class="play"><?= icon('play') ?></span>
                </button>
            </div>
            <ul class="partner-list">
                <?php foreach ($partner as [$ic, $t]): ?>
                <li><span class="badge-ic"><?= icon($ic) ?></span><?= h($t) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<section class="sec">
    <div class="wrap">
        <div class="sec-head center">
            <p class="kicker">Why Choose Anest Iwata?</p>
            <h2>Japanese Engineering. Proven Across Thousands of Indian Installations.</h2>
            <p>Decades of innovation, industry-first technology, and proven reliability — powering industries across India since 1926.</p>
        </div>
        <div class="anest">
            <?php foreach ($anest as [$ic, $t, $d]): ?>
            <article><span class="badge-ic blue"><?= icon($ic) ?></span><h3><?= h($t) ?></h3><p><?= h($d) ?></p></article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="sec" id="faq">
    <div class="wrap faq-wrap">
        <div class="sec-head">
            <p class="kicker">Common Questions</p>
            <h2>Frequently Asked Questions</h2>
            <p>Still have a question? Our team is happy to help.</p>
            <div class="hero-btns">
                <a class="btn btn-red" href="<?= $tel ?>" data-track="call" data-section="FAQ"><?= icon('phone') ?> Call Now</a>
                <a class="btn btn-wa" href="<?= $waDefault ?>" target="_blank" rel="noopener" data-track="wa" data-section="FAQ"><?= icon('whatsapp') ?> WhatsApp</a>
            </div>
        </div>
        <div class="faq">
            <?php foreach ($faqs as $i => $f): ?>
            <details<?= $i === 0 ? ' open' : '' ?>><summary><?= h($f[0]) ?></summary><p><?= h($f[1]) ?></p></details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="sec sec-dark cta-final" id="contact">
    <div class="wrap cta-in">
        <div>
            <p class="kicker">Contact Us</p>
            <h2>Ready to talk? Our team is in Rajasthan.</h2>
            <p>Call us or send a quick message — we respond within 2 hours.</p>
            <div class="hero-btns">
                <a class="btn btn-red" href="<?= $tel ?>" data-track="call" data-section="Contact Section"><?= icon('phone') ?> Call <?= SITE_PHONE ?></a>
                <a class="btn btn-wa" href="<?= $waDefault ?>" target="_blank" rel="noopener" data-track="wa" data-section="Contact Section"><?= icon('whatsapp') ?> WhatsApp</a>
            </div>
            <ul class="contact-list">
                <li><?= icon('mail') ?> <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a></li>
                <li><?= icon('map-pin') ?> B-85, Bais Godam Industrial Area, 302006 – Jaipur</li>
            </ul>
        </div>
        <div class="form-card"><?php render_form('footer', $errForm === 'footer' ? $err : ''); ?></div>
    </div>
</section>
</main>

<footer class="footer">
    <div class="wrap footer-in">
        <div class="footer-brand">
            <img src="assets/img/ahuja-logo.webp" alt="Ahuja Group" width="140" height="70" class="footer-logo" loading="lazy">
            <p>Authorised dealer for Anest Iwata Motherson compressors &amp; vacuum pumps in Rajasthan.</p>
        </div>
        <div>
            <h3>Contact Us</h3>
            <ul class="footer-list">
                <li><?= icon('map-pin') ?> B-85, Bais Godam Industrial Area, 302006 – Jaipur</li>
                <li><?= icon('phone') ?> <a href="<?= $tel ?>" data-track="call" data-section="Footer"><?= SITE_PHONE ?></a></li>
                <li><?= icon('mail') ?> <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a></li>
            </ul>
        </div>
        <div>
            <h3>Links</h3>
            <ul class="footer-links">
                <?php foreach ($nav as $id => $label): ?><li><a href="#<?= $id ?>"><?= $label ?></a></li><?php endforeach; ?>
                <li><a href="https://ahujagroup.in/terms-of-use/" target="_blank" rel="noopener">Terms of Use</a></li>
                <li><a href="https://ahujagroup.in/privacy-policy/" target="_blank" rel="noopener">Privacy Policy</a></li>
            </ul>
        </div>
    </div>
    <p class="copy">Copyright © 2011 – <?= date('Y') ?> Ahuja Group, India. All rights reserved.</p>
</footer>

<nav class="mobile-bar" aria-label="Quick contact">
    <a href="<?= $tel ?>" data-track="call" data-section="Mobile Sticky Bar"><?= icon('phone') ?> Call</a>
    <a href="<?= $waDefault ?>" target="_blank" rel="noopener" data-track="wa" data-section="Mobile Sticky Bar" class="mb-wa"><?= icon('whatsapp') ?> WhatsApp</a>
    <a href="#enquiry" class="mb-quote">Get Quote</a>
</nav>

<dialog class="lightbox" id="lightbox" aria-label="Award image">
    <button type="button" class="lb-close" aria-label="Close">×</button>
    <img src="" alt="">
</dialog>

<script src="assets/js/main.js?v=6" defer></script>
</body>
</html>
