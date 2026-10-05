# Sorouh Digital — AI Project Handoff

## 1. Project identity
- Product: Sorouh Digital | صروح الرقمية
- Parent company: شركة صروح الشامي المحدودة
- Production subdomain: `https://digital.suroohalshami.com`
- Scope: digital services only. Do not mix this project into the main ready-mix concrete / contracting website.
- Primary language: Arabic (RTL), with English technical terms only where useful.
- Brand direction: premium, modern, technical, dark/black + metallic gold, with restrained digital accents.
- Approved brand: use the approved Sorouh Digital logo already present in the repository/assets when available. Do not replace it with a new logo unless explicitly requested.

## 2. Business goals
The site must generate qualified leads for:
1. Website design and development
2. Digital marketing
3. SEO
4. Graphic design and visual identity
5. Haraj and Mourjan classified-ad management
6. Custom software / digital solutions

Initial acquisition focus:
- Haraj
- Mourjan
- Direct/organic traffic
- Later: Google Ads, Meta, TikTok, Snapchat and other channels

## 3. Engineering philosophy
- Treat the repository as a production application, not a mockup.
- Read the existing code before changing architecture.
- Preserve working features and existing routes.
- Prefer incremental, reversible changes.
- Never delete or rewrite major modules without first understanding dependencies.
- Do not expose secrets, database credentials, API keys, SMTP passwords or production tokens in Git.
- Keep `.env` excluded from source control.
- If the actual repository differs from this document, the repository is the source of truth. Update this document after verified architectural changes.

## 4. Current intended stack
Preferred/current architecture:
- PHP 8.x
- Lightweight MVC-style application structure
- MySQL / MariaDB
- PDO with prepared statements
- HTML5
- Modern CSS
- Vanilla JavaScript unless an existing dependency is already justified
- Git / GitHub
- Hostinger deployment
- GA4 + Google Tag Manager integration layer
- Internal first-party lead/event tracking

Do not migrate the whole project to Laravel, WordPress, React, Vue, Next.js or another framework unless explicitly requested.

## 5. Core application modules

### Public website
Required sections/pages:
- Homepage
- Service pages
- Portfolio / case studies
- Blog
- Individual article page
- Request-a-service / contact
- Privacy policy
- Terms and conditions
- Custom 404 / error views

Initial service pages:
- `/services/web-development`
- `/services/digital-marketing`
- `/services/seo`
- `/services/graphic-design`
- `/services/classified-ads`
- `/services/custom-software`

### Admin
The admin area should provide:
- Secure authentication
- Dashboard
- Blog/article CRUD
- Lead management
- Lead status pipeline
- Tracking analytics
- Service management where appropriate
- SEO fields
- Site settings
- Media handling if implemented

Lead statuses:
- new
- contacted
- interested
- quotation
- won
- unqualified

Use Arabic labels in UI, stable English enum/value names in the database/code.

## 6. Lead tracking
The platform must preserve first-party tracking for conversion attribution.

Required events:
- `page_view`
- `whatsapp_click`
- `phone_click`
- `form_start`
- `form_submit`
- `cta_click` where relevant
- `service_view` where relevant

Capture when available:
- visitor_id
- session_id
- event_name
- page_url
- landing_page
- referrer
- utm_source
- utm_medium
- utm_campaign
- utm_content
- utm_term
- created_at

Do not store unnecessarily sensitive personal data in analytics events.

### Lead attribution
Forms should associate the lead with available attribution data:
- source
- campaign
- landing page
- UTM values

Lead IDs should be human-readable, e.g.:
`SD-YYMMDD-XXXXXX`

## 7. Haraj / Mourjan tracking
Marketing links must support campaign-specific UTMs, for example:

Haraj:
`?utm_source=haraj&utm_medium=classified&utm_campaign=web_design&utm_content=ad_01`

Mourjan:
`?utm_source=mourjan&utm_medium=classified&utm_campaign=web_design&utm_content=ad_01`

Do not hardcode a single campaign value globally.

