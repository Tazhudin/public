<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

/**
 * @var CMain $APPLICATION
 * @var array $arResult
 */

foreach ($arResult['ITEMS_ID'] as $itemId => $tmp) {
    list($placeholder, $amountInPack) = $tmp;
	$APPLICATION->IncludeComponent(
		'05:show.price',
		'.default',
		[
			'PRODUCT_ID' => $itemId,
			'WHERE' => 'SECTION',
			'PLACEHOLDER' => $placeholder,
			'SHOW_DELAY' => 'Y',
            'AMOUNT_IN_PACK' => $amountInPack,
		],
        $this,
        ['HIDE_ICONS' => 'Y']
	);
}
