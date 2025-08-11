<?php

use Bitrix\Main\Localization\Loc;
use Dev05\Classes\Constant;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$arIBlockType = CIBlockParameters::GetIBlockTypes();
$iblockFilter = (
!empty($arCurrentValues['IBLOCK_TYPE'])
    ? array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y')
    : array('ACTIVE' => 'Y')
);

$rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);
while ($arr = $rsIBlock->Fetch()) {
    $arIBlocks[$arr['ID']] = $arr['NAME'];
}

$arProductCollectionTypes = Constant::getProductCollectionTypes();
$arProductCollectionTypeParams = [];
foreach($arProductCollectionTypes as $code => $type) {
    $arProductCollectionTypeParams[$code] =  $type['NAME'];
    $groups[$code]['NAME'] = 'Параметры "' . $type['NAME'] . '"';
}

$parameters = [
    'IBLOCK_TYPE' => [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('IBLOCK_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlockType,
        'REFRESH' => 'Y',
        'SORT' => '100',
    ],
    'IBLOCK_ID' => [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('IBLOCK_ID'),
        'VALUES' => $arIBlocks,
        'SORT' => '200',
    ],
    'DISPLAY_TYPE' => [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('DISPLAY_TYPE'),
        'TYPE' => 'LIST',
        'REFRESH' => 'Y',
        'VALUES' => [
            'SINGLE' => Loc::getMessage('DISPLAY_TYPE_SINGLE'),
            'MULTIPLE' => Loc::getMessage('DISPLAY_TYPE_MULTIPLE'),
        ],
        'SIZE' => 2,
        'SORT' => '100',
    ],
    'COLLECTION_TYPE' => [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('COLLECTION_TYPE'),
        'TYPE' => 'LIST',
        'MULTIPLE' => $arCurrentValues['DISPLAY_TYPE'] === 'MULTIPLE' ? 'Y' : 'N',
        'VALUES' => $arProductCollectionTypeParams,
        'SIZE' => count($arProductCollectionTypeParams),
        'SORT' => '200',
    ],
    'ELEMENTS_COUNT' => [
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'STRING',
        'NAME' => Loc::getMessage('ELEMENTS_COUNT'),
        'DEFAULT' => 20,
        'SORT' => '300',
    ],
    'BUYING_NOW_LIMIT_IN_ORDERS' => [
        'PARENT' => 'BUYING_NOW',
        'TYPE' => 'STRING',
        'NAME' => 'В заказах выбрать среди последних',
        'DEFAULT' => 50,
        'SORT' => '100',
    ],
];

if ($arCurrentValues['DISPLAY_TYPE'] === 'MULTIPLE') {
    $parameters = array_merge($parameters, [
        'ACTIVE_COLLECTION_TYPE' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('ACTIVE_COLLECTION_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arProductCollectionTypeParams,
            'SIZE' => count($arProductCollectionTypeParams),
            'SORT' => '300',
        ],
    ]);

    $parameters = array_merge($parameters, [
        'USE_FILTER_BY_SECTIONS' => [
            'PARENT' => 'BASE',
            'TYPE' => 'CHECKBOX',
            'NAME' => Loc::getMessage('FILTER_BY_SECTIONS'),
            'DEFAULT' => 20,
            'SORT' => '400',
        ],
    ]);
}

$arComponentParameters = [
    'GROUPS' => $groups,
    'PARAMETERS' => $parameters
];
