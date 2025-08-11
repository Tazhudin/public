<?php


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}
    $arComponentParameters = [
            'GROUPS' => [], 
            'PARAMETERS' => [
                'CACHE_TIME' => [
                    'PARENT' => 'BASE',
                    'NAME' => 'Время кеширования',
                    'TYPE' => 'STRING',
                    'DEFAULT' => '3600'
                ],
            ],
    ];