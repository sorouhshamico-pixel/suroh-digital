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
- The supplied folder had no Git history or remote. A local `development` repository was initialized with an unmodified application baseline (apart from exclusions for runtime data). GitHub/Hostinger deployment is not configured or performed.
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
