<?php 
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
                "SEF_MODE" => "Y",
                "SEF_FOLDER" => "/changelog/",
                "SEF_URL_TEMPLATES" => [
                    "list" => "index.php",
                    "detail" => "detail.php"
                ],
            ],
    ];