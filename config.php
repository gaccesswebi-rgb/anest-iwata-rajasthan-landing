<?php
// ===== Site settings: edit these =====
const SITE_PHONE      = '+91 9001848552';
const SITE_PHONE_RAW  = '919001848552';           // used for tel: and wa.me links
const SITE_EMAIL      = 'projects1@ahujagroup.in';

// Google Tag Manager container for this landing page. Leave empty to disable.
const GTM_ID            = 'GTM-5CH9GZMQ';

// Google Ads conversion tracking WITHOUT GTM. Leave empty if you set up the Ads conversion tags inside GTM
// (filling both GTM tags and these would count every conversion twice).
const GADS_ID           = '';   // e.g. 'AW-123456789'
const GADS_LEAD_LABEL   = '';   // conversion label for form submit (fires on thank-you.php)
const GADS_CALL_LABEL   = '';   // optional: conversion label for Call button clicks
const GADS_WA_LABEL     = '';   // optional: conversion label for WhatsApp button clicks

// Offline conversion import: when a lead reaches one of these CRM statuses, Google Ads gets told
// "this ad click became a good lead" (via the lead's GCLID). Names must EXACTLY match conversion actions
// created in Google Ads > Goals > Conversions > New > Import > "CRM, files, or other data sources" > Track conversions from clicks.
// value = optional amount in INR (0 = use the conversion action's default value).
const OCI_STAGES = [
    'Quotation Sent' => ['name' => 'CRM - Quotation Sent', 'value' => 0],
    'Won'            => ['name' => 'CRM - Won',            'value' => 0],
];
// Login Google Ads uses to fetch the feed automatically (Uploads > Schedules > HTTPS). Password lives in config.local.php.
const OCI_FEED_USER     = 'googleads';

// CRM username. Password hash lives in config.local.php.
const CRM_USER          = 'admin';

// ===== Secrets: kept in config.local.php (NOT in git). Copy config.local.example.php to config.local.php. =====
if (is_file(__DIR__ . '/config.local.php')) require __DIR__ . '/config.local.php';
defined('CRM_PASSWORD_HASH') || define('CRM_PASSWORD_HASH', '');   // empty = /crm/login.php shows the hash generator
defined('OCI_FEED_PASSWORD') || define('OCI_FEED_PASSWORD', '');   // empty = feed only works while logged in to the CRM
defined('DB_PATH') || define('DB_PATH', __DIR__ . '/data/leads.sqlite'); // data/ is blocked from the web by data/.htaccess

date_default_timezone_set('Asia/Kolkata');
