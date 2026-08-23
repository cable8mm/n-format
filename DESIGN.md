# N-Format: AI Project Context

This is an internal engineering reference for AI agents working on N-Format. It describes the current architecture, invariants, extension points, and verification requirements. It is not end-user documentation; use [README.md](README.md) for installation and usage examples.

## Project identity

N-Format is a Laravel package for locale-aware number, currency, percentage, and ordinal formatting. It is built on PHP's `NumberFormatter` and preserves a framework-independent static formatting API while adding Laravel integration in v2.

- Package: `cable8mm/n-format`
- Namespace: `Cable8mm\NFormat`
- PHP: `^8.2`
- Required extensions: `ext-intl`
- Laravel dependencies: `illuminate/contracts` and `illuminate/database`, `^12.0|^13.0`
- License: MIT

The package evolved from a single `NFormat` wrapper into a Laravel package. Do not describe it as only a one-class library when updating project documentation or code.

## Core invariants

1. Locale- and currency-specific behavior belongs in drivers, not in conditional branches inside `NFormat`.
2. Core formatting through `NFormat` must remain usable outside a Laravel application. Laravel-specific behavior belongs in the service provider and Eloquent casts.
3. Eloquent casts return presentation-oriented value objects on read and raw numeric values on write.
4. `NFormat::$locale` and `NFormat::$currency` are global static state. Change them sparingly and restore them in tests.
5. Preserve public type declarations, argument order, default values, and PHPDoc examples unless a breaking change is intentional.
6. `ext-intl` is a runtime requirement. Keep it declared in `composer.json`.

## Repository map

```text
src/
├── NFormat.php                         # NumberFormatter subclass and static API
├── NFormatServiceProvider.php           # Laravel config integration
├── Casts/
│   ├── AsCurrency.php                   # Eloquent attribute -> Money
│   └── AsNumber.php                     # Eloquent attribute -> Number
├── ValueObjects/
│   ├── Money.php                        # Immutable currency value object
│   └── Number.php                       # Immutable number value object
└── Drivers/
    ├── DriverRegistry.php               # Static lazy driver registry
    ├── Contracts/
    │   ├── OrdinalDriver.php            # spellOut(int): string
    │   └── CurrencyDriver.php           # currencySpellOut(), roundDigits()
    ├── Ordinal/KoKrOrdinalDriver.php    # Korean ordinal behavior
    └── Currency/KrwCurrencyDriver.php   # KRW spell-out and rounding behavior

config/n-format.php                      # locale and currency defaults
tests/                                    # PHPUnit and Orchestra Testbench tests
```

Other important files:

- `README.md`: user-facing documentation.
- `CHANGELOG.md`: release history; v2 is the Laravel package transition.
- `composer.json`: package metadata, runtime/dev dependencies, Laravel auto-discovery.
- `phpunit.xml.dist`: PHPUnit configuration.
- `pint.json`: Laravel Pint configuration.
- `doctum.php`: API documentation generation configuration.

## `NFormat` behavior

`NFormat` extends `NumberFormatter`, but its public helpers are static wrappers. When a locale is omitted, use `NFormat::$locale`; when a currency is omitted, use `NFormat::$currency`.

| Method | Behavior |
| --- | --- |
| `spellOut(int, ?string)` | Spell out a number with `NumberFormatter::SPELLOUT`. |
| `ordinalSpellOut(int, ?string)` | Resolve an ordinal driver by locale. |
| `currency(int\|float\|null, string, ?string, ?string)` | Format currency; the second argument is the zero/null replacement string. |
| `currencySpellOut(int\|float, ?string, ?string)` | Format currency and pass the result through a currency driver. |
| `decimal(int\|float\|null, string, ?string)` | Format a decimal with locale separators. |
| `percent(int\|float, ?string)` | Format the input multiplied by 100. |
| `rawPercent(int\|float, ?string)` | Divide the input by 100, then format as a percentage. |
| `price(int\|float, ?int)` | Round using the supplied `round()` precision. |
| `smartPrice(int\|float, ?string)` | Apply currency-driver rounding by digit count. |

`price()` and `smartPrice()` return `string|false`. `smartPrice()` returns non-positive input as a string. Preserve these existing return semantics.

Default static state:

```php
NFormat::$locale = 'ko_KR';
NFormat::$currency = 'KRW';
```

In Laravel, `NFormatServiceProvider::boot()` overwrites these values from `config('n-format.locale')` and `config('n-format.currency')`. A direct runtime mutation affects subsequent calls that omit explicit locale/currency arguments.

## Laravel integration

`NFormatServiceProvider` is registered through Composer Laravel auto-discovery.

