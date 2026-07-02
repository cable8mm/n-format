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

> **참고:** 이 메서드들은 현재 드라이버 파일이 있는 `ko_KR` 로케일만 지원합니다. 드라이버 파일을 추가하여 다른 로케일 지원을 확장할 수 있습니다.

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

> **참고:** `src/OrdinalDriver/` 및 `src/CurrencyDriver/` 디렉토리에 드라이버 파일을 생성하여 다른 로케일 지원을 추가할 수 있습니다.

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
