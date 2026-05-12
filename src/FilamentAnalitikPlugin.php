<?php

namespace Kholil\FilamentAnalitik;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentAnalitikPlugin implements Plugin
{
    protected string $navigationLabel = 'Analitik';
    protected string $navigationIcon = 'heroicon-o-chart-bar';
    protected string $navigationGroup = 'System';
    protected ?string $projectId = null;

    public function projectId(?string $id): static
    {
        $this->projectId = $id;
        return $this;
    }

    public function getProjectId(): ?string
    {
        return $this->projectId;
    }

    public function getId(): string
    {
        return 'filament-analitik';
    }

    public function register(Panel $panel): void
    {
        if ($this->projectId) {
            config(['filament-analitik.project_id' => $this->projectId]);
        }

        $panel
            ->resources([
                \Kholil\FilamentAnalitik\Resources\PageViewResource::class,
            ])
            ->pages([
                \Kholil\FilamentAnalitik\Pages\AnalyticsDashboard::class,
            ])
            ->widgets([
                \Kholil\FilamentAnalitik\Widgets\AnalitikStatsOverview::class,
                \Kholil\FilamentAnalitik\Widgets\PageViewsChart::class,
                \Kholil\FilamentAnalitik\Widgets\VisitorsCountryChart::class,
                \Kholil\FilamentAnalitik\Widgets\TopPagesTable::class,
                \Kholil\FilamentAnalitik\Widgets\TopCountriesTable::class,
            ]);
    }

    public function navigationLabel(string $label): static
    {
        $this->navigationLabel = $label;
        return $this;
    }

    public function navigationIcon(string $icon): static
    {
        $this->navigationIcon = $icon;
        return $this;
    }

    public function navigationGroup(string $group): static
    {
        $this->navigationGroup = $group;
        return $this;
    }

    public function getNavigationLabel(): string
    {
        return $this->navigationLabel;
    }

    public function getNavigationIcon(): string
    {
        return $this->navigationIcon;
    }

    public function getNavigationGroup(): string
    {
        return $this->navigationGroup;
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament('filament-analitik');

        return $plugin;
    }
}
