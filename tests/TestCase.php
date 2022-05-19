<?php

namespace Worksome\RequestFactories\Tests;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use Worksome\RequestFactories\RequestFactories;
use Worksome\RequestFactories\RequestFactoriesServiceProvider;
use Worksome\RequestFactories\Tests\Doubles\Controllers\ExampleController;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        RequestFactories::location(
            __DIR__ . '/tmp/tests/RequestFactories',
            'Worksome\\RequestFactories\\Tests\\Doubles\\Factories',
        );

        /**
         * Let's register some fake routes for our tests to make use of when
         * testing auto request injection.
         */
        Route::post('/example', ExampleController::class);
        Route::post('/example-2', [ExampleController::class, 'store']);
    }

    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            RequestFactoriesServiceProvider::class,
        ];
    }
}
