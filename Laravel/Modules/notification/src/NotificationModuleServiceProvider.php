<?php

namespace Notification;

use Illuminate\Foundation\Application;
use Illuminate\Log\Logger;
use Illuminate\Support\ServiceProvider;
use Notification\Api\CustomerPhoneProvider;
use Notification\Persist\EloquentNotificationRepository;
use Notification\Persist\EloquentPushTokenRepository;
use Notification\Persist\NotificationRepository;
use Notification\Persist\PushTokenRepository;
use Notification\Providers\Push\Firebase;
use Notification\Providers\Push\PushProvider;
use Notification\Providers\Sms\SmsProvider;
use Notification\Providers\Sms\SmsRu;

class NotificationModuleServiceProvider extends ServiceProvider
{
    /**
     * @var array<string, string>
     */
    public array $singletons = [
        NotificationRepository::class => EloquentNotificationRepository::class,
        PushTokenRepository::class => EloquentPushTokenRepository::class,
        CustomerPhoneProvider::class => UserApiCustomerPhoneProvider::class,
    ];

    public function boot(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/config/services.php', 'notification.services');

        $this->app->singleton(SmsProvider::class, function () {
            $endPoint = config('notification.services.sms_ru.endpoint');
            $api = config('notification.services.sms_ru.api_id');

            return new SmsRu($endPoint, $api);
        });

        $this->app->singleton(PushProvider::class, function (Application $app) {
            $credentialPath = config('notification.services.firebase.credentials');

            return new Firebase($credentialPath, $app->get(Logger::class));
        });
    }
}
