<?php

declare(strict_types=1);

use BBSLab\NovaItemsField\Rules\ArrayItemsRule;

/**
 * Run the rule and return the list of (decoded) failure payloads it produced.
 *
 * @return array<int, mixed>
 */
function runRule(ArrayItemsRule $rule, mixed $value): array
{
    $failures = [];

    $rule->validate('tags', $value, function ($message) use (&$failures) {
        $failures[] = json_decode((string) $message, true);
    });

    return $failures;
}

it('passes when there are no rules', function () {
    expect(runRule(new ArrayItemsRule, '["a","b"]'))->toBeEmpty();
});

it('passes when every item satisfies the item rules', function () {
    $rule = new ArrayItemsRule(itemRules: ['email']);

    expect(runRule($rule, '["a@b.com","c@d.com"]'))->toBeEmpty();
});

it('reports per-item errors keyed by their index', function () {
    $rule = new ArrayItemsRule(itemRules: ['email']);

    $failures = runRule($rule, '["good@x.com","nope","also@x.com"]');

    expect($failures)->toHaveCount(1)
        ->and($failures[0])->toHaveKey('items')
        ->and($failures[0]['items'])->toHaveKey('1')
        ->and($failures[0]['items'])->not->toHaveKey('0')
        ->and($failures[0]['items'])->not->toHaveKey('2');
});

it('reports array-level errors under the field key', function () {
    $rule = new ArrayItemsRule(arrayRules: ['min:3']);

    $failures = runRule($rule, '["only","two"]');

    expect($failures[0])->toHaveKey('field')
        ->and($failures[0]['field'])->toBeArray()
        ->and($failures[0])->not->toHaveKey('items');
});

it('enforces the minimum number of items', function () {
    expect(runRule(new ArrayItemsRule(min: 2), '["one"]'))->not->toBeEmpty()
        ->and(runRule(new ArrayItemsRule(min: 2), '["one","two"]'))->toBeEmpty();
});

it('reports size failures with item semantics, not string length', function () {
    $failures = runRule(new ArrayItemsRule(min: 2), '["one"]');

    expect($failures[0]['field'][0])->toContain('item')
        ->and($failures[0]['field'][0])->not->toContain('character');
});

it('enforces the maximum number of items', function () {
    expect(runRule(new ArrayItemsRule(max: 2), '["one","two","three"]'))->not->toBeEmpty()
        ->and(runRule(new ArrayItemsRule(max: 2), '["one","two"]'))->toBeEmpty();
});

it('treats an empty or null value as an empty list', function () {
    expect(runRule(new ArrayItemsRule, ''))->toBeEmpty()
        ->and(runRule(new ArrayItemsRule, null))->toBeEmpty()
        ->and(runRule(new ArrayItemsRule(min: 1), ''))->not->toBeEmpty();
});

it('treats invalid JSON as an empty list', function () {
    expect(runRule(new ArrayItemsRule(min: 1), 'not-json'))->not->toBeEmpty()
        ->and(runRule(new ArrayItemsRule, 'not-json'))->toBeEmpty();
});

it('accepts a value that is already decoded as an array', function () {
    expect(runRule(new ArrayItemsRule, ['a', 'b']))->toBeEmpty()
        ->and(runRule(new ArrayItemsRule(itemRules: ['email']), ['nope']))->not->toBeEmpty();
});

it('combines array-level and per-item errors in a single payload', function () {
    $rule = new ArrayItemsRule(itemRules: ['email'], min: 3);

    $failures = runRule($rule, '["bad","also-bad"]');

    expect($failures)->toHaveCount(1)
        ->and($failures[0])->toHaveKey('field')
        ->and($failures[0])->toHaveKey('items');
});
