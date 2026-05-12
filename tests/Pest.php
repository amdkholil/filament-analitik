<?php

use Kholil\FilamentAnalitik\Tests\TestCase;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\MessageBag;

uses(TestCase::class)
    ->beforeEach(function () {
        view()->share('errors', (new ViewErrorBag)->put('default', new MessageBag));
    })
    ->in(__DIR__);
