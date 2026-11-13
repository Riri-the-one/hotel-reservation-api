# Security

## Implemented controls

- Laravel session authentication and CSRF middleware for Inertia web mutations.
- Sanctum bearer authentication for REST clients, current-token revocation and seven-day expiry.
- Rate-limited API login; Breeze retains its login throttling.
- Booking Form Request with strict ISO dates, capacity, stay horizon and UUID validation.
- Owner-only confirmation and cancellation through ReservationPolicy.
- Administrator Gate on all inventory resources, check-in/out and the management page.
- is_admin excluded from public mass assignment; default false.
- Server-owned user ID, room type, price and status. Monetary inputs are not trusted.
- Room-row locking, transactions, availability checks and unique request keys.
- Atomic conditional transitions; protected booking history on room deletion.
- Removed /debug-inertia, public exception/file/line disclosures and console debugging.
- Generic public error pages; JSON exceptions do not expose traces with debugging disabled.
- Environment files, databases, dependency directories and caches ignored by Git.

No cross-origin browser SPA is used. The browser uses same-origin session cookies, not localStorage bearer tokens. API routes do not rely on browser session CSRF exemptions.

## Operational configuration

Keep APP_DEBUG=false outside private development. Set SESSION_SECURE_COOKIE=true when using HTTPS. Keep APP_KEY stable and secret in a real environment. Restrict database access and mail credentials. Do not expose the local development server publicly.

Administrator promotion must be an explicit trusted operator action. Do not add an is_admin field to registration or publish a shared administrator password. The demo creates no users automatically.

Reset mail uses Laravel's configured mail driver. This work did not configure a production mail service or enforce email verification.

## Verification and limits

PHPUnit covers anonymous access, ownership, role escalation, role restrictions, token login/revocation, throttling, invalid booking input and internal error suppression. Browser tests cover the actual CSRF-protected booking/cancellation flow.

Composer and npm security audits were executed after updating compatible dependencies; final results are recorded in TESTING.md. This is an application review, not a penetration test or a guarantee against all vulnerabilities. The inactive Vite prototype is outside the supported dependency installation.

Existing cascade foreign keys and nullable legacy room IDs need review before importing old data. No history-wide secret audit or external security scan is claimed.
