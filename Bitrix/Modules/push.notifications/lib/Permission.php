<?php

namespace PushNotifications;

class Permission
{
    /**
     * @return bool
     */
    public static function canWrite(): bool
    {
        global $APPLICATION;
        return $APPLICATION->GetGroupRight('push.notifications') >= 'W';
    }

    /**
     * @return bool
     */
    public static function canRead(): bool
    {
        global $APPLICATION;
        return $APPLICATION->GetGroupRight('push.notifications') >= 'R';
    }
}
