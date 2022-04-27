<?php

namespace Worksome\RequestFactories\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Worksome\RequestFactories\Contracts\Finder as FinderContract;
use Worksome\RequestFactories\RequestFactoriesServiceProvider;
use Worksome\RequestFactories\Support\Finder;
use Worksome\RequestFactories\Tests\Doubles\TestFinder;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->singleton(FinderContract::class, fn () => new TestFinder(new Finder()));
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
