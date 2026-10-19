<?php
// Include right after <meta charset> / viewport. Pages may set $pageType and $dlEvents before including.
$dlPush = array_merge([['page_type' => $pageType ?? 'landing_page', 'page_name' => 'anest-iwata-rajasthan']], $dlEvents ?? []);
?>
<script>
  window.dataLayer = window.dataLayer || [];
<?php foreach ($dlPush as $e): ?>
  dataLayer.push(<?= json_encode($e, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);
<?php endforeach; ?>
</script>
<?php if (GTM_ID): ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?= h(GTM_ID) ?>');</script>
<!-- End Google Tag Manager -->
<?php endif; ?>
<?php if (GADS_ID): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= h(GADS_ID) ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?= h(GADS_ID) ?>');
  window.GADS = {id: '<?= h(GADS_ID) ?>', call: '<?= h(GADS_CALL_LABEL) ?>', wa: '<?= h(GADS_WA_LABEL) ?>'};
</script>
<?php endif; ?>
