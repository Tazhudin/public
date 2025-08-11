<?php

namespace Admin\Orchid\Models\Catalog;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Types\Ilike;

class CategoryShow extends Model
{
    use HasUuids;

    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    protected $table = 'catalog_category_show_view';

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true)
            ->orderBy('sort')
            ->orderBy('name');
    }

    protected $allowedFilters = [
        'name' => Ilike::class,
    ];
}
