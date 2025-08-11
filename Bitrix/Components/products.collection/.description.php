<?php

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

$arComponentDescription = [
    'NAME' => Loc::getMessage('NAME'),
    'DESCRIPTION' => Loc::getMessage('DESCRIPTION'),
    'PATH' => [
        'ID' => 'ProductsCollection'
    ],
    'ICON' => '',
];
