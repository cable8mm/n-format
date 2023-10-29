# N-Format

[![Build](https://github.com/cable8mm/n-format/actions/workflows/php.yml/badge.svg)](https://github.com/cable8mm/n-format/actions/workflows/php.yml)
[![Latest Stable Version](http://poser.pugx.org/cable8mm/n-format/v)](https://packagist.org/packages/cable8mm/n-format) [![Total Downloads](http://poser.pugx.org/cable8mm/n-format/downloads)](https://packagist.org/packages/cable8mm/n-format) [![Latest Unstable Version](http://poser.pugx.org/cable8mm/n-format/v/unstable)](https://packagist.org/packages/cable8mm/n-format) [![License](http://poser.pugx.org/cable8mm/n-format/license)](https://packagist.org/packages/cable8mm/n-format) [![PHP Version Require](http://poser.pugx.org/cable8mm/n-format/require/php)](https://packagist.org/packages/cable8mm/n-format)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cable8mm/n-format/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/cable8mm/n-format/?branch=master)

## About

N-Format is small NumberFormatter Extension Library.

# Install

Install:

```sh
composer require cable8mm/n-format
```

Test:

```sh
composer test
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

## License

The N-Format is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
