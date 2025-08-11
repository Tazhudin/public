<?php

// phpcs:ignoreFile

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Dev05\Classes\SiteHelper;
use PushNotifications\Helper;
use PushNotifications\Permission;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

global $APPLICATION;
$module_id = 'push.notifications';
CModule::IncludeModule($module_id);
if (!Permission::canWrite()) {
    $APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}
Loc::loadMessages(__FILE__);
$request = Application::getInstance()->getContext()->getRequest();
$currentSiteId = $request->get('siteId') ?: SiteHelper::DEFAULT_SITE_ID;
$helper = Helper::getInstance($currentSiteId);
ob_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php';
$htmlGroupRights = ob_get_clean();
CUtil::InitJSCore([$module_id]);
$arRequestParams = [];
if ($request->isPost() && check_bitrix_sessid() && ($request['send'])) {
    $arRequestParams = [
        'SITE_ID' => $_POST['push-notifications-testing-send-site-id'] ?: '',
        'USER_ID' => $_POST['push-notifications-testing-send-user-id'] ?: '',
        'TITLE' => $_POST['push-notifications-testing-send-messaging-title'] ?: '',
        'TEXT' => $_POST['push-notifications-testing-send-messaging-text'] ?: '',
        'IMAGE' => $_POST['push-notifications-testing-send-image'] ?: '',
        'ICON' => $_POST['push-notifications-testing-send-icon'] ?: '',
        'DATA' => json_decode($_POST['push-notifications-testing-send-json-data'], true),
    ];
    $obSendPushResult = $helper->sendTestingPushNotification($arRequestParams);
}
ob_start();

$arSites = $helper->getActiveSites();
?>
    <tr class="heading">
        <td colspan="2"><b><?= GetMessage('PUSH_NOTIFICATION_TESTING_SEND_HEADER') ?></b></td>
    </tr>
    <tr>
        <td class='adm-detail-valign-top adm-detail-content-cell-l' width='40%'>
            <?= GetMessage('PUSH_NOTIFICATION_TESTING_SEND_FOR_SITE') ?>
        </td>
        <td width='60%' class='adm-detail-content-cell-r'>
            <form action="">
                <select name="push-notifications-testing-send-site-id">
                    <?php
                    foreach ($arSites as $arSite) { ?>
                        <option <?= $arSite['ID'] == $arRequestParams['SITE_ID'] ? 'selected' : null ?>
                                value="<?= $arSite['ID'] ?>"><?= $arSite['NAME'] ?>
                        </option>
                        <?php
                    } ?>
                </select>
            </form>
        </td>
    </tr>
    <tr>
        <td width='50' class='adm-detail-content-cell-l'>
            <?= GetMessage('PUSH_NOTIFICATION_TESTING_USER_ID') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <input type='text'
                   value='<?= $arRequestParams['USER_ID'] ?>'
                   name='push-notifications-testing-send-user-id'/>
        </td>
    </tr>
    <tr>
        <td width='50' class='adm-detail-content-cell-l'>
            <?= GetMessage('PUSH_NOTIFICATION_TESTING_SEND_TITLE') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <input type='text'
                   value='<?= $arRequestParams['TITLE'] ?>'
                   name='push-notifications-testing-send-messaging-title'/>
        </td>
    </tr>
    <tr>
        <td width='50' class='adm-detail-content-cell-l'>
            <?= GetMessage('PUSH_NOTIFICATION_TESTING_SEND_TEXT') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <input type='text'
                   value='<?= $arRequestParams['TEXT'] ?>'
                   name='push-notifications-testing-send-messaging-text'/>
        </td>
    </tr>
    <tr>
        <td width='50' class='adm-detail-content-cell-l'>
            <?= GetMessage('PUSH_NOTIFICATION_TESTING_SEND_IMAGE') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <input type='text'
                   value='<?= $arRequestParams['IMAGE'] ?>'
                   name='push-notifications-testing-send-image'/>
        </td>
    </tr>


    <tr>
        <td width='50' class='adm-detail-content-cell-l'>
            <?= GetMessage('PUSH_NOTIFICATION_TESTING_SEND_ICON') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <input type='text'
                   value='<?= $arRequestParams['ICON'] ?>'
                   name='push-notifications-testing-send-icon'/>
        </td>
    </tr>

    <tr>
        <td width='50' class='adm-detail-content-cell-l'>
            <?= GetMessage('PUSH_NOTIFICATION_TESTING_SEND_JSON_DATA') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <textarea name='push-notifications-testing-send-json-data' cols='50' rows='8'>
                <?= json_encode($arRequestParams['DATA'], true) ?>
            </textarea>
        </td>
    </tr>

