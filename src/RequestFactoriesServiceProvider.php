<?php

declare(strict_types=1);

namespace Worksome\RequestFactories;

use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\ServiceProvider;
use Worksome\RequestFactories\Actions\CreateFactoryResult;
use Worksome\RequestFactories\Actions\MergeFactoryIntoRequest;
use Worksome\RequestFactories\Commands\MakeCommand;
use Worksome\RequestFactories\Contracts\Actions\CreatesFactoryResult;
use Worksome\RequestFactories\Contracts\Actions\MergesFactoryIntoRequest;
use Worksome\RequestFactories\Contracts\Finder as FinderContract;
use Worksome\RequestFactories\Support\Finder;

final class RequestFactoriesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CreatesFactoryResult::class, CreateFactoryResult::class);
        $this->app->bind(MergesFactoryIntoRequest::class, MergeFactoryIntoRequest::class);
        $this->app->singleton(FinderContract::class, Finder::class);
        $this->app->singleton(FactoryManager::class);
    }

    public function boot(): void
    {
        $this->commands(MakeCommand::class);

        if (! $this->app->runningUnitTests()) {
            return;
        }

        // @phpstan-ignore-next-line
        $this->app['events']->listen(RouteMatched::class, Listeners\MergeFactoryIntoRequest::class);
    }
}
