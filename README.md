# N-Format

[![PHP Linting (Pint)](https://github.com/cable8mm/n-format/actions/workflows/lint.yml/badge.svg)](https://github.com/cable8mm/n-format/actions/workflows/lint.yml)
[![Test](https://github.com/cable8mm/n-format/actions/workflows/test.yml/badge.svg)](https://github.com/cable8mm/n-format/actions/workflows/test.yml)
[![Latest release](https://img.shields.io/github/v/release/cable8mm/n-format?sort=semver)](https://github.com/cable8mm/n-format/releases/latest)
[![Total Downloads](http://poser.pugx.org/cable8mm/n-format/downloads)](https://packagist.org/packages/cable8mm/n-format)
![GitHub License](https://img.shields.io/github/license/cable8mm/n-format)
[![PHP Version Require](http://poser.pugx.org/cable8mm/n-format/require/php)](https://packagist.org/packages/cable8mm/n-format)

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
