<?php

namespace Admin\Orchid\Models\Customer;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Notification\Model\Notification;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class CustomerNotification extends Notification
{
    use AsSource;
    use Filterable;

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    /**
     * @var string[]
     */
    protected array $allowedSorts = [
        'created_at'
    ];
}
