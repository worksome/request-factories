<?php

namespace Worksome\RequestFactories;

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
    }
}
