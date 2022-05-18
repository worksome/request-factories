<?php

namespace Worksome\RequestFactories\Concerns;

use Closure;
use Illuminate\Container\Container;
use Worksome\RequestFactories\Contracts\Finder;
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

        $factory->fake();
    }

    /**
     * Retrieve the related factory instance for this Form Request.
     */
    public static function factory(): RequestFactory
    {
        /**
         * We may be instantiating a factory using the Pest `fakeRequest`
         * helper. That being the case, we support calling `factory`
         * outside the Laravel lifecycle.
         */
        $finder = Container::getInstance()->has(Finder::class)
            ? Container::getInstance()->get(Finder::class)
            : new \Worksome\RequestFactories\Support\Finder();

        return (new Map($finder))->formRequestToFactory(static::class)::new();
    }
}
