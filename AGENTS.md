# AGENTS.md

# Project Overview
Filament Analitik: Simple page view analytics plugin for Filament v4/v5 (Laravel 12+).

## Tech Stack & Architecture
- PHP 8.2+, strictly typed. PSR-12 standard.
- Framework: Laravel 12+, Filament v4/v5.
- Core Flow: `TrackPageView` middleware -> `TrackPageViewJob` (async queue) -> `analitik` database table.
- IP Geo Lookup: `stevebauman/location`.
- Testing: Pest PHP (`vendor/bin/pest`).

## Development & Test Commands
```bash
composer install
vendor/bin/pest              # Run all tests
vendor/bin/pest --parallel   # Run tests in parallel
```

## Key Files & Entry Points
- `src/FilamentAnalitikPlugin.php`: Main Filament plugin registration.
- `src/FilamentAnalitikServiceProvider.php`: Laravel package service provider.
- `src/Http/Middleware/TrackPageView.php`: Page tracking middleware.
- `src/Jobs/TrackPageViewJob.php`: Async processing job for analytics.
- `src/Models/PageView.php`: Analytics database model.
- `doc/SRS.md`: System Requirements Specification & detailed architecture.

## Operational Rules & Conventions
- **Migrations**: Published manually via `php artisan vendor:publish --tag="filament-analitik-migrations"`. Do NOT auto-load migrations from vendor.
- **Code Style**: Strictly typed PHP 8.2+. Pest tests required for new feature/bugfix.
- **Commit Format**: Conventional commits (`fix:`, `feat:`, `docs:`, `test:`).
