<?php

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Route;
use Kholil\FilamentAnalitik\Http\Middleware\TrackPageView;
use Kholil\FilamentAnalitik\Jobs\TrackPageViewJob;
use Kholil\FilamentAnalitik\Models\PageView;

it('dispatches tracking job on page view', function () {
    Bus::fake();

    Route::get('/test-page', function () {
        return response('OK', 200);
    })->middleware(TrackPageView::class);

    $this->get('/test-page')
        ->assertOk();

    Bus::assertDispatched(TrackPageViewJob::class, function ($job) {
        return $job->data['path'] === 'test-page';
    });
});

it('does not dispatch job on failed requests', function () {
    Bus::fake();

    Route::get('/error-page', function () {
        return response('Error', 500);
    })->middleware(TrackPageView::class);

    $this->get('/error-page')
        ->assertStatus(500);

    Bus::assertNotDispatched(TrackPageViewJob::class);
});

it('does not dispatch job on non-GET requests', function () {
    Bus::fake();

    Route::post('/post-page', function () {
        return response('OK', 200);
    })->middleware(TrackPageView::class);

    $this->post('/post-page')
        ->assertOk();

    Bus::assertNotDispatched(TrackPageViewJob::class);
});

it('can be disabled via config', function () {
    Bus::fake();
    config(['filament-analitik.enabled' => false]);

    Route::get('/disabled-page', function () {
        return response('OK', 200);
    })->middleware(TrackPageView::class);

    $this->get('/disabled-page')
        ->assertOk();

    Bus::assertNotDispatched(TrackPageViewJob::class);
});

it('does not track filament panel pages', function () {
    Bus::fake();
    Route::get('/admin-test', function () {
        return response('OK', 200);
    })->middleware(TrackPageView::class);
    
    \Filament\Facades\Filament::shouldReceive('getCurrentPanel')
        ->andReturn(new \Filament\Panel());
    
    $this->get('/admin-test')
        ->assertOk();

    Bus::assertNotDispatched(TrackPageViewJob::class);
});

it('can execute track page view job and resolve location', function () {
    $position = new \Stevebauman\Location\Position();
    $position->cityName = 'Jakarta';
    $position->regionName = 'DKI Jakarta';
    $position->countryName = 'Indonesia';

    \Stevebauman\Location\Facades\Location::shouldReceive('get')
        ->once()
        ->with('8.8.8.8')
        ->andReturn($position);

    $job = new TrackPageViewJob([
        'url' => 'http://localhost/test',
        'path' => 'test',
        'method' => 'GET',
        'ip' => '8.8.8.8',
        'user_agent' => 'Mozilla/5.0',
        'project_id' => 'project-123',
    ]);

    $job->handle();

    $this->assertDatabaseHas('filament_page_views', [
        'url' => 'http://localhost/test',
        'path' => 'test',
        'method' => 'GET',
        'ip' => '8.8.8.8',
        'user_agent' => 'Mozilla/5.0',
        'city' => 'Jakarta',
        'state' => 'DKI Jakarta',
        'country' => 'Indonesia',
        'project_id' => 'project-123',
    ]);
});

it('handles unresolvable location gracefully', function () {
    \Stevebauman\Location\Facades\Location::shouldReceive('get')
        ->once()
        ->with('8.8.8.8')
        ->andReturn(false);

    $job = new TrackPageViewJob([
        'url' => 'http://localhost/test',
        'path' => 'test',
        'method' => 'GET',
        'ip' => '8.8.8.8',
        'user_agent' => 'Mozilla/5.0',
        'project_id' => null,
    ]);

    $job->handle();

    $this->assertDatabaseHas('filament_page_views', [
        'url' => 'http://localhost/test',
        'ip' => '8.8.8.8',
        'city' => null,
        'state' => null,
        'country' => null,
    ]);
});

