<?php

namespace Kholil\FilamentAnalitik\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Kholil\FilamentAnalitik\Models\PageView;

class PageViewsChart extends ChartWidget
{
    protected ?string $heading = 'Page Views';

    protected int | string | array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

    public ?string $filter = '7';

    protected function getFilters(): ?array
    {
        return [
            '1' => 'Last 24 Hours',
            '7' => 'Last 7 Days',
            '14' => 'Last 14 Days',
            '30' => 'Last 30 Days',
            '90' => 'Last 90 Days',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->resolveFilter();
        $driver = DB::getDriverName();
        $isSqlite = $driver === 'sqlite';
        $isPostgres = $driver === 'pgsql';

        $labelKeys = [];
        $displayLabels = [];

        if ($activeFilter === '1') {
            for ($i = 23; $i >= 0; $i--) {
                $hour = now()->subHours($i)->startOfHour();
                $labelKeys[] = $hour->format('Y-m-d H:00');
                $displayLabels[] = $hour->format('H:00');
            }

            if ($isSqlite) {
                $format = "strftime('%Y-%m-%d %H:00', created_at)";
            } elseif ($isPostgres) {
                $format = "to_char(created_at, 'YYYY-MM-DD HH24:00')";
            } else {
                $format = "DATE_FORMAT(created_at, '%Y-%m-%d %H:00')";
            }

            $start = now()->subHours(23)->startOfHour();
        } else {
            for ($i = (int) $activeFilter - 1; $i >= 0; $i--) {
                $day = now()->subDays($i);
                $labelKeys[] = $day->format('Y-m-d');
                $displayLabels[] = $day->format('Y-m-d');
            }

            if ($isSqlite) {
                $format = "date(created_at)";
            } elseif ($isPostgres) {
                $format = "created_at::date";
            } else {
                $format = "DATE(created_at)";
            }

            $start = now()->subDays((int) $activeFilter)->startOfDay();
        }

        $query = $this->baseQuery()->select(
            DB::raw("{$format} as label"),
            DB::raw('count(*) as count')
        );

        $results = $query->where('created_at', '>=', $start)
            ->groupBy('label')
            ->orderBy('label', 'asc')
            ->get()
            ->pluck('count', 'label')
            ->toArray();

        $data = array_map(fn ($key) => (int) ($results[$key] ?? 0), $labelKeys);

        return [
            'datasets' => [
                [
                    'label' => 'Page Views',
                    'data' => $data,
                    'fill' => 'start',
                    'tension' => 0.4,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $displayLabels,
        ];
    }

    protected function baseQuery(): Builder
    {
        $query = PageView::query();
        $projectId = config('filament-analitik.project_id');

        if (filled($projectId)) {
            $query->where('project_id', $projectId);
        }

        return $query;
    }

    protected function resolveFilter(): string
    {
        $filters = $this->getFilters() ?? [];
        $key = (string) $this->filter;

        return array_key_exists($key, $filters) ? $key : '7';
    }

    protected function getType(): string
    {
        return 'line';
    }
}
