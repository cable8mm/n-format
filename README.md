# N-Format

[![code-style](https://github.com/cable8mm/n-format/actions/workflows/code-style.yml/badge.svg)](https://github.com/cable8mm/n-format/actions/workflows/code-style.yml)
[![run-tests](https://github.com/cable8mm/n-format/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cable8mm/n-format/actions/workflows/run-tests.yml)
![Packagist Version](https://img.shields.io/packagist/v/cable8mm/n-format)
![Packagist Downloads](https://img.shields.io/packagist/dt/cable8mm/n-format)
![Packagist License](https://img.shields.io/packagist/l/cable8mm/n-format)

Laravel 애플리케이션에서 숫자, 통화, 퍼센트, 서수 표현을 일관되게 다루기 위한 PHP 패키지입니다. PHP의 `NumberFormatter`를 확장한 `NFormat` API와 함께 Laravel 서비스 프로바이더, 설정 파일, Eloquent 캐스트, 불변 값 객체를 제공합니다.

v2부터 N-Format은 `NFormat` 클래스 하나만 제공하던 래퍼에서 Laravel 통합을 포함하는 패키지로 확장되었습니다.

## 요구 사항

- PHP 8.2 이상
- `intl` PHP 확장
- Laravel 12 또는 13

## 설치

```sh
composer require cable8mm/n-format
```

Laravel의 패키지 자동 검색을 지원하므로 별도로 서비스 프로바이더를 등록할 필요가 없습니다.

## 설정

기본 설정은 한국어·대한민국 원화입니다.

```php
// config/n-format.php
return [
    'locale' => 'ko_KR',
    'currency' => 'KRW',
];
```

설정 파일이 필요하면 애플리케이션으로 게시합니다.

```sh
php artisan vendor:publish --tag=n-format
```

게시한 설정은 모든 `NFormat` 메서드와 값 객체의 기본값으로 사용됩니다. 특정 호출에서 로케일이나 통화를 직접 전달하여 기본값을 덮어쓸 수도 있습니다.

## 기본 사용법

```php
use Cable8mm\NFormat\NFormat;

NFormat::currency(358762);        // ₩358,762
NFormat::decimal(12346);          // 12,346
NFormat::spellOut(5);             // 오
NFormat::ordinalSpellOut(10);     // 열번째
NFormat::currencySpellOut(12346); // 12,346 원
NFormat::percent(12346);          // 1,234,600%
NFormat::rawPercent(12346);       // 12,346%
```

`percent()`는 입력값에 100을 곱하고, `rawPercent()`는 입력값을 100으로 나눈 뒤 퍼센트로 표시합니다.

호출별로 로케일과 통화를 지정할 수 있습니다.

```php
NFormat::$locale = 'ja_JP';
NFormat::spellOut(5); // 五

NFormat::currency(12346, '0', 'ja_JP', 'JPY'); // ￥12,346
```

> `currency()`의 두 번째 인자는 0 또는 null일 때 사용할 대체 문자열입니다. 세 번째와 네 번째 인자가 각각 로케일과 통화입니다.

## 가격 반올림

`price()`는 지정한 자릿수로 반올림하고, `smartPrice()`는 통화 드라이버의 규칙에 따라 자릿수별로 반올림합니다.

```php
NFormat::price(12346, -2); // 12300
NFormat::smartPrice(12346); // 12300
NFormat::smartPrice(1234678); // 1230000
```

기본 KRW 규칙은 다음과 같습니다.

- 1~2자리: 반올림하지 않음
- 3자리: 10의 자리
- 4~5자리: 100의 자리
- 6자리: 1,000의 자리
- 7자리 이상: 10,000의 자리

## Eloquent 캐스트

v2는 Eloquent 속성을 불변 값 객체로 변환하는 두 가지 캐스트를 제공합니다.

- `AsCurrency`: `Money` 값 객체 반환
- `AsNumber`: `Number` 값 객체 반환

```php
use Cable8mm\NFormat\Casts\AsCurrency;
use Cable8mm\NFormat\Casts\AsNumber;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected function casts(): array
    {
        return [
            'price' => AsCurrency::class,
            'jpy' => AsCurrency::class.':ja_JP,JPY',
            'discount' => AsNumber::class,
            'count' => AsNumber::class.':ja_JP',
        ];
    }
}
```

읽을 때는 값 객체가 반환되고, 저장할 때는 통화 기호와 천 단위 구분자를 제거한 숫자만 데이터베이스에 저장됩니다.

```php
$product = new Product;
$product->price = '₩12,350원';

echo $product->price;                // 12350 (raw value)
echo $product->price->currency();    // ₩12,350
echo $product->price->price(-2);     // 12400
echo $product->price->smartPrice();  // 12400
echo $product->price->spellOut();    // 12,350 원
echo $product->price->value();       // 12350
```

금액이 `0`이면 통화 기호 대신 번역된 무료 문구를 표시합니다.

```php
$product->price = 0;
echo $product->price;                // 0 (raw value)
echo $product->price->currency();    // 무료
```

기본 번역은 한국어 `무료`, 영어 `Free`, 일본어 `無料`이며, `AsCurrency`에 지정한 로케일을 기준으로 선택합니다. 번역 파일은 다음 명령으로 게시할 수 있습니다.

```sh
php artisan vendor:publish --tag=n-format-translations
```

번역 키는 `n-format::messages.free`입니다.

`Number`는 기본적으로 천 단위 구분자를 사용해 출력합니다.

```php
$product->discount = 12346;

echo $product->discount;                    // 12,346
echo $product->discount->percent();         // 1,234,600%
echo $product->discount->rawPercent();      // 12,346%
echo $product->discount->spellOut();        // 일만이천삼백사십육
echo $product->discount->ordinalSpellOut(); // 열번째
echo $product->discount->value();            // 12346
```

두 값 객체는 `Stringable`, `JsonSerializable`을 구현합니다. 문자열로 출력하면 포맷된 값을 사용하고, JSON으로 직렬화하면 원시 숫자를 사용합니다.

Blade에서도 그대로 사용할 수 있습니다.

```blade
{{ $product->price }}
{{ $product->discount->percent() }}
```

숫자·통화 캐스트는 숫자와 포맷된 숫자 문자열을 저장할 수 있습니다. `null`, 빈 문자열, 숫자로 변환할 수 없는 값은 `null`로 저장되거나 읽힙니다.

## 드라이버 확장

로케일별 서수 표현과 통화별 가격 규칙은 드라이버로 분리되어 있습니다. 다음 인터페이스를 구현한 뒤 등록하면 지원 범위를 확장할 수 있습니다.

```php
use Cable8mm\NFormat\Drivers\Contracts\OrdinalDriver;

final class EnUsOrdinalDriver implements OrdinalDriver
{
    public function spellOut(int $number): string
    {
        return match ($number) {
            1 => 'first',
            2 => 'second',
            default => $number.'th',
        };
    }
}

NFormat::registerOrdinal('en_US', new EnUsOrdinalDriver);
NFormat::ordinalSpellOut(2, 'en_US'); // second
```

통화 드라이버는 `currencySpellOut()`과 `roundDigits()`를 구현합니다.

```php
use Cable8mm\NFormat\Drivers\Contracts\CurrencyDriver;

final class UsdCurrencyDriver implements CurrencyDriver
{
    public function currencySpellOut(string $formatted): string
    {
        return $formatted.' dollars';
    }

    public function roundDigits(): array
    {
        return [3 => -1, 4 => -2, 5 => -2];
    }
}

NFormat::registerCurrency('USD', new UsdCurrencyDriver);
```

기본 제공 드라이버는 `ko_KR` 서수 표현과 `KRW` 통화 규칙입니다. 등록되지 않은 드라이버를 요청하면 `ordinalSpellOut()`과 `currencySpellOut()`은 NumberFormatter의 기본 결과를 반환합니다.

## API

| 메서드                                                 | 설명                    |
| ------------------------------------------------------ | ----------------------- |
| `spellOut(int, ?string)`                               | 숫자를 단어로 변환      |
| `ordinalSpellOut(int, ?string)`                        | 서수 표현               |
| `currency(int\|float\|null, string, ?string, ?string)` | 통화 포맷               |
| `currencySpellOut(int\|float, ?string, ?string)`       | 통화와 단어를 함께 표시 |
| `decimal(int\|float\|null, string, ?string)`           | 천 단위 구분자 포맷     |
| `percent(int\|float, ?string)`                         | 100을 곱한 퍼센트       |
| `rawPercent(int\|float, ?string)`                      | 100으로 나눈 퍼센트     |
| `price(int\|float, ?int)`                              | 지정 자릿수 반올림      |
| `smartPrice(int\|float, ?string)`                      | 통화 규칙에 따른 반올림 |
| `registerOrdinal(string, OrdinalDriver)`               | 로케일 드라이버 등록    |
| `registerCurrency(string, CurrencyDriver)`             | 통화 드라이버 등록      |

## 개발

```sh
composer install
composer test      # PHPUnit 테스트
composer inspect   # 코드 스타일 검사
composer check     # 스타일 검사와 테스트
composer lint      # 코드 스타일 자동 수정
```

## 라이선스

N-Format은 [MIT 라이선스](https://opensource.org/licenses/MIT)로 제공됩니다.
