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

Soooooo much nicer. And all thanks to Request Factories. Let's dive in.

> Psst. Although our examples use Pest PHP, this works just as well in PHPUnit.

## Installation

You can install the package as a developer dependency via composer:

```bash
composer require --dev worksome/request-factories 
```

## Usage

```php
$requestFactories = new Worksome\RequestFactories();
echo $requestFactories->echoPhrase('Hello, Worksome!');
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
