<?php

namespace Kholil\FilamentAnalitik\Widgets;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Kholil\FilamentAnalitik\Models\PageView;
use Illuminate\Support\Facades\DB;

class TopCountriesTable extends TableWidget
{
    protected int | string | array $columnSpan = 1;

    protected static ?string $heading = 'Top Countries by Visits';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => PageView::query()
                ->select('country', DB::raw('MAX(id) as id'), DB::raw('count(*) as total_visits'))
                ->whereNotNull('country')
                ->groupBy('country')
                ->orderByDesc('total_visits')
            )
            ->columns([
                TextColumn::make('country')
                    ->label('Country')
                    ->searchable(),
                TextColumn::make('total_visits')
                    ->label('Total Visits')
                    ->sortable(),
            ]);
    }
}
