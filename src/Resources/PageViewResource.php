<?php

namespace Kholil\FilamentAnalitik\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Kholil\FilamentAnalitik\Models\PageView;
use Kholil\FilamentAnalitik\Resources\PageViewResource\Pages;
use Kholil\FilamentAnalitik\FilamentAnalitikPlugin;

class PageViewResource extends Resource
{
    protected static ?string $model = PageView::class;

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
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                    ->options(fn () => PageView::query()->distinct()->pluck('project_id', 'project_id')->toArray())
                    ->label('Project'),
            ])
            ->actions([
                ViewAction::make(),
            ])
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
