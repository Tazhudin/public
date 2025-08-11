<?php

namespace Api\Models\DeliveryArea;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Library\ValueObject\Address;
use Api\Infrastructure\Security\AuthToken;
use User\UserAddress;

class UsersDeliveryArea extends Model
{
    protected $table = 'user_delivery_area';

    protected $keyType = 'string';

    public $timestamps = false;

    protected $primaryKey = 'token';
    public $incrementing = false;

    protected $fillable = ['token', 'delivery_area_id', 'address', 'address_id'];

    protected $casts = [
        'address' => Address::class
    ];

    protected function casts(): array
    {
        return [
            'address' => Address::class
        ];
    }

    public function deliveryArea(): BelongsTo
    {
        return $this->belongsTo(DeliveryArea::class, 'delivery_area_id', 'id');
    }

    public function address(): Address
    {
        if ($this->address_id) {
            $address = $this->belongsTo(UserAddress::class, 'address_id', 'id');
            return $address->first()->address;
        }

        return $this->address;
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(AuthToken::class, 'token', 'token');
    }
}
