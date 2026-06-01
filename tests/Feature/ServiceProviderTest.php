<?php

declare(strict_types=1);

use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

it('merges the package configuration', function () {
    expect(config('nova-items-field.input_type'))->toBe('text')
        ->and(config('nova-items-field.add_button_position'))->toBe('bottom');
});

it('registers the field script and style when Nova is served', function () {
    event(new ServingNova(app(), app('request')));

    $scripts = collect(Nova::allScripts())->map(fn ($asset) => (string) $asset->name());
    $styles = collect(Nova::allStyles())->map(fn ($asset) => (string) $asset->name());

    expect($scripts)->toContain('nova-items-field')
        ->and($styles)->toContain('nova-items-field');
});
