<?php

namespace Notification\Api;

use Notification\Api\RequestModel\PushTokenRequestModel;
use Notification\Model\PushToken;
use Notification\Persist\PushTokenRepository;

readonly class UpdatePushTokenUseCase
{
    public function __construct(
        private PushTokenRepository $pushTokenRepository
    ) {
    }

    public function update(PushTokenRequestModel $pushTokenRequestModel): void
    {
        $pushToken = new PushToken();
        $pushToken->token = $pushTokenRequestModel->token;
        $pushToken->user_id = $pushTokenRequestModel->customerId;
        $pushToken->device_id = $pushTokenRequestModel->deviceId;

        $this->pushTokenRepository->save($pushToken);
    }
}
