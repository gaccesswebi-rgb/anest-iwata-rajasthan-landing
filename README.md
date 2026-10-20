# Anest Iwata Rajasthan – Landing Page + Leads CRM

Plain PHP (7.4+) + SQLite. No WordPress, no MySQL, no Composer.

```
index.php        Landing page (Google Ads ke liye)
submit.php       Form handler -> saves lead -> https://ahujagroup.in/thank-you/?source=anest-iwata-lp&lead=<id>
cta.php          Logs Call / WhatsApp button clicks (called by assets/js/main.js)
thank-you.php    Local thank-you page (only used if THANK_YOU_URL in config.php is empty)
quickair/        QuickAir aluminium piping landing page (index.php) + its own thank-you.php – same CRM, GTM and events
config.php       Phone, GTM / Google Ads IDs, status mapping (git me hai)
config.local.php Secrets: CRM password hash, feed password, DB file name (git me NAHI – har server pe alag)
crm/             Leads CRM (login required)
data/            SQLite DB (auto-created, web access blocked)
```

## Upload / setup

1. Code hosting pe daalein – GitHub se (neeche dekhein) ya files upload karke, e.g. `public_html/anest-iwata/`.
2. Server pe `config.local.example.php` ko copy karke `config.local.php` banayein. `DB_PATH` me random file naam rakhein.
3. `data/` folder writable hona chahiye (cPanel: 755 enough hota hai).
4. `https://yourdomain/anest-iwata/crm/` kholein → password daalein → jo hash dikhe usse `config.local.php` me `CRM_PASSWORD_HASH` me paste karein → login (username `admin`).
5. Check: `https://yourdomain/anest-iwata/data/` aur `.../data/leads-xxxx.sqlite` dono **403** dene chahiye. Nginx server ho to `data/` ko deny karne ka rule add karein.

## GitHub se hosting pe deploy

- **cPanel:** Git™ Version Control → Create → *Clone a Repository* → repo URL daalein, *Repository Path* = jahan site chalani hai (e.g. `/home/USER/public_html/anest-iwata`). Naya code aane pe: Manage → Pull or Deploy → *Update from Remote*.
- **Hostinger:** Websites → Advanced → GIT → repo URL + branch `main` + install path → Deploy (Auto-deployment webhook bhi laga sakte hain).
- `config.local.php` aur `data/*.sqlite` git me nahi hain, isliye pull/deploy karne pe server ke leads aur password safe rehte hain.

## Google Ads setup

**1. Conversion tracking** – Google Ads → Goals → Conversions → New → Website → manual.
`AW-XXXXXXXXX` → `GADS_ID`, label → `GADS_LEAD_LABEL` in `config.php` (sirf tab jab GTM use nahi kar rahe).
GTM use karna ho to `GTM_ID` me apna container ID daalein (WordPress site pe `GTM-TWP5TT94` laga hai).
Optional: Call / WhatsApp click ke liye alag conversion actions banake `GADS_CALL_LABEL`, `GADS_WA_LABEL` bharein.

**2. Campaign / keyword tracking** – Account settings (ya campaign) → *Final URL suffix* me ye paste karein:

```
utm_source=google&utm_medium=cpc&utm_campaign={_campaign}&utm_term={keyword}&campaignid={campaignid}&adgroupid={adgroupid}&keyword={keyword}&matchtype={matchtype}&device={device}&network={network}
```

Har campaign me *Custom parameter* add karein: name `_campaign`, value = campaign ka naam (e.g. `Jaipur_Screw_Compressor`).
Google Ads URL me campaign name khud nahi bhejta; custom param na ho to CRM me "Campaign ID 12345" dikhega.
Auto-tagging ON rakhein (gclid ke liye).

**Meta / other ads:** URL me `utm_source=facebook&utm_medium=paid_social&utm_campaign=...` lagayein. CRM me "Meta Ads" dikhega.

## GTM dataLayer events (GTM-5CH9GZMQ)

GTM snippet `<head>` me sabse upar aur `<body>` ke turant baad (noscript) laga hai – `inc/gtag.php`, `inc/gtm-body.php`.
Har page load pe pehle `page_type` (`landing_page` / `thank_you`) aur `page_name` push hota hai.

