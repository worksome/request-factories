<?php

declare(strict_types=1);

use Faker\Generator;
use Worksome\RequestFactories\Tests\Doubles\Factories\ExampleFormRequestFactory;

it('uses the laravel faker instance', function () {
    $testGenerator = new class() extends Generator {
    };
    $this->app->instance(Generator::class, $testGenerator);

    expect(ExampleFormRequestFactory::new()->faker())->toBe($testGenerator);
});
