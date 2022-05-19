<?php

declare(strict_types=1);

namespace Worksome\RequestFactories;

use Illuminate\Routing\Events\RouteMatched;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Worksome\RequestFactories\Commands\MakeCommand;
use Worksome\RequestFactories\Contracts\Finder as FinderContract;
use Worksome\RequestFactories\Support\Finder;

final class RequestFactoriesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('request-factories')
            ->hasCommand(MakeCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(FinderContract::class, Finder::class);
        $this->app->singleton(FactoryManager::class);
    }

    public function packageBooted(): void
    {
        if (! $this->app->runningUnitTests()) {
            return;
        }

        // @phpstan-ignore-next-line
        $this->app['events']->listen(RouteMatched::class, function (RouteMatched $event) {
            // @phpstan-ignore-next-line
            $this->app[FactoryManager::class]->mergeFactoryIntoRequest($event->request);
        });
    }
}
