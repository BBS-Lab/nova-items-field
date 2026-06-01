<?php

declare(strict_types=1);

use BBSLab\NovaItemsField\Items;

it('serializes localized labels in english', function () {
    $labels = Items::make('Tags')->jsonSerialize()['labels'];

    expect($labels)->toMatchArray([
        'add' => 'Add an item',
        'remove' => 'Remove',
        'empty' => 'No items yet.',
        'placeholder' => 'Add a new item',
    ]);
});

it('serializes localized labels in french', function () {
    app()->setLocale('fr');

    $labels = Items::make('Tags')->jsonSerialize()['labels'];

    expect($labels)->toMatchArray([
        'add' => 'Ajouter un élément',
        'remove' => 'Supprimer',
        'placeholder' => 'Ajouter un nouvel élément',
    ]);
});

it('lets custom labels override the translations', function () {
    $labels = Items::make('Tags')
        ->addButtonLabel('Add tag')
        ->deleteButtonLabel('Del')
        ->placeholder('Type…')
        ->jsonSerialize()['labels'];

    expect($labels)->toMatchArray([
        'add' => 'Add tag',
        'remove' => 'Del',
        'placeholder' => 'Type…',
    ]);
});
