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
$arSites = $helper->getActiveSites();
if ($request->isPost() && check_bitrix_sessid() && ($request['Update'] || $request['Apply'])) {
    $helper->setConf(
        [
            'PUSH_NOTIFICATION_SETTINGS_MESSAGING_SERVER_KEY' => $_POST['push-notifications-messaging-server-key'],
            'PUSH_NOTIFICATION_SETTINGS_MESSAGING_SERVER_API_ADDRESS' => $_POST['push-notifications-messaging-server-api-address'],
            'PUSH_NOTIFICATION_SETTINGS_SENDING_WITH_STATUSES_TYPE_CODE' => $_POST['send-push-with-order-statuses-type-code'],
            'PUSH_NOTIFICATION_SETTINGS_SENDING_WITH_STATUSES' => $_POST['send-push-with-order-statuses'],
            'PUSH_NOTIFICATION_SETTINGS_SENDING_STATUSES_TITLE_TEMPLATE' => $_POST['push-notifications-status-title-template'],
            'PUSH_NOTIFICATION_SETTINGS_SENDING_STATUSES_BODY_TEMPLATES' => array_combine($_POST['send-push-with-order-statuses'], $_POST['push-notifications-status-body-templates']),
            'PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED' => $_POST['send-push-when-order-changed'],
            'PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED_TYPE_CODE' => $_POST['send-push-when-order-changed-type-code'],
            'PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED_SEND_SMS' => $_POST['send-push-when-order-changed-send-sms'] ? 'Y' : '',
            'PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED_TITLE_TEMPLATE' => $_POST['send-push-when-order-changed-title-template'],
            'PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED_BODY_TEMPLATE' => $_POST['send-push-when-order-changed-body-template'],
            'PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE' => $_POST['send-push-when-order-evaluate'],
            'PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_TYPE_CODE' => $_POST['send-push-when-order-evaluate-type-code'],
            'PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_SEND_SMS' => $_POST['send-push-when-order-evaluate-send-sms'] ? 'Y' : '',
            'PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_IN_STATUS' => $_POST['send-push-when-order-evaluate-in-status'],
            'PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_TITLE_TEMPLATE' => $_POST['send-push-when-order-evaluate-title-template'],
            'PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_BODY_TEMPLATE' => $_POST['send-push-when-order-evaluate-body-template'],
        ]
    );
    $helper->save();
}
ob_start();

