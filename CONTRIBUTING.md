# Contributing

Contributions are welcome and will be fully credited.

## Development setup

```bash
git clone git@github.com:BBS-Lab/nova-items-field.git
cd nova-items-field
composer install   # requires access to nova.laravel.com
npm install
npm run build
```

Run the embedded Nova app to try the field by hand:

```bash
composer serve
```

## Quality gates

Every pull request must keep all of these green:

| Gate                       | Command                  | Requirement          |
| -------------------------- | ------------------------ | -------------------- |
| PHP test suite             | `composer test`          | all green            |
| PHP coverage               | `composer test-coverage` | **100%**             |
| Mutation testing           | `composer test-mutation` | **MSI ≥ 80%**        |
| Static analysis            | `composer analyse`       | PHPStan level 8, 0 errors |
| PHP code style             | `composer format`        | Pint, no diff        |
| JS test suite & coverage   | `npm run test:coverage`  | all green, **100%**  |
| JS lint / format           | `npm run lint`           | no errors            |

## Conventions

- `declare(strict_types=1);` in every PHP file; no `private` members (favour `protected` for extensibility).
- No debugging helpers (`dd`, `dump`, `ray`) — enforced by an architecture test.
- All user-facing strings go through the translation files in `resources/lang`.
- Keep components thin: behavior lives in `resources/js/composables` and `resources/js/support`,
  which are unit-tested in isolation.
- Practice TDD: add a failing test first, then the implementation.
- The compiled `dist/` is committed. After changing `resources/js` or `resources/css`, run
  `npm run build` and commit the rebuilt assets — CI fails if `dist/` is out of date.

## Pull requests

- One feature or fix per pull request, with a clear description.
- Add tests for any new behavior and document it in the README.
- Update the CHANGELOG under an "Unreleased" heading.

## Reporting bugs

Open an issue using the bug template and include your PHP, Laravel and Nova versions plus clear
reproduction steps.
