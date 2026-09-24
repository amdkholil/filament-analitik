<?php

namespace Kholil\FilamentAnalitik\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Kholil\FilamentAnalitik\FilamentAnalitikServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Filament\Panel;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;

class TestCase extends Orchestra
{
    use InteractsWithViews;
    use InteractsWithSession;
    protected function setUp(): void
    {
        parent::setUp();
        $this->startSession();
    }
    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            FilamentServiceProvider::class,
            SupportServiceProvider::class,
            ActionsServiceProvider::class,
            WidgetsServiceProvider::class,
            NotificationsServiceProvider::class,
            \Filament\Forms\FormsServiceProvider::class,
            \Filament\Tables\TablesServiceProvider::class,
            \Filament\Schemas\SchemasServiceProvider::class,
            BladeIconsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            \Stevebauman\Location\LocationServiceProvider::class,
            FilamentAnalitikServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('app.key', 'base64:Hupx3yAySlyS9vFj3u719y5I0E0s9jS092jS092jS09=');
        config()->set('session.driver', 'array');
        config()->set('view.compiled', __DIR__ . '/../tests/compiled');
        config()->set('location.driver', \Stevebauman\Location\Drivers\IpApi::class);

        // Register panel via resolving() so it is applied when PanelRegistry is first resolved.
        // Filament::registerPanel() must run before PanelRegistry is booted/resolved.
        Filament::registerPanel(
            Panel::make()
                ->default()
                ->id('test')
                ->plugin(new \Kholil\FilamentAnalitik\FilamentAnalitikPlugin()),
        );

        $app->booted(function () use ($app) {
            $errors = new \Illuminate\Support\ViewErrorBag;
            $errors->put('default', new \Illuminate\Support\MessageBag);
            $app['view']->share('errors', $errors);

            \Livewire\Livewire::component('analitik-stats-overview', \Kholil\FilamentAnalitik\Widgets\AnalitikStatsOverview::class);
            \Livewire\Livewire::component('page-views-chart', \Kholil\FilamentAnalitik\Widgets\PageViewsChart::class);
            \Livewire\Livewire::component('top-pages-table', \Kholil\FilamentAnalitik\Widgets\TopPagesTable::class);
            \Livewire\Livewire::component('top-countries-table', \Kholil\FilamentAnalitik\Widgets\TopCountriesTable::class);
            \Livewire\Livewire::component('visitors-country-chart', \Kholil\FilamentAnalitik\Widgets\VisitorsCountryChart::class);
        });
    }
}
