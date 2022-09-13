<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Tests\Doubles\Factories;

use Worksome\RequestFactories\RequestFactory;
use Worksome\RequestFactories\Tests\Doubles\Factories\Models\UserFactory;

class ModelFactoryRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'model' => UserFactory::new(),
            'nested' => [
                'model' => UserFactory::new(),
            ],
        ];
    }
}
