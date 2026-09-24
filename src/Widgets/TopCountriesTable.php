<?php

namespace Kholil\FilamentAnalitik\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Kholil\FilamentAnalitik\Models\PageView;

class TopCountriesTable extends TableWidget
{
    protected int | string | array $columnSpan = 1;

    protected static ?string $heading = 'Top Countries by Visits';

    public function getTableQuery(): Builder
    {
        $tableName = (new PageView)->getTable();

        $query = PageView::query()
            ->fromSub(function ($q) use ($tableName) {
                $q->from($tableName)
                    ->select('country', DB::raw('MAX(id) as id'), DB::raw('count(*) as total_visits'))
                    ->whereNotNull('country');

                $projectId = config('filament-analitik.project_id');
                if (filled($projectId)) {
                    $q->where('project_id', $projectId);
                }

                $q->groupBy('country');
            }, $tableName);

        return $query;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('country')
                    ->label('Country')
                    ->searchable(),
                TextColumn::make('total_visits')
                    ->label('Total Visits')
                    ->sortable(),
            ])
            ->defaultSort('total_visits', 'desc');
    }
}
