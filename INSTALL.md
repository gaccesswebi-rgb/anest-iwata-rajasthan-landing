# Installation guide – Anest Iwata & QuickAir landing pages + Leads CRM

Plain PHP + SQLite. No WordPress, no MySQL server, no Composer, no build step.
Upload the files, create one config file, open the CRM once to set a password. Done.

---

## 1. What is in this package

```
/                         -> Anest Iwata landing page        (index.php)
/quickair/                -> QuickAir landing page            (quickair/index.php)
/quickair/thank-you.php   -> QuickAir thank-you page (fires the lead conversion)
/thank-you.php            -> Anest Iwata local thank-you page (only used if THANK_YOU_URL is empty, see §4)
/crm/                     -> Leads CRM for BOTH pages (login required)

submit.php                -> form handler for both pages (saves the lead, redirects to the right thank-you page)
cta.php                   -> logs Call / WhatsApp / Email / Get Quote / form-start / video clicks for the CRM
config.php                -> site settings (phone, GTM ID, Google Ads, thank-you URL) – in the package
config.local.example.php  -> template for the secrets file you create on the server (§4)
inc/                      -> shared PHP (database, tracking, GTM snippets)
data/                     -> the SQLite database is created here automatically (web access is blocked)
assets/, quickair/assets/ -> CSS, JS, images
.htaccess, data/.htaccess, crm/.htaccess -> security + caching rules (Apache / LiteSpeed)
robots.txt
README.md                 -> GTM events, Google Ads setup, CRM features (reference)
```

After a form submit:
- **Anest Iwata** -> `https://ahujagroup.in/thank-you/?source=anest-iwata-lp&lead=<id>` (main website thank-you page)
- **QuickAir** -> `quickair/thank-you.php?lead=<id>` (its own thank-you page)

Both pages, all leads and all click tracking go into **one CRM** (`/crm/`), with a Page filter (Anest Iwata / QuickAir).

---

## 2. Server requirements

| Need | Notes |
|---|---|
| PHP **7.4 or newer** (8.x recommended) | |
| PHP extensions **pdo_sqlite** and **mbstring** | Enabled by default on most cPanel / Hostinger plans. Check in cPanel → *Select PHP Version* → Extensions. |
| **HTTPS** (SSL) | Required for the Google Ads automatic conversion upload and recommended for forms. |
| Apache or LiteSpeed with `.htaccess` | Nginx works too – add the rules in §8. |
| Write permission on `data/` | PHP creates the database file there. |

No MySQL database is needed. The CRM uses **SQLite** – a single file inside `data/`.

---

## 3. Upload

1. Decide the URL, for example `https://ahujagroup.in/lp/`
   (a sub-folder of the main site, or a sub-domain such as `https://lp.ahujagroup.in/`).
2. Upload **everything in this zip** into that folder (cPanel File Manager → Upload → Extract, or FTP).
   Keep the folder structure exactly as it is – QuickAir must stay inside `quickair/` because it uses the shared `submit.php`, `cta.php` and CRM in the parent folder.
3. Make sure hidden files were uploaded too: `.htaccess`, `data/.htaccess`, `crm/.htaccess`
   (in cPanel File Manager: *Settings → Show Hidden Files*).
4. Permissions: folders `755`, files `644`. `data/` must be writable by PHP (`755` is enough on cPanel/LiteSpeed; use `775` only if PHP runs as a different user and cannot write).

Resulting URLs (example):
- Anest Iwata: `https://ahujagroup.in/lp/`
- QuickAir: `https://ahujagroup.in/lp/quickair/`
- CRM: `https://ahujagroup.in/lp/crm/`

---

## 3A. Same domain as the WordPress site (ahujagroup.in) – recommended setup

The landing pages and CRM live in their **own folder inside the WordPress root**. WordPress, its theme, plugins,
`.htaccess` and MySQL database are **not touched**.

1. In cPanel File Manager open the WordPress root (`public_html`, where `wp-config.php` is) and create the folder **`lp`**.
2. Upload and extract this zip **into `public_html/lp/`** (the files, not the `anest-iwata-quickair-site` wrapper folder).
3. Result:
   - `https://ahujagroup.in/lp/` – Anest Iwata landing page
   - `https://ahujagroup.in/lp/quickair/` – QuickAir landing page
   - `https://ahujagroup.in/lp/crm/` – CRM

Why WordPress is not affected: WordPress' rewrite rules only handle URLs that are **not** real files or folders
(`RewriteCond %{REQUEST_FILENAME} !-f / !-d`). `/lp/` is a real folder, so the server serves it directly and every other URL
still goes to WordPress. The CRM uses its own SQLite file in `lp/data/`, never the WordPress MySQL database.

