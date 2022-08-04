<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Worksome\RequestFactories\RequestFactory;
use Worksome\RequestFactories\Tests\Doubles\Factories\ExampleFormRequestFactory;
use Faker\Factory;

beforeEach(function () {
    RequestFactory::setFakerResolver(fn () => Factory::create());
});

it('can generate an array of data', function () {
    $createdData = creator(ExampleFormRequestFactory::new());

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
    $data = creator(ExampleFormRequestFactory::new(['name' => 'Luke Downing']));

    expect($data['name'])->toBe('Luke Downing');
});

it('can alter attributes with the state method', function () {
    $data = creator(ExampleFormRequestFactory::new()->state([
        'name' => 'Luke Downing',
        'email' => 'luke@downing.tech',
    ]));

    expect($data)
        ->name->toBe('Luke Downing')
        ->email->toBe('luke@downing.tech');
});

it('can alter nested properties using the state method and dot notation', function () {
    $factory = ExampleFormRequestFactory::new()->state(['foo.bar' => 'baz']);
    $data = creator($factory->state(['foo.bar' => 'boom']));

    expect($data['foo']['bar'])->toBe('boom');
});

it('can escape properties that should have dots in it', function () {
    $data = creator(ExampleFormRequestFactory::new()->state(['foo\.bar' => 'baz']));

    expect($data['foo.bar'])->toBe('baz');
});

it('can handle dot-notation with lists', function () {
    $data = creator(ExampleFormRequestFactory::new()->state([
        'foo.0.bar' => 'baz',
        'luke\.0.downing' => 'developer',
    ]));

    expect($data['foo'][0]['bar'])->toBe('baz');
    expect($data['luke.0']['downing'])->toBe('developer');
});

it('can resolve nested form request factories', function () {
    $data = creator(ExampleFormRequestFactory::new()->state([
        'secret_identity' => ExampleFormRequestFactory::new()->state([
            'super_secret_identity' => ExampleFormRequestFactory::new()
        ])
    ]));

    expect($data)
        ->toHaveKey('secret_identity')
        ->secret_identity->toHaveKey('super_secret_identity')
        ->secret_identity->super_secret_identity->toHaveKeys(['email', 'name']);
});

it('can resolve property closures, and passes those closures all other parameters', function () {
    $data = creator(ExampleFormRequestFactory::new()->state([
        'name' => 'Luke Downing',
        'description' => fn (array $attributes) => "Hello, my name is {$attributes['name']}"
    ]));

    expect($data['description'])->toBe('Hello, my name is Luke Downing');
});

it('allows adding custom functionality in an afterCreating hook', function () {
    $data = creator(ExampleFormRequestFactory::new()->afterCreating(function (array $attributes) {
        return array_merge($attributes, ['foo' => 'bar']);
    }));

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

    expect(creator(ExampleFormRequestFactory::new())->all())->toBe(['foo' => 'bar']);
});

it('can extract files from the request', function () {
    $data = creator(ExampleFormRequestFactory::new()->state([
        'profile_picture' => UploadedFile::fake()->image('luke.png', 120, 120),
    ]));

    // Note that 'banner_image' and 'resume' are found on the base definition.
    foreach (['profile_picture', 'banner_image', 'resume'] as $file) {
        expect($data[$file])->toBeInstanceOf(UploadedFile::class);
        expect($data->files()[$file])->toBeInstanceOf(UploadedFile::class);
    }
});

it('can return input without files', function () {
    $data = creator(ExampleFormRequestFactory::new()->state([
        'profile_picture' => UploadedFile::fake()->image('luke.png', 120, 120),
    ]));

    expect($data->input())->not->toHaveKey('profile_picture');
});

it('is iterable', function () {
    $data = creator(ExampleFormRequestFactory::new());

    expect($data)->toBeIterable();
});

it('can unset keys using dot notation', function () {
    $data = creator(ExampleFormRequestFactory::new()->without(['name', 'address.line_one']));

    expect($data)->not->toHaveKeys(['name', 'address.line_one']);
});

it('can overwrite deeply nested array data', function () {
    $data = creator(ExampleFormRequestFactory::new()->state([
        'work.position' => 'Software Engineer',
    ]));

    expect($data)->toHaveKeys(['work.name', 'work.position'])
        ->work->position->toBe('Software Engineer');
});

it('can set a custom faker instance', function () {
    $testGenerator = new class extends \Faker\Generator {
    };

    ExampleFormRequestFactory::setFakerResolver(fn () => $testGenerator);
    expect(ExampleFormRequestFactory::new()->faker())->toBe($testGenerator);

    ExampleFormRequestFactory::setFakerResolver(fn () => Factory::create('en_US'));
    expect(ExampleFormRequestFactory::new()->faker())->not->toBe($testGenerator);
});
