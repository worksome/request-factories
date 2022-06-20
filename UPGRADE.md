# Upgrade Guide

This guide documents breaking changes to Request Factories and details how to make the required changes in your application.

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
