# N-Format - AI Assistant Guide

This file is written to help AI assistants (Claude, GPT, etc.) effectively understand and work with this project.

## 📋 Project Overview

**N-Format** is a PHP library that extends NumberFormatter to support number formatting for Korean (ko_KR) and Japanese (ja_JP).

- **Package Name**: cable8mm/n-format
- **Type**: Library
- **PHP Version**: ^8.0
- **License**: MIT
- **Main Features**: Number spelling, ordinal expressions, currency formatting, percentage conversion, price rounding

## 🏗️ Project Structure

```
n-format/
├── src/
│   ├── NFormat.php                    # Main class (NumberFormatter extension)
│   ├── NFormatServiceProvider.php     # Laravel service provider (auto-discovery)
│   ├── Casts/
│   │   ├── NumberCast.php             # Abstract base CastsAttributes implementation
│   │   ├── CurrencyCast.php           # ₩12,346 style currency formatting
│   │   ├── PriceCast.php              # Rounded price (round digits argument)
│   │   ├── SmartPriceCast.php          # Smart rounding for shopping carts
│   │   ├── DecimalCast.php             # Thousand separators
│   │   ├── PercentCast.php             # Percentage (×100)
│   │   ├── RawPercentCast.php          # Percentage (÷100)
│   │   ├── SpellOutCast.php            # Number to words
│   │   └── OrdinalCast.php             # Ordinal expressions (1st, 2nd)
│   ├── Drivers/
│   │   ├── DriverRegistry.php          # Static registry (lazy default boot)
│   │   ├── Contracts/
│   │   │   ├── OrdinalDriver.php        # Ordinal driver interface
│   │   │   └── CurrencyDriver.php       # Currency driver interface
│   │   ├── Ordinal/
│   │   │   └── KoKrOrdinalDriver.php    # Korean ordinal expressions (1st, 2nd, etc.)
│   │   └── Currency/
│   │       └── KrwCurrencyDriver.php    # Korean Won settings (smart rounding rules)
├── config/
│   └── n-format.php                    # Laravel config (locale, currency defaults)
├── tests/
│   ├── NFormatTest.php                # PHPUnit tests (11 tests)
│   ├── CastsTest.php                  # Eloquent cast unit + DB roundtrip tests
│   ├── Product.php                    # Test model using every cast
│   └── TestCase.php                   # Orchestra Testbench base test case
├── composer.json                      # Package configuration
├── README.md                          # User documentation
├── phpunit.xml.dist                   # PHPUnit configuration
├── pint.json                          # Code style configuration
└── doctum.php                         # API documentation generation configuration
```

## 🔑 Core Classes and Methods

### NFormat Class (src/NFormat.php)

A static method class that extends PHP's built-in `NumberFormatter`.

#### Main Methods

| Method                               | Return Type   | Description                   | Example                                 |
| ------------------------------------ | ------------- | ----------------------------- | --------------------------------------- |
| `spellOut(int)`                      | string        | Convert number to words       | `spellOut(5)` → `오`                    |
| `ordinalSpellOut(int)`               | string        | Ordinal expression (1st, 2nd) | `ordinalSpellOut(10)` → `열번째`        |
| `currency(int\|float\|null, string)` | string        | Currency formatting           | `currency(358762)` → `₩358,762`         |
| `currencySpellOut(int\|float)`       | string        | Currency + words              | `currencySpellOut(12346)` → `12,346 원` |
| `percent(int)`                       | string        | Percentage (×100)             | `percent(12346)` → `1,234,600%`         |
| `rawPercent(int)`                    | string        | Percentage (÷100)             | `rawPercent(12346)` → `12,346%`         |
| `decimal(int\|float\|null, string)`  | string        | Thousand separators           | `decimal(12346)` → `12,346`             |
| `price(int\|float, ?int)`            | string\|false | Rounding                      | `price(12346, -2)` → `12300`            |
| `smartPrice(int\|float)`             | string\|false | Smart rounding                | `smartPrice(12346)` → `12300`           |

#### Static Properties

- `$locale` (default: `'ko_KR'`): Locale setting
- `$currency` (default: `'KRW'`): Currency code setting

### Driver Architecture

#### DriverRegistry (Driver resolution)
- **Location**: `src/Drivers/DriverRegistry.php`
- **Pattern**: Static registry with lazy boot of built-in drivers
- **API**: `registerOrdinal()`, `registerCurrency()`, `ordinal()`, `currency()`, `reset()`
- `NFormat::registerOrdinal()` / `NFormat::registerCurrency()` are registered facades

