<?php

namespace Kholil\FilamentAnalitik\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Kholil\FilamentAnalitik\Models\PageView;

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

    public function getTableQuery(): Builder
    {
        $activeFilter = $this->resolveFilter();
        $tableName = (new PageView)->getTable();

        $query = PageView::query()
            ->fromSub(function ($q) use ($activeFilter, $tableName) {
                $q->from($tableName)
                    ->select(
                        'path',
                        DB::raw('MAX(id) as id'),
                        DB::raw('count(*) as views_count'),
                        DB::raw('count(distinct ip) as unique_visitors')
                    )
                    ->when($activeFilter === '1', fn ($sub) => $sub->where('created_at', '>=', now()->subDay()))
                    ->when($activeFilter !== '1', fn ($sub) => $sub->where('created_at', '>=', now()->subDays((int) $activeFilter)));

                $projectId = config('filament-analitik.project_id');
                if (filled($projectId)) {
                    $q->where('project_id', $projectId);
                }

                $q->groupBy('path');
            }, $tableName);

        return $query;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('path')
                    ->label('Page Path'),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unique_visitors')
                    ->label('Unique Visitors')
                    ->sortable(),
            ])
            ->defaultSort('views_count', 'desc')
            ->paginated(false);
    }

    protected function resolveFilter(): string
    {
        $filters = $this->getFilters() ?? [];
        $key = (string) $this->filter;

        return array_key_exists($key, $filters) ? $key : '7';
    }
}
