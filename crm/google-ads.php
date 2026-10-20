<?php
require __DIR__ . '/auth.php';
require_login();

$rows = oci_conversions(db());
$ready = array_filter($rows, fn($r) => (string)$r['gclid'] !== '');
$scheme = is_https() ? 'https' : 'http';
$feedUrl = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'yourdomain') . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/oci.php';
$feedOn = strlen(OCI_FEED_PASSWORD) >= 16;

crm_header('Google Ads Sync');
?>
<h1>Google Ads Sync</h1>
<p class="muted">When a lead's status reaches a stage below, Google Ads is told that this ad click became a quality lead. Smart Bidding then looks for more people like them.</p>

<section class="stats">
    <div><span>Signals ready to send (last 90 days)</span><strong class="t-green"><?= count($ready) ?></strong></div>
    <div><span>Can't send (no Google click ID)</span><strong><?= count($rows) - count($ready) ?></strong></div>
</section>

<div class="two-col">
    <div class="card">
        <h2>CRM status → Google Ads conversion</h2>
        <table>
            <thead><tr><th>When status becomes</th><th>Google Ads conversion name</th></tr></thead>
            <tbody>
            <?php foreach (OCI_STAGES as $status => $s): ?>
                <tr><td><?= badge($status) ?></td><td><code><?= h($s['name']) ?></code></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p class="muted">Create conversion actions with exactly these names in Google Ads → Goals → Conversions → New → Import → "CRM, files, or other data sources" → Track conversions from clicks. Change the mapping in <code>config.php</code> (<code>OCI_STAGES</code>).</p>
    </div>
    <div class="card">
        <h2>Send to Google Ads</h2>
        <p><strong>Option A – automatic (recommended):</strong> Google Ads → Goals → Conversions → Uploads → Schedules → Source <em>HTTPS</em>, daily.</p>
        <dl class="kv">
            <dt>URL</dt><dd><code><?= h($feedUrl) ?></code></dd>
            <dt>Username</dt><dd><code><?= h(OCI_FEED_USER) ?></code></dd>
            <dt>Password</dt><dd><?= $feedOn ? 'Set in <code>config.local.php</code> (OCI_FEED_PASSWORD)' : '<span class="t-red">Not set</span> – put a 16+ character password in <code>config.local.php</code> → <code>OCI_FEED_PASSWORD</code>' ?></dd>
        </dl>
        <p><strong>Option B – manual:</strong> download the file and upload it in Conversions → Uploads.</p>
        <a class="btn" href="oci.php?download=1">Download Google Ads CSV</a>
    </div>
</div>

<h2>Signals (last 90 days)</h2>
<div class="table-wrap">
<table>
    <thead><tr><th>Lead</th><th>Stage reached</th><th>Google Ads conversion</th><th>When</th><th>Will it be sent?</th></tr></thead>
    <tbody>
    <?php if (!$rows): ?><tr><td colspan="5" class="empty">Nothing yet. Mark a lead as <?= implode(' or ', array_map('h', array_keys(OCI_STAGES))) ?> to create a signal.</td></tr><?php endif; ?>
    <?php foreach ($rows as $r): ?>
        <tr>
            <td><a href="lead.php?id=<?= $r['lead_id'] ?>">#<?= $r['lead_id'] ?> <?= h($r['name']) ?></a></td>
            <td><?= badge($r['status']) ?></td>
            <td><code><?= h(OCI_STAGES[$r['status']]['name']) ?></code></td>
            <td class="nowrap"><?= date('d M y, h:i A', strtotime($r['conv_time'])) ?></td>
            <td><?php if ((string)$r['gclid'] !== ''): ?>✅ Yes
                <?php elseif ($r['gbraid'] || $r['wbraid']): ?>⚠️ No – iPhone app click (gbraid), not supported by file upload
                <?php else: ?>— No – lead came from <?= h($r['platform']) ?>, not a Google Ads click<?php endif; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php crm_footer();
