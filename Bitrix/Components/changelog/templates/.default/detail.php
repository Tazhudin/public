<?php

$APPLICATION->IncludeComponent(
    "05:changelog.release.detail",
    ".default",
    array(
        "CACHE_TIME" => "36000000",
        "COMPONENT_TEMPLATE" => ".default",
        "CACHE_TYPE" => "A",
        "SEF_MODE" => "Y",
        "RELEASE_ID" => $arResult['RELEASE_ID'],
    ),
    false
);
