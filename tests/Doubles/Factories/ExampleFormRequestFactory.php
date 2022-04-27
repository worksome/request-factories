<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Tests\Doubles\Factories;

use Worksome\RequestFactories\RequestFactory;

final class ExampleFormRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker()->unique()->safeEmail,
            'name' => $this->faker()->name,
            'address' => AddressFormRequestFactory::new()->withPostCode(),
        ];
    }
}
