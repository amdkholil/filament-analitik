<?php

namespace Kholil\FilamentAnalitik;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentAnalitikPlugin implements Plugin
{
    protected ?string $navigationLabel = null;
    protected ?string $navigationIcon = null;
    protected ?string $navigationGroup = null;
    protected ?string $projectId = null;
    protected static ?\Closure $canAccessCallback = null;

    public function canAccessUsing(?\Closure $callback): static
    {
        static::$canAccessCallback = $callback;
        return $this;
    }

    public static function canAccess(): bool
    {
        if (static::$canAccessCallback) {
            return app()->call(static::$canAccessCallback);
        }

        $user = auth()->user();

        if (! $user) {
            return false;
        }

        // 1. Check custom gate/permission if configured
        $gate = config('filament-analitik.access.gate');
        if ($gate && ! $user->can($gate)) {
            return false;
        }

        // 2. Check roles if configured
        $roles = config('filament-analitik.access.roles');
        if ($roles) {
            $roles = (array) $roles;
            
            if (method_exists($user, 'hasAnyRole')) {
                if (! $user->hasAnyRole($roles)) {
                    return false;
                }
            } elseif (method_exists($user, 'hasRole')) {
                if (! collect($roles)->contains(fn ($role) => $user->hasRole($role))) {
                    return false;
                }
            } else {
                if (! isset($user->role) || ! in_array($user->role, $roles)) {
                    return false;
                }
            }
        }

        return true;
    }

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

    public function navigationLabel(?string $label): static
    {
        $this->navigationLabel = $label;
        return $this;
    }

    public function navigationIcon(?string $icon): static
    {
        $this->navigationIcon = $icon;
        return $this;
    }

    public function navigationGroup(?string $group): static
    {
        $this->navigationGroup = $group;
        return $this;
    }

    public function getNavigationLabel(): string
    {
        return $this->navigationLabel ?? config('filament-analitik.navigation.label', 'Analitik');
    }

    public function getNavigationIcon(): string
    {
        return $this->navigationIcon ?? config('filament-analitik.navigation.icon', 'heroicon-o-chart-bar');
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup ?? config('filament-analitik.navigation.group', null);
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
