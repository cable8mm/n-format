# N-Format

[![code-style](https://github.com/cable8mm/n-format/actions/workflows/code-style.yml/badge.svg)](https://github.com/cable8mm/n-format/actions/workflows/code-style.yml)
[![run-tests](https://github.com/cable8mm/n-format/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cable8mm/n-format/actions/workflows/run-tests.yml)
![Packagist Version](https://img.shields.io/packagist/v/cable8mm/n-format)
![Packagist Downloads](https://img.shields.io/packagist/dt/cable8mm/n-format)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/n-format/php)
![Packagist Stars](https://img.shields.io/packagist/stars/cable8mm/n-format)
![Packagist License](https://img.shields.io/packagist/l/cable8mm/n-format)

PHP already includes NumberFormat classes and functions, but they may not be available for some countries like Korea and Japan. Therefore, we provide a small wrapper library to extend NumberFormat, similar to how Carbon extends DateTime. Additionally, some additional functions have been provided.

If you have used Laravel, you could use `NFormatHelper` helper class. Refer to the [Usage Laravel Helper](#laravel-helper) section.

We have provided the API Documentation on the web. For more information, please visit <https://www.palgle.com/n-format/> ❤️

## Install

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

**Note:**

- `percent()` multiplies by 100 (12346 → 1,234,600%)
- `rawPercent()` divides by 100 (12346 → 12,346%)

### Ordinal & Currency Spell Out

Special methods for Korean and Japanese ordinal expressions:

```php
print NFormat::ordinalSpellOut(10);
//=> 열번째

print NFormat::currencySpellOut(12346);
//=> 12,346 원
```

> **Note:** These methods currently support `ko_KR` locale with driver files. You can extend support for other locales by adding driver files.

### Price Calculation

You can also use `price()` and `smartPrice()` to calculate the price for customers.

- `price()`: Simple rounding with specified digits
- `smartPrice()`: Intelligent rounding based on the number of digits (useful for shopping carts)

```php
print NFormat::price(12346, -2);
//=> 12300

print NFormat::price(12346.23, 1);
//=> 12346.20

print NFormat::smartPrice(12346);
//=> 12300

print NFormat::smartPrice(123467);
//=> 123000

print NFormat::smartPrice(1234678);
//=> 1230000

print NFormat::smartPrice(12346432);
//=> 12350000

print NFormat::smartPrice(3212343232);
//=> 3212340000
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

## API Reference

### Available Methods

| Method                                                   | Description                                | Example                                 |
| -------------------------------------------------------- | ------------------------------------------ | --------------------------------------- |
| `spellOut(int $number)`                                  | Convert number to words                    | `spellOut(5)` → `오`                    |
| `ordinalSpellOut(int $number)`                           | Convert number to ordinal (1st, 2nd, etc.) | `ordinalSpellOut(10)` → `열번째`        |
| `currency(int\|float\|null $number, string $zero = '0')` | Format as currency                         | `currency(358762)` → `₩358,762`         |
| `currencySpellOut(int\|float $number)`                   | Format currency with words                 | `currencySpellOut(12346)` → `12,346 원` |
| `percent(int $number)`                                   | Convert to percentage (×100)               | `percent(12346)` → `1,234,600%`         |
| `rawPercent(int $number)`                                | Convert to percentage (÷100)               | `rawPercent(12346)` → `12,346%`         |
| `decimal(int\|float\|null $number, string $zero = '0')`  | Format with thousand separators            | `decimal(12346)` → `12,346`             |
| `price(int\|float $number, ?int $roundDigits = null)`    | Round price with specified digits          | `price(12346, -2)` → `12300`            |
| `smartPrice(int\|float $number)`                         | Intelligent rounding for shopping          | `smartPrice(12346)` → `12300`           |

### Static Properties

| Property    | Default   | Description                      |
| ----------- | --------- | -------------------------------- |
| `$locale`   | `'ko_KR'` | Default locale for formatting    |
| `$currency` | `'KRW'`   | Default currency code (ISO 4217) |

## Supported Locales

Currently supported locales and currencies:

- **ko_KR** (Korean - South Korea)
  - Currency: KRW (Korean Won)
  - Features: Full ordinal support, currency spell out, smart price rounding
  
- **ja_JP** (Japanese - Japan)
  - Currency: JPY (Japanese Yen)
  - Features: Basic spell out support

> **Note:** You can add support for other locales by creating driver files in `src/OrdinalDriver/` and `src/CurrencyDriver/` directories.

## Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Setup

```sh
# Install dependencies
composer install

# Run tests
composer test

# Check code style
composer inspect

# Fix code style
composer lint
```

## License

The N-Format is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
