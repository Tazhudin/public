<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$arComponentParameters = [
    'GROUPS' => [],
    'PARAMETERS' => [
        'SEF_MODE' => 'Y',
        'SEF_FOLDER' => '/brands/',
        'SEF_URL_TEMPLATES' => [
            'list' => 'index.php',
            'detail' => 'detail.php'
        ],
    ],
];