<?php

namespace Worksome\RequestFactories\Concerns;

use Closure;
use Worksome\RequestFactories\FactoryManager;
use Worksome\RequestFactories\RequestFactory;
use Worksome\RequestFactories\Support\Map;

trait HasFactory
{
    /**
     * Fake this Form Request using the related factory.
     *
     * @param array<mixed>|Closure(RequestFactory): RequestFactory $attributes
     */
    public static function fake(array|Closure $attributes = []): void
    {
        $factory = static::factory();

        $factory = $attributes instanceof Closure
            ? $attributes($factory)
            : $factory->state($attributes);

        app(FactoryManager::class)->fake(static::class, $factory);
    }

    /**
     * Retrieve the related factory instance for this Form Request.
     */
    public static function factory(): RequestFactory
    {
        return app(Map::class)->formRequestToFactory(static::class)::new();
    }
}
