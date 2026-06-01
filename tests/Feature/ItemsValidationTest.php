<?php

declare(strict_types=1);

use BBSLab\NovaItemsField\Items;
use BBSLab\NovaItemsField\Rules\ArrayItemsRule;
use Laravel\Nova\Http\Requests\NovaRequest;

function emptyNovaRequest(): NovaRequest
{
    return NovaRequest::create('/', 'GET');
}

/**
 * Resolve the field's generated rule via the given accessor and report whether
 * it fails for the supplied (JSON) value.
 */
function fieldRuleFails(Items $field, string $accessor, mixed $value): bool
{
    $failed = false;

    $field->{$accessor}(emptyNovaRequest())['tags'][0]
        ->validate('tags', $value, function () use (&$failed) {
            $failed = true;
        });

    return $failed;
}

it('wraps the field rules in a single ArrayItemsRule', function () {
    $rules = Items::make('Tags')
        ->rules('required')
        ->itemRules('email')
        ->getRules(emptyNovaRequest());

    expect($rules)->toHaveKey('tags')
        ->and($rules['tags'])->toHaveCount(1)
        ->and($rules['tags'][0])->toBeInstanceOf(ArrayItemsRule::class);
});

it('provides a fluent itemRules accepting variadic or array form', function () {
    $field = Items::make('Tags');

    expect($field->itemRules('email'))->toBe($field)
        ->and($field->itemRules('email', 'max:5'))->toBe($field)
        ->and($field->itemRules(['email', 'max:5']))->toBe($field);
});

it('feeds min and max into the generated rule', function () {
    $rule = Items::make('Tags')->min(2)->getRules(emptyNovaRequest())['tags'][0];

    $failed = false;
    $rule->validate('tags', '["one"]', function () use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeTrue();
});

it('merges creation rules into the wrapped rule', function () {
    $rules = Items::make('Tags')
        ->rules('required')
        ->creationRules('min:1')
        ->getCreationRules(emptyNovaRequest());

    expect($rules['tags'][0])->toBeInstanceOf(ArrayItemsRule::class);
});

it('merges update rules into the wrapped rule', function () {
    $rules = Items::make('Tags')
        ->updateRules('max:3')
        ->getUpdateRules(emptyNovaRequest());

    expect($rules['tags'][0])->toBeInstanceOf(ArrayItemsRule::class);
});

it('actually enforces array-level rules through the generated rule', function () {
    expect(fieldRuleFails(Items::make('Tags')->rules('min:2'), 'getRules', '["one"]'))->toBeTrue()
        ->and(fieldRuleFails(Items::make('Tags')->rules('min:2'), 'getRules', '["one","two"]'))->toBeFalse();
});

it('actually enforces item rules given in array form', function () {
    expect(fieldRuleFails(Items::make('Tags')->itemRules(['email']), 'getRules', '["nope"]'))->toBeTrue()
        ->and(fieldRuleFails(Items::make('Tags')->itemRules(['email']), 'getRules', '["a@b.com"]'))->toBeFalse();
});

it('actually enforces item rules given in variadic form', function () {
    expect(fieldRuleFails(Items::make('Tags')->itemRules('email', 'min:6'), 'getRules', '["a@b"]'))->toBeTrue();
});

it('actually enforces creation-only rules through the generated rule', function () {
    expect(fieldRuleFails(Items::make('Tags')->creationRules('min:2'), 'getCreationRules', '["one"]'))->toBeTrue();
});

it('actually enforces update-only rules through the generated rule', function () {
    expect(fieldRuleFails(Items::make('Tags')->updateRules('min:2'), 'getUpdateRules', '["one"]'))->toBeTrue();
});
