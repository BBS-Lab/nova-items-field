<?php

declare(strict_types=1);

namespace BBSLab\NovaItemsField\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class ArrayItemsRule implements ValidationRule
{
    /**
     * @param  array<int, mixed>  $arrayRules  Rules applied to the list as a whole.
     * @param  array<int, mixed>  $itemRules  Rules applied to every individual item.
     */
    public function __construct(
        protected array $arrayRules = [],
        protected array $itemRules = [],
        protected ?int $min = null,
        protected ?int $max = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $items = $this->decode($value);

        $payload = array_filter([
            'field' => $this->validateArray($attribute, $items),
            'items' => $this->validateItems($items),
        ]);

        if ($payload !== []) {
            $fail((string) json_encode($payload));
        }
    }

    /**
     * @return array<int, mixed>
     */
    protected function decode(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Validate the list as a whole and return the resulting messages.
     *
     * @param  array<int, mixed>  $items
     * @return array<int, string>
     */
    protected function validateArray(string $attribute, array $items): array
    {
        $rules = $this->arrayRules;

        if ($this->min !== null) {
            $rules[] = 'min:'.$this->min;
        }

        if ($this->max !== null) {
            $rules[] = 'max:'.$this->max;
        }

        if ($rules === []) {
            return [];
        }

        // Prepend the `array` rule so size constraints (min/max) report item
        // counts ("items") rather than string lengths ("characters").
        array_unshift($rules, 'array');

        return Validator::make([$attribute => $items], [$attribute => $rules])
            ->errors()
            ->all();
    }

    /**
     * Validate each item individually and return the messages keyed by index.
     *
     * @param  array<int, mixed>  $items
     * @return array<int, array<int, string>>
     */
    protected function validateItems(array $items): array
    {
        if ($this->itemRules === []) {
            return [];
        }

        $errors = [];

        foreach ($items as $index => $item) {
            $validator = Validator::make(['item' => $item], ['item' => $this->itemRules]);

            if ($validator->fails()) {
                $errors[$index] = $validator->errors()->all();
            }
        }

        return $errors;
    }
}
