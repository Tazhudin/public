<?php

namespace Notification\Providers\Push;

use Illuminate\Log\Logger;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Library\Helper\PrepareLogContextProcessor\Raw;
use Notification\Api\RequestModel\PushMessageRequestModel;
use Notification\Providers\NotificationProviderResponse;

readonly class Firebase implements PushProvider
{
    private Messaging $cloudMessaging;

    public function __construct(
        string $credentialPath,
        private Logger $logger
    ) {
        $factory = (new Factory())->withServiceAccount($credentialPath);
        $this->cloudMessaging = $factory->createMessaging();
    }

    /**
     * @throws FirebaseException
     */
    public function send(string $fcmToken, PushMessageRequestModel $message): NotificationProviderResponse
    {
        $notification = Notification::create(
            $message->title,
            $message->message,
            $message->image
        );

        $cloudMessage = CloudMessage::new()->toToken($fcmToken)
            ->withNotification($notification)
            ->withApnsConfig(
                ApnsConfig::new()
                    ->withApsField('mutable-content', 1)
                    ->withDefaultSound()
            )->withAndroidConfig(
                AndroidConfig::fromArray([
                    'notification' => [
                        'icon' => 'ic_ds_notification',
                    ]
                ])
            );

        if (!empty($message->data)) {
            $cloudMessage = $cloudMessage->withData($message->data);
        }

        $report = $this->cloudMessaging->send($cloudMessage);
        $this->logger->info('Отправка в firebase | Пуш отправлен', [
            'request' => $cloudMessage->jsonSerialize(),
            'response' => $report,
            'handler' => new Raw('notification-send'),
        ]);

        return new NotificationProviderResponse(collect($report));
    }
}
