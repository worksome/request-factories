<?php

namespace Worksome\RequestFactories\Tests;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use Worksome\RequestFactories\Contracts\Finder as FinderContract;
use Worksome\RequestFactories\RequestFactoriesServiceProvider;
use Worksome\RequestFactories\Tests\Doubles\Controllers\ExampleController;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->singleton(FinderContract::class, fn () => finder());

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