## 8. SEO requirements
SEO is part of implementation, not a post-launch add-on.

Every indexable page should support:
- unique `<title>`
- meta description
- canonical
- Open Graph
- social preview metadata
- clean semantic headings
- descriptive internal links
- image alt text
- schema where appropriate

Required technical SEO:
- `robots.txt`
- XML sitemap
- canonical URLs
- correct HTTP status codes
- no accidental duplicate indexable routes
- no placeholder/lorem ipsum content on production
- pagination/canonical logic if blog grows
- Article schema for articles
- Organization schema
- Service schema where valid
- BreadcrumbList schema where implemented

Avoid spammy claims such as "أفضل شركة" unless independently supportable.

## 9. Content / blog
Blog content should be stored in the database, not hard-coded in templates.

Suggested post fields:
- id
- title
- slug
- excerpt
- content
- featured_image
- status
- meta_title
- meta_description
- canonical_url
- published_at
- created_at
- updated_at

Requirements:
- unique slug
- draft/published workflow
- safe rendering
- SEO metadata
- article schema
- share preview
- no duplicate title/slug

## 10. Security baseline
Maintain or improve:
- CSRF protection
- password hashing using PHP password APIs
- secure session handling
- session ID regeneration on login
- authorization checks on all admin routes
- PDO prepared statements
- server-side validation
- output escaping
- upload validation if media uploads exist
- rate limiting / anti-spam for forms where practical
- safe error handling in production
- no directory listing
- secure headers
- secrets only in environment/config outside Git

Never log passwords, raw auth tokens, or full sensitive request bodies.

## 11. Performance
Target excellent mobile performance and Core Web Vitals.

Use:
- responsive images
- WebP/AVIF where practical
- lazy loading below the fold
- explicit width/height to reduce CLS
- minimal JS
- deferred/non-blocking scripts where possible
- optimized CSS
- caching where safe
- database indexes for common filters
- local/self-hosted critical assets when appropriate

Animations:
- premium and restrained
- use transform/opacity where possible
- respect `prefers-reduced-motion`
- never block conversion actions
- avoid animation libraries unless needed

## 12. UX / design rules
- Arabic-first RTL
- mobile-first
- premium black/gold visual language
- high contrast and readable typography
- strong, repeated but non-intrusive CTAs
- WhatsApp and call actions must be clear on mobile
- avoid visual clutter
- real/credible imagery where licensing permits
- do not use fake client logos, fake reviews, fake metrics or fabricated case-study results

## 13. Git workflow
Recommended branches:
- `main` = production-ready
- `development` = active integration

Rules:
- Keep commits focused and descriptive.
- Do not commit `.env`, secrets, logs, DB dumps with real user data, or production uploads.
- Before major changes, inspect `git status` and current branch.
- Run tests/lint/syntax checks before merge.
- Production deploy should track `main`.

Example commit messages:
- `feat: add article SEO fields`
- `fix: preserve UTM attribution on form submit`
- `perf: optimize hero images`
- `security: harden admin session handling`

## 14. Hostinger deployment
Production target:
`digital.suroohalshami.com`

Expected deployment model:
GitHub -> Hostinger Git deployment / supported deployment path -> production document root.

Important:
- Public web root must point to the intended `public` directory or equivalent secure document root.
- Environment secrets are configured on the server and never committed.
- Database migrations/schema updates must be deliberate and backed up.
- Do not overwrite user uploads or production `.env` during deploy.
- Verify file/folder permissions.
- Verify PHP version/extensions.

## 15. Required pre-deploy checks
Before any production deployment:
1. PHP syntax check passes.
2. No secrets are staged.
3. `.env` is ignored.
4. Admin authentication works.
5. Homepage and all service routes return expected HTTP status.
6. Contact form works.
7. WhatsApp/call tracking does not prevent navigation.
8. UTM attribution persists correctly.
9. Sitemap and robots are correct for production domain.
10. Canonical URLs use HTTPS production host.
11. No development URLs remain.
12. No placeholder credentials/content.
13. Database backup exists before schema changes.
14. Basic mobile QA completed.

