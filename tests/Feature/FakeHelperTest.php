<?php

use App\Http\Requests\ExampleFormRequest;
use Illuminate\Http\UploadedFile;
use Worksome\RequestFactories\Tests\Doubles\Factories\ExampleFormRequestFactory;

use function Pest\Laravel\post;

it('can merge in data to the correct request', function () {
    ExampleFormRequest::fake();

    post('/example')->assertJsonStructure(['email', 'name', 'address']);
});

it('will not override input that has been set manually', function () {
    ExampleFormRequest::fake();

    post('/example', [
        'email' => 'foo@bar.com'
    ])
        ->assertJsonStructure(['email', 'name', 'address'])
        ->assertJson(['email' => 'foo@bar.com']);
});

it('can provide an array of attributes to fake', function () {
    ExampleFormRequest::fake(['email' => 'foo@bar.com', 'name' => 'Luke Downing', 'title' => 'Mr.']);

    /**
     * Note that data passed into the request ALWAYS takes precedence.
     * So here, even though our factory defines 'Luke' as the name,
     * 'Oliver' will be used instead.
     */
    post('/example', ['name' => 'Oliver Nybroe'])
        ->assertJsonStructure(['email', 'name', 'address', 'title'])
        ->assertJson([
            'email' => 'foo@bar.com',
            'name' => 'Oliver Nybroe',
            'title' => 'Mr.',
        ]);
});

it('can provide a closure when faking that allows for state transformations on the factory', function () {
    ExampleFormRequest::fake(fn (ExampleFormRequestFactory $factory) => $factory->state(['framework' => 'Laravel']));

    post('/example')->assertJson(['framework' => 'Laravel']);
});

it('can include fake files', function () {
    ExampleFormRequest::fake(fn (ExampleFormRequestFactory $factory) => $factory->state([
        'profile_picture' => UploadedFile::fake()->image('luke', 20, 20),
    ]));

    post('/example')->assertJson(['files' => ['profile_picture']]);
});

it('includes an autoloaded fakeRequest helper for Pest', function () {
    post('/example')->assertJsonStructure(['email', 'name', 'address']);
})->fakeRequest(ExampleFormRequest::class);

it('can provide an array of attributes to fakeRequest', function () {
    post('/example')->assertJson(['email' => 'luke@worksome.com']);
})->fakeRequest(ExampleFormRequest::class, ['email' => 'luke@worksome.com']);

it('can provide a closure when faking using fakeRequest that allows for state transformations on the factory', function () {
    post('/example')->assertJson(['framework' => 'Laravel']);
})->fakeRequest(ExampleFormRequest::class, fn (ExampleFormRequestFactory $factory) => $factory->state(['framework' => 'Laravel']));

it('can register a factory using the `fake` method on the factory itself', function () {
    ExampleFormRequest::factory()->state(['foo' => 'bar'])->fake();

    post('/example')->assertJson(['foo' => 'bar']);
});
