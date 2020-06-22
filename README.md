# N-Format

![Build](https://github.com/cable8mm/n-format/workflows/Build/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/cable8mm/n-format/v)](//packagist.org/packages/cable8mm/n-format)
[![StyleCI](https://github.styleci.io/repos/272403982/shield?branch=master)](https://github.styleci.io/repos/272403982)
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

## License

The N-Format is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
