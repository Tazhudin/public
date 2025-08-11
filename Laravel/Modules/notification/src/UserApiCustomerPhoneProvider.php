<?php

namespace Notification;

use Library\ValueObject\Phone;
use Notification\Api\CustomerPhoneProvider;
use User\Api\UserApi;

readonly class UserApiCustomerPhoneProvider implements CustomerPhoneProvider
{
    public function __construct(
        private UserApi $userApi
    ) {
    }

    public function getCustomerPhone(string $customerId): ?Phone
    {
        $phone = $this->userApi->getById($customerId)?->phone;

        if ($phone == null) {
            return null;
        }

        return Phone::fromString($phone);
    }
}
