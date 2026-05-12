<?php

namespace Kholil\FilamentAnalitik\Pages;

use Filament\Pages\Page;
use Kholil\FilamentAnalitik\FilamentAnalitikPlugin;
use Kholil\FilamentAnalitik\Widgets\AnalitikStatsOverview;
use Kholil\FilamentAnalitik\Widgets\PageViewsChart;
use Kholil\FilamentAnalitik\Widgets\TopPagesTable;
use Kholil\FilamentAnalitik\Widgets\TopCountriesTable;
use Kholil\FilamentAnalitik\Widgets\VisitorsCountryChart;

class AnalyticsDashboard extends Page
{
    protected string $view = 'filament-analitik::filament.pages.analytics-dashboard';

    public static function getNavigationLabel(): string
    {
        return FilamentAnalitikPlugin::get()->getNavigationLabel();
    }

    public static function getNavigationIcon(): ?string
    {
        return FilamentAnalitikPlugin::get()->getNavigationIcon();
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentAnalitikPlugin::get()->getNavigationGroup();
    }

    public function getTitle(): string
    {
        return FilamentAnalitikPlugin::get()->getNavigationLabel();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AnalitikStatsOverview::class,
            PageViewsChart::class,
            TopPagesTable::class,
            TopCountriesTable::class,
            VisitorsCountryChart::class,
        ];
    }
}
