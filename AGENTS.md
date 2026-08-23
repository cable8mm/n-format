# AI Contribution Rules for N-Format

This file is for AI agents only. It defines repository-specific constraints and workflow rules. Do not treat it as user-facing documentation.

## Source of truth

- Read `DESIGN.md` first when you need to understand the architecture, invariants, extension points, or current package behavior.
- Read `README.md` when checking user-facing installation, configuration, and usage documentation.
- Treat the implementation and tests as the source of truth when documentation conflicts with code.
- Preserve unrelated user changes in the working tree. Inspect `git status` before editing and do not reset or overwrite unrelated work.

## Critical repository rules

### Never edit `CHANGELOG.md`

`CHANGELOG.md` is maintained automatically when GitHub Releases are created. AI agents must not modify, regenerate, reorder, or format this file during feature work, bug fixes, refactors, documentation changes, or release preparation.

If a requested change would normally require a changelog entry, leave `CHANGELOG.md` untouched and mention that the GitHub Release automation will handle it.

### Keep the package Laravel-compatible

N-Format is a reusable Laravel package, not an application. Changes must work for package consumers across the supported Laravel versions.

- Keep Laravel-specific integration in the service provider, config, casts, and other explicitly Laravel-facing code.
- Keep the core `NFormat` API and driver system usable outside a Laravel application whenever possible.
- Do not add application-specific models, routes, controllers, migrations, commands, or environment assumptions to the package.
- Preserve Composer Laravel auto-discovery unless there is a deliberate, documented reason to change it.
- Keep `ext-intl` declared as a runtime Composer requirement; `NumberFormatter` is required by the package.
- When adding Laravel support, update Composer constraints, service-provider behavior, Testbench coverage, and README usage together.

## Change workflow

Before changing code:

1. Inspect `git status --short`.
2. Read the relevant sections of `DESIGN.md` and the affected source files.
3. Search existing tests and public API usage with `rg`.
4. Identify whether the change affects global static state, serialization, database storage, or public signatures.

When implementing a change:

1. Prefer a focused regression test before modifying behavior.
2. Make the smallest compatible change that satisfies the request.
3. Preserve strict type declarations, PHPDoc, existing naming, and PSR-12 style.
4. Update `README.md` when public behavior, installation, configuration, or examples change.
5. Update `DESIGN.md` when architecture, invariants, extension points, or AI-relevant constraints change.
6. Do not update `CHANGELOG.md`.

After changing code, run the checks appropriate to the risk. For normal source changes, run:

```sh
composer check
composer validate --strict
git diff --check
```

For documentation-only changes, at minimum run `git diff --check`. If Composer metadata or PHP behavior changed, also run the relevant Composer and PHPUnit checks.

## Laravel package design rules

### Service provider

`NFormatServiceProvider` is auto-discovered by Composer. It is responsible for:

- merging `config/n-format.php` under the `n-format` key;
- applying configured locale and currency defaults;
- publishing the config file with the `n-format` tag.

Do not require consumers to manually register the provider unless Laravel auto-discovery is intentionally changed and documented.

### Configuration

Keep configuration in `config/n-format.php`. The current defaults are:

```php
'locale' => 'ko_KR',
'currency' => 'KRW',
```

Use config values for package defaults, while preserving explicit method arguments as higher-priority overrides.

### Eloquent casts and value objects

- `AsCurrency` reads as `Money` and writes a raw numeric value.
- `AsNumber` reads as `Number` and writes a raw numeric value.
- Formatted strings, nulls, empty strings, and value-object input must remain covered by tests.
- `Money` and `Number` are immutable `Stringable` and `JsonSerializable` value objects.
- String conversion is for presentation; JSON serialization is the raw numeric value.
- Never store currency symbols, percentage signs, locale separators, or formatted labels in the database.

## API and compatibility constraints

- Do not break public method signatures, parameter order, defaults, return types, namespaces, or Composer package identity without explicit user approval.
- `NFormat::$locale` and `NFormat::$currency` are global static state. Avoid introducing more global state.
- Restore static state in tests so test order does not affect results.
- Locale-specific and currency-specific rules belong in drivers implementing the existing contracts.
- Do not add locale-specific conditionals directly to `NFormat` when a driver can express the behavior.
- Preserve fallback behavior for missing drivers unless a behavior change is explicitly requested.
- Keep `ext-intl` available in development and CI environments.

## Adding a locale or currency

For a new ordinal locale:

1. Implement `Drivers\Contracts\OrdinalDriver`.
2. Register it through `NFormat::registerOrdinal()` or `DriverRegistry::registerOrdinal()`.
3. Add focused tests for normal, boundary, and fallback behavior.
4. Document the supported locale in `README.md` if it is public package functionality.

For a new currency:

1. Implement `Drivers\Contracts\CurrencyDriver`.
2. Define `currencySpellOut()` and `roundDigits()`.
3. Register it through `NFormat::registerCurrency()` or `DriverRegistry::registerCurrency()`.
4. Add tests for formatting and every relevant smart-rounding digit range.
5. Document the supported currency in `README.md` if it is public package functionality.

Do not modify built-in driver behavior casually; it can change localized output and pricing semantics.

## Testing expectations

Tests use PHPUnit and Orchestra Testbench. Maintain coverage for:

- all public `NFormat` helpers;
- default and custom driver registration;
- registry reset and lazy default boot;
- service-provider configuration;
- Eloquent cast read/write behavior;
- SQLite in-memory database roundtrips;
- immutable value-object behavior and JSON serialization;
- null, zero, formatted-string, negative, and boundary inputs where applicable.

Use the existing test conventions and fixtures. Do not introduce network calls or external services into the test suite.

## Documentation rules

- `README.md` is user-facing and should contain practical Laravel installation and usage examples.
- `DESIGN.md` is AI-facing and should contain architecture and maintenance context, not duplicate the full README.
- `CHANGELOG.md` is release automation output and must never be edited by AI.
- Keep examples executable or consistent with the actual public API.
- If a change affects a public method, config key, cast, driver contract, or supported runtime, update the appropriate documentation except `CHANGELOG.md`.

## Safety and handoff

- Never run destructive commands such as `git reset --hard`, `git checkout --`, or broad recursive deletion unless the user explicitly requests the exact operation.
- Do not commit, tag, push, create releases, or alter external systems unless explicitly requested.
- At handoff, report changed files, verification commands, and any remaining limitation.
