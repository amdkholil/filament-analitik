<?php

use Kholil\FilamentAnalitik\Tests\TestCase;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\MessageBag;

uses(TestCase::class)
    ->beforeEach(function () {
        $errors = new ViewErrorBag;
        $errors->put('default', new MessageBag);
        view()->share('errors', $errors);
    })
    ->in(__DIR__);
