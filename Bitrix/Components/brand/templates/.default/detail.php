<?php

$APPLICATION->IncludeComponent(
    "05:brands.detail",
    ".default",
    array(
        "CACHE_TIME" => "36000000",
        "COMPONENT_TEMPLATE" => ".default",
        "CACHE_TYPE" => "A",
        "SEF_MODE" => "Y",
        "BRAND_LINK" => $arResult['BRAND_LINK'],
    ),
    false
);
