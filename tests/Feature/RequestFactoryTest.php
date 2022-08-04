<?php

declare(strict_types=1);

use Faker\Generator;
use Faker\Factory;
use Worksome\RequestFactories\Tests\Doubles\Factories\ExampleFormRequestFactory;

it('uses the laravel faker instance', function () {
    $this->app->instance(Generator::class, Factory::create('en_GB'));
    $data = ExampleFormRequestFactory::new()->withFakerPhoneNumber()->create();

    expect($data['number'])->toStartWith('+44');
});
