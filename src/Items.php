<?php

declare(strict_types=1);

namespace BBSLab\NovaItemsField;

use BBSLab\NovaItemsField\Rules\ArrayItemsRule;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Laravel\Nova\Fields\Copyable;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\SupportsDependentFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Support\Fluent;

class Items extends Field
{
    use Copyable;
    use SupportsDependentFields;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-items-field';

    /**
     * Whether the field value is rendered as raw HTML. Always false: this field
     * never renders HTML, but the flag is required by the Copyable trait guard.
     *
     * @var bool
     */
    public $asHtml = false;

    /**
     * The minimum number of items required.
     */
    protected ?int $minItems = null;

    /**
     * The maximum number of items allowed.
     */
    protected ?int $maxItems = null;

    /**
     * The validation rules applied to every individual item.
     *
     * @var array<int, mixed>
     */
    protected array $perItemRules = [];

    /**
     * @param  string  $name
     * @param  string|null  $attribute
     */
    public function __construct($name, $attribute = null, ?callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->withMeta([
            'chips' => config('nova-items-field.chips'),
            'draggable' => config('nova-items-field.draggable'),
            'inputType' => config('nova-items-field.input_type'),
            'addButtonPosition' => config('nova-items-field.add_button_position'),
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
    }

    /**
     * Hydrate the model attribute from the request by decoding the submitted
     * JSON payload into a PHP array (or null when empty).
     *
     * @param  Model|Fluent  $model
     */
    protected function fillAttributeFromRequest(NovaRequest $request, string $requestAttribute, object $model, string $attribute)
    {
        if (! $request->exists($requestAttribute)) {
            return;
        }

        $value = $request->input($requestAttribute);

        $decoded = $this->isValidNullValue($value) ? null : json_decode((string) $value, true);

        $this->fillModelWithData(
            $model,
            is_array($decoded) ? array_values(array_filter($decoded, 'is_scalar')) : null,
            $attribute
        );
    }

    /**
     * Render the items as chips/tags instead of structured rows.
     */
    public function chips(bool $chips = true): static
    {
        $this->withMeta(['chips' => $chips]);

        return $this;
    }

    /**
     * Allow drag-and-drop reordering of the items.
     */
    public function draggable(bool $draggable = true): static
    {
        $this->withMeta(['draggable' => $draggable]);

        return $this;
    }

    /**
     * Set the HTML input type used for each item.
     */
    public function inputType(string $type): static
    {
        $this->withMeta(['inputType' => $type]);

        return $this;
    }

    /**
     * Constrain the list to a scrollable max height (in pixels).
     */
    public function maxHeight(int $maxHeight): static
    {
        $this->withMeta(['maxHeight' => $maxHeight]);

        return $this;
    }

    /**
     * Provide predefined suggestions for autocompletion.
     *
     * @param  array<int, scalar>  $suggestions
     */
    public function suggestions(array $suggestions): static
    {
        $this->withMeta(['suggestions' => $suggestions]);

        return $this;
    }

    /**
     * Set the minimum number of items required.
     */
    public function min(int $min): static
    {
        $this->minItems = $min;
        $this->withMeta(['min' => $min]);

        return $this;
    }

    /**
     * Set the maximum number of items allowed.
     */
    public function max(int $max): static
    {
        $this->maxItems = $max;
        $this->withMeta(['max' => $max]);

        return $this;
    }

    /**
     * Set the validation rules applied to every individual item.
     *
     * @param  mixed  $rules
     */
    public function itemRules($rules): static
    {
        $this->perItemRules = is_array($rules) && func_num_args() === 1
            ? $rules
            : func_get_args();

        return $this;
    }

    /**
     * Customize the label of the "add" button.
     */
    public function addButtonLabel(string $label): static
    {
        $this->withMeta(['addButtonLabel' => $label]);

        return $this;
    }

    /**
     * Customize the label of the "delete" button.
     */
    public function deleteButtonLabel(string $label): static
    {
        $this->withMeta(['deleteButtonLabel' => $label]);

        return $this;
    }

    /**
     * Hide the "add" button entirely.
     */
    public function hideAddButton(bool $hide = true): static
    {
        $this->withMeta(['hideAddButton' => $hide]);

        return $this;
    }

    /**
     * Position the "add" control relative to the list.
     */
    public function addButtonPosition(string $position): static
    {
        if (! in_array($position, ['top', 'bottom'], true)) {
            throw new InvalidArgumentException(
                sprintf('The add button position must be "top" or "bottom", "%s" given.', $position)
            );
        }

        $this->withMeta(['addButtonPosition' => $position]);

        return $this;
    }

    /**
     * Render the index cell as overflowing chips instead of a count.
     */
    public function indexAsChips(bool $asChips = true): static
    {
        $this->withMeta(['indexAsChips' => $asChips]);

        return $this;
    }

    /**
     * Render the index cell as truncated comma-joined text instead of a count.
     */
    public function indexAsList(bool $asList = true): static
    {
        $this->withMeta(['indexAsList' => $asList]);

        return $this;
    }

    /**
     * Render the detail view as a count/summary instead of the full list.
     */
    public function detailsAsTotal(bool $asTotal = true): static
    {
        $this->withMeta(['detailsAsTotal' => $asTotal]);

        return $this;
    }

    /**
     * Prepare the field for JSON serialization, adding the localized labels the
     * front-end uses for the add/remove buttons, the placeholder and empty state.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'labels' => [
                'add' => $this->meta['addButtonLabel'] ?? trans('nova-items-field::items.add_item'),
                'remove' => $this->meta['deleteButtonLabel'] ?? trans('nova-items-field::items.remove'),
                'empty' => trans('nova-items-field::items.empty'),
                'placeholder' => $this->placeholder ?? trans('nova-items-field::items.placeholder'),
            ],
        ]);
    }

    /**
     * Get the validation rules for this field.
     *
     * @return array<string, array<int, ArrayItemsRule>>
     */
    public function getRules(NovaRequest $request): array
    {
        return $this->wrapRules(
            $this->resolveRuleSet($this->rules, $request)
        );
    }

    /**
     * Get the creation rules for this field.
     *
     * @return array<string, array<int, ArrayItemsRule>>
     */
    public function getCreationRules(NovaRequest $request): array
    {
        return $this->wrapRules(array_merge(
            $this->resolveRuleSet($this->rules, $request),
            $this->resolveRuleSet($this->creationRules, $request),
        ));
    }

    /**
     * Get the update rules for this field.
     *
     * @return array<string, array<int, ArrayItemsRule>>
     */
    public function getUpdateRules(NovaRequest $request): array
    {
        return $this->wrapRules(array_merge(
            $this->resolveRuleSet($this->rules, $request),
            $this->resolveRuleSet($this->updateRules, $request),
        ));
    }

    /**
     * Normalize a Nova rule set (callable or array) into a flat array.
     *
     * @param  mixed  $rules
     * @return array<int, mixed>
     */
    protected function resolveRuleSet($rules, NovaRequest $request): array
    {
        $resolved = is_callable($rules) ? call_user_func($rules, $request) : $rules;

        return array_values((array) $resolved);
    }

    /**
     * Wrap the resolved array-level rules and the per-item rules into a single
     * ArrayItemsRule keyed by the field attribute.
     *
     * @param  array<int, mixed>  $arrayRules
     * @return array<string, array<int, ArrayItemsRule>>
     */
    protected function wrapRules(array $arrayRules): array
    {
        return [
            $this->attribute => [
                new ArrayItemsRule($arrayRules, $this->perItemRules, $this->minItems, $this->maxItems),
            ],
        ];
    }
}
