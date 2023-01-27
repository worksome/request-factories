# Changelog

All notable changes to `request-factories` will be documented in this file.

## 3.0.0 - 2023-01-27

> 💡 Be sure to check out our upgrade guide for 3.0.0 in [UPGRADE.md](UPGRADE.md)

- Removes the `HasFactory` trait in favor of macros [#33](https://github.com/worksome/request-factories/pull/33)
- Bumps minimum PHP version to 8.2 [#32](https://github.com/worksome/request-factories/pull/32)

## 2.5.0 - 2022-09-15

- Added support for using `without` with a string for single properties [#26](https://github.com/worksome/request-factories/pull/26)

## 2.4.0 - 2022-09-13

- Added support for lazily resolving model factories [#24](https://github.com/worksome/request-factories/pull/24)
- Closures, nested request factories and model factories now work in infinitely nested arrays [#24](https://github.com/worksome/request-factories/pull/24)

## 2.3.0 - 2022-09-01

- Added a handy `RequestFactory::image` method as a shortcut for `$factory->file()->image('name.png')` [#23](https://github.com/worksome/request-factories/pull/23)

## 2.2.0 - 2022-08-30

- Added support for passing an instance of a factory to the factory `::new` method [#22](https://github.com/worksome/request-factories/pull/22)

## 2.1.0 - 2022-08-04

- Added support for using custom Faker `Generator`s [#21](https://github.com/worksome/request-factories/pull/21)

## 2.0.2 - 2022-07-25

- Fixes a bug where using dot notation in an array would cause sibling elements to disappear [#19](https://github.com/worksome/request-factories/pull/19)

## 2.0.1 - 2022-06-23

- Fixes a bug when using lists and nested dot notation [#14](https://github.com/worksome/request-factories/pull/14)

## 2.0.0 - 2022-06-20

> 💡 Be sure to check out our upgrade guide for 2.0.0 in [UPGRADE.md](UPGRADE.md)

- Added support for altering state using dot notation [#10](https://github.com/worksome/request-factories/pull/10)

## 1.0.0 - 2022-05-23

- initial release
