<?php

use Kholil\FilamentAnalitik\FilamentAnalitikPlugin;
use Kholil\FilamentAnalitik\Resources\PageViewResource;

it('can resolve navigation config defaults', function () {
    expect(config('filament-analitik.navigation.label'))->toBe('Analitik');
    expect(config('filament-analitik.navigation.group'))->toBeNull();
    expect(config('filament-analitik.navigation.icon'))->toBe('heroicon-o-chart-bar');

    $plugin = FilamentAnalitikPlugin::make();
    
    expect($plugin->getNavigationLabel())->toBe('Analitik');
    expect($plugin->getNavigationGroup())->toBeNull();
    expect($plugin->getNavigationIcon())->toBe('heroicon-o-chart-bar');
});

it('can customize navigation dynamically via config', function () {
    config()->set('filament-analitik.navigation.label', 'Custom Analytics');
    config()->set('filament-analitik.navigation.group', 'System Metrics');
    config()->set('filament-analitik.navigation.icon', 'heroicon-o-presentation-chart-line');

    $plugin = FilamentAnalitikPlugin::make();
    
    expect($plugin->getNavigationLabel())->toBe('Custom Analytics');
    expect($plugin->getNavigationGroup())->toBe('System Metrics');
    expect($plugin->getNavigationIcon())->toBe('heroicon-o-presentation-chart-line');
});

it('can customize navigation fluently via plugin methods', function () {
    $plugin = FilamentAnalitikPlugin::make()
        ->navigationLabel('Fluent Label')
        ->navigationGroup('Fluent Group')
        ->navigationIcon('heroicon-o-academic-cap');

    expect($plugin->getNavigationLabel())->toBe('Fluent Label');
    expect($plugin->getNavigationGroup())->toBe('Fluent Group');
    expect($plugin->getNavigationIcon())->toBe('heroicon-o-academic-cap');
});
