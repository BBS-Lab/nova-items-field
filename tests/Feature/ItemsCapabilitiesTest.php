<?php

declare(strict_types=1);

use BBSLab\NovaItemsField\Items;

it('supports dependent fields', function () {
    $field = Items::make('Tags');

    expect($field->dependsOn('title', function () {}))->toBe($field);
});

it('can be made copyable to the clipboard', function () {
    $field = Items::make('Tags')->copyable();

    expect($field->copyable)->toBeTrue();
});
