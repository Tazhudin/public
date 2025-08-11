<?php

namespace Admin\Orchid\Models\Customer;

use Api\Infrastructure\Security\AuthToken;
use Api\Models\DeliveryArea\UsersDeliveryArea;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use User\UserAddress;

class CustomerAddress extends UserAddress
{
    use Filterable;
    use AsSource;

    public function isSelected(): bool
    {
        $actualUserDevicesTokens = AuthToken::select('token')
            ->where('user_id', $this->customer_id)
            ->whereIn('token', function ($query) {
                $query->selectRaw('DISTINCT ON (client_app) token')
                    ->from('auth_token')
                    ->where('user_id', $this->customer_id)
                    ->orderBy('client_app')
                    ->orderByDesc('expires_time');
            })
            ->pluck('token');

        return $this->deliveryArea()
            ->whereIn('token', $actualUserDevicesTokens)
            ->exists();
    }

    public function deliveryArea(): HasOne
    {
        return $this->hasOne(UsersDeliveryArea::class, 'address_id', 'id');
    }
}
