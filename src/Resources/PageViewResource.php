<?php

namespace Kholil\FilamentAnalitik\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Kholil\FilamentAnalitik\FilamentAnalitikPlugin;
use Kholil\FilamentAnalitik\Models\PageView;
use Kholil\FilamentAnalitik\Resources\PageViewResource\Pages;

class PageViewResource extends Resource
{
    protected static ?string $model = PageView::class;

    protected static ?string $breadcrumb = 'Analitik Logs';

    public static function getModelLabel(): string
    {
        return 'Analitik Log';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Analitik Logs';
    }

    public static function getNavigationLabel(): string
    {
        return FilamentAnalitikPlugin::get()->getNavigationLabel() . ' Logs';
    }

    public static function getNavigationIcon(): ?string
    {
        return FilamentAnalitikPlugin::get()->getNavigationIcon();
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentAnalitikPlugin::get()->getNavigationGroup();
    }

    public static function canAccess(): bool
    {
        return FilamentAnalitikPlugin::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('path')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('state')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project_id')
                    ->getStateUsing(fn($record): ?string => data_get($record, 'project_id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                    ->options(fn(): array => PageView::query()
                        ->whereNotNull('project_id')
                        ->where('project_id', '!=', '')
                        ->distinct()
                        ->pluck('project_id', 'project_id')
                        ->filter(fn($value) => filled($value))
                        ->values()
                        ->toArray())
                    ->label('Project'),
            ])
            ->actions([])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPageViews::route('/'),
        ];
    }
}
