<?php

namespace Admin\Orchid\Metrics;

use Admin\Orchid\Models\Catalog\Product;
use Illuminate\Database\Eloquent\Builder;

class AvailableProductsCountQuery
{
    public function count(): int
    {
        return Product::isExistImage()
            ->whereHas('category')
            ->whereHas('priceAndStock', function (Builder $query): void {
                $query->where('stock', '>', 0)
                    ->where(function ($query) {
                        return $query
                            ->orWhere('discount_price', '>', 0)
                            ->orWhere('price', '>', 0);
                    });
            })->count();
    }
}
