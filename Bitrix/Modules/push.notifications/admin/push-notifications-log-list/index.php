<?php

use Bitrix\Main\Localization\Loc;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/push.notifications/admin/PushNotificationAjax.php');
include('include/model.php');
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');
include('include/template.php');
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
