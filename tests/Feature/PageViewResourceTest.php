<?php

use Kholil\FilamentAnalitik\Models\PageView;
use Kholil\FilamentAnalitik\Resources\PageViewResource;
use Kholil\FilamentAnalitik\Resources\PageViewResource\Pages\ListPageViews;
use Filament\Tables\Table;

it('filters out null and empty project ids in page view resource options', function () {
    // Create test records
    PageView::create([
        'url' => 'https://example.com',
        'path' => '/',
        'method' => 'GET',
        'project_id' => 'project-1',
    ]);

    PageView::create([
        'url' => 'https://example.com/about',
        'path' => '/about',
        'method' => 'GET',
        'project_id' => null,
    ]);

    PageView::create([
        'url' => 'https://example.com/contact',
        'path' => '/contact',
        'method' => 'GET',
        'project_id' => '',
    ]);

    PageView::create([
        'url' => 'https://example.com/blog',
        'path' => '/blog',
        'method' => 'GET',
        'project_id' => 'project-2',
    ]);

    // Instantiate ListPageViews Livewire component via app container
    $livewire = app(ListPageViews::class);

    // Instantiate resource table
    $table = PageViewResource::table(Table::make($livewire));

    // Get the project_id filter
    $filter = $table->getFilters()['project_id'] ?? null;

    expect($filter)->not->toBeNull();

    // Retrieve the options
    $options = $filter->getOptions();

    // Verify only 'project-1' and 'project-2' are present
    expect($options)->toBe([
        'project-1' => 'project-1',
        'project-2' => 'project-2',
    ]);
});