<?php
if ($obSendPushResult) {
    ?>
    <tr class="heading">
        <td colspan="2"><b><?= GetMessage('PUSH_NOTIFICATION_TESTING_SEND_RESULT_HEADER') ?></b></td>
    </tr>
    <?php if (!$obSendPushResult->isSuccess()) { ?>
        <td width='50' class='adm-detail-content-cell-l'>
            <?= GetMessage('PUSH_NOTIFICATION_TESTING_SEND_RESULT_ERROR') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <p><?= implode('; </br>', $obSendPushResult->getErrorMessages()) ?></p>
        </td>
    <?php } else { ?>
        <td width='50' class='adm-detail-content-cell-l'>
            <?= GetMessage('PUSH_NOTIFICATION_TESTING_SEND_RESULT_SUCCESS_REQUEST') ?>
        </td>
        <td width='50%'
            class='adm-detail-content-cell-r'
        >

            <div style="height:200px;width:500px;overflow-y:scroll;">
                <pre width="100px"><?= print_r($obSendPushResult->getData()['REQUEST_DATA'], true) ?></pre>
            </div>
        </td>

        <tr>
            <td width='50' class='adm-detail-content-cell-l'>
                <?= GetMessage('PUSH_NOTIFICATION_TESTING_SEND_RESULT_SUCCESS_RESPONSE') ?>
            </td>
            <td width='50%'
                class='adm-detail-content-cell-r'
            >
                <div style="height:200px;width:500px;overflow-y:scroll;">
                    <pre><?= print_r($obSendPushResult->getData()['RESPONSE_DATA'], true) ?></pre>
                </div>
            </td>
        </tr>
        <?php
    }
}
?>
<?php
$settingsTemplate = ob_get_clean();
$arTabs = [
    [
        'DIV' => 'push-notifications_settings',
        'TAB' => Loc::getMessage('PUSH_NOTIFICATION_TESTING_SEND_TAB_TITLE'),
        'TITLE' => '',
        'HTML' => $settingsTemplate
    ],
    [
        'DIV' => 'rights',
        'TAB' => 'Доступ',
        'TITLE' => 'Настройка прав доступа',
        'HTML' => $htmlGroupRights
    ],
];
$tabController = new CAdminTabControl('tabControl', $arTabs);
// save options
if ($request->isPost() && check_bitrix_sessid() && $request['Apply']) {
    LocalRedirect(
        $APPLICATION->GetCurPage() . '?' .
        $tabController->ActiveTabParam()
    );
}

$tabController->Begin();
?>
    <form method="post" enctype="multipart/form-data"
          action="<?= $APPLICATION->GetCurPage(); ?>?clear_cache=Y"
          name="<?= $moduleID ?>">
        <?php
        foreach ($arTabs as $aTab) {
            $tabController->BeginNextTab();
            echo $aTab['HTML'];
        }

        $tabController->Buttons();
        echo bitrix_sessid_post();
        ?>
        <input type="hidden" id="tabControl_active_tab" name="tabControl_active_tab"
               value="<?= $request["tabControl_active_tab"]; ?>"/>
        <input type="submit" name="send" value="<?= GetMessage("PUSH_NOTIFICATION_TESTING_SEND_SEND"); ?>"/>
        <input type="submit" name="Update" value="<?= GetMessage("MAIN_SAVE"); ?>"/>
        <input type="submit" name="Apply" value="<?= GetMessage("MAIN_APPLY"); ?>"/>
    </form>
<?php
$tabController->End();


require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
