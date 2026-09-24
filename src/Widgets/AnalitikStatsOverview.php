<?php

namespace Kholil\FilamentAnalitik\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Kholil\FilamentAnalitik\Models\PageView;

class AnalitikStatsOverview extends BaseWidget
{
    protected ?array $dailyViewsCache = null;

    protected ?array $dailyUniqueIpsCache = null;

    protected function getStats(): array
    {
        $views = $this->dailyViews();

        return [
            Stat::make('Views Today', $this->baseQuery()->whereDate('created_at', today())->count())
                ->description('Views today (calendar day)')
                ->chart($views)
                ->color('info'),
            Stat::make('Unique Visitors', $this->baseQuery()->distinct('ip')->count())
                ->description('Total unique IP addresses')
                ->chart($this->dailyUniqueIps())
                ->color('warning'),
            Stat::make('Total Views', $this->baseQuery()->count())
                ->description('All time page views')
                ->chart($views)
                ->color('success'),
        ];
    }

    protected function baseQuery()
    {
        $query = PageView::query();
        $projectId = config('filament-analitik.project_id');

        if (filled($projectId)) {
            $query->where('project_id', $projectId);
        }

        return $query;
    }

    protected function dailyViews(int $days = 7): array
    {
        if ($this->dailyViewsCache !== null) {
            return $this->dailyViewsCache;
        }

        $series = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $series[] = $this->baseQuery()->whereDate('created_at', now()->subDays($i))->count();
        }

        return $this->dailyViewsCache = $series;
    }

    protected function dailyUniqueIps(int $days = 7): array
    {
        if ($this->dailyUniqueIpsCache !== null) {
            return $this->dailyUniqueIpsCache;
        }

        $series = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $series[] = $this->baseQuery()
                ->whereDate('created_at', now()->subDays($i))
                ->distinct('ip')
                ->count();
        }

        return $this->dailyUniqueIpsCache = $series;
    }
}
