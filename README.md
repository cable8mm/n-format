# N-Format

[![code-style](https://github.com/cable8mm/n-format/actions/workflows/code-style.yml/badge.svg)](https://github.com/cable8mm/n-format/actions/workflows/code-style.yml)
[![run-tests](https://github.com/cable8mm/n-format/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cable8mm/n-format/actions/workflows/run-tests.yml)
![Packagist Version](https://img.shields.io/packagist/v/cable8mm/n-format)
![Packagist Downloads](https://img.shields.io/packagist/dt/cable8mm/n-format)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/n-format/php)
![Packagist Stars](https://img.shields.io/packagist/stars/cable8mm/n-format)
![Packagist License](https://img.shields.io/packagist/l/cable8mm/n-format)

PHP already includes NumberFormat classes and functions, but they may not be available for some countries like Korea and Japan. Therefore, we provide a small wrapper library to extend NumberFormat, similar to how Carbon extends DateTime. Additionally, some additional functions have been provided.

If you have used Laravel, you could use `NFormatHelper` helper class. Refer to the [Usage Laravel Helper](###Laravel-Helper) section.

We have provided the API Documentation on the web. For more information, please visit https://www.palgle.com/n-format/ ❤️

# Install

```sh
composer require cable8mm/n-format
```

## Usage

General:

```php
print NFormat::currency(358762);
// default locale = 'ko_KR' currency = 'KRW'
//=> ₩358,762
```

```php
print NFormat::spellOut(5);
// default locale = 'ko_KR' currency = 'KRW'
//=> 오
```

```php
NFormat::$locale = 'ja_JP';

print NFormat::spellOut(5);
//=> 五

```

```php
print NFormat::decimal(12346);
//=> 12,346

print NFormat::percent(12346);
//=> 1,234,600%

print NFormat::rawPercent(12346);
//=> 12,346%

```

New special method `ordinalSpellOut` and `currencySpellOut`(only ko_KR):

```php
print NFormat::ordinalSpellOut(10);
//=> 열번째

print NFormat::currencySpellOut(12346);
//=> 12,346 원
```

### Laravel Helper

You can utilize this in Laravel Blade without any need for installation:

```blade
{{ NFormatHelper::currency(12346) }}
```

## Formatting

```sh
composer lint
# Modify all files to comply with the PSR-12.

composer inspect
# Inspect all files to ensure compliance with PSR-12.
```

## Test

```sh
composer test
```

## License

The N-Format is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
