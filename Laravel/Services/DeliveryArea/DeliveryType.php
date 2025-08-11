<?php

namespace Api\Models\DeliveryArea;

use Api\Casts\DeliveryPriceCast;
use Api\Casts\DeliveryTimeCast;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $stock_id
 */
class DeliveryType extends Model
{
    use HasUuids;

    public const string EXPRESS = "express";
    public const string DELAYED = "delayed";
    public const int EXPRESS_DEFAULT_PRICE = 49;
    public const int EXPRESS_DEFAULT_MIN_SUM_FOR_FREE_DELIVERY = 500;

    protected $table = 'delivery_type';
    protected $appends = ['delivery_time'];
    protected $casts = [
        'price' => DeliveryPriceCast::class,
        'delivery_time' => DeliveryTimeCast::class
    ];
    public $timestamps = false;

    public function deliveryArea(): BelongsToMany
    {
        return $this->belongsToMany(DeliveryArea::class, 'delivery_area_delivery_type', 'delivery_type_id');
    }
}
