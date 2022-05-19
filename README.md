# Request Factories

Test Form Requests in Laravel without all of the boilerplate.

[![Unit Tests](https://github.com/worksome/request-factories/actions/workflows/run-tests.yml/badge.svg)](https://github.com/worksome/request-factories/actions/workflows/run-tests.yml)
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

> 💡 Psst. Although our examples use Pest PHP, this works just as well in PHPUnit.

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

You can even chain factory methods onto the end of the `fakeRequest` method:

```php
it('can sign up a user with an international phone number', function () {
    put('/users')->assertValid();
})
    ->fakeRequest(SignupRequest::class)
    ->state(['name' => 'Jane Bloggs']);
```

#### Overriding request factory data

It's important to note the order of importance request factories take when injecting data into your request.

1. Any data passed to `get`, `post`, `put`, `patch`, `delete` or similar methods will always take precedence.
2. Data defined using `state`, or methods called on a factory that alter state will be next in line.
3. Data defined in the factory `definition` and `files` methods come last, only filling out missing properties from the request.

Let's take a look at an example to illustrate this order of importance:

```php
it('can sign up a user with an international phone number', function () {
    SignupRequest::factory()->state(['name' => 'Oliver Nybroe', 'email' => 'oliver@worksome.com'])->fake();
    
    put('/users', ['email' => 'luke@worksome.com'])->assertValid();
});
```

The default email defined in `SignupRequestFactory` is `foo@bar.com`. The default name is `Luke Downing`.
Because we override the `name` property using the `state` method before calling `fake`, the name used in
the form request will actually be `Oliver Nybroe`, not `Luke Downing`. 

However, because we pass `luke@worksome.com` as data to the `put` function, that will take priority over
*all other defined data*, both `foo@bar.com` and `oliver@worksome.com`.

### The power of factories

Factories are really cool, because they allow us to create a domain-specific-language for our feature tests. Because factories
are classes, we can add declarative methods that serve as state transformers.

```php
// In our factory...
class SignupRequestFactory extends RequestFactory
{
    // After the definition...
    public function withOversizedProfilePicture(): static
    {
        return $this->state(['profile_picture' => $this->file()->image('profile.png', 2001, 2001)])
    }
}

// In our test...
it('does not allow profile pictures larger than 2000 pixels', function () {
    SignupRequest::factory()->withOversizedProfilePicture()->fake();
    
    put('/users')->assertInvalid(['profile_picture' => 'size']);
});
```

The `state` method is your friend for any data you want to add or change on your factory. What about if you'd like to omit a property
from the request? Try the `without` method!

```php
it('requires an email address', function () {
    SignupRequest::factory()->without(['email'])->fake();
    
    put('/users')->assertInvalid(['email' => 'required']);
});
```

> 💡 You can use dot syntax in the `without` method to unset deeply nested keys

Sometimes, you'll have a property that you want to be based on the value of other properties.
In that case, you can provide a closure as the property value, which receives an array of all other parameters:

```php
class SignupRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => 'Luke Downing',
            'company' => 'Worksome',
            'email' => fn ($properties) => Str::of($properties['name'])
                ->replace(' ', '.')
                ->append("@{$properties['company']}.com")
                ->lower()
                ->__toString(), // luke.downing@worksome.com
        ];
    }
}
```

Occasionally, you'll notice that multiple requests across your application share a similar subset of fields. For example,
a signup form and a payment form might both contain an address array. Rather than duplicating these fields in your factory, you can 
nest factories inside factories:

```php
class SignupRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => 'Luke Downing',
            'company' => 'Worksome',
            'address' => AddressRequestFactory::new(),
        ];
    }
}
```

Now, when the `SignupRequestFactory` is created, it will resolve the `AddressRequestFactory` for you
and fill the `address` property with all fields contained in the `AddressRequestFactory` definition.
Pretty cool hey?

### Using factories without form requests

Not every controller in your app requires a backing form request. Thankfully, we also support faking a generic request:

```php
it('lets a guest sign up to the newsletter', function () {
    NewsletterSignupFactory::new()->fake();
    
    post('/newsletter', ['email' => 'foo@bar.com'])->assertRedirect('/thanks');
});
```

## Solving common issues

### I'm getting a `CouldNotLocateRequestFactoryException`

When using the `HasFactory` trait on a `FormRequest`, we attempt to auto-locate the relevant
request factory for you. If your directory structure doesn't match for whatever reason, this exception
will be thrown.

It can easily be resolved by adding a `public static $factory` property to your form request:

```php
class SignupRequest extends FormRequest
{
    use HasFactory;
    
    public static $factory = SignupRequestFactory::class; 
}
```

### I call multiple routes in a single test and want to fake both

No sweat. Just place a call to `fake` on the relevant request factory before making each request:

```php
it('allows a user to sign up and update their profile', function () {
    SignupRequest::fake();
    post('/signup');
    
    ProfileRequest::fake();
    post('/profile')->assertValid();
});
```

### I don't want to use the default location for storing request factories

Not a problem. Use the `RequestFactories::location` method in your Laravel `TestCase::setUp`
to point us in the right direction:

```php
class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        RequestFactories::location(
            base_path('request_factories'),
            'App\\RequestFactories',
        );
    }
}
```

## Testing

We pride ourselves on a thorough test suite and strict static analysis. You can run all of our checks via a composer script:

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Luke Downing](https://github.com/lukeraymonddowning)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
