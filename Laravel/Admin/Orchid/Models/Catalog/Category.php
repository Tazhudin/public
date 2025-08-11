<?php

namespace Admin\Orchid\Models\Catalog;

use Admin\Orchid\Filters\IdFilter;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
use Orchid\Screen\AsSource;

class Category extends \Catalog\Infrastructure\Repository\Db\Model\Category
{
    use AsSource;
    use Filterable;

    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    protected $table = 'catalog_categories';

    protected $fillable = [
        'name',
        'code',
        'sort',
        'active',
        'image',
        'image_main_page',
        'image_bg_color',
        'image_desk_large',
        'image_desk_medium',
        'image_desk_small',
        'image_mob_large',
        'image_mob_small',
        'image_app_large',
        'image_app_small',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true)
            ->orderBy('sort')
            ->orderBy('name');
    }

    protected $allowedFilters = [
        'id' => IdFilter::class,
        'code' => Ilike::class,
        'name' => Ilike::class,
    ];

    protected $allowedSorts = [
        'name',
        'code',
        'sort',
        'created_at',
        'updated_at',
    ];
}
