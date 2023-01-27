# Upgrade Guide

This guide documents breaking changes to Request Factories and details how to make the required changes in your application.

## 3.0.0

Estimated upgrade time: less than 2 minutes.

### Upgrade to PHP 8.2

Likelihood of impact: high.

The minimum PHP version for Request Factories is not 8.2. If you are not yet running PHP 8.2, you are 
of course still able to use v2 of Request Factories. 

### Remove `HasFactory` trait

Likelihood of impact: high.

One of the biggest gripes with Request Factories was that if you wanted to use the `HasFactory` trait, you had to install this package as a production dependency.
In order to fix that, we've removed the `HasFactory` trait and achieved the same functionality using
macros on the `FormRequest` instead.

To upgrade, simply remove the `HasFactory` trait from any `FormRequest` classes that you previously added it to.

```php
// Before
use Illuminate\Foundation\Http\FormRequest;
use Worksome\RequestFactories\Concerns\HasFactory;

class MyFormRequest extends FormRequest
{
    use HasFactory;
}

// After
use Illuminate\Foundation\Http\FormRequest;

class MyFormRequest extends FormRequest
{
}
```

So long as you haven't done any really "out there" customisation, your tests should continue to work exactly as they did before!

## 2.0.0

Estimated upgrade time: less than 2 minutes.

### `state` method dot notation

Likelihood of impact: low. 

In v2, we [introduced support for updating attribute state using dot notation](https://github.com/worksome/request-factories/pull/10). This does mean however that any
property keys referenced in the `state` method that should include dots now need escaping.

```php
// Before
$data = $factory->state(['worksome.co.uk' => 'Worksome UK'])->create();

// After
$data = $factory->state(['worksome\.co\.uk' => 'Worksome UK'])->create();
```
