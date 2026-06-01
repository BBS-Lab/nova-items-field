<?php

declare(strict_types=1);

namespace Workbench\App\Nova;

use BBSLab\NovaItemsField\Items;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Post extends Resource
{
    /**
     * @var class-string<\Workbench\App\Models\Post>
     */
    public static $model = \Workbench\App\Models\Post::class;

    public static $title = 'title';

    /**
     * @var array<int, string>
     */
    public static $search = [
        'id', 'title',
    ];

    /**
     * Showcase every display mode and option of the Items field.
     *
     * @return array<int, Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Title')
                ->sortable()
                ->rules('required', 'max:255'),

            Boolean::make('Has tags', 'has_tags')
                ->sortable(),

            // Chips mode: short values, datalist suggestions, chips on index.
            Items::make('Tags')
                ->chips()
                ->suggestions(['laravel', 'nova', 'vue', 'vite', 'php'])
                ->indexAsChips()
                ->rules('max:8')
                ->itemRules('string', 'max:20')
                ->help('Press Enter to add a tag.')
                ->dependsOn('has_tags', function (Items $field, NovaRequest $request, FormData $formData) {
                    if (! $formData->boolean('has_tags')) {
                        $field->hide();
                    }
                }),

            // Structured rows: drag to reorder, bounded length, shown as a list on detail.
            Items::make('Steps')
                ->draggable()
                ->min(1)
                ->max(10)
                ->placeholder('Describe a step…')
                ->help('Drag the handle to reorder the steps.'),

            // Per-item validation with a dedicated input type and truncated index.
            Items::make('Emails')
                ->inputType('email')
                ->itemRules('email')
                ->rules('max:5')
                ->indexAsList()
                ->deleteButtonLabel('Remove'),
        ];
    }
}
