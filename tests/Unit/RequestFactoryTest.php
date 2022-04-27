<?php

declare(strict_types=1);

use Worksome\RequestFactories\Tests\Doubles\Factories\ExampleFormRequestFactory;

it('can generate an array of data', function () {
    $createdData = ExampleFormRequestFactory::new()->create();

    expect($createdData)->toHaveKeys([
        'email',
        'name',
        'address.line_one',
        'address.line_two',
        'address.city',
        'address.country',
        'address.postcode',
    ]);
});

it('can provide attributes when instantiating', function () {
    $name =
    $data = ExampleFormRequestFactory::new(['name' => 'Luke Downing'])->create();

    expect($data['name'])->toBe('Luke Downing');
});

it('can provide attributes when creating', function () {
    $data = ExampleFormRequestFactory::new()->create(['name' => 'Luke Downing']);

    expect($data['name'])->toBe('Luke Downing');
});

it('can alter attributes with the state method', function () {
    $data = ExampleFormRequestFactory::new()->state([
        'name' => 'Luke Downing',
        'email' => 'luke@downing.tech',
    ])->create();

    expect($data)
        ->name->toBe('Luke Downing')
        ->email->toBe('luke@downing.tech');
});
