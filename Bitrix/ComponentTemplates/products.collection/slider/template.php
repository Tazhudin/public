<?php

use Dev05\Classes\Constant;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$itemsIds = [];
$displayType = $arParams['DISPLAY_TYPE'];

if ($displayType == 'SINGLE' || ($displayType == 'MULTIPLE' && count($arParams['COLLECTION_TYPE']) == 1)) {
    include __DIR__ . '/inc/single.php';
} elseif ($displayType == 'MULTIPLE') {
    $arProductCollectionTypes = Constant::getProductCollectionTypes();
    $collectionTypes = $arParams['COLLECTION_TYPE'];
    include __DIR__ . '/inc/tabs.php';
}

$arResult['ITEMS_ID'] = $itemsIds;

$this->getComponent()->setResultCacheKeys(['ITEMS_ID']);
