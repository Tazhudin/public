<?php

Bitrix\Main\Loader::registerAutoloadClasses(
    'push.notifications',
    [
        '\\PushNotifications\\Permission' => 'lib/Permission.php',
        '\\PushNotifications\\Helper' => 'lib/Helper.php',
    ]
);
