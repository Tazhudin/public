<?php

namespace Admin\Orchid\Rules\Delivery;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Validator;

class CoordinateValidator implements ValidationRule
{
    /**
     * The validator instance.
     *
     * @var Validator
     */
    protected Validator $validator;

    /**
     * Run the validation rule.
     *
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!json_validate($value)) {
            $fail('Неверный формат json.');
            return;
        }
        $inputData = json_decode($value, true);
        if (current($inputData) !== end($inputData)) {
            $fail('Полигон не закрыт.');
            return;
        }
        reset($inputData);

        foreach ($inputData as $coordinate) {
            if (!isset($coordinate['latitude']) || !isset($coordinate['longitude'])) {
                $fail("Неверные координаты");
                return;
            }

            if (
                !preg_match(
                    '/^-?(90|[1-8][0-9][.][0-9]{1,20}|[0-9][.][0-9]{1,20})$/',
                    $coordinate['latitude']
                )
            ) {
                $fail("Широта {$coordinate['latitude']} не соответствует формату или выходит за пределы допустимого диапазона.");
                return;
            }

            if (
                !preg_match(
                    '/^-?(180|1[1-7][0-9][.][0-9]{1,20}|[1-9][0-9][.][0-9]{1,20}|[0-9][.][0-9]{1,20})$/',
                    $coordinate['longitude']
                )
            ) {
                $fail("Долгота {$coordinate['longitude']} не соответствует формату или выходит за пределы допустимого диапазона.");
                return;
            }
        }
    }
}
