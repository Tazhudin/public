<?php

namespace Admin\Orchid\Models\Catalog;

use Admin\Models\PriceAndStock\Stock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Orchid\Screen\AsSource;

//phpcs:disable SlevomatCodingStandard
class ProductReserve extends Model
{
    use AsSource;

    protected $table = 'price_and_stock_reserve';

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class, 'id', 'stock_id');
    }
}
