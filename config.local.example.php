<?php
// Copy this file to config.local.php on each server and fill in the values. config.local.php is not in git.

// CRM password hash: open /crm/login.php while this is empty, type a password, paste the hash it shows here.
define('CRM_PASSWORD_HASH', '');

// Password Google Ads uses to fetch /crm/oci.php automatically (16+ random characters). Empty = disabled.
define('OCI_FEED_PASSWORD', '');

// SQLite file. Use a random file name (e.g. leads-8f3k2j9x.sqlite) as extra protection on servers that ignore .htaccess.
define('DB_PATH', __DIR__ . '/data/leads-CHANGE-ME.sqlite');
