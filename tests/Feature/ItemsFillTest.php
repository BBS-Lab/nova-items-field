<?php

declare(strict_types=1);

use BBSLab\NovaItemsField\Items;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * A cast-less Eloquent model so that the assertions exercise the field's own
 * JSON decoding rather than an attribute cast.
 */
function itemsModel(): Model
{
    return new class extends Model
    {
        protected $guarded = [];
    };
}

function novaPost(array $data): NovaRequest
{
    return NovaRequest::create('/', 'POST', $data);
}

it('fills the model by decoding the submitted JSON string', function () {
    $model = itemsModel();

    Items::make('Tags')->fill(novaPost(['tags' => '["php","laravel"]']), $model);

    expect($model->tags)->toBe(['php', 'laravel']);
});

it('fills null when the submitted value is an empty string', function () {
    $model = itemsModel();
    $model->tags = ['stale'];

    Items::make('Tags')->fill(novaPost(['tags' => '']), $model);

    expect($model->tags)->toBeNull();
});

it('leaves the model untouched when the attribute is absent from the request', function () {
    $model = itemsModel();
    $model->tags = ['keep'];

    Items::make('Tags')->fill(novaPost([]), $model);

    expect($model->tags)->toBe(['keep']);
});

it('keeps only scalar items and drops nested structures', function () {
    $model = itemsModel();

    Items::make('Tags')->fill(novaPost(['tags' => '["good",["nested"],{"k":"v"},42,"ok"]']), $model);

    expect($model->tags)->toBe(['good', 42, 'ok']);
});

it('respects a custom fillUsing callback', function () {
    $model = itemsModel();

    Items::make('Tags')
        ->fillUsing(function ($request, $model, $attribute) {
            $model->{$attribute} = ['custom'];
        })
        ->fill(novaPost(['tags' => '["ignored"]']), $model);

    expect($model->tags)->toBe(['custom']);
});
