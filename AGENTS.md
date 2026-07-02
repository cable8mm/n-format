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
│   ├── CurrencyDriver/
│   │   └── KRW.php                    # Korean Won settings (smart rounding rules)
│   └── OrdinalDriver/
│       └── ko_KR.php                  # Korean ordinal expressions (1st, 2nd, etc.)
├── tests/
│   └── NFormatTest.php                # PHPUnit tests (11 tests)
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

### Driver File Structure

#### OrdinalDriver (Ordinal Expressions)
- **Location**: `src/OrdinalDriver/{locale}.php`
- **Format**: Returns Closure
- **Example**: `ko_KR.php` - Unique ordinal words for 1-10 + NumberFormatter for 11+

#### CurrencyDriver (Currency Settings)
- **Location**: `src/CurrencyDriver/{currency}.php`
- **Format**: Returns associative array
- **Keys**:
  - `currencySpellOut`: Regex pattern array
  - `roundDigits`: Rounding rules by digit count

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
- 11 tests, 30 assertions
- All methods tested
- Edge cases included (0, null, various rounding)

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
1. Create `src/OrdinalDriver/{locale}.php`
2. Return Closure with number → ordinal conversion logic
3. Add tests

### Adding New Currency
1. Create `src/CurrencyDriver/{currency}.php`
2. Define `currencySpellOut` patterns and `roundDigits` rules
3. Add tests

### Bug Fixes
1. Write failing test first (TDD)
2. Fix code
3. Verify all tests pass with `composer test`
4. Check code style with `composer inspect`

## ⚠️ Important Notes

### Don'ts
1. **Minimize static state changes**: `$locale`, `$currency` are global state
2. **Be careful modifying driver files**: KRW.php, ko_KR.php, etc.
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
