<?php

namespace Worksome\RequestFactories\Concerns;

use BadMethodCallException;
use Closure;
use Illuminate\Support\Str;
use Worksome\RequestFactories\Contracts\Finder;
use Worksome\RequestFactories\FactoryManager;
use Worksome\RequestFactories\RequestFactory;

trait HasFactory
{

    /**
     * Fake this Form Request using the related factory.
     *
     * @param array<mixed>|Closure(RequestFactory): RequestFactory $attributes
     */
    public static function fake(array|Closure $attributes = []): void
    {
        $factory = static::factory()::new();

        $factory = $attributes instanceof Closure
            ? $attributes($factory)
            : $factory->state($attributes);

        app(FactoryManager::class)->fake(static::class, $factory);
    }

    /**
     * Retrieve the related factory FQCN for this Form Request.
     *
     * @return class-string<RequestFactory>
     */
    public static function factory(): string
    {
        if (property_exists(static::class, 'factory')) {
            return static::$factory;
        }

        $requestPartialFQCN = Str::after(
            self::class,
            "App\\Http\\Requests\\",
        );

        $factoryNamespace = app(Finder::class)->requestFactoriesNamespace();
        $guessedRequestFQCN = $factoryNamespace . '\\' . $requestPartialFQCN . 'Factory';

        if (class_exists($guessedRequestFQCN)) {
            return $guessedRequestFQCN;
        }

        throw new BadMethodCallException("Could not find [{$guessedRequestFQCN}]. Please specify a relevant Request FQCN using the \$factory property.");
    }

}
