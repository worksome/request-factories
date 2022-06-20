<?php

declare(strict_types=1);

namespace Worksome\RequestFactories;

use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\ServiceProvider;
use Worksome\RequestFactories\Actions\CreateFactoryResult;
use Worksome\RequestFactories\Actions\MergeFactoryIntoRequest;
use Worksome\RequestFactories\Actions\UndotArrayKeys;
use Worksome\RequestFactories\Commands\MakeCommand;
use Worksome\RequestFactories\Contracts\Actions\CreatesFactoryResult;
use Worksome\RequestFactories\Contracts\Actions\MergesFactoryIntoRequest;
use Worksome\RequestFactories\Contracts\Actions\UndotsArrayKeys;
use Worksome\RequestFactories\Contracts\Finder;
use Worksome\RequestFactories\Support\ConfigBasedFinder;

final class RequestFactoriesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CreatesFactoryResult::class, CreateFactoryResult::class);
        $this->app->bind(MergesFactoryIntoRequest::class, MergeFactoryIntoRequest::class);
        $this->app->bind(UndotsArrayKeys::class, UndotArrayKeys::class);
        $this->app->singleton(Finder::class, fn () => new ConfigBasedFinder(config('request-factories')));
        $this->app->singleton(FactoryManager::class);
    }

    public function boot(): void
    {
        $this->commands(MakeCommand::class);
        $this->publishes($this->filesToPublish(), 'request-factories');

        $this->mergeConfigFrom(__DIR__ . '/../config/request-factories.php', 'request-factories');

        if ($this->app->runningUnitTests()) {
            $this->app['events']->listen(RouteMatched::class, Listeners\MergeFactoryIntoRequest::class);
        }
    }

    /**
     * @return array<string, string>
     */
    private function filesToPublish(): array
    {
        return [
            __DIR__ . '/../config/request-factories.php' => config_path('request-factories.php'),
        ];
    }
}
