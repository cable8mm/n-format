# N-Format

[![code-style](https://github.com/cable8mm/n-format/actions/workflows/code-style.yml/badge.svg)](https://github.com/cable8mm/n-format/actions/workflows/code-style.yml)
[![run-tests](https://github.com/cable8mm/n-format/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cable8mm/n-format/actions/workflows/run-tests.yml)
![Packagist Version](https://img.shields.io/packagist/v/cable8mm/n-format)
![Packagist Downloads](https://img.shields.io/packagist/dt/cable8mm/n-format)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/n-format/php)
![Packagist Stars](https://img.shields.io/packagist/stars/cable8mm/n-format)
![Packagist License](https://img.shields.io/packagist/l/cable8mm/n-format)

## 왜 이 패키지를 만들었나?

PHP에는 NumberFormatter 클래스와 함수가 내장되어 있지만, 한국이나 일본과 같은 일부 국가에서는 사용할 수 없을 수 있습니다. 따라서 우리는 Carbon이 DateTime을 확장하는 것과 유사하게 NumberFormatter를 확장하는 작은 래퍼 라이브러리를 제공합니다. 또한 몇 가지 추가 기능을 제공합니다.

Laravel을 사용해 보셨다면 `NFormatHelper` 헬퍼 클래스를 사용할 수 있습니다. [Laravel Helper 사용](#laravel-helper) 섹션을 참조하세요.

API 문서를 웹에서 제공하고 있습니다. 자세한 내용은 <https://www.palgle.com/n-format/>를 방문해주세요 ❤️

## 설치

```sh
composer require cable8mm/n-format
```

## 사용법

### 기본 사용

```php
print NFormat::currency(358762);
// 기본 로케일 = 'ko_KR' 통화 = 'KRW'
//=> ₩358,762
```

```php
print NFormat::spellOut(5);
// 기본 로케일 = 'ko_KR' 통화 = 'KRW'
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

**참고:**

- `percent()`는 100을 곱합니다 (12346 → 1,234,600%)
- `rawPercent()`는 100으로 나눕니다 (12346 → 12,346%)

### 서수 및 통화 철자

한국어 및 일본어 서수 표현을 위한 특별한 메서드들:

```php
print NFormat::ordinalSpellOut(10);
//=> 열번째

print NFormat::currencySpellOut(12346);
//=> 12,346 원
```

> **참고:** 이 메서드들은 현재 드라이버가 등록된 `ko_KR` 로케일만 지원합니다. 인터페이스를 구현한 드라이버를 등록하여 다른 로케일/통화 지원을 확장할 수 있습니다.

### 사용자 정의 드라이버

`src/Drivers/Contracts/` 아래 인터페이스를 구현한 클래스를
`NFormat::registerOrdinal()` / `NFormat::registerCurrency()`로 등록하여
서수 표현과 통화 규칙을 확장할 수 있습니다:

```php
use Cable8mm\NFormat\Drivers\Contracts\OrdinalDriver;

class EnUsOrdinalDriver implements OrdinalDriver
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

print NFormat::ordinalSpellOut(2, 'en_US'); // second
```

```php
use Cable8mm\NFormat\Drivers\Contracts\CurrencyDriver;

class UsdCurrencyDriver implements CurrencyDriver
{
    public function currencySpellOut(string $formatted): string
    {
        return $formatted.' dollars';
    }

    public function roundDigits(): array
    {
        return [
            3 => -1,
            4 => -2,
            5 => -2,
        ];
    }
}

NFormat::registerCurrency('USD', new UsdCurrencyDriver);

NFormat::$currency = 'USD';
print NFormat::smartPrice(12346); // 12300
```

드라이버가 등록되지 않은 로케일/통화를 요청하면 기본 원본 문자열을 반환합니다.

### 가격 계산

고객에게 표시할 가격을 계산하기 위해 `price()`와 `smartPrice()`를 사용할 수 있습니다.

- `price()`: 지정된 자릿수로 단순 반올림
- `smartPrice()`: 숫자 자릿수에 따른 지능형 반올림 (쇼핑몰에 유용)

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

**스마트 가격 반올림 규칙 (KRW):**

- 1-2자리: 반올림 없음
- 3자리: 10의 자리 반올림
- 4-5자리: 100의 자리 반올림
- 6자리: 1000의 자리 반올림
- 7자리 이상: 10000의 자리 반올림

### Laravel Helper

Laravel Blade에서 별도의 설치 없이 사용할 수 있습니다:

```blade
{{ NFormatHelper::currency(12346) }}
```

### Laravel Eloquent Casts

Laravel 12 및 Laravel 13 패키지로 제공되며, 서비스 프로바이더가 자동 등록되어
앱의 설정을 `NFormat::$locale`과 `NFormat::$currency`에 반영합니다. 설정 파일을
게시하려면:

```sh
php artisan vendor:publish --tag=n-format
```

`Cable8mm\NFormat\Casts\AsCurrency` 캐스트는 모델 속성을 `Money` 값 객체로 변환합니다
(Value Object Casting 패턴). 읽을 때는 `Money` 객체를, 쓸 때는 정제된 숫자를 DB에
저장합니다:

```php
use Illuminate\Database\Eloquent\Model;
use Cable8mm\NFormat\Casts\AsCurrency;

class Product extends Model
{
    protected function casts(): array
    {
        return [
            'price' => AsCurrency::class,                  // ko_KR / KRW
            'jpy'   => AsCurrency::class.':ja_JP,JPY',   // 컬럼별 로케일/통화
        ];
    }
}
```

`$product->price`는 `Cable8mm\NFormat\ValueObjects\Money` 인스턴스입니다. echo하거나
Blade에서 출력하면 기본적으로 `NFormat::currency()` 포맷이 적용됩니다.

```php
$product = new Product;
$product->price = 12346;

echo (string) $product->price;          // ₩12,346  (currency())
echo $product->price->price(-2);        // 12300    (NFormat::price())
echo $product->price->smartPrice();     // 12300    (NFormat::smartPrice())
echo $product->price->spellOut();       // 12,346 원 (NFormat::currencySpellOut())
echo $product->price->value();          // 12346    (원시값, 저장/계산용)
```

```blade
{{ $item->price }}                     {{-- ₩12,346 --}}
{{ $item->price->price(-2) }}          {{-- 12300 --}}
{{ $item->price->smartPrice() }}       {{-- 12300 --}}
```

포맷된 문자열을 속성에 할당하면 통화 기호와 구분자를 제거한 뒤 숫자로 변환하여
저장합니다:

```php
$product->price = '₩12,350원';

echo (string) $product->price; // ₩12,350
```

각 식별자는 콜론 뒤에 인자를 전달하여 로케일 / 통화를 지정할 수 있습니다
(예: `AsCurrency::class.':ja_JP,JPY'`). 인자를 생략하면 config 또는
`NFormat::$locale` / `NFormat::$currency` 기본값을 사용합니다.

`Money` 값 객체는 **불변(immutable)** 이며 JSON 직렬화 시 원시 숫자를 반환하므로
계산·비교·API 응답에 안전합니다.

## API 참조

### 사용 가능한 메서드

| 메서드                               | 반환타입      | 설명               | 예제                                    |
| ------------------------------------ | ------------- | ------------------ | --------------------------------------- |
| `spellOut(int)`                      | string        | 숫자를 단어로 변환 | `spellOut(5)` → `오`                    |
| `ordinalSpellOut(int)`               | string        | 서수 표현 (번째)   | `ordinalSpellOut(10)` → `열번째`        |
| `currency(int\|float\|null, string)` | string        | 통화 포맷          | `currency(358762)` → `₩358,762`         |
| `currencySpellOut(int\|float)`       | string        | 통화 + 단어        | `currencySpellOut(12346)` → `12,346 원` |
| `percent(int)`                       | string        | 퍼센트 (×100)      | `percent(12346)` → `1,234,600%`         |
| `rawPercent(int)`                    | string        | 퍼센트 (÷100)      | `rawPercent(12346)` → `12,346%`         |
| `decimal(int\|float\|null, string)`  | string        | 천단위 구분자      | `decimal(12346)` → `12,346`             |
| `price(int\|float, ?int)`            | string\|false | 반올림             | `price(12346, -2)` → `12300`            |
| `smartPrice(int\|float)`             | string\|false | 스마트 반올림      | `smartPrice(12346)` → `12300`           |

### Static Properties

| 속성        | 기본값    | 설명                      |
| ----------- | --------- | ------------------------- |
| `$locale`   | `'ko_KR'` | 기본 로케일 설정          |
| `$currency` | `'KRW'`   | 기본 통화 코드 (ISO 4217) |

## 지원 로케일

현재 지원되는 로케일 및 통화:

- **ko_KR** (한국어 - 대한민국)
  - 통화: KRW (한국 원화)
  - 기능: 서수 지원, 통화 철자, 스마트 가격 반올림
  
- **ja_JP** (일본어 - 일본)
  - 통화: JPY (일본 엔화)
  - 기능: 기본 spell out 지원

> **참고:** `Cable8mm\NFormat\Drivers\Contracts\OrdinalDriver` 및 `Cable8mm\NFormat\Drivers\Contracts\CurrencyDriver` 인터페이스를 구현한 드라이버 클래스를 [사용자 정의 드라이버](#사용자-정의-드라이버)와 같이 등록하여 다른 로케일/통화 지원을 추가할 수 있습니다.

## 기여하기

기여를 환영합니다! 다음 단계를 따라주세요:

1. 저장소를 포크합니다
2. 기능 브랜치를 생성합니다 (`git checkout -b feature/amazing-feature`)
3. 변경사항을 커밋합니다 (`git commit -m 'Add some amazing feature'`)
4. 브랜치에 푸시합니다 (`git push origin feature/amazing-feature`)
5. Pull Request를 엽니다

### 개발 환경 설정

```sh
# 의존성 설치
composer install

# 테스트 실행
composer test

# 코드 스타일 검사
composer inspect

# 코드 스타일 자동 수정
composer lint
```

## 포맷팅

```sh
composer lint
# 모든 파일을 PSR-12에 맞게 수정합니다.

composer inspect
# 모든 파일이 PSR-12을 준수하는지 검사합니다.
```

## 테스트

```sh
composer test
```

## 라이선스

N-Format은 [MIT 라이선스](https://opensource.org/licenses/MIT) 하에 오픈소스로 제공됩니다.
