<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var CMain $APPLICATION
 * @var array $arResult
 */

foreach ($arResult['ITEMS_ID'] as $id => $placeholders) {

    $APPLICATION->IncludeComponent(
        '05:show.price',
        '.default',
        [
            'WHERE'       => 'SECTION',
            'PRODUCT_ID'  => $id,
            'SHOW_DELAY'  => 'Y',
            'PLACEHOLDER' => $placeholders['PRICE'],

            'AMOUNT_IN_PACK' => (int)$placeholders['AMOUNT_IN_PACK'],
        ],
        $this,
        ['HIDE_ICONS' => 'Y']
    );
    $APPLICATION->IncludeComponent(
        '05:execute.delay',
        'compare_icon',
        [
            'PRODUCT_ID'  => $id,
            'PLACEHOLDER' => $placeholders['COMPARE'],
        ],
        $this,
        ['HIDE_ICONS' => 'Y']
    );
    $APPLICATION->IncludeComponent(
        '05:execute.delay',
        'favorite_icon',
        [
            'PRODUCT_ID'  => $id,
            'PLACEHOLDER' => $placeholders['FAVORITE'],
        ],
        $this,
        ['HIDE_ICONS' => 'Y']
    );
}
