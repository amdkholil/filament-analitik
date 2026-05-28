<?php

use Kholil\FilamentAnalitik\FilamentAnalitikPlugin;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\User;

class TestUser extends User {
    protected $table = 'users';
    public $role = null;

    public function hasRole($role)
    {
        return $this->role === $role;
    }
}

it('allows access by default', function () {
    $user = new TestUser();
    $this->actingAs($user);

    expect(FilamentAnalitikPlugin::canAccess())->toBeTrue();
});

it('denies access if guest', function () {
    expect(FilamentAnalitikPlugin::canAccess())->toBeFalse();
});

it('checks gate authorization when configured', function () {
    config()->set('filament-analitik.access.gate', 'view-analytics-logs');

    $user = new TestUser();
    $this->actingAs($user);

    // Gate denier
    Gate::define('view-analytics-logs', fn () => false);
    expect(FilamentAnalitikPlugin::canAccess())->toBeFalse();

    // Gate allower
    Gate::define('view-analytics-logs', fn () => true);
    expect(FilamentAnalitikPlugin::canAccess())->toBeTrue();
});

it('checks role authorization when configured', function () {
    config()->set('filament-analitik.access.roles', ['admin', 'super-admin']);

    $user = new TestUser();
    $user->role = 'manager';
    $this->actingAs($user);

    expect(FilamentAnalitikPlugin::canAccess())->toBeFalse();

    $user->role = 'admin';
    expect(FilamentAnalitikPlugin::canAccess())->toBeTrue();
});

it('uses custom fluent access callback when provided', function () {
    $plugin = FilamentAnalitikPlugin::make()
        ->canAccessUsing(fn () => false);

    $user = new TestUser();
    $this->actingAs($user);

    expect(FilamentAnalitikPlugin::canAccess())->toBeFalse();

    $plugin->canAccessUsing(fn () => true);
    expect(FilamentAnalitikPlugin::canAccess())->toBeTrue();
});
