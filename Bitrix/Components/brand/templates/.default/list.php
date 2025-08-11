<?php

$APPLICATION->IncludeComponent(
    '05:brands.list',
    '.default',
    [
        'COMPONENT_TEMPLATE' => '.default',
        'CACHE_TIME' => '36000000',
        'CACHE_TYPE' => 'A',
    ],
    false
);