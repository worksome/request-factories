<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Tests\Doubles\Factories;

use Worksome\RequestFactories\RequestFactory;

final class AddressFormRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'line_one' => $this->faker()->streetAddress(),
            'line_two' => $this->faker()->address(),
            'city' => $this->faker()->city(),
            'country' => $this->faker()->country(),
        ];
    }

    public function withPostCode(): self
    {
        return $this->state(['postcode' => $this->faker->postcode()]);
    }
}
