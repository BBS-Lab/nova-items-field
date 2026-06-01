<?php

declare(strict_types=1);

use BBSLab\NovaItemsField\Items;
use BBSLab\NovaItemsField\Rules\ArrayItemsRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Laravel\Nova\Fields\Field;

arch('no debugging helpers are left behind')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'dexit'])
    ->not->toBeUsed();

arch('the whole package declares strict types')
    ->expect('BBSLab\NovaItemsField')
    ->toUseStrictTypes();

arch('the field extends the Nova base field')
    ->expect(Items::class)
    ->toExtend(Field::class);

arch('the validation rule implements the Laravel contract')
    ->expect(ArrayItemsRule::class)
    ->toImplement(ValidationRule::class);

arch('rules live in the Rules namespace')
    ->expect('BBSLab\NovaItemsField\Rules')
    ->toImplement(ValidationRule::class);

arch('package classes stay extensible (no final classes)')
    ->expect('BBSLab\NovaItemsField')
    ->classes()
    ->not->toBeFinal();
