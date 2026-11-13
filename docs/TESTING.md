# Testing

Run commands from hotel-reservation/backend.

## Isolation

PHPUnit forces DB_CONNECTION=sqlite, DB_DATABASE=:memory: and an empty DB_URL. TestCase checks these before any RefreshDatabase migration and supplies an ephemeral application key. It disables Vite rendering for HTTP assertions.

The process race test validates its database path, exclusively creates a new file under storage/framework/testing, refuses cached configuration, boots only that connection and removes its own database afterwards.

Both demo launchers generate a new database path and print the resolved SQLite connection before migrations. They refuse cached configuration. Existing databases are never reset.

## Commands

```sh
composer install
npm ci
php vendor/bin/phpunit
php tests/Concurrency/booking-race.php
php vendor/bin/pint --test
composer validate --strict
composer audit
npm run lint
npm run build
npm audit
php scripts/demo.php
# In another terminal:
npx playwright test
```

Local browser tests use installed Edge. CI uses Playwright Chromium. Set DEMO_URL to target a different **isolated local demo** port. The browser test writes actual screenshots to docs/screenshots and creates only fictional example.test accounts. It should never be aimed at a real database.

## Coverage

Authentication, token revocation/throttling, registration privilege escalation, anonymous access, management Gates, reservation ownership, strict dates/capacity, server pricing, nightly seasonal overrides, room-level availability, overlap containment/boundaries, idempotency, cancellation/released inventory, lifecycle transitions and rollback.

Responsive checks cover catalogue and booking form widths 320, 390, 768 and 1440 px. Browser test verifies search, registration, full booking confirmation, cancellation, management denial and absence of JavaScript page errors.

## Results

Exact final executed results are maintained in FINAL_REPORT.md. An initial browser assertion exposed SQLite's numeric money serialization; adding an explicit decimal:2 cast fixed confirmation formatting. The final browser journey passed.

The initial npm installation exposed peer-dependency incompatibilities. Inertia client/server were aligned to major 2, React's Vite plugin updated, unused Tailwind 4 integration removed, and PostCSS/Tailwind 3 processing restored. npm audit fix updated compatible vulnerable dependencies.

Initial composer audit found outdated framework/transitive packages. A compatible Composer update resolved the reported advisories.

## Limits

The concurrent test executes two independent SQLite processes; it does not prove MySQL behavior. No existing or production database is used. There is no visual pixel-diff suite, load test, production mail delivery or payment test. Browser coverage is one integrated journey rather than an exhaustive cross-browser suite.

An early artisan test invocation emitted missing-.env warnings; the final direct PHPUnit command is clean and uses no .env file.
