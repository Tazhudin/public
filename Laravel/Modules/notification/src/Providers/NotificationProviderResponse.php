<?php

namespace Notification\Providers;

readonly class NotificationProviderResponse implements \JsonSerializable
{
    public function __construct(
        public \JsonSerializable $response
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return $this->response->jsonSerialize();
    }
}
