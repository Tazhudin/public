<?php

namespace Admin\Orchid\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Screen\AsSource;

//phpcs:disable SlevomatCodingStandard
class ProductShowInCategories extends Model
{
    use AsSource;

    protected $table = 'catalog_product_show_in_categories';

    public function categories(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id', 'category_id');
    }
}
