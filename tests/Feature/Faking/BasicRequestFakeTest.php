<?php

use App\Http\Requests\ExampleFormRequest;
use Worksome\RequestFactories\Tests\Doubles\Factories\AddressFormRequestFactory;

use function Pest\Laravel\post;

it('can fake data in a basic request', function () {
    // There is no matching request for this factory.
    AddressFormRequestFactory::new()->fake();

    post('/example-2')->assertJsonStructure(['line_one', 'line_two', 'city', 'country']);
});

it('can fake a series of requests', function () {
    ExampleFormRequest::fake();
    post('/example')->assertJsonStructure(['name', 'email']);

    AddressFormRequestFactory::new()->fake();
    post('/example-2')->assertJsonStructure(['line_one', 'line_two']);
});
