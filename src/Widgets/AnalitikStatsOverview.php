<?php

namespace Kholil\FilamentAnalitik\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Kholil\FilamentAnalitik\Models\PageView;

class AnalitikStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Views Today', PageView::whereDate('created_at', now())->count())
                ->description('Views in the last 24 hours')
                ->chart([10, 10, 10, 10, 10, 10, 10])
                ->color('info'),
            Stat::make('Unique Visitors', PageView::distinct('ip')->count())
                ->description('Total unique IP addresses')
                ->chart([15, 4, 10, 2, 12, 4, 12])
                ->color('warning'),
            Stat::make('Total Views', PageView::count())
                ->description('All time page views')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }
}
