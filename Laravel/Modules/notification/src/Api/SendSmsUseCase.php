<?php

namespace Notification\Api;

use Illuminate\Log\Logger;
use Library\Helper\PrepareLogContextProcessor\Raw;
use Notification\Model\Notification;
use Notification\Model\NotificationStatus;
use Notification\Model\NotificationType;
use Notification\Persist\NotificationRepository;
use Notification\Providers\NotificationProviderResponse;
use Notification\Providers\Sms\SmsProvider;

readonly class SendSmsUseCase
{
    private Logger $logger;

    public function __construct(
        private SmsProvider $smsProvider,
        private CustomerPhoneProvider $customerPhoneProvider,
        private NotificationRepository $notificationRepository,
        Logger $logger
    ) {
        $this->logger = $logger->withContext([
            'handler' => new Raw('sms-sender'),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function send(string $customerId, string $message): void
    {
        $this->logger->withContext(['customer_id' => new Raw($customerId)]);
        $phone = $this->customerPhoneProvider->getCustomerPhone($customerId);

        if ($phone == null) {
            $this->logger->warning('Не найден номер телефона');
            throw new \Exception('Номер пользователя не найден');
        }

        $notification = new Notification([
            'customer_id' => $customerId,
            'type' => NotificationType::SMS,
            'message' => $message,
            'payload' => [
                'phone' => $phone->toString()
            ],
        ]);

        try {
            $response = $this->smsProvider->send([$phone], $message);

            $notification->status = NotificationStatus::SENT;
            $notification->provider_response = $response;
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage());

            $notification->status = NotificationStatus::FAILED;
            $notification->provider_response = new NotificationProviderResponse(
                collect(['error' => $exception->getMessage()]),
            );
        }

        $this->notificationRepository->save($notification);
    }
}
