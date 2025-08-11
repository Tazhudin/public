<?php

namespace Notification\Providers\Push;

use Notification\Api\RequestModel\PushMessageRequestModel;
use Notification\Providers\NotificationProviderResponse;

interface PushProvider
{
    /**
     * @throws \Exception
     */
    public function send(string $fcmToken, PushMessageRequestModel $message): NotificationProviderResponse;
}
