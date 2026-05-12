<?php

namespace Kholil\FilamentAnalitik\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Kholil\FilamentAnalitik\Models\PageView;
use Illuminate\Support\Facades\DB;

class TopPagesTable extends BaseWidget
{
    protected static ?string $heading = 'Top 10 Visited Pages';

    protected int | string | array $columnSpan = 'full';

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

    public function table(Table $table): Table
    {
        $activeFilter = $this->filter;

        return $table
            ->query(
                PageView::query()
                    ->select('path', DB::raw('MAX(id) as id'), DB::raw('count(*) as views_count'), DB::raw('count(distinct ip) as unique_visitors'))
                    ->when($activeFilter === '1', fn($q) => $q->where('created_at', '>=', now()->subDay()))
                    ->when($activeFilter !== '1', fn($q) => $q->where('created_at', '>=', now()->subDays((int)$activeFilter)))
                    ->groupBy('path')
                    ->orderBy('views_count', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('path')
                    ->label('Page Path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unique_visitors')
                    ->label('Unique Visitors')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
