# Security Policy

## Reporting a Vulnerability

Please report security vulnerabilities privately via email to **amdkholil@gmail.com**.

Do **not** open a public GitHub issue for security reports.

Include as much detail as possible:

- Description of the vulnerability
- Steps to reproduce
- Affected version(s)
- Potential impact

You will receive an acknowledgement within 72 hours. A fix and public disclosure will follow once the issue is confirmed and patched.

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |

## Scope

This package:

- Tracks page views via middleware and a queued job (no client-side JS, no cookies).
- Stores IP address, user agent, URL, and geo-location data in the `analitik` table.
- Provides Filament dashboard widgets and a resource, gated by configurable authorization.

Security reports in scope:

- Authorization bypass on the analytics dashboard/resource (e.g. `canAccess`, gate/role checks).
- SQL injection or mass-assignment issues in queries or model handling.
- Data leakage of tracked analytics data to unauthorized users.
- Unsafe deserialization or job payload handling in `TrackPageViewJob`.

Out of scope:

- Issues in dependencies (`filament/filament`, `stevebauman/location`, Laravel) — report upstream.
- Privacy concerns about IP/user-agent collection itself (by-design analytics behavior; see README).
- Local development dummy IP rewrite (`8.8.8.8` when `app.env=local`).

## Recommendations for Integrators

- Publish and review `config/filament-analitik.php`; set `access.gate` or `access.roles` to restrict the dashboard.
- Keep the queue driver asynchronous (`QUEUE_CONNECTION=redis`/`database`/etc.) so tracking failures never affect end users.
- Treat the `analitik` table as containing personal data (IP, user agent, location); apply your own retention/cleanup policy.
- Only publish migrations manually (`--tag="filament-analitik-migrations"`) as documented; do not autoload migrations from vendor.
