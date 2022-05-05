<?php

namespace Worksome\RequestFactories\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use Worksome\RequestFactories\Contracts\Finder as FinderContract;
use Worksome\RequestFactories\RequestFactoriesServiceProvider;
use Worksome\RequestFactories\Support\Finder;
use Worksome\RequestFactories\Tests\Doubles\Controllers\ExampleController;
use Worksome\RequestFactories\Tests\Doubles\TestFinder;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->singleton(FinderContract::class, fn () => new TestFinder(new Finder()));

        /**
         * Let's register some fake routes for our tests to make use of when
         * testing auto request injection.
         */
        Route::post('/example', ExampleController::class);
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
