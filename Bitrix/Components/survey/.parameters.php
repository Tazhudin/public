<?php

use Bitrix\Main\Localization\Loc;
use Dev05\Classes\Constant;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

Loc::loadMessages(__FILE__);

$parameters = [
    'IBLOCK_CODE' => [
        'PARENT' => 'BASE',
        'TYPE' => 'STRING',
        'NAME' => Loc::getMessage('IBLOCK_CODE'),
        'REFRESH' => 'Y',
        'DEFAULT' => Constant::getInfoBlockCode('SURVEY')
    ],
    'AJAX_MODE' => array()
];
$arSurveys = [];
$dbSurveys = Bitrix\Iblock\SectionTable::getList([
    'select' => ['CODE', 'NAME', 'IBLOCK'],
    'filter' => ['IBLOCK.CODE' => $arCurrentValues['IBLOCK_CODE'], 'IBLOCK_SECTION_ID' => false]
]);
while($arRes = $dbSurveys->fetch()) {
    $arSurveys[$arRes['CODE']] = $arRes['NAME'];
}
if ($arSurveys > 0) {
    $arSurveyTypeParams = [
        'SURVEY_CODE' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('SURVEY_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arSurveys,
            'SIZE' => count($arSurveys)
        ]
    ];
    $parameters = array_merge($parameters, $arSurveyTypeParams);
}
$arComponentParameters = [
    'PARAMETERS' => $parameters
];