## 16. AI agent working protocol
Whenever an AI agent continues this repository:

1. Read this file first.
2. Inspect:
   - repository tree
   - README
   - git status/log
   - routing
   - configuration
   - DB schema
   - public assets
   - admin/auth implementation
3. Summarize the current state internally before editing.
4. Preserve existing functionality.
5. Implement the smallest complete change.
6. Validate the change.
7. Update documentation if architecture or behavior changes.
8. Never claim something was tested if it was not actually tested.
9. Do not invent production credentials, analytics IDs, customer data, testimonials or performance metrics.
10. If a requested change risks production data or deployment, create a safe migration/backup path first.

## 17. Current priority backlog
Unless the repository already contains these features, prioritize in this order:

### P0 — launch blockers
- Verify full routing and production document-root setup
- MySQL production configuration
- Admin authentication hardening
- Lead persistence
- Contact form validation / anti-spam
- WhatsApp/call/form tracking
- UTM persistence
- sitemap / robots / canonicals
- privacy and terms pages
- 404/error handling
- responsive QA
- security review
- production deployment documentation

### P1 — growth
- Full blog CMS
- SEO controls
- media library
- portfolio/case studies
- improved analytics dashboard
- filtering leads by source/campaign/status/date
- CSV export of leads
- GA4/GTM integration
- conversion events

### P2 — optimization
- campaign dashboard
- content scheduling
- advanced CRM notes/history
- email notifications
- role-based admin permissions
- performance audits
- structured data refinements
- automated backups/deploy checks

## 18. Definition of done
A feature is not done until:
- implementation is complete
- responsive UI is checked
- server-side validation exists where relevant
- security implications are considered
- relevant tracking/SEO is preserved
- no PHP syntax errors
- no obvious console/server errors
- documentation is updated when needed
- code is committed with a clear message when working in Git

## 19. Non-negotiable constraints
- Do not modify the main Sorouh Alshami concrete/contracting website as part of this repository.
- Do not expose credentials.
- Do not silently remove tracking.
- Do not break RTL.
- Do not replace the approved Sorouh Digital identity without explicit approval.
- Do not fabricate business claims.
- Do not deploy destructive database changes without a backup/migration plan.

## 20. Verified continuation — 2026-10-05
- The supplied folder had no Git history or remote. A local `development` repository was initialized with an unmodified application baseline (apart from exclusions for runtime data). The supplied GitHub origin is now configured; Hostinger deployment has not been performed.
- Security phase: router authorization now runs before every protected admin read/mutation, including post CRUD and lead updates. Active admin role is rechecked in MySQL. Sessions have 30-minute inactivity and 8-hour absolute expiry, strict cookie-only IDs, regeneration and CSRF rotation at login, HttpOnly/SameSite cookies and Secure cookies in production.
- Runtime sessions use private `storage/sessions`; logs use `storage/logs`. These and rate-limit state must be writable by PHP and excluded from Git. The lock-based limiter in `app/RateLimiter.php` supports a single Hostinger application instance.
- Login is throttled by IP and account. CSRF rejects missing and array tokens. Errors return generic 500 responses with private reference IDs and no sensitive request or database text. Admin pages are no-store/noindex.
- Built-in server now serves public assets correctly; trailing-slash GETs redirect to canonical paths; method mismatches return 405. Framework, brand, legacy routes, CRM and CMS are preserved.
- Remaining P0 work at this phase: MySQL-backed contact/attribution verification, tracking validation and conversion correctness, legal pages, dynamic SEO resources, responsive/browser QA, complete release runbook and deployment preflight.

