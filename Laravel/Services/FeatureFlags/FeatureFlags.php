<?php

namespace Api\Models\FeatureFlags;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class FeatureFlags extends Model
{
    use HasUuids;

    protected $table = 'feature_flags';

    protected $fillable = ['name', 'state'];

    public $timestamps = false;

    public const array FEATURES = [
        [
            'name' => 'order.payment.can_by_pay',
            'state' => true
        ],
        [
            'name' => 'ordering.maybe_more',
            'state' => false
        ],
        [
            'name' => 'agent.notify_delivery_area_status',
            'state' => false
        ],
        [
            'name' => 'agent.notify_warehouse_status',
            'state' => false
        ],
        [
            'name' => 'agent.notify_products_without_images',
            'state' => false
        ]
    ];

    public static function isActive(string $featureName): bool
    {
        $defaultValue = Arr::first(self::FEATURES, function ($item) use ($featureName) {
            return $item['name'] === $featureName;
        })?->state ?? false;

        $flagFromStore = self::firstWhere(['name' => $featureName]);

        if ($flagFromStore != null) {
            $defaultValue = $flagFromStore->state;
        }

        return $defaultValue;
    }
}
