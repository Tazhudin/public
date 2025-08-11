<?php

use Dev05\Classes\Constant;

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$currentUri = $request->getRequestUri();
$uriObj = new Bitrix\Main\Web\Uri($currentUri);

$this->getComponent()->SetResultCacheKeys([
    'CACHED_TPL',
    'ITEMS_ID',
    'PLACEHOLDERS',
    'WEIGHT',
    'PROPS',
]);

$arResult['PLACEHOLDERS'] = [];

//ТИПЫ СОРТИРОВКИ

$arSortTypes = Constant::getSortProductTypes();

foreach ($arSortTypes as $sortName => $sortParams) {

    if (isset($_GET['sort'])) {
        $arDelUriParams[] = 'sort';
    }

    if (isset($_GET['by'])) {
        $arDelUriParams[] = 'by';
    }

    if (!empty($arDelUriParams)) {
        $uriObj->deleteParams($arDelUriParams);
    }

    $arUrlParams = ['sort' => $sortParams['TYPE'], 'by' => $sortParams['BY']];
    $uriObj->addParams($arUrlParams);
    $arResult['SORT_TYPES'][$sortName]['NAME'] = $sortParams['NAME'];
    $arResult['SORT_TYPES'][$sortName]['TYPE'] = $sortParams['TYPE'];
    $arResult['SORT_TYPES'][$sortName]['BY'] = $sortParams['BY'];
    $arResult['SORT_TYPES'][$sortName]['URI'] = $uriObj->getUri();
    $arResult['SORT_TYPES'][$sortName]['ACTIVE'] = (isset($_GET['sort']) && $_GET['sort'] == $sortParams['TYPE'] && isset($_GET['by']) && $_GET['by'] == $sortParams['BY']) ? true : false;

    unset($arDelUriParams);
}


if ($arResult['ID'] > 0) {

    $db_sectionsParam = CIBlockSection::GetList(
        array(),
        array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', 'ID' => $arResult['ID']),
        false,
        array('ID', 'IBLOCK_ID', 'UF_WEIGHT'),
        false
    );

    if ($res = $db_sectionsParam->GetNext()) {
        $arResult['WEIGHT'] = $res['UF_WEIGHT'];
    }
}

$arResult['ITEMS_ID'] = [];

if (is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0) {
    foreach ($arResult['ITEMS'] as $key => &$arProduct) {
        $arResult['ITEMS_ID'][$arProduct['ID']] = $arProduct['IBLOCK_ID'];
    }
    unset($arProduct);
}
