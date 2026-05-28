<?php

use Kholil\FilamentAnalitik\Models\PageView;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    PageView::create([
        'url' => 'http://localhost/test',
        'path' => '/test',
        'method' => 'GET',
        'ip' => '127.0.0.1',
        'country' => 'Indonesia',
        'created_at' => now(),
    ]);

    PageView::create([
        'url' => 'http://localhost/test2',
        'path' => '/test2',
        'method' => 'GET',
        'ip' => '127.0.0.1',
        'country' => 'Indonesia',
        'created_at' => now(),
    ]);
});

it('computes stats for analitik stats overview widget', function () {
    $viewsToday = PageView::whereDate('created_at', today())->count();
    $uniqueVisitors = PageView::distinct('ip')->count('ip');
    $totalViews = PageView::count();

    expect($viewsToday)->toBe(2);
    expect($uniqueVisitors)->toBe(1);
    expect($totalViews)->toBe(2);
});

it('computes chart data for page views chart widget', function () {
    $results = PageView::select(
        DB::raw("strftime('%H:00', created_at) as label"),
        DB::raw('count(*) as count')
    )
        ->where('created_at', '>=', now()->subDay())
        ->groupBy('label')
        ->orderBy('label', 'asc')
        ->get()
        ->pluck('count', 'label')
        ->toArray();

    expect($results)->not->toBeEmpty();
});

it('builds top pages table query', function () {
    $records = PageView::select(
        'path',
        DB::raw('MAX(id) as id'),
        DB::raw('count(*) as views_count'),
        DB::raw('count(distinct ip) as unique_visitors')
    )
        ->where('created_at', '>=', now()->subDays(7))
        ->groupBy('path')
        ->orderBy('views_count', 'desc')
        ->limit(10)
        ->get();

    expect($records)->toHaveCount(2);
    expect($records->first()->path)->toBe('/test');
    expect((int) $records->first()->views_count)->toBe(1);
    expect((int) $records->first()->unique_visitors)->toBe(1);
});

it('builds top countries table query', function () {
    $records = PageView::select(
        'country',
        DB::raw('MAX(id) as id'),
        DB::raw('count(*) as total_visits')
    )
        ->whereNotNull('country')
        ->groupBy('country')
        ->orderByDesc('total_visits')
        ->get();

    expect($records)->toHaveCount(1);
    expect($records->first()->country)->toBe('Indonesia');
    expect((int) $records->first()->total_visits)->toBe(2);
});
