<?php
require __DIR__ . '/auth.php';
require_login();

$pdo = db();
// Reuses the lead filters (platform / campaign / date); q and status are not used on this page.
$base = array_intersect_key($_GET, array_flip(['site', 'platform', 'campaign', 'from', 'to']));
[$where, $params] = lead_filters($base);
[$leadWhere, $leadParams] = lead_filters($base);
$type = $_GET['type'] ?? '';
if (isset(CTA_TYPES[$type])) {
    $where .= ($where ? ' AND' : 'WHERE') . ' type = ?';
    $params[] = $type;
}
$per = 50;
$page = max(1, (int)($_GET['page'] ?? 1));

$q = function (string $sql, array $p = []) use ($pdo) {
    $st = $pdo->prepare($sql);
    $st->execute($p);
    return $st;
};
// One count column per event type (keys come from the CTA_TYPES constant, not user input).
$counts = implode(', ', array_map(fn($k) => "SUM(type = '$k') AS $k", array_keys(CTA_TYPES))) . ', COUNT(*) AS total';
$today = date('Y-m-d 00:00:00');

$stats = $q("SELECT $counts FROM clicks $where", $params)->fetch();
$todayWhere = ($where ? "$where AND" : 'WHERE') . ' created_at >= ?';
$statsToday = $q("SELECT $counts FROM clicks $todayWhere", array_merge($params, [$today]))->fetch();
$bySection = $q("SELECT COALESCE(NULLIF(section, ''), '(unknown)') AS label, $counts FROM clicks $where GROUP BY label ORDER BY total DESC", $params)->fetchAll();
$whereProd = ($where ? "$where AND" : 'WHERE') . " product <> ''";
$byProduct = $q("SELECT product AS label, $counts FROM clicks $whereProd GROUP BY product ORDER BY total DESC", $params)->fetchAll();
$byCampaign = $q("SELECT platform, " . CAMPAIGN_SQL . " AS campaign, $counts FROM clicks $where GROUP BY platform, campaign ORDER BY total DESC", $params)->fetchAll();

// Form funnel: forms started vs. leads actually submitted (same platform / campaign / date filters).
$leadsCount = (int)$q("SELECT COUNT(*) FROM leads $leadWhere", $leadParams)->fetchColumn();
$formStarts = (int)$q("SELECT COUNT(*) FROM clicks " . ($leadWhere ? "$leadWhere AND" : 'WHERE') . " type = 'form_start'", $leadParams)->fetchColumn();

$total = (int)$stats['total'];
$pages = max(1, (int)ceil($total / $per));
$recent = $q("SELECT * FROM clicks $where ORDER BY id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $params)->fetchAll();

$platformOpts = $pdo->query('SELECT DISTINCT platform FROM clicks ORDER BY platform')->fetchAll(PDO::FETCH_COLUMN);
$campaignOpts = $pdo->query('SELECT DISTINCT ' . CAMPAIGN_SQL . ' AS c FROM clicks ORDER BY c')->fetchAll(PDO::FETCH_COLUMN);
$f = fn($k) => h($_GET[$k] ?? '');
$qsNoPage = http_build_query(array_diff_key($_GET, ['page' => 1]));

function type_cells(array $r): string
{
    $out = '';
    foreach (array_keys(CTA_TYPES) as $k) $out .= '<td class="num">' . (int)$r[$k] . '</td>';
    return $out . '<td class="num"><strong>' . (int)$r['total'] . '</strong></td>';
}

function type_heads(): string
{
    $out = '';
    foreach (CTA_TYPES as $label) $out .= '<th class="num">' . h($label) . '</th>';
    return $out . '<th class="num">Total</th>';
}

function count_table(string $title, array $rows): void
{ ?>
    <div class="table-wrap">
    <table>
        <thead><tr><th><?= $title ?></th><?= type_heads() ?></tr></thead>
        <tbody>
        <?php if (!$rows): ?><tr><td colspan="<?= count(CTA_TYPES) + 2 ?>" class="empty">No data yet.</td></tr><?php endif; ?>
        <?php foreach ($rows as $r): ?><tr><td><?= h($r['label']) ?></td><?= type_cells($r) ?></tr><?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php }

crm_header('Clicks & Events');
?>
<h1>Clicks &amp; visitor events</h1>
<p class="muted">Every Call, WhatsApp, Email and Get Quote tap, every form a visitor starts filling, and every video play on both landing pages (Anest Iwata, QuickAir) — the same events that go to Google Tag Manager. Caller numbers are not available; this shows how many, where on the page, and which ad they came from.</p>

<section class="stats">
    <?php foreach (CTA_TYPES as $k => $label): ?>
    <a class="stat-link" href="?<?= h(http_build_query($base + ['type' => $k])) ?>"><span><?= h($label) ?><?= $where && !$type ? ' (filtered)' : '' ?></span><strong><?= (int)$stats[$k] ?></strong><small>Today <?= (int)$statsToday[$k] ?></small></a>
    <?php endforeach; ?>
    <div><span>Form funnel</span><strong><?= $leadsCount ?> / <?= $formStarts ?></strong><small class="muted">leads submitted / forms started<?= $formStarts ? ' · ' . round(100 * min($leadsCount, $formStarts) / $formStarts) . '% completed' : '' ?></small></div>
</section>

<form class="filters" method="get">
    <?= site_select() ?>
    <select name="type"><option value="">All events</option>
        <?php foreach (CTA_TYPES as $k => $label): ?><option value="<?= $k ?>" <?= $type === $k ? 'selected' : '' ?>><?= h($label) ?></option><?php endforeach; ?>
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

<h2>By section of the page</h2>
<?php count_table('Section', $bySection); ?>

<h2>By product</h2>
<?php count_table('Product', $byProduct); ?>

<h2>By platform &amp; campaign</h2>
<div class="table-wrap">
<table>
    <thead><tr><th>Platform</th><th>Campaign</th><?= type_heads() ?></tr></thead>
    <tbody>
    <?php if (!$byCampaign): ?><tr><td colspan="<?= count(CTA_TYPES) + 3 ?>" class="empty">No data yet.</td></tr><?php endif; ?>
    <?php foreach ($byCampaign as $c): ?>
        <tr>
            <td><?= platform_badge($c['platform'] ?? '') ?></td>
            <td><a href="?<?= h(http_build_query(['platform' => $c['platform'], 'campaign' => $c['campaign']])) ?>"><?= h($c['campaign'] ?: '—') ?></a></td>
            <?= type_cells($c) ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<h2>Recent events</h2>
<div class="table-wrap">
<table>
    <thead><tr><th>Date</th><th>Page</th><th>Event</th><th>Section</th><th>Product</th><th>Platform</th><th>Campaign / Keyword</th><th>Device</th></tr></thead>
    <tbody>
    <?php if (!$recent): ?><tr><td colspan="8" class="empty">No events yet.</td></tr><?php endif; ?>
    <?php foreach ($recent as $r): ?>
        <tr>
            <td class="nowrap"><?= date('d M y', strtotime($r['created_at'])) ?><br><small><?= date('h:i A', strtotime($r['created_at'])) ?></small></td>
            <td><?= site_badge($r['site']) ?></td>
            <td><span class="badge ev-<?= h($r['type']) ?>"><?= h(CTA_TYPES[$r['type']] ?? $r['type']) ?></span></td>
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
