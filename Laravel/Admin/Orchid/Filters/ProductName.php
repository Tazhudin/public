<?php

declare(strict_types=1);

namespace Admin\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\BaseHttpEloquentFilter;

class ProductName extends BaseHttpEloquentFilter
{
    public function run(Builder $builder): Builder
    {
        return $builder->whereHas('product', function ($query): void {
            $query->where('name', 'ILIKE', '%' . $this->getHttpValue() . '%');
        });
    }
}
