# Mood Foam Mattresses Website

Marketing and product-catalogue website for **Mood Foam Mattresses**, a brand of **Busujju Industries Limited**, Mityana, Uganda.

- Live site: <https://www.moodfoammattresses.com>
- Repository: <https://github.com/ekajuboolox-source/moodfoam-website>
- Built by: Boolox Technologies

The site is **plain static HTML, CSS and JavaScript**. There is no framework, no build step, no package manager and no database. What you see in `public_html/` is exactly what gets uploaded to the web host.

---

## Contents

1. [Quick start](#quick-start)
2. [Project structure](#project-structure)
3. [How the pages are built](#how-the-pages-are-built)
4. [Common tasks (how to…)](#common-tasks-how-to)
5. [CSS guide](#css-guide)
6. [JavaScript guide](#javascript-guide)
7. [SEO setup](#seo-setup)
8. [Deploying to the host](#deploying-to-the-host)
9. [Helper scripts](#helper-scripts)
10. [Known limitations and ideas](#known-limitations-and-ideas)

---

## Quick start

You only need a browser and a text editor.

**Option A: open directly.** Double-click `public_html/index.html`. Most things work, but root-relative paths (favicons, `404.html`) only resolve correctly through a server.

**Option B: run the included local server (recommended).** Requires [Node.js](https://nodejs.org):

```powershell
node .claude/static-server.js
```

Then open <http://localhost:5175>. The server serves `public_html/` and nothing else.

> If you use Claude Code, `.claude/launch.json` already defines this server as `moodfoam-site-root`.

Edit any file in `public_html/`, refresh the browser, and you will see the change.

---

## Project structure

```
Mood_Foam/
├── README.md                  ← this file
├── .gitignore
├── .claude/                   ← developer tooling only (NOT uploaded to the host)
│   ├── static-server.js       ← local preview server (Node, port 5175)
│   ├── launch.json            ← preview config for Claude Code
│   └── *.ps1                  ← one-off image helper scripts (see "Helper scripts")
├── Instruction potos/         ← client's original change-request screenshots (reference only)
├── Moodfoam_Claude_Code_Website_Redesign_Assignment.docx   ← original brief
└── public_html/               ← THE WEBSITE: everything here is uploaded to the host
    ├── .htaccess              ← server rules: https/www redirect, gzip, caching, 404 page
    ├── index.html             ← homepage
    ├── about.html, contact.html, showroom.html
    ├── <product>.html         ← one page per product line (see table below)
    ├── 404.html               ← "Page not found" page (served by .htaccess)
    ├── robots.txt, sitemap.xml
    ├── css/style.css          ← ALL styling for every page
    ├── java/script.js         ← ALL behaviour for every page
    ├── images/                ← logos (logo.png, logo-header.png, logo-icon.png)
    ├── favicon_io/            ← favicons, touch icons, site.webmanifest
    └── New folder-1/          ← product and gallery photos (≈150 files, JPG/PNG)
```

> `New folder-1/` is an inherited name. Pages reference it by path, so renaming it means updating every `src="New folder-1/…"` in the HTML. The folders `Mood Foam Beds/`, `Sofas/` and `Chusions & Foam Sheets/` also hold some images used by the beds, sofas and cushions pages.

### Pages

| File | Page |
|---|---|
| `index.html` | Homepage: animated hero, product categories, trust strip, about teaser |
| `about.html` | Company story, vision/mission/values, blog section |
| `contact.html` | Contact details and WhatsApp-based enquiry form |
| `showroom.html` | Photo gallery with lightbox |
| `delux-quilted-mattress.html`, `pvc.html`, `tape-edge.html`, `open-end.html` | Standard mattresses |
| `Rebonded.html`, `Orthopedic.html`, `spring.html` | Premium and spring mattresses |
| `beds.html`, `sofas.html`, `cushions.html` | Beds, sofas, cushions and foam sheets |
| `pillow.html`, `topper.html`, `mattres-protectors.html`, `leather.html` | Beddings |
| `404.html` | Error page (`noindex`, not in the sitemap) |

> File names are used in URLs and the sitemap exactly as written, including the capitals in `Orthopedic.html` and `Rebonded.html` and the `mattres-protectors.html` spelling. Linux hosts are **case-sensitive**, so keep names and links identical.

---

## How the pages are built

There is no templating system, so **every page repeats the same shared blocks**. They are identical across all 18 pages + `404.html`:

1. **`<head>`**: title, favicons, fonts, `css/style.css?v=N`, meta description, Open Graph tags, `canonical` link.
2. **Top contact bar and header**: logo, navigation with dropdowns, search button, hamburger menu on mobile.
3. **Search overlay**: the markup that `script.js` drives.
4. **Page content**: different on each page.
5. **Footer**: logo, tagline, contact list, link columns, copyright and credit.
6. **`<script src="java/script.js?v=N">`** at the end of `<body>`.

Because the shared blocks are copied, **a change to the header, footer or nav must be made on every page**. The simplest safe way is a find-and-replace across all `public_html/*.html` files in your editor (VS Code: *Search → Replace in Files*). Always search for a short, unique snippet first and confirm it matches every page.

### A product page (`pvc.html` is a good model)

```html
<div class="container">
  <h1 class="product-card2">PVC MATTRESS</h1>
  <div class="product-display">
    <img src="New folder-1/pvc.jpg" alt="Main mattress" class="card-img" />
    <p class="description">…</p>
  </div>
</div>

<section class="main-section">
  <div class="thumbnails">
    <div class="thumbnail-card">
      <img src="New folder-1/pvc.jpg" alt="Mood Foam Pvc 3x6" loading="lazy" />
      <h3 class="thumbnail-title">Pvc 3x6</h3>
      <button class="choose-options-btn">Choose option</button>
    </div>
    <!-- more .thumbnail-card blocks -->
  </div>
</section>

<div id="productModal" class="modal"> … </div>   <!-- the pop-up, filled in by script.js -->
```

Clicking **Choose option** copies that card's own image and title into the modal, which has WhatsApp and size options. There is no shopping cart or prices: ordering happens through WhatsApp.

---

## Common tasks (how to…)

### Change the phone number, email or address
These appear in the top bar, the footer, `contact.html`, the homepage JSON-LD (`index.html`) and the WhatsApp links. Search for the old number across `public_html/` (for example `256743053096`) and replace everywhere, including inside `java/script.js` (the contact form's `wa.me` link).

### Add a new photo to an existing product page
1. Put the optimised image in `public_html/New folder-1/` (aim for under ~300 KB, max ~2000 px wide).
2. Copy one `<div class="thumbnail-card">…</div>` block, then change `src`, `alt` and the `<h3>` title.
3. Always write a descriptive `alt` (it helps accessibility and SEO), keep `loading="lazy"`, and add the image's real pixel size as `width="…" height="…"` so the page does not jump while it loads (the CSS rule `img { height: auto }` keeps the attributes from distorting the picture).

### Add a whole new product page
1. Copy the most similar page (e.g. `pvc.html`) to a new file name in lowercase with hyphens (e.g. `memory-foam.html`).
2. Update `<title>`, `meta description`, `og:*` tags, **`canonical`** URL and the `<h1>`.
3. Replace the images and descriptions.
4. Add a link to it in the navigation dropdown on **every page**.
5. Add it to `sitemap.xml` and to the `searchIndex` array in `java/script.js` (see [Site search](#site-search)).
6. Bump the cache version (see below) and upload.

### Add a hero image to the homepage carousel
The homepage hero crossfades **20 images over 80 seconds** (4 s each), and the timing is hard-wired in CSS. Adding or removing an image means recalculating the keyframes (`@keyframes crossfade`) and the per-image `animation-delay` rules (`.hero .imgN`) in `style.css`. The showroom hero (`showroomCrossfade`, 3 images / 12 s) and the about-page image (5 images / 20 s) work the same way. For N images, the cycle is `N × 4s`.

### Change colours, fonts or sizes
Edit the **design tokens** at the top of `css/style.css` (the `:root { … }` block): brand greens, gold accent, text colours, heading sizes, max content width. Changing a token updates every place that uses it.

### Force visitors to get the latest CSS/JS ("cache busting")
Browsers cache `style.css` and `script.js`. After editing either, increase the version number on the link in **every** page:

```html
<link rel="stylesheet" href="css/style.css?v=29" />   → ?v=30
<script src="java/script.js?v=6"></script>             → ?v=7
```

Current versions: **CSS `v=29`, JS `v=6`**. Keep them the same on all pages.

---

## CSS guide

Everything is in `public_html/css/style.css` (~3,200 lines). Rough map, in file order:

| Section | What it covers |
|---|---|
| Design tokens (`:root`) | Colours, type scale, layout widths |
| Reset, header, hamburger menu | Navigation and mobile menu |
| Info grid, goals, institutional gallery, products, blog | Homepage and about-page sections |
| Footer (`.footer-top`, `.footer-brand`, `.footer-links`, `.footer-bottom`) | The current simple footer |
| Modal, WhatsApp floating button | Product pop-up and the "Chat with us" button |
| Tablet / mobile media queries | Responsive adjustments |
| Site search (`#search-overlay` rules) | Search overlay |
| "Homepage refresh" block (near the end) | Hero carousel, word-by-word intro, trust strip, CTA band |

Things worth knowing:

- **Responsive:** tested from 320 px phones up to 2560 px monitors with no horizontal scrolling. Breakpoints are mainly around 480 / 600 / 900 px. Use `clamp()` for sizes rather than fixed pixels.
- **Later rules win.** The file grew over many passes; if a change seems ignored, search for the same selector further down (equal specificity means the later rule wins).
- **Reduced motion:** animations are switched off for visitors who request it (`prefers-reduced-motion`).
- **Floating WhatsApp button** sits `fixed` at bottom-right on desktop and tablet; on phones it is replaced by a bottom contact bar. Don't place important text hard against the bottom-right corner (the footer's bottom bar is centred for this reason).

---

## JavaScript guide

Everything is in `public_html/java/script.js` (~360 lines), wrapped in one `DOMContentLoaded` handler. Each feature quietly does nothing on pages that don't have its markup.

| Feature | What it does |
|---|---|
| Product modal | "Choose option" opens a pop-up with the clicked card's image and title; closes via ×, outside click, or size selection |
| Hamburger menu | Toggles the mobile nav; closes on link click or outside click |
| Dropdown menus | Product dropdowns in the nav, one open at a time |
| Scroll reveal | One shared `IntersectionObserver` fades/slides elements in when they scroll into view (`.reveal-*` classes) |
| Hero intro (homepage) | Words fly in one by one, hold, disappear together, and repeat with the image cycle |
| <a id="site-search"></a>Site search | Client-side search over a hand-written `searchIndex` array |
| Contact form | No backend: builds a message and opens `https://wa.me/256743053096?text=…` |
| Gallery lightbox | Click/tap/keyboard-activate showroom photos to enlarge |

### Site search
There is no server to query, so search matches against the `searchIndex` array near the top of the search section:

```js
{ title: 'PVC Mattress', url: 'pvc.html', keywords: 'pvc mattress vinyl standard' },
```

When you add a page or product, add a line here with plain-English keywords people might type.

---

## SEO setup

Already in place on every page:

- Unique `<title>` and `meta description`, one `<h1>` per page.
- `canonical` link and Open Graph tags using `https://www.moodfoammattresses.com/…`.
- Descriptive `alt` text on images.
- `sitemap.xml` (18 URLs with `lastmod`) and `robots.txt` pointing at it.
- Structured data (JSON-LD): `LocalBusiness`/`FurnitureStore` + `WebSite` on the homepage, `BreadcrumbList` on every inner page.
- Link-preview tags (Open Graph + Twitter card) with a 1200×630 share image, and `max-image-preview:large` so Google can show large images.
- A single standard viewport tag (pinch-zoom allowed, which accessibility checks require).
- `404.html` marked `noindex, follow`.
- Favicons, Apple touch icon and web manifest in `favicon_io/`.
- `.htaccess` redirects to **https + www** so the canonical URLs are the only ones that resolve.

When you add or change a page: update its canonical/og URL, add it to `sitemap.xml`, and update `lastmod` for pages you changed. After launch, submit `sitemap.xml` in Google Search Console.

Possible improvements: `Product` structured data once prices or SKUs exist. The 1200×630 share image (`images/og-image.jpg`) is generated by `.claude/make-og-image.ps1`; edit and re-run that script to change it.

---

## Deploying to the host

The host is a standard Apache shared host (iFastnet). Deployment is a manual upload:

1. Make sure everything works locally and the changes are committed.
2. Upload the **contents of `public_html/`** into the host's web root (also called `public_html`). Via File Manager you can upload a zip and extract it there.
3. **Make sure the hidden file `.htaccess` is included.** Many tools skip dotfiles. Without it the site still works but loses the https/www redirect, compression, caching and the branded 404 page. In cPanel File Manager, enable *Show Hidden Files* to check.
4. Hard-refresh (Ctrl+F5) and test: `http://moodfoammattresses.com` should redirect to `https://www.moodfoammattresses.com/`, and a made-up URL should show the custom 404 page.

To build a clean zip from the last commit (correct paths, includes `.htaccess`, Linux line endings):

```powershell
git -c core.autocrlf=false archive --format=zip --prefix=public_html/ -o public_html.zip HEAD:public_html
```

> The `.htaccess` assumes **SSL is active** for the domain. Do not deploy it to a host or staging domain without a valid certificate, or the site will be unreachable.

---

## Helper scripts

In `.claude/` (Windows PowerShell, use `System.Drawing`). These are **developer conveniences, not part of the site**, and are not uploaded.

| Script | Purpose |
|---|---|
| `optimize-images.ps1` | Downsizes and recompresses photos (max 2000 px, JPEG quality 80). **Do not re-run on already optimised images**: each pass is lossy and only loses quality. Edit the `-Root` default path to your machine first. |
| `crop-logo*.ps1`, `crop-icon-bounds.ps1`, `detect-content-bounds.ps1` | One-off scripts used to crop the logo and produce `images/logo-icon.png` |
| `make-touch-icons.ps1` | Generated the favicon/touch-icon set in `favicon_io/` |
| `make-og-image.ps1` | Generates `images/og-image.jpg` (1200×630), the link-preview image used by `og:image` and `twitter:image` |
| `check-image-dims.ps1` | Prints image dimensions (diagnostic) |
| `ps-static-server.ps1`, `static-server.js` | Local preview servers (Node version is the one to use) |

Several scripts contain hard-coded paths such as `D:\Mood_Foam\public_html`; adjust them to your checkout before running.

---

## Known limitations and ideas

- **Repeated header/footer markup.** Fine for 19 pages, but a future change to a static-site generator (Eleventy, Astro) or server-side includes would remove the copy-paste.
- **No prices, cart or checkout.** All enquiries go through WhatsApp by design.
- **Contact form** relies on the visitor's WhatsApp being available; there is no email fallback or server-side storage.
- **Search** is keyword-based over a hand-maintained list.
- **Image folder naming** (`New folder-1/`) is inherited; renaming means updating all references.
- Unused CSS from earlier designs (old subscribe form, legacy contact-info block) remains and could be pruned.

---

## Credits

Website design and development: **Boolox Technologies**
Client: **Busujju Industries Limited** (Mood Foam Mattresses), Mityana, Uganda