### Data and conversion phase
- MySQL is the source of truth for leads and events. An unavailable database returns 503 rather than writing personal data to fallback log files or reporting success. Lead and confirmed `form_submit` are committed atomically.
- `app/Attribution.php` captures all five UTM fields, a query-free original landing path and referrer hostname in the server session. Campaign visits refresh the campaign snapshot; navigation keeps it. No query string or arbitrary browser metadata is stored in analytics. A random HttpOnly first-party `sd_visitor` cookie (one year) links visits; session identifiers remain separate from authentication session IDs.
- Contact validation enforces name, phone, optional email, service allowlist, message limits and explicit privacy consent. Honeypot, minimum form age, locked IP throttling and unique submission IDs protect against spam and duplicates. Consent time is recorded. Tracking accepts only approved events, JSON bodies up to 8 KiB, valid session CSRF, and bounded rates. Client-side `form_submit` cannot fabricate conversions.
- Fresh schema: `database/schema.sql`. Existing installs: verified backup, then `php database/migrate.php --backup-confirmed`. Migration adds nullable lead `utm_term`, `referrer`, `visitor_id`, `session_id`, `submission_id`, `consent_at` and unique `idx_leads_submission`; adds event `utm_content`, `utm_term`, `landing_page`. It is idempotent and preserves all records and the existing `lost` status. `lost` is the verified legacy equivalent of the intended `unqualified`; no destructive enum conversion is made.
- Blog HTML is allowlist-sanitized both on write and render; legacy articles are protected. Slug uniqueness and safe image/canonical URLs are validated. JSON embedded in HTML is hex-escaped. CSP uses script nonces, forbids objects and limits external resources; admin inline handlers moved to `assets/js/admin.js`.
- Integration validation at this phase: 110 real HTTP/MySQL assertions passed on a disposable loopback test database, including all main routes, authentication, denied mutations, CSRF, lead persistence/UTM/duplicate prevention, confirmed conversions, event validation, CMS CRUD and CRM status updates. Production accounts/data were not used.

### Public launch surfaces and verified deployment behavior
- New GET routes: `/privacy`, `/terms`, `/contact` (redirect to the working homepage form), `/robots.txt`, `/sitemap.xml`. Static robots/sitemap were removed so Apache cannot shadow dynamic routes. Sitemap includes the homepage, six services, blog, policy pages and currently published articles. Draft/future articles are excluded. Production uses the configured HTTPS host; local pages and robots prevent indexing. Canonical/OG URLs are page-specific without query strings; social image/Twitter metadata and Service schema were added.
- `/services/custom-software` is now the canonical sixth service; the existing `/services/custom-solutions` remains available via a permanent redirect. Homepage links use the canonical route.
- Privacy and terms are Arabic public pages linked from the footer and form. Explicit consent and its timestamp are stored. Business owner must approve their wording for actual operations and any future advertising/analytics configuration; no legal-compliance certification is claimed.
- All critical brand assets now load locally: the original logo is unchanged; existing Tajawal weights and Lucide 0.468.0 are self-hosted with licenses; the same existing Pexels photos are local WebP (~140 KiB total) with dimensions and lazy loading below the fold. Sources are recorded in `docs/ASSET_SOURCES.md`. No invented case studies or business claims were introduced.
- Mobile QA discovered and fixed RTL overflow from the honeypot and admin grid/table sizing. Public menu has expanded state/Escape handling, reduced-motion support is explicit, and content remains visible without JavaScript.
- Root `.htaccess` fails closed on accidental whole-app deployment; `public/.htaccess` explicitly grants the intended public root, rewrites application routes, protects sensitive extensions and caches assets. `uploads/.htaccess` rejects executable/multiple-extension files. Actual Apache testing verified these boundaries.
- CSRF failure now uses standard HTTP 403. Apache mod_php turned legacy nonstandard 419 into 500; PHP built-in and Apache integration checks now agree. Disabled accounts and password resets immediately and permanently revoke existing admin authentication sessions.
- `database/create_admin.php` accepts `--password-stdin`, validates email/password bounds and refuses credential replacement without explicit `--reset`. Legacy positional-password usage remains supported for compatibility; use stdin in deployment.
- `tools/preflight.php` is a read-only production configuration gate (domain, runtime, database/schema, admin, private storage and public boundary). It deliberately rejects development, missing admins and disconnected databases.
- `tools/package_hostinger.php /home/ACCOUNT/PRIVATE_APP_DIRECTORY` creates a **new** public-only package under private `storage/cache`, with a front-controller wrapper for a separately deployed private app. It copies no customer uploads, secrets or private code and does not publish. Use only after determining the actual Hostinger private path; never overwrite existing uploads or the main company site.
- GA4/GTM IDs remain optional and empty. Valid GTM loads its container; otherwise valid GA4 loads directly, without double-loading both. First-party tracking does not depend on Google. Advertising consent and actual tag delivery require owner configuration/verification.

