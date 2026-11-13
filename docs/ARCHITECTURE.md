# Architecture and migration notes

## Decision

Retain the existing Laravel + React/Inertia monolith in backend/. The separate frontend/src/App.jsx is a Vite demonstration, not an integrated SPA. No second interface, permissive CORS setup or browser token storage is needed.

The existing pages, Breeze authentication, models and migrations remain the foundation. Booking rules now live in BookingService; StoreReservationRequest validates input, ReservationPolicy controls ownership, and manage-hotel protects administration.

## Data and pricing

A RoomType has Rooms and Tariffs; a Room has Reservations and RoomImages; a User owns Reservations. Reservation retains room_type_id for compatibility and stores its physical room_id, guest name, guests, total and status.

Tariffs are valid on inclusive start/end dates, with null endpoints meaning unbounded validity. Among overlapping matching tariffs the highest ID wins deterministically. Each occupied night is priced independently; base_price is the fallback. Currency is EUR for this demo. Accepted tariff input has at most two decimals; integer cents and BCMath avoid rounding drift. Totals must fit DECIMAL(8,2).

Half-open booking intervals use existing.start < requested.end AND existing.end > requested.start. Only confirmed and checked_in block availability. Maintenance rooms cannot be booked.

## Concurrency and lifecycle

Booking transactions first update the room row, then lock it and check availability. This obtains an InnoDB row lock and serializes SQLite writers before a read snapshot is established. Transactions retry supported concurrency failures up to five times. A real two-process SQLite race test verifies one success and one conflict.

A unique request_key deduplicates successful identical retries. Reusing a key with changed input or a different owner is rejected. Unique-key races return a conflict rather than exposing a database exception.

Status updates use an atomic conditional UPDATE: confirmed → cancelled before arrival; confirmed → checked_in during the stay; checked_in → checked_out. Repeated or incompatible actions return 409. Early checkout is allowed. Check-in requires an administrator.

## Safe migration procedure

Only 2026_11_13_000001_harden_booking_integrity.php changes the schema: users.is_admin defaults false; reservations.guests defaults 1; nullable unique request_key preserves legacy rows; a room/status/date index accelerates availability queries.

This migration was applied only to newly created disposable databases. No existing database was migrated or reset.

Before any future existing-data migration: back up and restore into a separate staging database, verify the resolved connection/database name, inspect null room_id values, invalid dates, overlaps, unexpected statuses, zero prices and legacy guest counts. Manually map historical bookings to physical rooms; do not infer mappings silently. Existing nullable request keys need no synthetic values.

Test migrations on the restored copy and review counts/foreign keys before scheduling a real migration. Rolling back drops the new metadata and must not be used casually after bookings rely on it.

Legacy cascade rules remain unchanged. HTTP room deletion rejects rooms with booking history, and room-type deletion rejects types in use. Active reservations prevent account deletion. Direct SQL/imports bypass these application rules; future work should introduce retention constraints after an explicit legacy-data audit.

## Preserved prototypes

The old standalone frontend, Welcome page and legacy seeders remain for traceability. DatabaseSeeder now delegates to the guarded DemoSeeder; unsupported individual old seeders are not run. DemoSeeder refuses production and a non-empty room-type catalogue.
