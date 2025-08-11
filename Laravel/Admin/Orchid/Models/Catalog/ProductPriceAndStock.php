<?php

namespace Admin\Orchid\Models\Catalog;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Screen\AsSource;

class ProductPriceAndStock extends \Catalog\Infrastructure\Repository\Db\Model\ProductPriceAndStock
{
    use AsSource;

    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    protected $with = [
        'stockrel',
    ];

    public function stockrel(): BelongsTo
    {
        return $this->belongsTo(\Admin\Models\PriceAndStock\Stock::class, 'stock_id', 'id');
    }
}
