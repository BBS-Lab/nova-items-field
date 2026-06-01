<?php

declare(strict_types=1);

use BBSLab\NovaItemsField\Items;

it('exposes sane default meta values', function () {
    expect(Items::make('Tags')->meta())->toMatchArray([
        'chips' => false,
        'draggable' => false,
        'inputType' => 'text',
        'addButtonPosition' => 'bottom',
        'maxHeight' => null,
        'suggestions' => [],
        'min' => null,
        'max' => null,
        'addButtonLabel' => null,
        'deleteButtonLabel' => null,
        'hideAddButton' => false,
        'indexAsChips' => false,
        'indexAsList' => false,
        'detailsAsTotal' => false,
    ]);
});

it('toggles chips mode', function () {
    expect(Items::make('Tags')->chips()->meta()['chips'])->toBeTrue()
        ->and(Items::make('Tags')->chips(false)->meta()['chips'])->toBeFalse();
});

it('toggles draggable mode', function () {
    expect(Items::make('Tags')->draggable()->meta()['draggable'])->toBeTrue()
        ->and(Items::make('Tags')->draggable(false)->meta()['draggable'])->toBeFalse();
});

it('sets the input type', function () {
    expect(Items::make('Tags')->inputType('number')->meta()['inputType'])->toBe('number');
});

it('sets the placeholder (native Nova property)', function () {
    expect(Items::make('Tags')->placeholder('Add a tag')->placeholder)->toBe('Add a tag');
});

it('toggles full width (native Nova property)', function () {
    expect(Items::make('Tags')->fullWidth()->fullWidth)->toBeTrue();
});

it('sets the max height', function () {
    expect(Items::make('Tags')->maxHeight(300)->meta()['maxHeight'])->toBe(300);
});

it('sets predefined suggestions', function () {
    expect(Items::make('Tags')->suggestions(['php', 'js'])->meta()['suggestions'])->toBe(['php', 'js']);
});

it('sets the minimum number of items', function () {
    expect(Items::make('Tags')->min(2)->meta()['min'])->toBe(2);
});

it('sets the maximum number of items', function () {
    expect(Items::make('Tags')->max(5)->meta()['max'])->toBe(5);
});

it('sets the add button label', function () {
    expect(Items::make('Tags')->addButtonLabel('Add a tag')->meta()['addButtonLabel'])->toBe('Add a tag');
});

it('sets the delete button label', function () {
    expect(Items::make('Tags')->deleteButtonLabel('Remove')->meta()['deleteButtonLabel'])->toBe('Remove');
});

it('hides the add button', function () {
    expect(Items::make('Tags')->hideAddButton()->meta()['hideAddButton'])->toBeTrue()
        ->and(Items::make('Tags')->hideAddButton(false)->meta()['hideAddButton'])->toBeFalse();
});

it('positions the add button', function () {
    expect(Items::make('Tags')->addButtonPosition('top')->meta()['addButtonPosition'])->toBe('top')
        ->and(Items::make('Tags')->addButtonPosition('bottom')->meta()['addButtonPosition'])->toBe('bottom');
});

it('rejects an invalid add button position', function () {
    Items::make('Tags')->addButtonPosition('left');
})->throws(InvalidArgumentException::class);

it('renders the index as chips', function () {
    expect(Items::make('Tags')->indexAsChips()->meta()['indexAsChips'])->toBeTrue()
        ->and(Items::make('Tags')->indexAsChips(false)->meta()['indexAsChips'])->toBeFalse();
});

it('renders the index as a list', function () {
    expect(Items::make('Tags')->indexAsList()->meta()['indexAsList'])->toBeTrue()
        ->and(Items::make('Tags')->indexAsList(false)->meta()['indexAsList'])->toBeFalse();
});

it('renders the detail as a total', function () {
    expect(Items::make('Tags')->detailsAsTotal()->meta()['detailsAsTotal'])->toBeTrue()
        ->and(Items::make('Tags')->detailsAsTotal(false)->meta()['detailsAsTotal'])->toBeFalse();
});

it('provides a fluent interface', function () {
    $field = Items::make('Tags');

    expect($field->chips())->toBe($field)
        ->and($field->draggable())->toBe($field)
        ->and($field->inputType('text'))->toBe($field)
        ->and($field->maxHeight(100))->toBe($field)
        ->and($field->suggestions([]))->toBe($field)
        ->and($field->min(1))->toBe($field)
        ->and($field->max(3))->toBe($field)
        ->and($field->addButtonLabel('a'))->toBe($field)
        ->and($field->deleteButtonLabel('d'))->toBe($field)
        ->and($field->hideAddButton())->toBe($field)
        ->and($field->addButtonPosition('top'))->toBe($field)
        ->and($field->indexAsChips())->toBe($field)
        ->and($field->indexAsList())->toBe($field)
        ->and($field->detailsAsTotal())->toBe($field);
});
