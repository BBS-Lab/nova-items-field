# Changelog

All notable changes to `bbs-lab/nova-items-field` will be documented in this file.

## Unreleased

## v1.0.0 — Initial release - 2026-06-01

First stable release of **Nova Items Field** — a modern [Laravel Nova 5](https://nova.laravel.com) field for managing a **list of scalar values** (tags, emails, steps, bullet points…) stored as JSON. A complete, modernized rewrite of [`blendbyte/nova-items-field`](https://github.com/blendbyte/nova-items-field).

### ✨ Features

- **Two display modes** — structured rows (numbered, drag-to-reorder) or chips/tags (`->chips()`)
- **First-class validation** — array-level `->rules()` **and** per-item `->itemRules()` with inline, per-row error messages
- **Constraints** — `->min()` / `->max()` item counts
- **Rich form UX** — drag-and-drop reordering (with keyboard support), scrollable lists (`->maxHeight()`), custom `->inputType()`, datalist `->suggestions()`, custom button labels, `->addButtonPosition()`, `->hideAddButton()`
- **Tailored views** — index as a count badge with hover tooltip (or `->indexAsChips()` / `->indexAsList()`), detail as a list/chips (or `->detailsAsTotal()`)
- **Dependent fields** (`dependsOn`) and **copy-to-clipboard** support
- **i18n** — English & French labels out of the box, publishable

### ✅ Quality

- **100% PHP coverage** + **100% JS coverage**
- Mutation tested (**MSI ≥ 80%**), PHPStan level 8, Pint, ESLint/Prettier
- Verified on **PHP 8.4 / 8.5 × Laravel 11 / 12 / 13 × Nova 5**

### 📦 Requirements

PHP `^8.4` · Laravel Nova `^5.0` · Laravel `^11.0 || ^12.0 || ^13.0`

### 🚀 Installation

```bash
composer require bbs-lab/nova-items-field
```

```php
use BBSLab\NovaItemsField\Items;

Items::make('Tags')
    ->chips()
    ->suggestions(['laravel', 'nova', 'vue'])
    ->rules('max:10')
    ->itemRules('string', 'max:255');
```

### 🙌 Credits

Rewrite of `blendbyte/nova-items-field` (itself a fork of `dillingham/nova-items-field`), by [Big Boss Studio](https://github.com/BBS-Lab).

> Migrating from the original package? See the migration table in the [README](https://github.com/BBS-Lab/nova-items-field#migrating-from-blendbytenova-items-field).