- `register()` merges `config/n-format.php` under the `n-format` key.
- `boot()` copies configured locale and currency into `NFormat` static properties.
- `boot()` publishes the config file with the `n-format` tag.

The package must not require a Laravel application for core `NFormat`, driver, or value-object behavior. Keep Laravel-only dependencies and logic confined to the service provider and casts where practical.

## Casts and value objects

### `AsCurrency` and `Money`

`AsCurrency` returns `Money` on read. On write it accepts a `Money`, numeric value, or formatted string and stores only the raw numeric value. Constructor arguments and cast arguments are ordered as `locale`, then `currency`; for example, `AsCurrency::class.':ja_JP,JPY'`.

`Money` is immutable and implements `Stringable` and `JsonSerializable`:

- `__toString()` and `currency()` delegate to `NFormat::currency()`.
- `price(?int)` delegates to `NFormat::price()`.
- `smartPrice()` delegates to `NFormat::smartPrice()`.
- `spellOut()` delegates to `NFormat::currencySpellOut()`.
- `value()` returns the raw numeric value.
- `jsonSerialize()` returns the raw numeric value.

### `AsNumber` and `Number`

`AsNumber` accepts an optional locale and returns `Number` on read. It stores raw numeric values on write. `Number` is immutable and implements `Stringable` and `JsonSerializable`:

- `__toString()` and `decimal()` delegate to `NFormat::decimal()`.
- `percent()`, `rawPercent()`, `spellOut()`, and `ordinalSpellOut()` delegate to `NFormat`.
- `value()` and `jsonSerialize()` return the raw numeric value.

Both casts preserve null, empty-string, and unparseable input as null. Formatted strings are normalized by removing commas and non-numeric formatting characters before storage. When changing cast behavior, test numeric input, formatted strings, nulls, invalid values, value-object input, and database roundtrips.

## Driver architecture

`DriverRegistry` stores ordinal drivers by locale and currency drivers by ISO 4217 code.

- Built-in drivers are registered lazily on the first lookup.
- Defaults are `ko_KR` -> `KoKrOrdinalDriver` and `KRW` -> `KrwCurrencyDriver`.
- Registering an existing key replaces that driver.
- `reset()` clears custom and built-in registrations; the next lookup boots defaults again.
- `NFormat::registerOrdinal()` and `NFormat::registerCurrency()` are public forwarding methods.

### Ordinal driver contract

```php
interface OrdinalDriver
{
    public function spellOut(int $number): string;
}
```

The Korean driver uses dedicated forms for 1 through 10 and appends `번째` to the Korean `NumberFormatter` result for other numbers.

### Currency driver contract

```php
interface CurrencyDriver
{
    public function currencySpellOut(string $formatted): string;

    /** @return array<int, int> */
    public function roundDigits(): array;
}
```

`roundDigits()` maps number-of-digits to the precision passed to `round()`. If an exact digit count is absent, `smartPrice()` uses the final rule in the driver array.

When adding locale or currency support, add the driver class, registration path, and focused tests. Do not add locale-specific conditionals to `NFormat`.

## Fallback and compatibility rules

- Missing ordinal driver: `ordinalSpellOut()` returns the input number cast to a string.
- Missing currency driver: `currencySpellOut()` returns the formatted NumberFormatter currency string unchanged.
- `intl` is mandatory because `NFormat` directly uses `NumberFormatter`.
- Preserve public signatures and return types under SemVer.
- Preserve current floating-point formatting and rounding behavior unless the change is explicitly intended.
- `Money` and `Number` are value objects, not mutable models; do not add setters or in-place mutation.

## Testing and verification

```sh
composer test      # Run all PHPUnit tests
composer inspect   # Run Laravel Pint in check mode
composer check     # Run inspect and test
composer lint      # Apply Laravel Pint fixes
composer validate  # Validate composer.json and composer.lock
```

Orchestra Testbench is used for Laravel integration tests, including service-provider configuration and SQLite in-memory database roundtrips. Current coverage includes the NFormat API, default/custom drivers, provider configuration, both casts, both value objects, JSON serialization, and persistence roundtrips.

For a new feature or bug fix:

1. Add a failing regression test first.
2. Implement the smallest compatible change.
3. Run `composer check` and `composer validate`.
4. Update `README.md`, `CHANGELOG.md`, and this document when public behavior or architecture changes.

## Change checklist

- Check for global static state leakage between tests.
- Preserve lazy registry boot and `reset()` behavior.
- Keep formatted presentation values out of database storage.
- Keep the package usable outside Laravel for core formatting.
- Keep `ext-intl` in runtime requirements.
- Verify README examples against actual `intl` output and tests.
- When supporting a new Laravel version, update Composer constraints, Testbench compatibility, and provider tests together.
