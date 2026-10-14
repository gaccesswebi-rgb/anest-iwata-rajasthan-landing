<?php
// Run: php tests/attribution_test.php
require __DIR__ . '/../inc/app.php';

$cases = [
    [['gclid' => 'abc', 'utm_source' => 'google'], 'Google Ads'],
    [['wbraid' => 'x'], 'Google Ads'],
    [['msclkid' => 'x'], 'Microsoft Ads'],
    [['utm_source' => 'facebook', 'utm_medium' => 'paid_social'], 'Meta Ads'],
    [['utm_source' => 'google', 'utm_medium' => 'cpc'], 'Google Ads'],
    [['utm_source' => 'newsletter', 'utm_medium' => 'email'], 'Newsletter (email)'],
    [['fbclid' => 'x'], 'Meta (organic/social)'],
    [['referrer' => 'https://www.google.co.in/'], 'Organic Search (google)'],
    [['referrer' => 'https://ahujagroup.in/blog'], 'Referral (ahujagroup.in)'],
    [[], 'Direct'],
];
foreach ($cases as [$in, $want]) {
    $got = detect_platform($in);
    assert($got === $want, json_encode($in) . " => $got, want $want");
}
assert(campaign_label(['utm_campaign' => '', 'campaignid' => '123']) === 'Campaign ID 123');
echo "ok\n";
