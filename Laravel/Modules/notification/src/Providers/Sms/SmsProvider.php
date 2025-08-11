<?php

namespace Notification\Providers\Sms;

use Notification\Providers\NotificationProviderResponse;

interface SmsProvider
{
    /**
     * @param array<string> $phoneNumbers
     * @throws \Exception
     */
    public function send(array $phoneNumbers, string $message): NotificationProviderResponse;
}
