/* QuickAir landing page: menu, CTA routing, Google Ads attribution, click + lead tracking.
   Tracking goes to the shared CRM (../cta.php, ../submit.php) and to GTM with the same events as the Anest Iwata page. */
(function () {
  'use strict';

  var ROOT = '../'; // shared CRM endpoints in the project root
  var ATTR_KEY = 'qa_attr';
  var ATTR_TTL = 90 * 24 * 3600 * 1000; // keep ad attribution for 90 days (Google Ads click window)

  // URL params we capture. Map: stored field -> list of URL param names (first non-empty wins).
  var PARAMS = {
    utm_source: ['utm_source'],
    utm_medium: ['utm_medium'],
    utm_campaign: ['utm_campaign', 'campaign'],
    utm_term: ['utm_term'],
    utm_content: ['utm_content'],
    campaign_id: ['campaignid', 'campaign_id', 'utm_id'],
    adgroup_id: ['adgroupid', 'adgroup_id'],
    adgroup: ['adgroup', 'ad_group'],
    keyword: ['keyword', 'utm_term'],
    matchtype: ['matchtype'],
    device: ['device'],
    network: ['network'],
    placement: ['placement'],
    creative: ['creative', 'adid'],
    gclid: ['gclid'],
    gbraid: ['gbraid'],
    wbraid: ['wbraid']
  };

  function store(kind) {
    try { return window[kind]; } catch (e) { return null; }
  }
  function readJSON(key) {
    try { return JSON.parse((store('localStorage') || {}).getItem(key)) || null; } catch (e) { return null; }
  }
  function writeJSON(key, val) {
    try { store('localStorage').setItem(key, JSON.stringify(val)); } catch (e) { /* storage blocked: attribution lives for this page view only */ }
  }

  /* ---------- Attribution ---------- */
  var qs = new URLSearchParams(location.search);
  var fromUrl = {};
  var hasAdParams = false;
  Object.keys(PARAMS).forEach(function (field) {
    for (var i = 0; i < PARAMS[field].length; i++) {
      var v = qs.get(PARAMS[field][i]);
      if (v) { fromUrl[field] = v.slice(0, 255); hasAdParams = true; break; }
    }
  });

  var attr = readJSON(ATTR_KEY);
  if (attr && Date.now() - attr.ts > ATTR_TTL) attr = null;
  if (hasAdParams || !attr) {
    // Last ad click wins; a plain revisit keeps the earlier ad attribution.
    attr = Object.assign({}, fromUrl, {
      landing_url: location.href.slice(0, 1000),
      referrer: document.referrer.slice(0, 500),
      ts: Date.now()
    });
    writeJSON(ATTR_KEY, attr);
  }

  /* ---------- GTM (snippet is in <head>; event names match the Anest Iwata page) ---------- */
  window.dataLayer = window.dataLayer || [];
  function dl(obj) { window.dataLayer.push(obj); }

  /* ---------- Shared CRM ---------- */
  // Attribution in the shape the CRM expects (TRACK_FIELDS in inc/app.php).
  function crmFields() {
    var d = { site: 'quickair' };
    ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'keyword', 'matchtype', 'device',
      'network', 'gclid', 'gbraid', 'wbraid', 'landing_url', 'referrer'].forEach(function (k) { d[k] = attr[k] || ''; });
    d.campaignid = attr.campaign_id || '';
    d.adgroupid = attr.adgroup_id || '';
    return d;
  }
  var SECTION_NAMES = {
    topbar: 'Top Bar', header: 'Header', hero: 'Hero', 'quote-form': 'Quote Section', 'quote-section': 'Quote Section',
    'cta-compare': 'Compare CTA', 'cta-projects': 'Projects CTA', contact: 'Contact Section', footer: 'Footer',
    'sticky-bar': 'Mobile Sticky Bar', thankyou: 'Thank-you Page', 'thank-you': 'Thank-you Page', faq: 'FAQ',
    'logo-strip': 'Clients'
  };
  function sectionName(place) {
    return SECTION_NAMES[place] || String(place).replace(/-/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); });
  }
  function toCrm(type, section) {
    var body = new FormData();
    var d = crmFields();
    Object.keys(d).forEach(function (k) { body.append(k, d[k]); });
    body.append('type', type);
    body.append('section', section || '');
    body.append('product', '');
    // sendBeacon survives the page switching to the dialer / WhatsApp / mail app.
    if (navigator.sendBeacon && navigator.sendBeacon(ROOT + 'cta.php', body)) return;
    fetch(ROOT + 'cta.php', { method: 'POST', body: body, keepalive: true }).catch(function () {});
  }

  /* ---------- Click tracking (phone / WhatsApp / email) ---------- */
  function linkType(a) {
    if (a.dataset.track) return a.dataset.track;
    var h = a.getAttribute('href') || '';
    if (h.indexOf('tel:') === 0) return 'phone';
    if (/wa\.me|whatsapp/.test(h)) return 'whatsapp';
    if (h.indexOf('mailto:') === 0) return 'email';
    return null;
  }
  function placeOf(el) {
    if (el.dataset.place) return el.dataset.place;
    var sec = el.closest('[data-section]');
    if (sec) return sec.dataset.section;
    if (el.closest('.site-header')) return 'header';
    if (el.closest('.sticky-bar')) return 'sticky-bar';
    if (el.closest('.topbar')) return 'topbar';
    if (el.closest('.site-footer')) return 'footer';
    return 'unknown';
  }
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a');
    if (!a) return;
    var type = linkType(a);
    if (type) {
      var crmType = type === 'phone' ? 'call' : type; // call | whatsapp | email
      var section = sectionName(placeOf(a));
      toCrm(crmType, section);
      dl({ event: 'cta_click', cta_type: crmType, cta_section: section, cta_product: '' });
    }
  });

  /* ---------- Mobile menu ---------- */
  var nav = document.getElementById('main-nav');
  var toggle = document.querySelector('.menu-toggle');
  if (nav && toggle) {
  function setMenu(open) {
    nav.classList.toggle('open', open);
    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    toggle.querySelector('use').setAttribute('href', open ? '#i-x' : '#i-menu');
  }
  toggle.addEventListener('click', function () { setMenu(!nav.classList.contains('open')); });
  nav.addEventListener('click', function (e) { if (e.target.closest('a')) setMenu(false); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setMenu(false); });
  document.addEventListener('click', function (e) {
    if (nav.classList.contains('open') && !e.target.closest('#main-nav, .menu-toggle')) setMenu(false);
  });
  }

  /* ---------- In-page links: land exactly on the section ---------- */
  // Lazy images above the target can load mid-scroll and push it down; re-align once scrolling stops.
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a[href^="#"]');
    if (!a || a.getAttribute('href').length < 2) return;
    var target = document.getElementById(a.getAttribute('href').slice(1));
    if (!target) return;
    e.preventDefault();
    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    history.replaceState(null, '', a.getAttribute('href'));
    var settle = function () {
      var off = parseFloat(getComputedStyle(document.documentElement).scrollPaddingTop) || 0;
      if (Math.abs(target.getBoundingClientRect().top - off) > 4) target.scrollIntoView({ block: 'start' });
    };
    if ('onscrollend' in window) window.addEventListener('scrollend', settle, { once: true });
    else setTimeout(settle, 900);
  });

  /* ---------- Back to top ---------- */
  var toTop = document.querySelector('.to-top');
  if (toTop) window.addEventListener('scroll', function () { toTop.classList.toggle('show', window.scrollY > 700); }, { passive: true });

  var y = document.getElementById('year');
  if (y) y.textContent = new Date().getFullYear();

  /* ---------- CTA buttons -> quote form ---------- */
  var form = document.getElementById('quote-form');
  if (!form) return; // pages without the quote form (thank-you page)
  var quote = document.getElementById('quote');
  var title = document.getElementById('quote-title');
  document.querySelectorAll('[data-cta]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var label = btn.dataset.cta;
      form.elements.cta.value = label;
      form.elements.form_place.value = placeOf(btn);
      title.textContent = label;
      var section = sectionName(placeOf(btn));
      dl({ event: 'get_quote_click', cta_section: section, cta_label: label });
      toCrm('get_quote', section);
      quote.classList.remove('flash');
      void quote.offsetWidth;
      quote.classList.add('flash');
      setTimeout(function () { form.elements.name.focus({ preventScroll: true }); }, 600);
    });
  });

  /* ---------- Lead form: normal POST to ../submit.php -> CRM -> thank-you.php?lead=<id> ---------- */
  var msg = form.querySelector('.form-msg');
  var submitBtn = form.querySelector('[type=submit]');

  var fields = crmFields();
  Object.keys(fields).forEach(function (k) {
    if (form.elements[k]) { form.elements[k].value = fields[k]; return; }
    var i = document.createElement('input');
    i.type = 'hidden'; i.name = k; i.value = fields[k];
    form.appendChild(i);
  });

  var started = false;
  form.addEventListener('focusin', function () {
    if (started) return;
    started = true;
    dl({ event: 'form_start', form_id: 'quickair', form_section: 'Quote Section' });
    toCrm('form_start', 'Quote Section');
  });

  function showMsg(text, isError) {
    msg.textContent = text;
    msg.classList.toggle('error', !!isError);
  }
  function validate() {
    var ok = true;
    ['name', 'company', 'phone', 'email'].forEach(function (n) {
      var el = form.elements[n];
      var v = el.value.trim();
      var bad = !v ||
        (n === 'phone' && !/^[6-9]\d{9}$/.test(v.replace(/\D/g, '').slice(-10))) ||
        (n === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v));
      el.classList.toggle('invalid', bad);
      if (bad && ok) { el.focus(); ok = false; }
    });
    return ok;
  }

  form.addEventListener('submit', function (e) {
    if (!validate()) { e.preventDefault(); showMsg('Please fill all fields correctly (10-digit mobile, valid email).', true); return; }
    dl({ event: 'form_submit', form_id: 'quickair', form_section: 'Quote Section', form_product: form.elements.product.value || '', cta_label: form.elements.cta.value });
    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending…';
  });

  // Server-side validation errors come back as ?err=… (message printed by PHP): bring the form into view.
  if (qs.get('err')) document.getElementById('quote').scrollIntoView();
})();
