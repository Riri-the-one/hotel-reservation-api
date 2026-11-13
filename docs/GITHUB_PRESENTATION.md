# GitHub and interview presentation

Suggested description: Full-stack hotel booking portfolio — Laravel 12, React 19, Inertia, secure reservations and automated tests.

Suggested topics: laravel, react, inertiajs, hotel-booking, rest-api, php, portfolio, phpunit.

## Five-minute demonstration

1. Show the catalogue at desktop and mobile widths.
2. Search a future stay, open a room and explain capacity and base estimates.
3. Register with fictional data and book two nights.
4. Show the confirmation and personal reservation list, then cancel.
5. Explain half-open intervals, per-room transactions and idempotency.
6. Open PHPUnit and the process race test; distinguish SQLite evidence from untested MySQL behavior.

The administrator API and reservation dashboard require a trusted operator-provisioned administrator. No shared admin password is published.

## Engineering points to explain

- Why the existing Inertia interface was retained instead of building a second SPA.
- Why rooms of the same type must have independent availability.
- Why browser totals cannot be trusted.
- Why adjacent checkout/check-in dates do not overlap.
- Why a transaction alone is insufficient without a locking strategy.
- Why booking ownership and administrator privileges are separate.
- Why unexecuted checks are explicitly listed as limitations.

## Pull request

Suggested title: Finalize secure hotel bookings and portfolio presentation.

Review focus: BookingService, StoreReservationRequest, ReservationPolicy, management Gate, additive integrity migration, isolated demo scripts and booking/concurrency tests.

Do not merge automatically or deploy production. Local .git writes are restricted in this session; the GitHub connector also requires approval unavailable under session policy. No remote publication occurred. PUBLISHING.md provides the exact remaining commands and PR_DESCRIPTION.md contains the prepared review description.
