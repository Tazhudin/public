<?php

use Dev05\Classes\GlobalFactory;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->__component->SetResultCacheKeys([
    "CACHED_TPL",
    "ITEMS_ID",
    "WEIGHT",
    "PROPS"
]);

$compares = GlobalFactory::getInstance()->getCompare()->getList();
$arAction = array();

$arResult['ITEMS_ID'] = [];

if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0) {
    foreach ($arResult['ITEMS'] as $key => &$arProduct) {
        $arResult['ITEMS_ID'][$arProduct['ID']] = $arProduct['IBLOCK_ID'];

        if (empty($arProduct['PROPERTIES']['CML2_ELEMENT_PAGE_TITLE']['VALUE'])) {
            $arProduct['PROPERTIES']['CML2_ELEMENT_PAGE_TITLE']['VALUE'] = substr($arProduct['NAME'], 0, 40);
        }

    }
    unset($arProduct);
}