<?php

namespace Admin\Orchid\Models\Catalog;

use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;
use Admin\Orchid\Filters\ProductName;

class NewProduct extends \Catalog\Infrastructure\Repository\Db\Model\NewProduct
{
    use AsSource;
    use Filterable;

    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    protected $fillable = [
        'product_id',
        'novelty_to',
        'novelty_sort',
    ];

    protected $allowedFilters = [
        'product_id' => ProductName::class,
    ];

    protected $allowedSorts = [
        'novelty_to',
        'novelty_sort',
    ];
}
