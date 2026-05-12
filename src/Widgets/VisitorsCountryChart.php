<?php

namespace Kholil\FilamentAnalitik\Widgets;

use Filament\Widgets\ChartWidget;
use Kholil\FilamentAnalitik\Models\PageView;

class VisitorsCountryChart extends ChartWidget
{
    protected ?string $heading = 'Visitors by Country';

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
        $activeFilter = $this->filter;

        $query = PageView::select('country')
            ->selectRaw('count(*) as count')
            ->whereNotNull('country')
            ->when($activeFilter === '1', fn($q) => $q->where('created_at', '>=', now()->subDay()))
            ->when($activeFilter !== '1', fn($q) => $q->where('created_at', '>=', now()->subDays((int)$activeFilter)))
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->limit(5);

        $data = $query->get();

        return [
            'datasets' => [
                [
                    'label' => 'Visitors',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                    ],
                ],
            ],
            'labels' => $data->pluck('country')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