| Event | Kab | Parameters (Data Layer Variables) |
|---|---|---|
| `cta_click` | Call / WhatsApp button | `cta_type` (call/whatsapp), `cta_section`, `cta_product` |
| `get_quote_click` | Header / Mobile bar / Industries "Get Quote" | `cta_section` |
| `form_start` | Form ka pehla field touch | `form_id` (hero/footer), `form_section` |
| `form_submit` | Form validation pass, submit hone se pehle | `form_id`, `form_section`, `form_product` |
| `generate_lead` | Sirf local thank-you.php pe (jab `THANK_YOU_URL` khaali ho) | `lead_id` (Ads transaction ID), `form_id`, `form_section`, `lead_product`, `traffic_platform` |
| `video_play` | YouTube video play | `video_id` |

**Lead conversion (form):** form save hone ke baad user `https://ahujagroup.in/thank-you/?source=anest-iwata-lp&lead=<id>` pe jaata hai
(main site, GTM-TWP5TT94). Main site ke GTM me Google Ads conversion tag ka trigger: *Page View – Page URL contains `source=anest-iwata-lp`*,
aur Transaction ID = URL query `lead` (URL Variable, component: Query, key `lead`) – refresh pe double count nahi hoga.
Spam/bot submissions bina `source` ke bhejte hain, isliye count nahi hote. Is LP ke GTM (GTM-5CH9GZMQ) me `form_submit` event bhi hai. Call/WhatsApp conversion triggers me
`cta_section` ≠ `Thank-you Page` condition lagayein. Koi personal data (naam/phone/email) dataLayer me nahi jaata.
Yahi events (`cta_click`, `get_quote_click`, `form_start`, `video_play`) CRM → **Clicks & Events** me bhi gine jaate hain.

Page `noindex, nofollow` hai (meta tag + `X-Robots-Tag` header) – sirf ads traffic ke liye.

## QuickAir page (`/quickair/`)

- Form `../submit.php` pe jaata hai (`site=quickair`), lead CRM me save hoti hai, phir **QuickAir ka apna** `quickair/thank-you.php?lead=<id>` khulta hai (`generate_lead` yahin fire hota hai).
  Anest Iwata ka form main site `https://ahujagroup.in/thank-you/` pe jaata hai – dono ke thank-you alag hain.
- QuickAir form me Company aur Email zaroori hain; "What do you need" → CRM me Requirement; jis CTA se form khula ("Speak to an Engineer") → Message.
- Same GTM (`GTM-5CH9GZMQ`) aur same events. Fark sirf `page_name`: `anest-iwata-rajasthan` ya `quickair` – GTM me isse dono pages alag kar sakte hain.
  QuickAir me extra: email link click → `cta_click` with `cta_type` = `email`; "Speak to an Engineer" buttons → `get_quote_click` (+ `cta_label`).
- CRM: Leads aur Clicks & Events pages pe **Page** filter (All / Anest Iwata / QuickAir) aur Page column.

## CRM me kya milta hai

- Har lead: naam, mobile (call/WhatsApp button), city, product, message
- Source: Platform (Google Ads / Meta Ads / Organic Search / Direct / Referral), campaign, keyword, match type, device, gclid, landing URL
- Status dropdown (12): New, Contacted, Not Reachable, Following Up, Site Visit Scheduled, Quotation Sent, Negotiation, Won, Lost, Not Interested, Wrong Lead, Duplicate / Spam — plus "Lead status" section with count per status (click = filter), notes
- Filter by status / platform / campaign / date, CSV export
- "Leads by platform & campaign" report (leads, won, junk per campaign)
- **Clicks & Events** page (Call, WhatsApp, Get Quote, Form started, Video play + form funnel): har Call/WhatsApp button tap ki ginti — kaunse section (Hero, Product Range, FAQ, Footer, Mobile Sticky Bar...), kaunse product, kaunse platform/campaign/keyword se. Caller ka number nahi milta, sirf clicks.

- **Google Ads Sync**: lead ka status "Quotation Sent" / "Won" hote hi uska GCLID Google Ads ko offline conversion ke roop me jaata hai (daily auto-fetch ya CSV download). Mapping: `config.php` → `OCI_STAGES`.

Ad click 90 din tak browser me yaad rehta hai, to user baad me wapas aakar form bhare tab bhi sahi campaign credit hota hai.

## Local test

```
php -S 127.0.0.1:8087
php -d zend.assertions=1 tests/attribution_test.php
```
