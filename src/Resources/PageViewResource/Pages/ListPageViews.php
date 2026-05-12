<?php

namespace Kholil\FilamentAnalitik\Resources\PageViewResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Kholil\FilamentAnalitik\Resources\PageViewResource;

class ListPageViews extends ListRecords
{
    protected static string $resource = PageViewResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \Kholil\FilamentAnalitik\Widgets\AnalitikStatsOverview::class,
            \Kholil\FilamentAnalitik\Widgets\PageViewsChart::class,
        ];
    }
}
