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
    protected ?\Closure $canAccessCallback = null;

    public function canAccessUsing(?\Closure $callback): static
    {
        $this->canAccessCallback = $callback;
        return $this;
    }

    public function checkAccess(): bool
    {
        if ($this->canAccessCallback !== null) {
            return (bool) app()->call($this->canAccessCallback);
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

    public static function canAccess(): bool
    {
        return static::get()->checkAccess();
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
        // Prefer instance value for this panel; keep config in sync for middleware writes.
        if ($this->projectId !== null) {
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

    public function getNavigationIcon(): ?string
    {
        // Filament forbids icons on both a navigation group and its items.
        // When the configured group already defines an icon, suppress the item icon.
        if ($this->navigationGroupHasIcon()) {
            return null;
        }

        return $this->navigationIcon ?? config('filament-analitik.navigation.icon', 'heroicon-o-chart-bar');
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup ?? config('filament-analitik.navigation.group', null);
    }

    protected function navigationGroupHasIcon(): bool
    {
        $groupLabel = $this->getNavigationGroup();

        if (blank($groupLabel)) {
            return false;
        }

        try {
            $panel = filament()->getCurrentPanel() ?? filament()->getDefaultPanel();
        } catch (\Throwable) {
            return false;
        }

        foreach ($panel->getNavigationGroups() as $group) {
            if (
                $group instanceof \Filament\Navigation\NavigationGroup
                && $group->getLabel() === $groupLabel
                && filled($group->getIcon())
            ) {
                return true;
            }
        }

        return false;
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
