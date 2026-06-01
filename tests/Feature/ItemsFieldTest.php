<?php

declare(strict_types=1);

use BBSLab\NovaItemsField\Items;

it('uses the nova-items-field Vue component', function () {
    expect(Items::make('Tags')->component)->toBe('nova-items-field');
});