Rules for this setup:
- Use the folder name **`lp`** (checked: it is free). Do **not** use `/quickair/` at the domain root – WordPress already uses it
  (it redirects to `/quickair-aluminium-air-pipeline/`); a real folder with that name would hide that WordPress page.
- Never put the files inside `wp-content`, `wp-admin` or `wp-includes`.
- **LiteSpeed Cache** (WordPress plugin): nothing to change. `lp/.htaccess` already turns LiteSpeed's page cache off for this folder,
  and CRM pages send `Cache-Control: no-store`.
- **Cloudflare** (the domain is behind Cloudflare):
  - Caching → Cache Rules → add a rule: *URI Path starts with `/lp/crm/`* → **Bypass cache**. Needed only if any
    "Cache Everything" / Edge-TTL rule exists, but safe to add anyway.
  - Security → WAF → add a skip/allow rule for *URI Path equals `/lp/crm/oci.php`* if you use the Google Ads automatic upload,
    so Google's fetcher is not challenged.
  - If a form or tracking misbehaves, disable **Rocket Loader** for `/lp/*` (Configuration Rules).
  - Visitor IPs: the code reads Cloudflare's `CF-Connecting-IP`, so the spam limits work per real visitor.
- **WordPress security plugins** (Wordfence etc.) may also run on these PHP files; if a form submit is blocked, whitelist `/lp/submit.php` and `/lp/cta.php`.
- **Backups**: a full-site backup plugin may or may not include `/lp/`. Back up `lp/data/<db>.sqlite` and `lp/config.local.php` separately.
- `robots.txt` inside `/lp/` is ignored by search engines (only the domain-root file counts) – that is fine, the pages already send `noindex`.
- The Anest Iwata form sends visitors to `https://ahujagroup.in/thank-you/` – the WordPress thank-you page on the same domain.

---

## 4. Configure (one time)

### 4.1 Create the secrets file
Copy `config.local.example.php` to **`config.local.php`** (same folder) and edit it:

```php
define('CRM_PASSWORD_HASH', '');                                   // filled in step 4.2
define('OCI_FEED_PASSWORD', '');                                   // optional, see 4.3
define('DB_PATH', __DIR__ . '/data/leads-k3x9q2m7w1.sqlite');      // pick your own random file name
```

- `DB_PATH`: change `leads-CHANGE-ME.sqlite` to a random name (letters/numbers). The file is created automatically on the first form submit or CRM visit. A random name is extra protection if `.htaccess` is ever ignored.
- `config.local.php` is **not** in the zip and must never be shared or committed – it holds the passwords.

### 4.2 CRM login password
1. Open `https://<your-url>/crm/` – because `CRM_PASSWORD_HASH` is empty, it shows **First-time setup**.
2. Type the password you want (8+ characters) → **Generate hash**.
3. Copy the long hash (starts with `$2y$`) into `CRM_PASSWORD_HASH` in `config.local.php`, save.
4. Reload `/crm/` and log in with username **`admin`** and that password.

To change the password later: empty `CRM_PASSWORD_HASH`, reload `/crm/`, repeat.
(The username can be changed in `config.php` → `CRM_USER`.)

### 4.3 Optional – Google Ads offline conversions
The CRM can tell Google Ads when a lead reaches *Quotation Sent* or *Won* (CRM → **Google Ads Sync**).
For automatic daily upload set a 16+ character random password in `OCI_FEED_PASSWORD`, then in Google Ads:
Goals → Conversions → Uploads → Schedules → Source **HTTPS** → URL `https://<your-url>/crm/oci.php`, username `googleads`, that password.
Conversion action names must match `OCI_STAGES` in `config.php` (`CRM - Quotation Sent`, `CRM - Won`).

### 4.4 Settings in `config.php` (already filled – change only if needed)
| Setting | Current value |
|---|---|
| `SITE_PHONE`, `SITE_PHONE_RAW`, `SITE_EMAIL` | +91 9001848552, projects1@ahujagroup.in |
| `GTM_ID` | `GTM-5CH9GZMQ` (both pages and both thank-you pages) |
| `THANK_YOU_URL` | `https://ahujagroup.in/thank-you/` – Anest Iwata form destination. Empty = use the local `thank-you.php`. |
| `GADS_ID`, `GADS_*_LABEL` | empty – conversions are set up in GTM instead (do not fill both, or conversions count twice) |

---

## 5. Database

