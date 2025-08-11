<?php

namespace Admin\Orchid\Rules\Delivery;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class WorkTimeValidation implements ValidationRule
{
    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail("Неверное значение рабочего времени");
        }

        foreach ($value as $day => $data) {
            if (empty($data["time"])) {
                $fail("Не заполнено рабочее время для {$day}");
            }

            $timeRange = $data["time"];
            if (!$this->isValidTimeRange((string)$timeRange)) {
                $fail("Неверный формат рабочего времени для {$day}. Используйте HH:MM-HH:MM.");
            }
        }
    }

    /**
     * @param string $timeRange
     * @return bool
     */
    private function isValidTimeRange(string $timeRange): bool
    {
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d-(?:[01]\d|2[0-3]):[0-5]\d$/', $timeRange)) {
            return false;
        }

        [$startTimeStr, $endTimeStr] = explode('-', $timeRange);

        $startTime = Carbon::createFromFormat('H:i', $startTimeStr);
        $endTime = Carbon::createFromFormat('H:i', $endTimeStr);

        return $startTime < $endTime;
    }
}
