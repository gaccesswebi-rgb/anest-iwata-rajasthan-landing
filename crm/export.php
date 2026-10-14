<?php
require __DIR__ . '/auth.php';
require_login();

[$where, $params] = lead_filters($_GET);
$st = db()->prepare("SELECT * FROM leads $where ORDER BY id DESC");
$st->execute($params);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="leads-' . date('Y-m-d') . '.csv"');
$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // BOM so Excel opens UTF-8 correctly
$first = true;
while ($row = $st->fetch()) {
    $row['campaign'] = campaign_label($row);
    if ($first) { fputcsv($out, array_keys($row), ',', '"', ''); $first = false; }
    // Prefix cells starting with = + - @ so Excel doesn't run them as formulas.
    fputcsv($out, array_map(fn($v) => preg_match('/^[=+\-@\t\r]/', (string)$v) ? "'" . $v : $v, $row), ',', '"', '');
}
if ($first) fputcsv($out, ['No leads for this filter'], ',', '"', '');
