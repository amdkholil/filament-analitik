# CLAUDE.md

## Project: Filament Analitik
Simple page analytics for Filament v4/v5 (compatible with Laravel 12+).

## Commands
- `composer install`: Install dependencies.
- `composer update`: Update dependencies.
- `vendor/bin/pest`: Run all tests.
- `vendor/bin/pest --parallel`: Run tests in parallel.
- `vendor/bin/pest --coverage`: Run tests with coverage (requires Xdebug/PCOV).
- `php artisan vendor:publish --tag="filament-analitik-config"`: Publish config.
- `php artisan vendor:publish --tag="filament-analitik-migrations"`: Publish migrations.

## Technical Context
- **Documentation**: See [doc/SRS.md](doc/SRS.md) for full requirements and architecture.
- **Tracking Logic**: Uses `TrackPageView` middleware to dispatch `TrackPageViewJob`.
- **Database**: Stores data in `filament_page_views` table.
- **Location**: Uses `stevebauman/location` for geo-IP lookup.

## Coding Standards
- **PHP**: PHP 8.2+, strictly typed. PSR-12 standard.
- **Filament**: Use Filament v5 patterns for Widgets, Resources, and Plugins.
- **Testing**: Pest PHP for all tests (Feature & Unit).
- **Architecture**: Keep tracking logic asynchronous (Jobs). Use service providers for package bootstrapping.

## Skills 🪨
- **Caveman**: Ultra-compressed communication. Cuts tokens ~75%. Trigger: `/caveman`.
- **Cavecrew**: Delegate to compressed subagents. Trigger: `/cavecrew`.
- **Caveman-Commit**: Terse conventional commits. Trigger: `/caveman-commit`.
- **Caveman-Review**: One-line code reviews. Trigger: `/caveman-review`.
- **Caveman-Help**: Skill documentation. Trigger: `/caveman-help`.
