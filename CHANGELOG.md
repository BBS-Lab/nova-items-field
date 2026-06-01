# Changelog

All notable changes to `bbs-lab/nova-items-field` will be documented in this file.

## Unreleased

### Added

- Initial release: a modern Laravel Nova 5 field for a list of scalar values, rewritten from
  `blendbyte/nova-items-field`.
- Structured rows and chips display modes.
- Array-level `rules()` and per-item `itemRules()` validation with inline, per-row error messages.
- `min()` / `max()` constraints, drag-and-drop reordering, scrollable lists, datalist suggestions,
  custom input types and button labels, `addButtonPosition()`.
- Tailored index (count badge with tooltip, `indexAsChips()`, `indexAsList()`) and detail
  (`detailsAsTotal()`) rendering.
- Dependent fields and copy-to-clipboard support.
- English & French translations.
- 100% PHP and JavaScript test coverage, mutation testing, PHPStan level 8.
