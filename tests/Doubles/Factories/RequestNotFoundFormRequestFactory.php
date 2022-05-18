<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Tests\Doubles\Factories;

use Worksome\RequestFactories\RequestFactory;

final class RequestNotFoundFormRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [];
    }
}
