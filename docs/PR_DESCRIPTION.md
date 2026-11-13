## Problem and result

The existing booking flow blocked entire room types, used incomplete overlap logic, accepted unsafe lifecycle actions and exposed public debug details. This change retains the existing Laravel/React/Inertia application and makes bookings operate on physical rooms with transactional availability checks, server-side nightly pricing, repeat-request protection and role/ownership enforcement.

## Changes

- Add BookingService, strict Form Request, ownership Policy and administrator Gate.
- Calculate half-open intervals, nightly seasonal prices and integer-cent totals.
- Make cancellation/check-in/check-out atomic and reject incompatible transitions.
- Protect management endpoints, booking history and active accounts.
- Remove debug routes, internal error disclosures and duplicate middleware.
- Add the administrator/guest/request-key/index migration with safe legacy-data notes.
- Complete catalogue filters, responsive booking/history screens and management actions.
- Align Inertia client/server dependencies, restore Tailwind processing and update vulnerable packages.
- Add isolated demo launchers, tests, eight real screenshots and portfolio documentation.
- Add GitHub Actions for PHP checks, process concurrency, frontend lint/build and browser tests.
- Preserve the inactive standalone Vite prototype without creating another SPA.

## Executed validation

- PHPUnit 11.5.58: 44 tests, 154 assertions passed on PHP 8.2.12 / SQLite in memory.
- Two independent SQLite processes: one booking and one expected conflict.
- Pint, strict Composer validation, ESLint and Vite build passed.
- Reproducible npm ci passed.
- Composer audit: no vulnerability advisories; npm audit: zero vulnerabilities.
- Edge/Playwright: full registration/search/booking/confirmation/cancellation flow passed.
- Responsive overflow checks at 320, 390, 768 and 1440 px.
- git diff --check and targeted working-tree secret-pattern inspection passed.

## Review and rollout

No existing database was migrated/reset, no production was deployed and no merge is requested automatically. The new migration was tested only on disposable databases. Review legacy nullable room IDs and cascades before any real-data migration.

MySQL concurrency remains untested. The UI shows base estimates; seasonal totals are authoritative at confirmation. Inventory CRUD remains primarily API-based. ESLint 9 is retained for peer compatibility and emits an upstream deprecation notice.

Hosted GitHub Actions must be checked after this PR is opened; local checks do not constitute a hosted CI result.
