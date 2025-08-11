<?php

namespace Admin\Orchid\Models\Order;

use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Order extends \Order\Infrastructure\ORM\OrderAR
{
    use AsSource;
    use Filterable;
}
