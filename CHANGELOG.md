# Changelog

All notable changes to `filament-analitik` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.1] - 2026-09-24

### Fixed
- **Navigation Group Icon Conflict**: Suppress the analytics navigation item icon when the configured navigation group declares its own icon, preventing Filament's "group has an icon but one or more of its items also have icons" exception.

---

## [1.1.0] - 2026-09-24

### Changed
- **Logs Page UX**: Removed overview stats & chart header widgets from `ListPageViews` resource page (`Analitik Logs`) so it focuses solely on the raw logs table.

### Fixed
- **MySQL Strict Mode Compatibility**: Updated `TopPagesTable` and `TopCountriesTable` queries to use subqueries with model table aliases, eliminating `ONLY_FULL_GROUP_BY` syntax errors.
- **Repository Hygiene**: Removed `tests/TestCase.php` from `.gitignore` so CI and fresh clones can execute tests cleanly.
- **Fail-Safe Job Execution**: Wrapped `TrackPageViewJob` handle logic in `Throwable` try/catch with `report()`, added `$tries = 3`, `$backoff = 5`, and a `failed()` lifecycle handler (complies with SRS FR-4.2).
- **Migration Data Type**: Updated `url` column from `string('url')` to `text('url')` to prevent MySQL strict mode errors on long URLs / query strings.
- **Bot/Crawler Filtering**: Added crawler and empty user agent filtering logic via `exclude_bots` and `bot_patterns` configuration.
- **Multi-Tenant Scoping**: Ensured all widget queries scope by `project_id` when configured.
- **Chart Boundary & Overcount**: Aligned `PageViewsChart` 24h filter window and hour formatting (`Y-m-d H:00`) to eliminate double-counting on boundary hours.
- **Widget Test Authenticity**: Replaced inline SQL queries in `WidgetTest.php` with direct widget method calls and query extractions.
- **Resource UX**: Removed empty `ViewAction` modal from `PageViewResource`.
- **Widget Performance**: Optimized `AnalitikStatsOverview` query count by memoizing daily series.
- **Filter Whitelisting**: Added fallback to default filter `'7'` on invalid/non-numeric widget filter inputs.
- **Path Normalization**: Ensured paths tracked by middleware consistently include leading slash (`/`).
- **Table Naming Consistency**: Unified default database table name to `analitik`.

---

## [1.0.2] - 2026-09-24

### Added
- Multi-tenant `project_id` support in configuration, jobs, and `PageViewResource`.
- Custom sidebar navigation settings (label, group, icon) via plugin fluent methods and config.
- Flexible access control (Laravel Gate, roles check, custom fluent callback).

---

## [1.0.1] - 2026-09-24

### Added
- Support for Filament v4 and v5.
- Support for Laravel 11.x, 12.x, and 13.x.
- Top Countries table widget (`TopCountriesTable`).
- PostgreSQL compatibility in `PageViewsChart`.

---

## [1.0.0] - 2026-09-24

### Added
- Initial release of Filament Analitik plugin.
- Page view tracking middleware (`TrackPageView`) and queued job (`TrackPageViewJob`).
- IP Geolocation using `stevebauman/location`.
- Page views chart (`PageViewsChart`) and stats overview (`AnalitikStatsOverview`) widgets.
- Analytics logs resource (`PageViewResource`).
