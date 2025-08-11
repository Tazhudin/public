<?php

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}
    $arComponentParameters = [
            'GROUPS' => [], 
            'PARAMETERS' => [       
                'PAGE_ITEMS_COUNT' => [
                    'PARENT' => 'BASE',
                    'NAME' => 'Кол-во на странице',
                    'TYPE' => 'STRING',
                    'DEFAULT' => '5'
                ],
                'CACHE_TIME' => [
                    'PARENT' => 'BASE',
                    'NAME' => 'Время кеширования',
                    'TYPE' => 'STRING',
                    'DEFAULT' => '3600'
                ],
            ],
    ];