$orderStatuses = $helper->getOrderStatuses('PUSH_NOTIFICATION_SETTINGS_SENDING_WITH_STATUSES');
$selectedStatusesTemplates = $helper->getSelectedStatusesTemplates('PUSH_NOTIFICATION_SETTINGS_SENDING_STATUSES_BODY_TEMPLATES');
$arStatusesToEvaluate = $helper->getOrderStatuses('PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_IN_STATUS');
?>
    <tr class="heading">
        <td colspan="2"><b><?= GetMessage('PUSH_NOTIFICATION_SETTINGS_GENERAL') ?></b></td>
    </tr>

    <tr>
        <td class='adm-detail-valign-top adm-detail-content-cell-l' width='40%'>
            <?= GetMessage('PUSH_NOTIFICATION_SETTINGS_APPLY_FOR_SITE') ?>
        </td>
        <td width='60%' class='adm-detail-content-cell-r'>
            <form action="">
                <select name="siteId"
                        onchange="window.location='?siteId='+this[this.selectedIndex].value;">
                    <?php
                    foreach ($arSites as $arSite) { ?>
                        <option <?= $arSite['ID'] == $currentSiteId ? 'selected' : null ?>
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
            <?= GetMessage('PUSH_NOTIFICATION_SETTINGS_MESSAGING_SERVER_KEY') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <input type='text'
                   value='<?= $helper->get('PUSH_NOTIFICATION_SETTINGS_MESSAGING_SERVER_KEY') ?>'
                   name='push-notifications-messaging-server-key'/>
        </td>
    </tr>
    <tr>
        <td width='50' class='adm-detail-content-cell-l'>
            <?= GetMessage('PUSH_NOTIFICATION_SETTINGS_MESSAGING_SERVER_API_ADDRESS') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <input type='text'
                   value='<?= $helper->get('PUSH_NOTIFICATION_SETTINGS_MESSAGING_SERVER_API_ADDRESS') ?>'
                   name='push-notifications-messaging-server-api-address'/>
        </td>
    </tr>

    <tr class="heading">
        <td colspan="2"><b><?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING') ?></b></td>
    </tr>

    <tr>
    <tr>
        <td width='50' class='adm-detail-content-cell-l'>
            <?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_CHANGE_STATUSES_TYPE_CODE') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <input size="60" type='text'
                   value='<?= $helper->get('PUSH_NOTIFICATION_SETTINGS_SENDING_WITH_STATUSES_TYPE_CODE') ?>'
                   name='send-push-with-order-statuses-type-code'/>
        </td>
    </tr>
    <td width='50%'
        class='adm-detail-content-cell-l'><?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_CHANGE_STATUSES') ?></td>
    <td width="50%" class="adm-detail-content-cell-r">
        <select class="typeselect" name="send-push-with-order-statuses[]" size="5" multiple>
            <?php
            foreach ($orderStatuses as $arStatus) {
                if ($arStatus['SELECTED'] === true) {
                    $arSelectedStatuses[] = $arStatus;
                } ?>
                <option <?= $arStatus['SELECTED'] === true ? 'selected' : null ?>
                        value="<?= $arStatus['ID'] ?>">
                    <?= $arStatus['NAME'] ?>
                </option>
                <?php
            } ?>
            <option <?= empty($arSelectedStatuses) ? 'selected' : null ?> value="">Не выбрано</option>
        </select>
    </td>
<?php
if ($arSelectedStatuses) { ?>
    <tr>
        <td width='50' class='adm-detail-content-cell-l'
        ">
        <?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING_TITLE_TEMPLATE_FOR_STATUS') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <input size="60"
                   type='text' value='<?= $helper->get('PUSH_NOTIFICATION_SETTINGS_SENDING_STATUSES_TITLE_TEMPLATE') ?>'
                   name='push-notifications-status-title-template'/>
        </td>
    </tr>
    <?php
    foreach ($arSelectedStatuses as $arStatus) { ?>
        <tr>
            <td width='50' class='adm-detail-content-cell-l'
            ">
            <?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING_BODY_TEMPLATE_FOR_STATUS') . $arStatus['NAME'] ?>
            </td>
            <td width='50%' class='adm-detail-content-cell-r'>
                <input size="60"
                       type='text' value='<?= $selectedStatusesTemplates[$arStatus['ID']] ?>'
                       name='push-notifications-status-body-templates[]'/>
            </td>
        </tr>
        <?php
    }
} ?>
    <tr class="heading">
        <td colspan="2"></td>
    </tr>
    </tr>
    <tr>
        <td width="50%"
            class="adm-detail-content-cell-l"><?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED') ?></td>
        <td width="50%" class="adm-detail-content-cell-r">
            <input
                    type="checkbox"
                    value="Y"
                    name="send-push-when-order-changed"
                <?= $helper->get('PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED') == 'Y' ? 'checked' : '' ?>
            />
        </td>
    </tr>
<?php
if ($helper->get('PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED') == 'Y') { ?>
    <tr>
        <td width='50' class='adm-detail-content-cell-l'>
            <?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED_TYPE_CODE') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <input size="60" type='text'
                   value='<?= $helper->get('PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED_TYPE_CODE') ?>'
                   name='send-push-when-order-changed-type-code'/>
        </td>
    </tr>
    <tr>
        <td width='50' class='adm-detail-content-cell-l'>
            <?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED_TITLE_TEMPLATE') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <input size="60" type='text'
                   value='<?= $helper->get('PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED_TITLE_TEMPLATE') ?>'
                   name='send-push-when-order-changed-title-template'/>
        </td>
    </tr>
    <tr>
        <td width='50' class='adm-detail-content-cell-l'>
            <?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED_BODY_TEMPLATE') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <input size="60" type='text'
                   value='<?= $helper->get('PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED_BODY_TEMPLATE') ?>'
                   name='send-push-when-order-changed-body-template'/>
        </td>
    </tr>
    <tr>
        <td width="50%"
            class="adm-detail-content-cell-l"><?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED_SEND_SMS') ?></td>
        <td width="50%" class="adm-detail-content-cell-r">
            <input
                    type="checkbox"
                    value="Y"
                    name="send-push-when-order-changed-send-sms"
                <?= $helper->get('PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED_SEND_SMS') == 'Y' ? 'checked' : '' ?>
            />
        </td>
    </tr>
    <?php
} ?>
    <tr class="heading">
        <td colspan="2"></td>
    </tr>
    </tr>
    <tr>
        <td width="50%"
            class="adm-detail-content-cell-l"><?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE') ?></td>
        <td width="50%" class="adm-detail-content-cell-r">
            <input
                type="checkbox"
                value="Y"
                name="send-push-when-order-evaluate"
                <?= $helper->get('PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE') == 'Y' ? 'checked' : '' ?>
            />
        </td>
    </tr>
<?php
    if ($helper->get('PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE') == 'Y') { ?>
        <tr>
            <td width='50' class='adm-detail-content-cell-l'>
                <?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_TYPE_CODE') ?>
            </td>
            <td width='50%' class='adm-detail-content-cell-r'>
                <input size="60" type='text'
                       value='<?= $helper->get('PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_TYPE_CODE') ?>'
                       name='send-push-when-order-evaluate-type-code'/>
            </td>
        </tr>
        <td width='50%'
            class='adm-detail-content-cell-l'><?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_IN_STATUS_TEMPLATE') ?></td>
        <td width="50%" class="adm-detail-content-cell-r">
            <select class="typeselect" name="send-push-when-order-evaluate-in-status[]" size="5" multiple>
                <?php
                foreach ($arStatusesToEvaluate as $arStatus) {
                    if ($arStatus['SELECTED'] === true) {
                        $arSelectedStatusesToEvaluate[] = $arStatus;
                    } ?>
                    <option <?= $arStatus['SELECTED'] === true ? 'selected' : null ?>
                            value="<?= $arStatus['ID'] ?>">
                        <?= $arStatus['NAME'] ?>
                    </option>
                    <?php
                } ?>
                <option <?= empty($arSelectedStatusesToEvaluate) ? 'selected' : null ?> value="">Не выбрано</option>
            </select>
        </td>

    <tr>
        <td width='50' class='adm-detail-content-cell-l'>
            <?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_TITLE_TEMPLATE') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <input size="60" type='text'
                   value='<?= $helper->get('PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_TITLE_TEMPLATE') ?>'
                   name='send-push-when-order-evaluate-title-template'/>
        </td>
    </tr>
    <tr>
        <td width='50' class='adm-detail-content-cell-l'>
            <?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_BODY_TEMPLATE') ?>
        </td>
        <td width='50%' class='adm-detail-content-cell-r'>
            <input size="60" type='text'
                   value='<?= $helper->get('PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_BODY_TEMPLATE') ?>'
                   name='send-push-when-order-evaluate-body-template'/>
        </td>
    </tr>
        <tr>
            <td width="50%"
                class="adm-detail-content-cell-l"><?= GetMessage('PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_SEND_SMS') ?></td>
            <td width="50%" class="adm-detail-content-cell-r">
                <input
                        type="checkbox"
                        value="Y"
                        name="send-push-when-order-evaluate-send-sms"
                    <?= $helper->get('PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_SEND_SMS') == 'Y' ? 'checked' : '' ?>
                />
            </td>
        </tr>
<?php
} ?>
    <tr class="heading">
        <td colspan="2"></td>
    </tr>
    <tr>
        <td align="left" colspan="2"><b>Доступные поля в шаблонах:</b><br><br>
            #ORDER_ID# - номер заказа<br>
    </tr>
<?php
$settingsTemplate = ob_get_clean();
$arTabs = [
    [
        'DIV' => 'push-notifications_settings',
        'TAB' => Loc::getMessage('PUSH_NOTIFICATION_SETTINGS_TAB'),
        'TITLE' => Loc::getMessage('PUSH_NOTIFICATION_SETTINGS_TITLE'),
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
        <input type="submit" name="Update" value="<?= GetMessage("MAIN_SAVE"); ?>"/>
        <input type="submit" name="Apply" value="<?= GetMessage("MAIN_APPLY"); ?>"/>
    </form>
<?php
$tabController->End();


require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
