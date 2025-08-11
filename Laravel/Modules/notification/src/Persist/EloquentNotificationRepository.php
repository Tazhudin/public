<?php

namespace Notification\Persist;

use Illuminate\Support\Carbon;
use Notification\Model\Notification;

class EloquentNotificationRepository implements NotificationRepository
{
    public function save(Notification $notification): void
    {
        $notification->save();
    }

    public function checkDuplicate(string $hash): bool
    {
        return Notification::where('hash', $hash)
            ->where('created_at', '>', Carbon::now()->subMinute())
            ->first() || false;
    }
}
