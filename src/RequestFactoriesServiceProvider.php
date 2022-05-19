<?php

declare(strict_types=1);

namespace Worksome\RequestFactories;

use Illuminate\Foundation\Http\Kernel;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Worksome\RequestFactories\Commands\MakeCommand;
use Worksome\RequestFactories\Contracts\Finder as FinderContract;
use Worksome\RequestFactories\Middleware\InjectFakeDataMiddleware;
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
        $this->app->resolving(Kernel::class, function (Kernel $kernel) {
            $kernel->pushMiddleware(InjectFakeDataMiddleware::class);
        });
    }
}
