<?php

namespace Notification\Persist;

use Illuminate\Support\Collection;
use Notification\Model\PushToken;

interface PushTokenRepository
{
    public function save(PushToken $pushToken): void;

    /**
     * @return Collection<PushToken>
     */
    public function getCustomerPushTokens(string $customerId): Collection;
}