#### OrdinalDriver (Ordinal Expressions)
- **Interface**: `src/Drivers/Contracts/OrdinalDriver.php`
- **Contract**: `spellOut(int $number): string`
- **Example**: `KoKrOrdinalDriver` - Unique ordinal words for 1-10 + NumberFormatter for 11+

#### CurrencyDriver (Currency Settings)
- **Interface**: `src/Drivers/Contracts/CurrencyDriver.php`
- **Contract**:
  - `currencySpellOut(string $formatted): string` - pattern replacement
  - `roundDigits(): array` - rounding rules by digit count
- **Example**: `KrwCurrencyDriver` - Korean Won settings

## 🎯 Important Design Patterns

### 1. Driver Pattern
Separate locale/currency-specific logic into external files for extensibility

### 2. Static Factory Method
Create NumberFormatter instances with `static::create()` (works in inherited classes)

### 3. Graceful Degradation
- No driver file: Default behavior (return original number)
- Driver error: try-catch exception handling

### 4. Locale-based Configuration
Multi-language support by changing `NFormat::$locale`

## 🧪 Testing

### Running Tests
```bash
composer test          # Run all tests
composer check         # Code inspection + tests
composer inspect       # Code style inspection
composer lint          # Auto-fix code style
```

### Test Coverage
- 31 tests, 85 assertions (11 original + 13 cast + 7 driver registry tests)
- All methods tested
- Edge cases included (0, null, various rounding, formatted-string input)
- Eloquent cast DB roundtrip via SQLite in-memory (Orchestra Testbench)
- Driver registry unit tests (defaults, custom registration, reset)

## 📝 Coding Conventions

### PSR-12 Compliance
- Indentation: 4 spaces
- Naming: camelCase (methods), snake_case (constants)
- Type hints: Strict usage

### Comment Rules
- PHPDoc blocks for all public methods
- Include `@param`, `@return`, `@example`
- Korean descriptions recommended

### Error Handling
- try-catch required for file loading
- Return default values on driver failure
- Ensure type safety

## 🔧 Common Tasks

### Adding New Locale
1. Create a class implementing `Cable8mm\NFormat\Drivers\Contracts\OrdinalDriver`
2. Register it with `NFormat::registerOrdinal($locale, $driver)` or `DriverRegistry::registerOrdinal()`
3. Add tests

### Adding New Currency
1. Create a class implementing `Cable8mm\NFormat\Drivers\Contracts\CurrencyDriver`
2. Implement `currencySpellOut()` patterns and `roundDigits()` rules
3. Register it with `NFormat::registerCurrency($currency, $driver)` or `DriverRegistry::registerCurrency()`
4. Add tests

### Bug Fixes
1. Write failing test first (TDD)
2. Fix code
3. Verify all tests pass with `composer test`
4. Check code style with `composer inspect`

## ⚠️ Important Notes

### Don'ts
1. **Minimize static state changes**: `$locale`, `$currency` are global state
2. **Be careful modifying driver classes**: `KrwCurrencyDriver`, `KoKrOrdinalDriver`, etc.
3. **Breaking changes**: Follow SemVer when changing public API
4. **Never omit type hints**: Utilize PHP 8.0+ features

### Required Checks
1. All tests must pass
2. PSR-12 code style compliance
3. DocBlock required
4. Example code accuracy verification

## 📚 Reference Materials

- [PHP NumberFormatter](https://www.php.net/manual/en/class.numberformatter.php)
- [PSR-12 Coding Style](https://www.php-fig.org/psr/psr-12/)
- [Composer Schema](https://getcomposer.org/doc/04-schema.md)
- [Laravel Pint](https://github.com/laravel/pint)

## 🐛 Known Issues

1. `smartPrice()` only handles positive numbers (returns string for 0 or below)
2. `currencySpellOut()` uses EXPONENTIAL_SYMBOL style (temporary workaround)
3. `ordinalSpellOut()` return type is string, but returns original number when no driver exists

## 💡 Improvement Suggestions

1. **Caching**: `static::create()` called every time → instance caching
2. **Logging**: Add logs when driver loading fails
3. **Type Safety**: Explicit return type for `currencySpellOut()`
4. **Test Coverage**: Add edge cases (negative numbers, very large numbers)
5. **Performance**: Optimize NumberFormatter instance reuse

---

This file is for AI assistants to understand and work with this project.
