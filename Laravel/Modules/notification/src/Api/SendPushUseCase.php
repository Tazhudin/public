<?php

namespace Notification\Api;

use Illuminate\Log\Logger;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Library\Helper\PrepareLogContextProcessor\Raw;
use Notification\Api\RequestModel\PushMessageRequestModel;
use Notification\Model\Notification;
use Notification\Model\NotificationStatus;
use Notification\Model\NotificationType;
use Notification\Persist\NotificationRepository;
use Notification\Persist\PushTokenRepository;
use Notification\Providers\NotificationProviderResponse;
use Notification\Providers\Push\PushProvider;
use Order\Infrastructure\Exception\DuplicatePushException;

readonly class SendPushUseCase
{
    private Logger $logger;

    public function __construct(
        private PushProvider $pushProvider,
        private PushTokenRepository $pushTokenRepository,
        private NotificationRepository $notificationRepository,
        Logger $logger
    ) {
        $this->logger = $logger->withContext([
            'handler' => new Raw('push-sender')
        ]);
    }


    /**
     * @throws DuplicatePushException
     */
    public function send(string $clientId, PushMessageRequestModel $message): bool
    {
        $this->logger->withContext(['customer_id' => new Raw($clientId)]);
        $pushTokens = $this->pushTokenRepository->getCustomerPushTokens($clientId);
        if ($this->notificationRepository->checkDuplicate($message->hash())) {
            $this->logger->error('Пуш уведомление | Дублирование уведомления: ' . $message->message);
            throw new DuplicatePushException();
        }

        if ($pushTokens->count() == 0) {
            $this->logger->warning('Пуш уведомление | Не найден токен для клиента');
            return false;
        }

        $sendCount = 0;
        foreach ($pushTokens as $pushToken) {
            $notification = new Notification([
                'hash' => $message->hash(),
                'customer_id' => $clientId,
                'type' => NotificationType::PUSH,
                'message' => $message->message,
                'payload' => [
                    'title' => $message->title,
                    'push_token' => Str::of($pushToken->token)
                        ->mask('*', 0, 5)
                        ->mask('*', -5, 5)
                        ->toString(),
                    'data' => $message->data
                ],
            ]);

            try {
                $response = $this->pushProvider->send($pushToken->token, $message);

                $notification->status = NotificationStatus::SENT;
                $notification->provider_response = $response;

                $sendCount += 1;
            } catch (NotFound $exception) {
                $this->logger->error('Пуш уведомление | ошибка: ' . $exception->getMessage());

                $notification->status = NotificationStatus::FAILED;
                $notification->provider_response = new NotificationProviderResponse(
                    collect(['error' => $exception->getMessage()]),
                );
                $pushToken->delete();
            }

            $this->notificationRepository->save($notification);
        }

        if ($sendCount == 0) {
            $this->logger->warning('Пуш уведомление | Уведомление не было отправлено');
            return false;
        }

        return true;
    }
}
