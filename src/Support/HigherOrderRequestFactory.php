<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Support;

use Pest\PendingObjects\TestCall;
use Worksome\RequestFactories\RequestFactory;

/**
 * If we're calling the `fakeRequest` helper, it would be nice to allow
 * methods and properties on the given request factory to be called
 * in a chain, whilst deferring to the test elsewhere.
 *
 * @template TValue of \Worksome\RequestFactories\RequestFactory
 *
 * @property TValue $requestFactory
 *
 * @mixin RequestFactory
 * @mixin TValue
 */
final readonly class HigherOrderRequestFactory
{
    public function __construct(private RequestFactory $requestFactory)
    {
    }

    /**
     * @param array<mixed> $arguments
     *
     * @return HigherOrderRequestFactory<TValue>|TestCall|mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (! method_exists($this->requestFactory, $name)) {
            return test()->$name(...$arguments);
        }

        $result = $this->requestFactory->$name(...$arguments);

        if (! $result instanceof RequestFactory) {
            return test();
        }

        $result->fake();
        return new self($result);
    }

    /**
     * @return mixed|TestCall
     */
    public function __get(string $name)
    {
        return test()->$name;
    }
}
