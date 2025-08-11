<?php

namespace Notification\Api\RequestModel;

class PushTokenRequestModel
{
    public function __construct(
        public string $token,
        public string $customerId,
        public ?string $deviceId = null,
    ) {
    }
}
