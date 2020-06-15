# N-Format

## About

N-Format is small NumberFormatter Extension Library.

# Install

Install:

```sh
composer require Cable8mm\\NFormat
```

Test:

```sh
composer test
```

## Usage

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

New special method `ordinalSpellOut`(only ko_KR):
```php
<?php

echo NFormat::ordinalSpellOut(10);

// output
// 열번째
```

## License

The N-Format is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
