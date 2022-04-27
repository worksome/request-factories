<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\RequestFactory;

abstract class RequestFactory
{
    /**
     * Here you should provide all data necessary
     * to form a valid request.
     *
     * @return array<string, mixed>
     */
    abstract public function definition(): array;
}