### P0 status and release gate
| P0 item | Verified local implementation | Remaining target-server requirement |
| --- | --- | --- |
| Routing/document root | Public paths, method handling, redirects, Apache rewrite and access guards tested | Configure actual subdomain root and SSL/HTTPS in Hostinger |
| MySQL configuration | Fresh schema, dedicated local user and non-destructive migration tested | Supply real production database/user and verify backup |
| Admin authentication | Authorization, throttling, expiry, CSRF, disable/reset revocation tested | Create the real administrator via stdin |
| Lead persistence/anti-spam | Validation, consent, idempotency and atomic lead/conversion tested | Submit and confirm one launch QA request on the real host |
| Tracking/UTM | All required events, five UTM fields, session/visitor IDs and original landing tested | Validate real acquisition links and optional Google IDs |
| SEO/legal/errors | Dynamic robots/sitemap, canonical/OG, legal pages and generic 404/500 tested | Owner approval of policy text and final-domain crawl checks |
| Responsive/security review | Public/admin browser QA and real Apache boundary tests passed | Final device/hosting smoke test with production configuration |
| Deployment documentation | Hostinger runbook, packaging, backup/rollback and preflight implemented | Green hosted CI, Hostinger account/path and production backup |

This is a locally verified release candidate, **not an already deployed or certified production installation**. GitHub origin was supplied as https://github.com/sorouhshamico-pixel/suroh-digital.git. Hostinger credentials, production database, SSL endpoint and production admin remain unprovided. Do not claim these external launch gates passed.
Local preview stays on `http://127.0.0.1:8080` with dedicated `sorouh_local` MySQL on loopback 3307 and generated credentials in ignored `.env`; no admin credentials were invented. This development MySQL process uses a disposable local data directory under the Windows temporary directory; it is not production infrastructure. After a reboot, configure/start a regular local MySQL instance and update `.env` as needed.

