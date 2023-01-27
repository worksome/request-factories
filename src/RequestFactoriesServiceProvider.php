<?php

declare(strict_types=1);

namespace Worksome\RequestFactories;

use Closure;
use Faker\Generator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\ServiceProvider;
use Worksome\RequestFactories\Actions\CreateFactoryResult;
use Worksome\RequestFactories\Actions\MergeFactoryIntoRequest;
use Worksome\RequestFactories\Commands\MakeCommand;
use Worksome\RequestFactories\Contracts\Actions\CreatesFactoryResult;
use Worksome\RequestFactories\Contracts\Actions\MergesFactoryIntoRequest;
use Worksome\RequestFactories\Contracts\Finder;
use Worksome\RequestFactories\Support\ConfigBasedFinder;
use Worksome\RequestFactories\Support\Map;

final class RequestFactoriesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CreatesFactoryResult::class, CreateFactoryResult::class);
        $this->app->bind(MergesFactoryIntoRequest::class, MergeFactoryIntoRequest::class);
        $this->app->singleton(Finder::class, fn () => new ConfigBasedFinder(config('request-factories')));
        $this->app->singleton(FactoryManager::class);
    }

    public function boot(): void
    {
        $this->commands(MakeCommand::class);
        $this->publishes($this->filesToPublish(), 'request-factories');

        $this->mergeConfigFrom(__DIR__ . '/../config/request-factories.php', 'request-factories');

        if ($this->app->has(Generator::class)) {
            RequestFactory::setFakerResolver(fn () => $this->app->make(Generator::class));
        }

        if ($this->app->runningUnitTests()) {
            $this->app['events']->listen(RouteMatched::class, Listeners\MergeFactoryIntoRequest::class);

            $this->declareFactoryMethodOnFormRequests();
            $this->declareFakeMethodOnFormRequests();
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

    private function declareFactoryMethodOnFormRequests(): void
    {
        if (method_exists(FormRequest::class, 'factory')) {
            return;
        }

        FormRequest::macro(
            'factory',
            static fn() => app(Map::class)->formRequestToFactory(static::class)::new()
        );
    }

    private function declareFakeMethodOnFormRequests(): void
    {
        if (method_exists(FormRequest::class, 'fake')) {
            return;
        }

        FormRequest::macro('fake', static function (array|Closure $attributes = []) {
            $factory = static::factory();

            $factory = $attributes instanceof Closure
                ? $attributes($factory)
                : $factory->state($attributes);

            $factory->fake();
        });
    }
}
