# Request Factories

Test Form Requests in Laravel without all of the boilerplate.

[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/worksome/request-factories/run-tests?label=tests)](https://github.com/worksome/envy/actions?query=workflow%3Arun-tests+branch%3Amain)
[![PHPStan](https://github.com/worksome/request-factories/actions/workflows/phpstan.yml/badge.svg)](https://github.com/worksome/envy/actions/workflows/phpstan.yml)

Take a look at the following test:

```php
it('can sign up a user with an international phone number', function () {
    put('/users', [
        'phone' => '+375 154 767 1088',
        'email' => 'foo@bar.com', 🙄
        'name' => 'Luke Downing', 😛
        'company' => 'Worksome', 😒
        'bio' => 'Blah blah blah', 😫
        'profile_picture' => UploadedFile::fake()->image('luke.png', 200, 200), 😭
        'accepts_terms_and_conditions' => true, 🤬
    ]);
    
    expect(User::latest()->first()->phone)->toBe('+375 154 767 1088');
});
```

Oof. See, all we wanted to test was the phone number, but because our route's FormRequest has validation rules, we have to send all of these
additional fields at the same time. This approach has a few downsides:

1. *It muddies the test.* Tests are supposed to be terse and easy to read. This is anything but.
2. *It makes writing tests annoying.* You probably have more than one test for each route. Every test you write requires all of these fields over and over again.
3. *It requires knowledge of the FormRequest.* You'd need to understand what each field in this form does before being able to write a passing test. If you don't, you're likely going to be caught in a trial-and-error loop, or worse, a false positive, whilst creating the test.

We think this experience can be vastly improved. Take a look:

```php
it('can sign up a user with an international phone number', function () {
    SignupRequest::fake();  
    put('/users', ['phone' => '+375 154 767 1088']);
    
    expect(User::latest()->first()->phone)->toBe('+375 154 767 1088');
});
```

Soooooo much nicer. And all thanks to Request Factories. Let's dive in...

> Psst. Although our examples use Pest PHP, this works just as well in PHPUnit.

## Installation

You can install the package as a developer dependency via Composer:

```bash
composer require --dev worksome/request-factories 
```

## Usage

First, let's create a new `RequestFactory`. A `RequestFactory` usually compliments a `FormRequest`
in your application. You can create a `RequestFactory` using the `make:request-factory` Artisan command:

```bash
php artisan make:request-factory App\Http\Requests\SignupRequest
```

Note that we've passed the `SignupRequest` FQCN as an argument. This will create a new request factory
at `tests/RequestFactories/SignupRequestFactory.php`.

You can also pass your desired request factory name as an argument instead:

```bash
php artisan make:request-factory SignupRequestFactory
```

Whilst you're free to name your request factories as you please, we recommend two defaults for a seamless experience:

1. Place them in `tests/RequestFactories`. The Artisan command will do this for you.
2. Use a `Factory` suffix. So `SignupRequest` becomes `SignupRequestFactory`.

### Factory basics

Let's take a look at our newly created `SignupRequestFactory`. You'll see something like this:

```php
namespace Tests\RequestFactories;

use Worksome\RequestFactories;

class SignupRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            // 'email' => $this->faker->email,
        ];
    }
}
```

If you've used Laravel's [model factories](https://laravel.com/docs/database-testing#defining-model-factories) before,
this will look pretty familiar. That's because the basic concept is the same: a model factory is designed to generate data
for eloquent models, a request factory is designed to generate data for form requests.

The `definition` method should return an array of valid data that can be used when submitting your form. Let's fill it out for
our example `SignupRequestFactory`:

```php
namespace Tests\RequestFactories;

use Worksome\RequestFactories;

class SignupRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'phone' => '01234567890',
            'email' => 'foo@bar.com',
            'name' => 'Luke Downing',
            'company' => 'Worksome',
            'bio' => $this->faker->words(300, true),
            'accepts_terms_and_conditions' => true,
        ];
    }
    
    public function files(): array
    {
        return [
            'profile_picture' => $this->file()->image('luke.png', 200, 200),
        ];
    }
}
```

Note that we have access to a `faker` property for easily generating fake content, such as a paragraph
for our bio, along with a `files` method we can declare to segregate files from other request data.

### Usage in tests

So how do we use this factory in our tests? There are a few options, depending on your preferred style.

#### Using `fake` on the request factory 

The simplest way to get started is to use the `fake` method on a request factory. If you're using this approach, 
make sure that it's the *last method you call on the factory*, and that you call it before making a request
to the relevant endpoint.

```php
it('can sign up a user with an international phone number', function () {
    SignupRequestFactory::new()->fake();
    
    put('/users')->assertValid();
});
```

#### Using `fake` on the form request

If you've used Laravel model factories, you'll likely be used to calling `::factory()` on eloquent models to get a 
new factory instance. Request factories have similar functionality available. First, add the `Worksome\RequestFactories\Concerns\HasFactory`
trait to the relevant FormRequest:

```php
class SignupRequest extends \Illuminate\Foundation\Http\FormRequest
{
    use \Worksome\RequestFactories\Concerns\HasFactory;
}
```

This provides access to 2 new static methods on the form request: `::factory()` and `::fake()`. You can use these methods
in your tests instead of instantiating the request factory directly:

```php
it('can sign up a user with an international phone number', function () {
    // Using the factory method...
    SignupRequest::factory()->fake();
    
    // ...or using the fake method
    SignupRequest::fake();
    
    put('/users')->assertValid();
});
```

#### Using `fakeRequest` in Pest PHP

If you're using Pest, we provide a higher order method that you can chain onto your tests:

```php
// You can provide the form request FQCN...
it('can sign up a user with an international phone number', function () {
    put('/users')->assertValid();
})->fakeRequest(SignupRequest::class);

// Or the request factory FQCN...
it('can sign up a user with an international phone number', function () {
    put('/users')->assertValid();
})->fakeRequest(SignupRequestFactory::class);

// Or even a closure that returns a request factory...
it('can sign up a user with an international phone number', function () {
    put('/users')->assertValid();
})->fakeRequest(fn () => SignupRequest::factory());
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [luke](https://github.com/worksome)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
