<?php

global $APPLICATION;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;

/**
 * @var array $arHeaders
 * @var array $arPusNotifications
 * @var string $gridId
 * @var string $arUifilter
 * @var PageNavigation $obNavigation
 * @var array $arActionPanelButton
 */
?>
<div class='adm-toolbar-panel-container'>
    <div class='adm-toolbar-panel-flexible-space'>
        <?php
        $APPLICATION->includeComponent(
            'bitrix:main.ui.filter',
            '',
            [
                'FILTER_ID' => $gridId,
                'GRID_ID' => $gridId,
                'FILTER' => $arUifilter,
                'ENABLE_LIVE_SEARCH' => true,
                'ENABLE_LABEL' => true
            ]
        );
        ?>
    </div>
</div>
<?php

$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    [
        'GRID_ID' => $gridId,
        'COLUMNS' => $arHeaders,
        'ROWS' => $arPushNotifications,
        'NAV_OBJECT' => $obNavigation,
        'AJAX_MODE' => 'Y',
        'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
        'PAGE_SIZES' => [
            ['NAME' => '5', 'VALUE' => '5'],
            ['NAME' => '10', 'VALUE' => '10'],
            ['NAME' => '20', 'VALUE' => '20'],
            ['NAME' => '50', 'VALUE' => '50'],
            ['NAME' => '100', 'VALUE' => '100']
        ],
        'HANDLE_RESPONSE_ERRORS' => true,
        'AJAX_OPTION_JUMP' => 'N',
        'ACTION_PANEL' => $arActionPanelButton,
        'ADVANCED_EDIT_MODE' => true,
        'SHOW_ROW_ACTIONS_MENU' => true,
        'SHOW_GRID_SETTINGS_MENU' => true,
        'SHOW_NAVIGATION_PANEL' => true,
        'SHOW_PAGINATION' => true,
        'SHOW_TOTAL_COUNTER' => true,
        'SHOW_PAGESIZE' => true,
        'ALLOW_COLUMNS_SORT' => true,
        'ALLOW_COLUMNS_RESIZE' => true,
        'ALLOW_HORIZONTAL_SCROLL' => true,
        'ALLOW_SORT' => true,
        'ALLOW_PIN_HEADER' => true,
        'AJAX_OPTION_HISTORY' => 'N'
    ]
); ?>
<script>
    BX.addCustomEvent('BX.Main.Filter:apply', BX.delegate(function (command) {
        var workarea = $('#' + command);
        $.post(window.location.href, function (data) {
            workarea.html($(data).find('#' + command).html());
        })
    }));
</script>
