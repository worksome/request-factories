<?php

use Worksome\RequestFactories\FactoryManager;
use Worksome\RequestFactories\Tests\Doubles\Factories\AddressFormRequestFactory;

use function Pest\Laravel\post;

it('can return a result directly from a factory without faking', function () {
    $request = AddressFormRequestFactory::new()->create();

    expect(app(FactoryManager::class)->hasFake())->toBeFalse();
    post('/example-2', $request)->assertJsonStructure(['line_one', 'line_two', 'city', 'country']);
});

it('can change state in the create method', function () {
    $request = AddressFormRequestFactory::new()->create(['line_one' => 'Foo bar']);

    post('/example-2', $request)->assertJson(['line_one' => 'Foo bar']);
});
