<?php

namespace Admin\Orchid\Models\Catalog;

use Admin\Observers\ProductObserver;
use Admin\Orchid\Filters\CategoryName;
use Admin\Orchid\Filters\Code;
use Admin\Orchid\Filters\IdFilter;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Library\Db\WithUnfilledAttributes;
use Library\ValueObject\Status;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
use Orchid\Screen\AsSource;

#[ObservedBy(ProductObserver::class)]
class Product extends \Catalog\Infrastructure\Repository\Db\Model\Product
{
    use AsSource;
    use Filterable;
    use WithUnfilledAttributes;

    // phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
    protected $fillable = [
        'name',
        'category_id',
        'order_position_limit',
        'is_new',
        'is_active',
        'popularity'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'imported_at' => 'datetime',
    ];

    protected $allowedFilters = [
        'id' => IdFilter::class,
        'code' => Code::class,
        'name' => Ilike::class,
        'category_id' => CategoryName::class,
    ];

    protected $allowedSorts = [
        'code',
        'name',
        'popularity',
        'created_at',
        'updated_at',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id')->where('deleted', false);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(ProductPropertyValue::class, 'product_id', 'id');
    }

    public function priceAndStock(): HasMany
    {
        return $this->hasMany(ProductPriceAndStock::class, 'product_id', 'id');
    }

    public function reserve(): HasMany
    {
        return $this->hasMany(ProductReserve::class, 'product_id', 'id');
    }

    public function showInCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            CategoryShow::class,
            'catalog_product_show_in_categories',
            'product_id',
            'category_id'
        );
    }

    public static function recalculatePopularity(Status $status = Status::Completed, int $days = 90): void
    {
        DB::statement("
            WITH sales AS (
                SELECT
                    (item->>'product_id')::uuid AS product_id,
                    COUNT(*) AS sales_count
                FROM order__order, jsonb_array_elements(order__order.items) AS item
                WHERE created_at >= ? AND status = ?
                GROUP BY (item->>'product_id')::uuid
            )
            UPDATE catalog_products
            SET popularity = COALESCE((
                SELECT sales.sales_count
                FROM sales
                WHERE sales.product_id = catalog_products.id
            ), 0)
        ", [now()->subDays($days)->format('Y-m-d H:i:s'), $status->value]);
    }
}
