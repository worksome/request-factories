<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Tests\Doubles\Factories;

use Worksome\RequestFactories\RequestFactory;

class NestedArrayRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'foo' => [
                'bar' => fn () => 'baz',
                'baz' => [
                    'boom' => [
                        'bang' => fn () => 'whizz',
                    ]
                ],
                'factory' => AddressFormRequestFactory::new(),
            ]
        ];
    }
}