### Tests and how to continue
- `tests/run.php`: 120 real HTTP/MySQL assertions (executed under PHP's built-in server and Apache 2.4 with MySQL 8.4/PHP 8.3). Test database names must end in `_test`; runtime must be under `storage/testing`. Never use production data.
- `tests/browser.cjs`: 133 Chrome checks, public screens at 320/375/768/1440px, admin at 375/1440px, loaded local fonts/images/icons, form/UTM, navigation during intentional tracking outage and CRM under CSP. Expected deliberate `/api/track` abort is the only allowed network/console failure; no unexpected resource or JS errors passed.
- `tests/apache.php`: 8 actual document-root/upload/cache guard checks; requires separate public-root and deliberately misconfigured-root loopback Apache instances.
- `tests/migration.php`: 6 preservation/uniqueness checks against the original schema fixture, with the additive migration applied twice in a generated scratch database.
- `tests/production.php`: 10 simulated-production SEO/cookie/header checks on loopback; this does not verify real DNS/SSL.
- `tests/preflight.php`: 3 checks that valid simulated production passes and development/outage fail, using a disposable SELECT-only database user.
- `tests/admin_cli.php`: 7 stdin creation/reset/validation checks; passwords are random disposable values and never printed. Suites run sequentially against isolated data.
- PHP lint and JavaScript syntax checks pass. `.github/workflows/ci.yml` now defines MySQL integration, migration, simulated production, preflight and Playwright QA; hosted GitHub Actions status must be checked after publishing to the configured origin.
- Start from `tests/README.md`, then complete the external release gate in `docs/HOSTINGER_DEPLOYMENT.md`. P1/P2 remain as listed above; media uploads, portfolio and advanced CRM are not silently introduced in this P0 pass.

### GitHub publication — 2026-10-05
- User supplied origin: https://github.com/sorouhshamico-pixel/suroh-digital.git.
- Remote was verified empty before first publication; main and development use the same reviewed release snapshot, without force-pushing or replacing remote history.
- Secrets and runtime data remain ignored and were checked absent from Git history. Hostinger deployment remains a separate release gate.

### Frontend and asset-path repair — 2026-10-05
- Root cause was verified against baseline `e4372cf`: its PHP development router dispatched asset URLs as application routes. A local reproduction returned CSS as **404 text/html**, explaining the unstyled white page and raw image sizes in the supplied screenshot. Before this repair, root CSS delivery had already been corrected by the preceding P0 work, but root-only asset/navigation/font/tracking paths still broke subfolder installs and the public CSS contained conflicting layout overrides.
- New shared `app/paths.php` contains environment loading and pure path helpers, loaded by both the front controller and bootstrap without starting sessions for assets. `APP_URL` remains the one configuration source: its URL path becomes the installation prefix. No host-derived production canonicals or separate conflicting BASE_URL are introduced.
- `route_path()` accepts logical internal paths (for example `/blog`); `request_path()` removes the mount prefix before routing, access-control checks and canonical generation. Requests outside a configured mount cannot reach valid routes. `asset()` prefixes local asset URLs and attaches a SHA-256 content fingerprint, cached within the request, to invalidate old CSS/JS/image cache entries after deployment. `url()` still generates absolute canonical URLs from APP_URL.
- All public/admin templates, navigation, form actions and redirect targets use these helpers. Session and visitor cookies are scoped to the installation directory. The tracking endpoint and logical route are provided in `sd-context`; confirmed form events keep the actual mounted contact URL. Attribution still stores the original query-free landing path and all five UTM fields. SEO robots disallow paths also honor the prefix.
- `media_url()` resolves stored logical `/assets/...` and `/uploads/...` media at display time, leaving external HTTP(S) URLs intact. Blog sanitization has an optional display mode that resolves allowed local image/link URLs without changing database content or weakening the HTML allowlist. Continue storing logical paths in CMS, not deployment-specific prefixes.
- The front controller safely serves only allowed public asset/upload extensions with correct MIME and GET/HEAD handling before sessions/routing, including under the development server's virtual subfolder. Missing/disallowed assets return explicit 404, never an HTML page with a success status. Real Apache serves existing assets directly using its unchanged public-root guards and rewrites missing/application requests to index.php. Font URLs are relative to the stylesheet (`../fonts/...`), not to the current page route.
- Public `app.css` is now a single organized design system: 1360px outer container, responsive inline padding, reusable spacing tokens, explicit reset/typography/buttons/forms, charcoal/metallic gold/subtle blue identity, bounded grids and aspect-ratio media. Header is sticky, hero remains two columns at desktop with Arabic copy right and visual left; <=800px stacks copy before visual. Existing service/blog/legal/error/admin-login surfaces retain styling and functionality.
- Homepage preserves the original content/photos/logo and adds an illustrative solutions portfolio, factual counts derived from the six services/four stages and a WhatsApp CTA. No real customer projects, testimonials, client counts or revenue claims are fabricated. Portfolio CMS/case-study publishing remains future work; no route or table was added.
- Reveal motion is opt-in only for offscreen content, uses opacity/transform, does not hide the hero or content without JavaScript, respects reduced motion, and has no looping floating-card animation. Mobile navigation keeps Escape/outside-click/resize handling, an accessible WhatsApp label and a skip link.
- Deployment: set APP_URL to the complete installation URL (e.g. local `http://127.0.0.1:8080/sorouh` or target `https://digital.suroohalshami.com`). Public document-root/private-storage boundaries still apply. Actual Apache 2.4 Alias mounting was verified locally; do not remove root .htaccess to make a subfolder work. Existing Hostinger external launch gates remain required.
