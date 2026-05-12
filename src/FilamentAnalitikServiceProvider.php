<?php

namespace Kholil\FilamentAnalitik;

use Illuminate\Support\ServiceProvider;
use Kholil\FilamentAnalitik\Commands\FilamentAnalitikCommand;
use Kholil\FilamentAnalitik\Testing\TestsFilamentAnalitik;
use Livewire\Features\SupportTesting\Testable;

class FilamentAnalitikServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/filament-analitik.php' => config_path('filament-analitik.php'),
            ], 'filament-analitik-config');

            $this->publishes([
                __DIR__ . '/../database/migrations/' => database_path('migrations'),
            ], 'filament-analitik-migrations');
        }

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-analitik');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/filament-analitik.php', 'filament-analitik');
    }
}
