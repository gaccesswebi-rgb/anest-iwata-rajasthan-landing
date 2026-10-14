(function () {
  // ---- Ad attribution: remember the last ad click for 90 days so a lead submitted on a later visit is still credited. ----
  var CLICK_KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
    'campaignid', 'adgroupid', 'keyword', 'matchtype', 'device', 'network',
    'gclid', 'gbraid', 'wbraid', 'fbclid', 'msclkid'];
  var STORE = 'aw_attr', TTL = 90 * 864e5;
  var qs = new URLSearchParams(location.search);
  var current = { landing_url: location.href, referrer: document.referrer };
  var hasClick = false;
  CLICK_KEYS.forEach(function (k) { var v = qs.get(k); if (v) { current[k] = v; hasClick = true; } });

  var saved = null;
  try { saved = JSON.parse(localStorage.getItem(STORE) || 'null'); } catch (e) {}
  if (saved && Date.now() - saved.t > TTL) saved = null;
  // An internal reload (same-site referrer, no params) must not wipe a stored ad click.
  var external = !document.referrer || new URL(document.referrer).host !== location.host;
  var attr = saved ? saved.d : current;
  if (hasClick || (!saved && external)) {
    attr = current;
    try { localStorage.setItem(STORE, JSON.stringify({ t: Date.now(), d: attr })); } catch (e) {}
  }

  document.querySelectorAll('.lead-form').forEach(function (f) {
    Object.keys(attr).forEach(function (k) { if (f.elements[k]) f.elements[k].value = attr[k] || ''; });

    f.addEventListener('submit', function (e) {
      var bad = null;
      ['name', 'phone', 'email'].forEach(function (n) {
        var el = f.elements[n];
        var ok = el.checkValidity();
        el.classList.toggle('invalid', !ok);
        if (!ok && !bad) bad = el;
      });
      if (bad) { e.preventDefault(); bad.focus(); bad.reportValidity(); return; }
      var btn = f.querySelector('button[type=submit]');
      btn.disabled = true; btn.textContent = 'Sending…';
    });
  });

  // ---- Product filter tabs (a product can sit in several categories) ----
  var tabs = document.querySelectorAll('.tab');
  function filterProducts(f) {
    tabs.forEach(function (x) { var on = x.dataset.filter === f; x.classList.toggle('is-on', on); x.setAttribute('aria-selected', on); });
    document.querySelectorAll('.product').forEach(function (p) { p.hidden = f !== 'all' && p.dataset.cat.split(' ').indexOf(f) < 0; });
  }
  tabs.forEach(function (t) { t.addEventListener('click', function () { filterProducts(t.dataset.filter); }); });
  document.querySelectorAll('[data-goto]').forEach(function (a) {
    a.addEventListener('click', function () { filterProducts(a.dataset.goto); });
  });

  // ---- YouTube loads only when clicked (keeps the page fast for Ads quality score) ----
  var vb = document.querySelector('[data-yt]');
  if (vb) vb.addEventListener('click', function () {
    var f = document.createElement('iframe');
    f.src = 'https://www.youtube-nocookie.com/embed/' + vb.dataset.yt + '?autoplay=1&rel=0';
    f.title = 'Ahuja Group video';
    f.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
    f.allowFullscreen = true;
    vb.replaceWith(f);
  });

  // ---- Award lightbox ----
  var lb = document.getElementById('lightbox');
  if (lb && lb.showModal) {
    var lbImg = lb.querySelector('img');
    document.querySelectorAll('[data-lightbox]').forEach(function (a) {
      a.addEventListener('click', function (e) {
        e.preventDefault();
        lbImg.src = a.href; lbImg.alt = a.querySelector('img').alt;
        lb.showModal();
      });
    });
    lb.addEventListener('click', function (e) { if (e.target === lb || e.target.classList.contains('lb-close')) lb.close(); });
  }

  // ---- Call / WhatsApp clicks: log to CRM (section + product + ad source) and fire Google Ads conversion ----
  document.querySelectorAll('[data-track]').forEach(function (a) {
    a.addEventListener('click', function () {
      var fd = new FormData();
      fd.append('type', a.dataset.track === 'wa' ? 'whatsapp' : 'call');
      fd.append('section', a.dataset.section || '');
      fd.append('product', a.dataset.product || '');
      Object.keys(attr).forEach(function (k) { fd.append(k, attr[k] || ''); });
      // sendBeacon survives the page switching to the dialer / WhatsApp.
      if (!(navigator.sendBeacon && navigator.sendBeacon('cta.php', fd))) {
        fetch('cta.php', { method: 'POST', body: fd, keepalive: true }).catch(function () {});
      }
      // For GTM: trigger on Custom Event "cta_click" (GA4 event + Google Ads conversion tags).
      window.dataLayer = window.dataLayer || [];
      dataLayer.push({ event: 'cta_click', cta_type: fd.get('type'), cta_section: a.dataset.section || '', cta_product: a.dataset.product || '' });
      var g = window.GADS, label = g && g[a.dataset.track];
      if (label && window.gtag) gtag('event', 'conversion', { send_to: g.id + '/' + label });
    });
  });
})();
