<?php

namespace Admin\Orchid\Rules\Delivery;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DeliveryPriceValidation implements ValidationRule
{
    /**
     * Валидация стоимости доставки с учётом пересекающихся диапазонов.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail("Неверное значение для {$attribute}");
            return;
        }

        $previousOrderPrice = null;

        foreach ($value as $index => $range) {
            if (empty($range['order_price']) || empty($range['delivery_price'])) {
                $fail("Отсутствуют необходимые значения для диапазона");
            }

            $orderPrice = $range['order_price'];
            $deliveryPrice = $range['delivery_price'];

            if (!is_numeric($orderPrice) || !is_numeric($deliveryPrice)) {
                $fail("Значения 'Стоимость заказа' и 'Стоимость доставки' должны быть числовыми");
                continue;
            }

            if ($previousOrderPrice !== null && $orderPrice <= $previousOrderPrice) {
                $fail("Значения 'Стоимость заказа' должны возрастать последовательно.");
                continue;
            }

            $previousOrderPrice = $orderPrice;
        }
    }
}
