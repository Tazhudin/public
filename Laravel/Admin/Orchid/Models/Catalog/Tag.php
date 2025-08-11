<?php

namespace Admin\Orchid\Models\Catalog;

use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
use Orchid\Screen\AsSource;

class Tag extends \Catalog\Infrastructure\Repository\Db\Model\Tag
{
    use AsSource;
    use Filterable;

    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    protected $fillable = [
        'name',
        'color',
    ];

    protected $allowedFilters = [
        'name' => Ilike::class,
        'color' => Ilike::class,
    ];

    protected $allowedSorts = [
        'name',
        'color',
        'created_at',
        'updated_at',
    ];
}