- Engine: **SQLite** via PHP PDO. Nothing to install, no host/user/password.
- File: the path in `DB_PATH` (inside `data/`). Tables (`leads`, `clicks`, `lead_events`) and any new columns are **created/updated automatically** by `inc/app.php` on first use.
- Backup: download the `.sqlite` file (cPanel File Manager or FTP) – that file *is* the whole CRM. Recommended weekly. CSV export is also available in the CRM (Leads → Export CSV).
- Moving servers: copy the `.sqlite` file and `config.local.php` along with the code.
- Never upload a local/test `.sqlite` file over the live one – it would replace all live leads.
- If the host *requires* MySQL instead of SQLite, the code needs changes (the queries use SQLite syntax); it does not work by only changing a setting.

---

## 6. Security checks after upload (must do)

Open these in a browser – each must be **blocked** (403 or 404):

| URL | Expected |
|---|---|
| `https://<your-url>/data/` | 403 |
| `https://<your-url>/data/<your-db-file>.sqlite` | 403 |
| `https://<your-url>/README.md` and `/INSTALL.md` | 403 |
| `https://<your-url>/crm/clicks.php` (logged out) | redirects to the login page |
| `https://<your-url>/crm/oci.php` (logged out) | 401 |

If `/data/` is **not** blocked, the server ignores `.htaccess` (Nginx, or `AllowOverride None`) → apply §8 before going live.

Both landing pages send `noindex, nofollow` (ads traffic only) – this does not affect Google Ads.

---

## 7. Test before running ads

1. Open each page with a fake ad click, e.g.
   `https://<your-url>/?utm_source=google&utm_medium=cpc&utm_campaign=TEST&gclid=TEST123`
   `https://<your-url>/quickair/?utm_source=google&utm_medium=cpc&utm_campaign=TEST&gclid=TEST123`
2. Click a Call and a WhatsApp button, start filling the form, then submit it (wait a few seconds before submitting – submits under 3 seconds are treated as bots).
3. Check:
   - Anest Iwata → lands on `ahujagroup.in/thank-you/?source=anest-iwata-lp&lead=…`
   - QuickAir → lands on `quickair/thank-you.php?lead=…`
   - CRM → both leads appear with Page, Platform "Google Ads" and campaign "TEST"; **Clicks & Events** shows the clicks.
4. In the CRM set the test leads to *Duplicate / Spam* (or delete them from the database) before ads go live.
5. Optional: GTM Preview mode – events `cta_click`, `get_quote_click`, `form_start`, `form_submit`, `generate_lead` (QuickAir thank-you) should fire. Full event list in `README.md`.

Limits built in: 5 form submits per IP per 10 minutes, 60 click events per IP per 10 minutes, honeypot field against bots.

---

## 8. Nginx (only if the server does not use .htaccess)

Add inside the `server {}` block (adjust `/lp/` to your folder):

```nginx
location ~ ^/lp/(data|tests|\.git)(/|$)        { deny all; return 404; }
location ~ ^/lp/.*\.(sqlite|md|yml)$           { deny all; return 404; }
location = /lp/TEMP-LOGIN.txt                  { deny all; return 404; }
location = /lp/config.local.example.php        { deny all; return 404; }
add_header X-Robots-Tag "noindex, nofollow" always;
location ~* ^/lp/.*\.(webp|png|svg|css|js)$    { expires 30d; }
```

For the Google Ads feed with Basic Auth on PHP-FPM, pass the header:
`fastcgi_param HTTP_AUTHORIZATION $http_authorization;`

---

## 9. Updating later

- Code changes: upload the changed files only. **Never overwrite** `config.local.php` or `data/*.sqlite`.
- Optional Git deploy: the code is also on GitHub
  (`https://github.com/gaccesswebi-rgb/anest-iwata-rajasthan-landing`). cPanel → Git Version Control → clone into the site folder,
  then "Update from Remote" for new versions – `config.local.php` and the database are git-ignored, so they are never touched.

---

## 10. Troubleshooting

| Problem | Fix |
|---|---|
| Blank page / HTTP 500 | Check PHP version (≥ 7.4) and that `pdo_sqlite` + `mbstring` are enabled; see the host's error log. |
| "unable to open database file" | `data/` is not writable by PHP → set permissions (§3.4). |
| CRM keeps showing *First-time setup* | `config.local.php` missing, wrong file name, or hash not pasted inside the quotes. |
| Wrong password / cannot log in | Generate a new hash (§4.2). Username is `admin`. |
| Form says "Too many enquiries" | Rate limit (5 per 10 min per IP) – wait, or test from another network. |
| `/data/` opens in the browser | `.htaccess` ignored → Nginx rules (§8) or enable `AllowOverride All`. |
| Clicks not appearing in CRM | Ad-blockers can block tracking requests for some visitors; check Clicks & Events → Page filter = All. |
