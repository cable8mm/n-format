# N-Format

[![code-style](https://github.com/cable8mm/n-format/actions/workflows/code-style.yml/badge.svg)](https://github.com/cable8mm/n-format/actions/workflows/code-style.yml)
[![run-tests](https://github.com/cable8mm/n-format/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cable8mm/n-format/actions/workflows/run-tests.yml)
![Packagist Version](https://img.shields.io/packagist/v/cable8mm/n-format)
![Packagist Downloads](https://img.shields.io/packagist/dt/cable8mm/n-format)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/n-format/php)
![Packagist Stars](https://img.shields.io/packagist/stars/cable8mm/n-format)
![Packagist License](https://img.shields.io/packagist/l/cable8mm/n-format)

## About

N-Format is small NumberFormatter Extension Library.

# Install

```sh
composer require cable8mm/n-format
```

## Usage

General:

```php
<?php

echo NFormat::currency(358762);

// default locale = 'ko_KR' currency = 'KRW'
// output
// ₩358,762
```

```php
<?php

echo NFormat::spellOut(5);

// default locale = 'ko_KR' currency = 'KRW'
// output
// 오
```

```php
<?php

NFormat::$locale = 'ja_JP';

echo NFormat::spellOut(5);

// output
// 五

```

```php
<?php

echo NFormat::decimal(12346);

// output
// 12,346

echo NFormat::percent(12346);

// output
// 1,234,600%

echo NFormat::rawPercent(12346);

// output
// 12,346%

```

New special method `ordinalSpellOut` and `currencySpellOut`(only ko_KR):

```php
<?php

echo NFormat::ordinalSpellOut(10);

// output
// 열번째

echo NFormat::currencySpellOut(12346);

// output
// 12,346 원
```

Laravel Blade(No need installation):

```blade
{ NFormatHelper::currency(12346) }
```

## Coding style

```sh
composer lint
```

## Test

```sh
composer test
```

## License

The N-Format is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
