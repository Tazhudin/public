<?php

namespace Api\Models\DeliveryArea;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $stock_id
 */
class DeliveryAreaDelivery extends Model
{
    use HasUuids;

    protected $table = 'delivery_area_delivery_type';
    public $timestamps = false;
}
