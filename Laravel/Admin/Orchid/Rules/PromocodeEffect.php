<?php

namespace Admin\Orchid\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Order\Domain\Basket\Calculator\Promocode\Effect\PromoCodeEffectEnum;
use Throwable;

class PromocodeEffect implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            array_map(fn($row) => PromoCodeEffectEnum::fromCode($row['code'])::createFromAttributes(
                json_decode($row['attributes'], true)
            ), $value);
        } catch (Throwable $e) {
            $fail('Аттрибут :attribute содержит некорректные данные: ' . $e->getMessage());
        }
    }
}
