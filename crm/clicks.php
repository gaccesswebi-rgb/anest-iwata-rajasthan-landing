<?php
require __DIR__ . '/auth.php';
require_login();

$pdo = db();
// Reuses the lead filters (platform / campaign / date); q and status are not used on this page.
[$where, $params] = lead_filters(array_intersect_key($_GET, array_flip(['platform', 'campaign', 'from', 'to'])));
if (in_array($_GET['type'] ?? '', ['call', 'whatsapp'], true)) {
    $where .= ($where ? ' AND' : 'WHERE') . ' type = ?';
    $params[] = $_GET['type'];
}
$per = 50;
$page = max(1, (int)($_GET['page'] ?? 1));

$q = function (string $sql, array $p = []) use ($pdo) {
    $st = $pdo->prepare($sql);
    $st->execute($p);
    return $st;
};
$counts = "SUM(type = 'call') AS calls, SUM(type = 'whatsapp') AS wa, COUNT(*) AS total";

$stats = $q("SELECT $counts, SUM(type = 'call' AND created_at >= ?) AS calls_today, SUM(type = 'whatsapp' AND created_at >= ?) AS wa_today
    FROM clicks $where", array_merge([date('Y-m-d 00:00:00'), date('Y-m-d 00:00:00')], $params))->fetch();
$bySection = $q("SELECT COALESCE(NULLIF(section, ''), '(unknown)') AS label, $counts FROM clicks $where GROUP BY label ORDER BY total DESC", $params)->fetchAll();
$whereProd = ($where ? "$where AND" : 'WHERE') . " product <> ''";
$byProduct = $q("SELECT product AS label, $counts FROM clicks $whereProd GROUP BY product ORDER BY total DESC", $params)->fetchAll();
$byCampaign = $q("SELECT platform, " . CAMPAIGN_SQL . " AS campaign, $counts FROM clicks $where GROUP BY platform, campaign ORDER BY total DESC", $params)->fetchAll();

$total = (int)$stats['total'];
$pages = max(1, (int)ceil($total / $per));
$recent = $q("SELECT * FROM clicks $where ORDER BY id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $params)->fetchAll();

$platformOpts = $pdo->query('SELECT DISTINCT platform FROM clicks ORDER BY platform')->fetchAll(PDO::FETCH_COLUMN);
$campaignOpts = $pdo->query('SELECT DISTINCT ' . CAMPAIGN_SQL . ' AS c FROM clicks ORDER BY c')->fetchAll(PDO::FETCH_COLUMN);
$f = fn($k) => h($_GET[$k] ?? '');
$qsNoPage = http_build_query(array_diff_key($_GET, ['page' => 1]));

function count_table(string $title, string $col, array $rows): void
{ ?>
    <div class="table-wrap">
    <table>
        <thead><tr><th><?= $title ?></th><th class="num">📞 Calls</th><th class="num">💬 WhatsApp</th><th class="num">Total</th></tr></thead>
        <tbody>
        <?php if (!$rows): ?><tr><td colspan="4" class="empty">No clicks yet.</td></tr><?php endif; ?>
        <?php foreach ($rows as $r): ?>
            <tr><td><?= h($r[$col]) ?></td><td class="num"><?= (int)$r['calls'] ?></td><td class="num"><?= (int)$r['wa'] ?></td><td class="num"><strong><?= (int)$r['total'] ?></strong></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php }

crm_header('Call & WhatsApp Clicks');
?>
<h1>Call &amp; WhatsApp clicks</h1>
<p class="muted">Every tap on a Call or WhatsApp button on the landing page. The caller's number is not available — this shows how many taps, where on the page, and which ad they came from.</p>

<section class="stats">
    <div><span>📞 Call clicks<?= $where ? ' (filtered)' : '' ?></span><strong><?= (int)$stats['calls'] ?></strong></div>
    <div><span>💬 WhatsApp clicks<?= $where ? ' (filtered)' : '' ?></span><strong class="t-green"><?= (int)$stats['wa'] ?></strong></div>
    <div><span>📞 Calls today</span><strong><?= (int)$stats['calls_today'] ?></strong></div>
    <div><span>💬 WhatsApp today</span><strong class="t-green"><?= (int)$stats['wa_today'] ?></strong></div>
</section>

<form class="filters" method="get">
    <select name="type"><option value="">Call + WhatsApp</option>
        <option value="call" <?= ($_GET['type'] ?? '') === 'call' ? 'selected' : '' ?>>Call only</option>
        <option value="whatsapp" <?= ($_GET['type'] ?? '') === 'whatsapp' ? 'selected' : '' ?>>WhatsApp only</option>
    </select>
    <select name="platform"><option value="">All platforms</option>
        <?php foreach ($platformOpts as $s): ?><option <?= ($_GET['platform'] ?? '') === $s ? 'selected' : '' ?>><?= h($s) ?></option><?php endforeach; ?>
    </select>
    <select name="campaign"><option value="">All campaigns</option>
        <?php foreach ($campaignOpts as $s): if ($s === '') continue; ?><option <?= ($_GET['campaign'] ?? '') === $s ? 'selected' : '' ?>><?= h($s) ?></option><?php endforeach; ?>
    </select>
    <label>From <input type="date" name="from" value="<?= $f('from') ?>"></label>
    <label>To <input type="date" name="to" value="<?= $f('to') ?>"></label>
    <button class="btn">Filter</button>
    <a class="btn btn-light" href="clicks.php">Reset</a>
</form>

<div class="two-col">
    <div>
        <h2>By section of the page</h2>
        <?php count_table('Section', 'label', $bySection); ?>
    </div>
    <div>
        <h2>By product</h2>
        <?php count_table('Product', 'label', $byProduct); ?>
    </div>
</div>

<h2>By platform &amp; campaign</h2>
<div class="table-wrap">
<table>
    <thead><tr><th>Platform</th><th>Campaign</th><th class="num">📞 Calls</th><th class="num">💬 WhatsApp</th><th class="num">Total</th></tr></thead>
    <tbody>
    <?php if (!$byCampaign): ?><tr><td colspan="5" class="empty">No clicks yet.</td></tr><?php endif; ?>
    <?php foreach ($byCampaign as $c): ?>
        <tr>
            <td><?= platform_badge($c['platform'] ?? '') ?></td>
            <td><a href="?<?= h(http_build_query(['platform' => $c['platform'], 'campaign' => $c['campaign']])) ?>"><?= h($c['campaign'] ?: '—') ?></a></td>
            <td class="num"><?= (int)$c['calls'] ?></td><td class="num"><?= (int)$c['wa'] ?></td><td class="num"><strong><?= (int)$c['total'] ?></strong></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<h2>Recent clicks</h2>
<div class="table-wrap">
<table>
    <thead><tr><th>Date</th><th>Type</th><th>Section</th><th>Product</th><th>Platform</th><th>Campaign / Keyword</th><th>Device</th></tr></thead>
    <tbody>
    <?php if (!$recent): ?><tr><td colspan="7" class="empty">No clicks yet.</td></tr><?php endif; ?>
    <?php foreach ($recent as $r): ?>
        <tr>
            <td class="nowrap"><?= date('d M y', strtotime($r['created_at'])) ?><br><small><?= date('h:i A', strtotime($r['created_at'])) ?></small></td>
            <td><?= $r['type'] === 'call' ? '<span class="badge st-contacted">📞 Call</span>' : '<span class="badge st-won">💬 WhatsApp</span>' ?></td>
            <td><?= h($r['section']) ?></td>
            <td class="req"><?= h($r['product']) ?: '—' ?></td>
            <td><?= platform_badge($r['platform'] ?? '') ?></td>
            <td class="req"><?= h(campaign_label($r)) ?><?php $kw = $r['keyword'] ?: $r['utm_term']; if ($kw): ?><br><small>🔑 <?= h($kw) ?></small><?php endif; ?></td>
            <td><?= h(preg_match('/Mobi|Android|iPhone/i', (string)$r['user_agent']) ? 'Mobile' : 'Desktop') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php if ($pages > 1): ?>
<nav class="pager">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a class="<?= $i === $page ? 'on' : '' ?>" href="?<?= h($qsNoPage ? "$qsNoPage&" : '') ?>page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
</nav>
<?php endif; ?>
<?php crm_footer();
