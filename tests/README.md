# Verification
The PHP integration suite requires a **disposable MySQL database whose name ends in _test**. It clears test users/posts/leads/events. Never point it at production.
Set APP_ENV=local, APP_URL=http://127.0.0.1:8081, TEST_BASE_URL=http://127.0.0.1:8081, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME and DB_PASSWORD in the process environment.
Set STORAGE_PATH to an absolute path inside this project's storage/testing/runtime; create its logs, cache and sessions directories.
Start a separate server with the same environment:
`php -S 127.0.0.1:8081 -t public public/index.php`
Run `php tests/run.php`. The suite applies fresh schema and the additive migration twice, then exercises real HTTP flows and verifies records in MySQL.
For browser tests install Playwright as a development-only dependency (`npm install --no-save --package-lock=false playwright`). Run `node tests/browser.cjs` with the same environment and CHROME_PATH set to the installed Chrome/Chromium binary (Windows Chrome is the default). It uses random disposable admin credentials and tests Arabic RTL, mobile layouts, navigation, form conversion, UTM, tracking failures and CRM under CSP.
Screenshots and console/network reports go to ignored tests/artifacts. Browser tests do not imply that third-party CDN availability or real GA/GTM delivery was verified; inspect the report.
PHP syntax: lint all PHP files under app, config, public, resources, routes, database, tools and tests.
JavaScript syntax: `node --check public/assets/js/app.js` and `node --check public/assets/js/admin.js`.
`php tools/preflight.php` is a read-only production configuration check; it must fail on a local/development environment. No production deploy is performed by any test.
Additional suites, with the same isolated test environment: `php tests/admin_cli.php` (stdin creation/reset/validation), `php tests/migration.php` (legacy records retained, needs CREATE DATABASE permission), `php tests/production.php` (temporary loopback 8084 server), and `php tests/preflight.php` (temporary SELECT-only user, needs CREATE USER permission).
For actual Apache guard verification, configure loopback public-root 8082 and deliberately wrong whole-project-root 8083 hosts, then run `php tests/apache.php`. It creates and cleans two uniquely named executable fixtures in public/uploads and checks access denial; do not run against a published deployment.
Run suites sequentially: they share disposable fixtures and limiter state. CI prepares the schema with `php tests/prepare.php` before starting its server. Set a nonempty test MySQL password on Windows so .env loading cannot replace an unset empty process variable.
