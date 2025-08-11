<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\UI\Filter\Options;
use PushNotifications\Permission;
use Bitrix\Main\Localization\Loc;
use Dev05\Classes\EventLog\Entity\Notifications\Push;

global $APPLICATION;

Loc::loadMessages(__FILE__);
Loader::includeModule('push.notifications');
if (!Permission::canRead()) {
    $APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}
$obRequest = Application::getInstance()->getContext()->getRequest();
$gridId = 'PushNotificationLogsList';
$action = $obRequest->get('action') || $obRequest->get('action_button_' . $gridId) || $obRequest->get('grid_action');

$permissionWrite = Permission::canWrite();

if (!empty($action) && check_bitrix_sessid()) {
    $obPushNotificationLogsAjaxResult = (new PushNotificationAjax($gridId))->ajax();
    if (!$obPushNotificationLogsAjaxResult->isSuccess()) {
        die(json_encode($obPushNotificationLogsAjaxResult->getErrors()));
    }
}
$obPushNotificationLogs = new Push();
$arHeaders = [
    [
        'id' => 'DATE',
        'name' => 'Дата отправки',
        'default' => true,
    ],
    [
        'id' => 'USER_IDS',
        'name' => 'Пользователь',
        'default' => true,
    ],
    [
        'id' => 'SITE_ID',
        'name' => 'Сайт',
        'default' => true,
    ],
    [
        'id' => 'TITLE',
        'name' => 'Заголовок',
        'default' => true,
    ],
    [
        'id' => 'TEXT',
        'name' => 'Текст',
        'default' => true,
    ],
    [
        'id' => 'DATA',
        'name' => 'Данные',
        'default' => true,
    ],
];
$arMapKeys = [
    'id' => true,
    'user_id' => true,
    'title' => true,
    'text' => true,
    'date' => true,
    'data' => true,
];
$arUifilter = [
    ['id' => 'id', 'name' => 'ID', 'type' => 'int', 'default' => true],
    ['id' => 'site_id', 'name' => 'Сайт', 'type' => 'text', 'default' => ''],
    ['id' => 'user_ids', 'name' => 'Пользователь', 'type' => 'text', 'default' => ''],
];
$filterOption = new Options($gridId);
$filterData = $filterOption->getFilter([]);
$filter = [];

if ($filterData['id'] > 0) {
    $filter['ID'] = $filterData['id'];
}
if (isset($filterData['site_id'])) {
    $filter['SITE_ID'] = $filterData['site_id'] ?: '';
}
if (isset($filterData['user_ids'])) {
    $filter['USER_IDS'] = $filterData['user_ids'] ?: '';
}

$arNavParams = (new Bitrix\Main\Grid\Options($gridId))->GetNavParams();
$obNavigation = new Bitrix\Main\UI\PageNavigation($gridId);
$obNavigation->allowAllRecords(true)
    ->setPageSize($arNavParams['nPageSize'])
    ->initFromUri();

/** @var Push $PushNotificationsEntity */
$resultPushNotifications = $obPushNotificationLogs->getList([
    'filter' => $filter,
    'offset' => $obNavigation->getOffset(),
    'limit' => $obNavigation->allRecordsShown() ? 0 : $obNavigation->getLimit(),
    'count_total' => true
]);
$obNavigation->setRecordCount($resultPushNotifications->getCountTotal());
$arPushNotifications = [];
$arActions = [];
$arReward = [];
$arActionPanelButton = [];
if ($permissionWrite) {
    $arActionPanelButton = [
        'GROUPS' => [
            'TYPE' => [
                'ITEMS' => [
                    [
                        'ID' => 'apply',
                        'TYPE' => 'BUTTON',
                        'TEXT' => 'Применить',
                        'CLASS' => 'icon edit',
                        'ONCHANGE' => [
                            [
                                'ACTION' => \Bitrix\Main\Grid\Panel\Actions::CALLBACK,
                                'CONFIRM' => true,
                                'CONFIRM_APPLY_BUTTON' => 'Применить изменения',
                                'DATA' => [
                                    ['JS' => 'Grid.sendSelected()']
                                ]
                            ]
                        ]
                    ],
                    [
                        'ID' => 'delete',
                        'TYPE' => 'BUTTON',
                        'TEXT' => 'Удалить',
                        'CLASS' => 'icon remove',
                        'ONCHANGE' => [
                            [
                                'ACTION' => \Bitrix\Main\Grid\Panel\Actions::CALLBACK,
                                'CONFIRM' => true,
                                'CONFIRM_APPLY_BUTTON' => 'Подтвердить удаление',
                                'DATA' => [
                                    ['JS' => 'Grid.removeSelected()']
                                ]
                            ]
                        ]
                    ],
                ],
            ]
        ],
    ];
}
foreach ($resultPushNotifications->getData() as $arPushNotification) {
    $arPushNotificationData = '';
    foreach ($arPushNotification['DATA'] as $key => $value) {
        $arPushNotificationData = $arPushNotificationData . $key . ': ' . $value . '<br>';
    }
    $arPushNotification['DATA'] = $arPushNotificationData;
    $arPushNotification['USER_IDS'] = implode(',', $arPushNotification['USER_IDS']);
    $arPushNotifications[] = [
        'data' => $arPushNotification,
        'actions' => $arActions
    ];
}
$APPLICATION->SetTitle('Журнал записей');
