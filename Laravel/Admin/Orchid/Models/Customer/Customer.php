<?php

namespace Admin\Orchid\Models\Customer;

use Orchid\Filters\Filter;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Screen\AsSource;
use User\UserAR;

class Customer extends UserAR
{
    use AsSource;
    use Filterable;

    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    protected $keyType = 'string';

    public function getDisplayDescription(bool $short = false): string
    {
        if ($short) {
            $fullName = "$this->name";
        } else {
            $fullName = "$this->second_name $this->name";
        }

        if (strlen(trim($fullName)) > 0) {
            return $fullName;
        }

        return $this->phone_number;
    }

    /**
     * @var array<string, Filter>
     */
    protected array $allowedFilters = [
        'phone_number' => Like::class
    ];
}
