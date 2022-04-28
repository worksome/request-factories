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

it('can resolve nested form request factories', function () {
    $data = ExampleFormRequestFactory::new()->state([
        'secret_identity' => ExampleFormRequestFactory::new()->state([
            'super_secret_identity' => ExampleFormRequestFactory::new()
        ])
    ])->create();

    expect($data)
        ->toHaveKey('secret_identity')
        ->secret_identity->toHaveKey('super_secret_identity')
        ->secret_identity->super_secret_identity->toHaveKeys(['email', 'name']);
});

it('can resolve property closures, and passes those closures all other parameters', function () {
    $data = ExampleFormRequestFactory::new()->state([
        'name' => 'Luke Downing',
        'description' => fn (array $attributes) => "Hello, my name is {$attributes['name']}"
    ])->create();

    expect($data['description'])->toBe('Hello, my name is Luke Downing');
});

it('allows adding custom functionality in an afterCreating hook', function () {
    $data = ExampleFormRequestFactory::new()->afterCreating(function (array $attributes) {
        return array_merge($attributes, ['foo' => 'bar']);
    })->create();

    expect($data)
        ->toHaveKeys(['name', 'email', 'foo'])
        ->foo->toBe('bar');
});

it('allows the user to configure the factory', function () {
    /**
     * You would usually configure a factory internally using the `::configure` method,
     * but we want a little more control in tests, so we make use of the test helper
     * on ExampleFormRequestFactory to override the functionality.
     */
    ExampleFormRequestFactory::configureUsing(function (ExampleFormRequestFactory $factory) {
        return $factory->afterCreating(fn () => ['foo' => 'bar']);
    });

    expect(ExampleFormRequestFactory::new()->create())->toBe(['foo' => 'bar']);
});
