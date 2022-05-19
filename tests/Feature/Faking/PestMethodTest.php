<?php

use App\Http\Requests\ExampleFormRequest;
use Worksome\RequestFactories\Tests\Doubles\Factories\ExampleFormRequestFactory;

use function Pest\Laravel\post;

it('includes an autoloaded fakeRequest helper for Pest', function () {
    post('/example')->assertJsonStructure(['email', 'name', 'address']);
})->fakeRequest(ExampleFormRequest::class);

it('can pass a RequestFactory to fakeRequest', function () {
    post('/example')->assertJsonStructure(['email', 'name', 'address']);
})->fakeRequest(ExampleFormRequestFactory::class);

it('can provide an array of attributes to fakeRequest', function () {
    post('/example')->assertJson(['email' => 'luke@worksome.com']);
})->fakeRequest(ExampleFormRequest::class, ['email' => 'luke@worksome.com']);

it('can pass a RequestFactory to the Pest fakeRequest helper via a Closure', function () {
    post('/example')->assertJson(['foo' => 'bar']);
})->fakeRequest(fn() => ExampleFormRequest::factory()->state(['foo' => 'bar']));

it('can chain RequestFactory methods onto the fakeRequest helper', function () {
    post('/example')->assertJson([
        'foo' => 'bar',
        'profession' => 'Clown'
    ]);
})
    ->skip(false) // Note that we can call Pest methods before...
    ->fakeRequest(ExampleFormRequest::class)
    ->state(['foo' => 'bar'])
    ->withProfession('Clown')
    ->group('feature'); // ...or after the RequestFactory chain.
