<?php
require __DIR__ . '/auth.php';
require_login();

$pdo = db();
[$where, $params] = lead_filters($_GET);
$per = 50;
$page = max(1, (int)($_GET['page'] ?? 1));

$q = function (string $sql, array $p = []) use ($pdo) {
    $st = $pdo->prepare($sql);
    $st->execute($p);
    return $st;
};

$total = (int)$q("SELECT COUNT(*) FROM leads $where", $params)->fetchColumn();
$leads = $q("SELECT * FROM leads $where ORDER BY id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $params)->fetchAll();
$pages = max(1, (int)ceil($total / $per));

$stats = $q("SELECT
    SUM(created_at >= ?) AS today,
    SUM(status = 'New') AS fresh,
    SUM(status = 'Won') AS won,
    SUM(platform = 'Google Ads') AS gads
    FROM leads $where", array_merge([date('Y-m-d 00:00:00')], $params))->fetch();

// Call / WhatsApp button taps (details on clicks.php).
$clk = $pdo->query("SELECT SUM(type = 'call') AS calls, SUM(type = 'whatsapp') AS wa,
    SUM(type = 'call' AND created_at >= '" . date('Y-m-d') . "') AS calls_today,
    SUM(type = 'whatsapp' AND created_at >= '" . date('Y-m-d') . "') AS wa_today FROM clicks")->fetch();

$badIn = rtrim(str_repeat('?,', count(BAD_STATUSES)), ',');
$campaigns = $q("SELECT platform, " . CAMPAIGN_SQL . " AS campaign, COUNT(*) AS leads,
    SUM(status = 'Won') AS won, SUM(status IN ($badIn)) AS bad, MAX(created_at) AS last_at
    FROM leads $where GROUP BY platform, campaign ORDER BY leads DESC", array_merge(BAD_STATUSES, $params))->fetchAll();

// Status pipeline counts: same filters except status, so every chip shows its number.
$noStatus = $_GET;
unset($noStatus['status'], $noStatus['page']);
[$whereNs, $paramsNs] = lead_filters($noStatus);
$statusCounts = $q("SELECT status, COUNT(*) AS n FROM leads $whereNs GROUP BY status", $paramsNs)->fetchAll(PDO::FETCH_KEY_PAIR);

$platformOpts = $pdo->query('SELECT DISTINCT platform FROM leads ORDER BY platform')->fetchAll(PDO::FETCH_COLUMN);
$campaignOpts = $pdo->query('SELECT DISTINCT ' . CAMPAIGN_SQL . ' AS c FROM leads ORDER BY c')->fetchAll(PDO::FETCH_COLUMN);

$f = fn($k) => h($_GET[$k] ?? '');
$qsNoPage = http_build_query(array_diff_key($_GET, ['page' => 1]));
$back = h($_SERVER['QUERY_STRING'] ?? '');

crm_header('Leads');
?>
<section class="stats">
    <div><span>Leads<?= $where ? ' (filtered)' : '' ?></span><strong><?= $total ?></strong></div>
    <div><span>Today</span><strong><?= (int)$stats['today'] ?></strong></div>
    <div><span>New / uncontacted</span><strong class="t-red"><?= (int)$stats['fresh'] ?></strong></div>
    <div><span>From Google Ads</span><strong><?= (int)$stats['gads'] ?></strong></div>
    <div><span>Won</span><strong class="t-green"><?= (int)$stats['won'] ?></strong></div>
    <a class="stat-link" href="clicks.php"><span>📞 Call button clicks</span><strong><?= (int)$clk['calls'] ?></strong><small>Today <?= (int)$clk['calls_today'] ?> · details →</small></a>
    <a class="stat-link" href="clicks.php"><span>💬 WhatsApp clicks</span><strong class="t-green"><?= (int)$clk['wa'] ?></strong><small>Today <?= (int)$clk['wa_today'] ?> · details →</small></a>
</section>

<h2 class="pipe-title">Lead status</h2>
<nav class="pipeline" aria-label="Filter by status">
    <?php $cur = $_GET['status'] ?? ''; ?>
    <a class="pipe <?= $cur === '' ? 'on' : '' ?>" href="?<?= h(http_build_query($noStatus)) ?>">All <b><?= array_sum($statusCounts) ?></b></a>
    <?php foreach (LEAD_STATUSES as $s): ?>
        <a class="pipe st-<?= status_slug($s) ?> <?= $cur === $s ? 'on' : '' ?>" href="?<?= h(http_build_query($noStatus + ['status' => $s])) ?>"><?= h($s) ?> <b><?= (int)($statusCounts[$s] ?? 0) ?></b></a>
    <?php endforeach; ?>
</nav>

<form class="filters" method="get">
    <input name="q" value="<?= $f('q') ?>" placeholder="Search name, phone, city, keyword">
    <select name="status"><option value="">All status</option>
        <?php foreach (LEAD_STATUSES as $s): ?><option <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?>
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
    <a class="btn btn-light" href="./">Reset</a>
    <a class="btn btn-light" href="export.php?<?= h($qsNoPage) ?>">Export CSV</a>
</form>

<div class="table-wrap">
<table class="leads">
    <thead><tr><th>#</th><th>Date</th><th>Name / Phone</th><th>City</th><th>Requirement</th><th>Platform</th><th>Campaign / Keyword</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php if (!$leads): ?><tr><td colspan="9" class="empty">No leads yet.</td></tr><?php endif; ?>
    <?php foreach ($leads as $l): ?>
        <tr class="<?= $l['status'] === 'New' ? 'is-new' : '' ?>">
            <td><?= $l['id'] ?></td>
            <td class="nowrap"><?= date('d M y', strtotime($l['created_at'])) ?><br><small><?= date('h:i A', strtotime($l['created_at'])) ?></small></td>
            <td><strong><?= h($l['name']) ?></strong><br>
                <a href="tel:+91<?= h($l['phone']) ?>"><?= h($l['phone']) ?></a> ·
                <a href="https://wa.me/91<?= h($l['phone']) ?>" target="_blank" rel="noopener">WA</a></td>
            <td><?= h($l['city']) ?></td>
            <td class="req"><?= h($l['product']) ?></td>
            <td><?= platform_badge($l['platform'] ?? '') ?></td>
            <td class="req"><?= h(campaign_label($l)) ?><?php $kw = $l['keyword'] ?: $l['utm_term']; if ($kw): ?><br><small>🔑 <?= h($kw) ?></small><?php endif; ?></td>
            <td>
                <form method="post" action="lead.php" class="inline-status">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $l['id'] ?>">
                    <input type="hidden" name="back" value="<?= $back ?>">
                    <select name="status" class="st-select st-<?= status_slug($l['status']) ?>" onchange="this.form.submit()" aria-label="Status for lead <?= $l['id'] ?>">
                        <?php foreach (LEAD_STATUSES as $s): ?><option <?= $l['status'] === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?>
                    </select>
                </form>
            </td>
            <td><a class="btn btn-light btn-sm" href="lead.php?id=<?= $l['id'] ?>">Open</a></td>
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

<h2 id="campaigns">Leads by platform &amp; campaign</h2>
<div class="table-wrap">
<table>
    <thead><tr><th>Platform</th><th>Campaign</th><th class="num">Leads</th><th class="num">Won</th><th class="num">Lost / Wrong</th><th>Last lead</th></tr></thead>
    <tbody>
    <?php if (!$campaigns): ?><tr><td colspan="6" class="empty">No data.</td></tr><?php endif; ?>
    <?php foreach ($campaigns as $c): ?>
        <tr>
            <td><?= platform_badge($c['platform'] ?? '') ?></td>
            <td><a href="?<?= h(http_build_query(['platform' => $c['platform'], 'campaign' => $c['campaign']])) ?>"><?= h($c['campaign'] ?: '—') ?></a></td>
            <td class="num"><?= $c['leads'] ?></td>
            <td class="num"><?= (int)$c['won'] ?></td>
            <td class="num"><?= (int)$c['bad'] ?></td>
            <td><?= date('d M y, h:i A', strtotime($c['last_at'])) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php crm_footer();
