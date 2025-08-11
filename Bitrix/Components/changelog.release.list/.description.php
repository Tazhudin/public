<?php

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

$arComponentDescription = [
    'NAME' => 'Changelog',
    'DESCRIPTION' => Loc::getMessage('CHANGELOG_DESCRIPTION'),
    'PATH' => [
        'ID' => 'changelog.release.list'
    ],
    'ICON' => '',
];
