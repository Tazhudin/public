<?php

$APPLICATION->IncludeComponent(
    '05:changelog.release.list',
    '.default',
    [
        'PAGE_ITEMS_COUNT' => '3',
        'PAGER_TEMPLATE' => 'products_list',
        'PAGER_TITLE' => 'Релизы',
        'PAGE' => (int)request()['PAGEN_']?:1,
        'COMPONENT_TEMPLATE' => '.default',
        'CACHE_TIME' => '36000000',
        'CACHE_TYPE' => 'A',
    ],
    false
);