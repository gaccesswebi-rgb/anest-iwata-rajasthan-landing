<?php
require __DIR__ . '/auth.php';
require_login();

$pdo = db();
$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $set = ['updated_at = ?'];
    $p = [date('Y-m-d H:i:s')];
    $new = $_POST['status'] ?? '';
    if (in_array($new, LEAD_STATUSES, true)) {
        $set[] = 'status = ?';
        $p[] = $new;
        $old = $pdo->prepare('SELECT status FROM leads WHERE id = ?');
        $old->execute([$id]);
        if ($old->fetchColumn() !== $new) {
            $pdo->prepare('INSERT INTO lead_events (lead_id, status, created_at) VALUES (?, ?, ?)')->execute([$id, $new, $p[0]]);
        }
    }
    if (isset($_POST['notes'])) { $set[] = 'notes = ?'; $p[] = mb_substr($_POST['notes'], 0, 5000); }
    $p[] = $id;
    $pdo->prepare('UPDATE leads SET ' . implode(', ', $set) . ' WHERE id = ?')->execute($p);
    header('Location: ' . (isset($_POST['back']) ? './?' . $_POST['back'] : "lead.php?id=$id&saved=1"));
    exit;
}

$st = $pdo->prepare('SELECT * FROM leads WHERE id = ?');
$st->execute([$id]);
$l = $st->fetch();
if (!$l) {
    http_response_code(404);
    exit('Lead not found');
}

// Google Ads ValueTrack short codes -> readable labels.
$vt = ['e' => 'Exact', 'p' => 'Phrase', 'b' => 'Broad', 'm' => 'Mobile', 'c' => 'Computer', 't' => 'Tablet',
    'g' => 'Google Search', 's' => 'Search Partner', 'd' => 'Display', 'ytv' => 'YouTube', 'x' => 'Performance Max'];
$code = fn($v) => h(isset($vt[(string)$v]) ? "{$vt[$v]} ($v)" : $v);

$rows = [
    'Platform'       => platform_badge($l['platform'] ?? ''),
    'Campaign'       => h(campaign_label($l)),
    'Keyword'        => h($l['keyword'] ?: $l['utm_term']),
    'Match type'     => $code($l['matchtype']),
    'Ad group ID'    => h($l['adgroupid']),
    'Campaign ID'    => h($l['campaignid']),
    'Device'         => $code($l['device']),
    'Network'        => $code($l['network']),
    'utm_source'     => h($l['utm_source']),
    'utm_medium'     => h($l['utm_medium']),
    'utm_campaign'   => h($l['utm_campaign']),
    'utm_term'       => h($l['utm_term']),
    'utm_content'    => h($l['utm_content']),
    'GCLID'          => h($l['gclid'] ?: ($l['gbraid'] ?: $l['wbraid'])),
    'FBCLID'         => h($l['fbclid']),
    'MSCLKID'        => h($l['msclkid']),
    'Landing URL'    => h($l['landing_url']),
    'Referrer'       => h($l['referrer'] ?: '(none)'),
    'Form'           => h($l['form']),
    'IP'             => h($l['ip']),
    'Browser'        => h($l['user_agent']),
];

$ev = $pdo->prepare('SELECT status, created_at FROM lead_events WHERE lead_id = ? ORDER BY id');
$ev->execute([$id]);
$history = $ev->fetchAll();

crm_header('Lead #' . $l['id']);
?>
<p><a href="./">← All leads</a></p>
<?php if (isset($_GET['saved'])): ?><p class="alert ok">Saved.</p><?php endif; ?>

<div class="lead-grid">
    <section class="card">
        <h1><?= h($l['name']) ?> <?= badge($l['status']) ?></h1>
        <p class="muted">Lead #<?= $l['id'] ?> · <?= date('d M Y, h:i A', strtotime($l['created_at'])) ?><?= $l['updated_at'] ? ' · updated ' . date('d M, h:i A', strtotime($l['updated_at'])) : '' ?></p>
        <div class="contact-btns">
            <a class="btn" href="tel:+91<?= h($l['phone']) ?>">Call <?= h($l['phone']) ?></a>
            <a class="btn btn-green" href="https://wa.me/91<?= h($l['phone']) ?>" target="_blank" rel="noopener">WhatsApp</a>
            <?php if ($l['email']): ?><a class="btn btn-light" href="mailto:<?= h($l['email']) ?>"><?= h($l['email']) ?></a><?php endif; ?>
        </div>
        <dl class="kv">
            <dt>Landing page</dt><dd><?= site_badge($l['site']) ?></dd>
            <?php if ($l['company']): ?><dt>Company</dt><dd><?= h($l['company']) ?></dd><?php endif; ?>
            <dt>City</dt><dd><?= h($l['city']) ?: '—' ?></dd>
            <dt>Requirement</dt><dd><?= h($l['product']) ?: '—' ?></dd>
            <dt>Message</dt><dd><?= nl2br(h($l['message'])) ?: '—' ?></dd>
        </dl>
        <form method="post" class="stack">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $l['id'] ?>">
            <label>Status
                <select name="status" class="st-select st-<?= status_slug($l['status']) ?>"><?php foreach (LEAD_STATUSES as $s): ?><option <?= $l['status'] === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select>
            </label>
            <label>Notes / follow-up
                <textarea name="notes" rows="6" placeholder="Call outcome, quoted price, next follow-up date..."><?= h($l['notes']) ?></textarea>
            </label>
            <button class="btn">Save</button>
        </form>
        <h2 class="mt">Status history</h2>
        <ul class="history">
            <li><?= badge('New') ?> <small><?= date('d M y, h:i A', strtotime($l['created_at'])) ?></small></li>
            <?php foreach ($history as $e): ?>
                <li><?= badge($e['status']) ?> <small><?= date('d M y, h:i A', strtotime($e['created_at'])) ?></small>
                <?php if (isset(OCI_STAGES[$e['status']])): ?><small><?= $l['gclid'] ? '→ sent to Google Ads as <code>' . h(OCI_STAGES[$e['status']]['name']) . '</code>' : '(not a Google Ads click – no signal)' ?></small><?php endif; ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="card">
        <h2>Source &amp; campaign</h2>
        <dl class="kv kv-track">
            <?php foreach ($rows as $k => $v): if ($v === '') continue; ?>
                <dt><?= $k ?></dt><dd><?= $v ?></dd>
            <?php endforeach; ?>
        </dl>
    </section>
</div>
<?php crm_footer();
