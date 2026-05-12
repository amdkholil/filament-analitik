# Filament Analitik

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kholil/filament-analitik.svg?style=flat-square)](https://packagist.org/packages/kholil/filament-analitik)
[![Total Downloads](https://img.shields.io/packagist/dt/kholil/filament-analitik.svg?style=flat-square)](https://packagist.org/packages/kholil/filament-analitik)

Simple and lightweight page analytics plugin for Filament v4/v5. Track your website visitors, their location, and page views directly from your Filament dashboard.

**A simple, free, and privacy-friendly alternative to Google Analytics.** No complex setup, no external scripts, and no tracking cookies required. Just install and start tracking your website traffic instantly.

## Features

- 🚀 **Asynchronous Tracking**: Uses Laravel Jobs to ensure zero performance impact on your application.
- 📍 **Location Tracking**: Automatically detects city, state, and country using [stevebauman/location](https://github.com/stevebauman/location).
- 📊 **Dashboard Widgets**: Includes stats overview and page views chart widgets.
- 📋 **Resource View**: Manage and view detailed analytics logs in your Filament panel.
- 🛡️ **Privacy Focused**: Excludes Filament panel pages from tracking by default.

## Installation

You can install the package via composer:

```bash
composer require kholil/filament-analitik
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-analitik-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-analitik-config"
```

## Usage

### 1. Register the Plugin

Add the plugin to your Filament Panel provider:

```php
use Kholil\FilamentAnalitik\FilamentAnalitikPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            FilamentAnalitikPlugin::make(),
        ]);
}
```

### 2. Enable Tracking

To start tracking page views, you must add the `TrackPageView` middleware to your web routes.

#### Laravel 12+ (bootstrap/app.php)

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \Kholil\FilamentAnalitik\Http\Middleware\TrackPageView::class,
    ]);
})
```

#### Middleware Logic
The middleware will:
- Only track `GET` requests with a `200` status code.
- Automatically exclude any requests within the Filament admin panel.
- Dispatch a background job to handle the database insertion and location lookup.

## Configuration

The configuration file allows you to:
- Enable or disable tracking.
- Customize the database table name (if published).

## Documentation

Detailed technical documentation and requirements can be found in the [doc](doc) folder:
- [Software Requirements Specification (SRS)](doc/SRS.md)

## Requirements

- PHP 8.2+
- Filament v5.0+
- Laravel 12.0+

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
