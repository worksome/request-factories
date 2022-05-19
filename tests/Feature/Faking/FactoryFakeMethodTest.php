<?php

use Worksome\RequestFactories\Tests\Doubles\Factories\ExampleFormRequestFactory;

use function Pest\Laravel\post;

it('can register a factory using the `fake` method on the factory itself', function () {
    ExampleFormRequestFactory::new()->state(['foo' => 'bar'])->fake();

    post('/example')->assertJson(['foo' => 'bar']);
});
