<?php

declare(strict_types=1);

namespace Admin\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\BaseHttpEloquentFilter;

class Code extends BaseHttpEloquentFilter
{
    public function run(Builder $builder): Builder
    {
        return $builder->where($this->column, (int) $this->getHttpValue());
    }
}
