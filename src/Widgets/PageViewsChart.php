<?php

namespace Kholil\FilamentAnalitik\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Kholil\FilamentAnalitik\Models\PageView;

class PageViewsChart extends ChartWidget
{
    protected ?string $heading = 'Page Views';

    protected int | string | array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

    public ?string $filter = '1';

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
        $activeFilter = $this->filter;
        $isSqlite = DB::getDriverName() === 'sqlite';
        
        // Generate potential labels to fill gaps
        $labels = [];
        if ($activeFilter === '1') {
            for ($i = 23; $i >= 0; $i--) {
                $labels[] = now()->subHours($i)->format('H:00');
            }
        } else {
            for ($i = (int)$activeFilter - 1; $i >= 0; $i--) {
                $labels[] = now()->subDays($i)->format('Y-m-d');
            }
        }

        $format = $activeFilter === '1' 
            ? ($isSqlite ? "strftime('%H:00', created_at)" : "DATE_FORMAT(created_at, '%H:00')")
            : ($isSqlite ? "date(created_at)" : "DATE(created_at)");

        $query = PageView::select(
            DB::raw("{$format} as label"),
            DB::raw('count(*) as count')
        );

        if ($activeFilter === '1') {
            $query->where('created_at', '>=', now()->subDay());
        } else {
            $query->where('created_at', '>=', now()->subDays((int)$activeFilter));
        }

        $results = $query->groupBy('label')
            ->orderBy('label', 'asc')
            ->get()
            ->pluck('count', 'label')
            ->toArray();

        // Map results to labels, filling gaps with 0
        $data = array_map(fn($label) => $results[$label] ?? 0, $labels);

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
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
