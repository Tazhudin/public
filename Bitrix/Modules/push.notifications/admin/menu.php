<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use PushNotifications\Permission;

Loc::loadMessages(__FILE__);
Loader::includeModule('push.notifications');

$aMenu[] = [
    'parent_menu' => 'global_menu_05ru',
    'section' => '05_push_notifications',
    'sort' => 10,
    'text' => 'Push-уведомления',
    'title' => '05ru',
    'url' => '',
    'icon' => '',
    'page_icon' => 'push_notifications_icon',
    'items_id' => 'push_notifications_05ru',
    'items' => [
        [
            'text' => 'Настройки push-уведомлений',
            'title' => 'Настройки push-уведомлений',
            'url' => 'push-notifications-settings.php?lang=' . LANGUAGE_ID,
            'more_url' => [
                'push-notifications-settings.php',
            ]
        ],
        [
            'text' => 'Журнал записей push-уведомлений',
            'title' => 'Журнал записей push-уведомлений',
            'url' => 'push-notifications-log-list.php?lang=' . LANGUAGE_ID,
            'more_url' => [
                'push-notifications-log-list.php'
            ]
        ],
        [
            'text' => 'Тестовая отправка push-уведомлений',
            'title' => 'Тестовая отправка push-уведомлений',
            'url' => 'push-notifications-testing-send.php?lang=' . LANGUAGE_ID,
            'more_url' => [
                'push-notifications-testing-send.php'
            ]
        ],
    ]
];
return $aMenu;
