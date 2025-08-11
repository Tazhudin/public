<?php

namespace Notification\Persist;

use Notification\Model\Notification;

interface NotificationRepository
{
    public function save(Notification $notification): void;

    public function checkDuplicate(string $hash): bool;
}
