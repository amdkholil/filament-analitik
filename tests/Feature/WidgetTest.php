<?php

use Kholil\FilamentAnalitik\Models\PageView;
use Kholil\FilamentAnalitik\Widgets\AnalitikStatsOverview;
use Kholil\FilamentAnalitik\Widgets\PageViewsChart;
use Livewire\Livewire;

it('can render analitik stats overview widget', function () {
    PageView::create([
        'url' => 'http://localhost/test',
        'path' => 'test',
        'method' => 'GET',
        'ip' => '127.0.0.1',
    ]);

    Livewire::test(AnalitikStatsOverview::class)
        ->assertSee('Total Views')
        ->assertSee('1')
        ->assertSee('Unique Visitors')
        ->assertSee('Views Today');
});

it('can render page views chart widget', function () {
    PageView::create([
        'url' => 'http://localhost/test',
        'path' => 'test',
        'method' => 'GET',
        'ip' => '127.0.0.1',
        'created_at' => now(),
    ]);

    Livewire::test(PageViewsChart::class)
        ->assertSee('Page Views');
});
