<?php

namespace Notification\Api;

use Library\ValueObject\Phone;

interface CustomerPhoneProvider
{
    public function getCustomerPhone(string $customerId): ?Phone;
}
