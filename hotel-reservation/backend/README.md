# Active Laravel / React application

This directory contains the supported application: Laravel 12 serves React 19 pages through Inertia 2. Build and run both layers here.

See the [root README](../../README.md) for installation, isolated demo launch, REST endpoints, booking rules and test commands. The standalone sibling frontend is an inactive prototype.

Quick start after installing PHP/Composer and Node:

```sh
composer install
npm ci
npm run build
php scripts/demo.php
```

The demo launcher always creates a fresh disposable SQLite file. Never migrate or reset an existing database for demonstrations or tests.
