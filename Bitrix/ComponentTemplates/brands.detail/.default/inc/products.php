<?php
$GLOBALS['actionRelatedProducts'] = ['PROPERTY_CML2_BRANDS_REF' => $arResult['INFO'][key($arResult['INFO'])]['UF_XML_ID']];

if (!empty($_GET['sort'])) {
	$_SESSION['sortType'] = $_GET['sort'];
	$_SESSION['sortOrder'] = $_GET['by'];
} elseif (!isset($_SESSION['sortType']) || empty($_SESSION['sortType']) || empty($_SESSION['sortOrder'])) {
	$_SESSION['sortType'] = 'property_CML2_ORDER_NUM';
	$_SESSION['sortOrder'] = 'asc';
}

$arOrder[$_SESSION['sortType']] = $_SESSION['sortOrder'];

$APPLICATION->IncludeComponent(
    '05:catalog.products',
    'brandProducts',
    array(
        'IBLOCK_ID' => '59',
        'SECTION_ID' => '',
        'FILTER' => 'actionRelatedProducts',
        'DISPLAY_PRICES' => 'Y',
        'DISPLAY_PROPS' => 'Y',
        'USE_PAGE_NAVIGATION' => 'Y',
        'PAGE_ELEMENT_COUNT' => 20,
        'CACHE_TIME' => '36000000',
        'PAGER_SHOW_ALL' => 'Y',
        'ORDER' => $arOrder,
        'COMPONENT_TEMPLATE' => 'list',
        'ITEMS_COUNT' => '',
        'CACHE_TYPE' => 'A',
        'SET_TITLE' => 'Y',
        'DISPLAY_TOP_PAGER' => 'N',
        'DISPLAY_BOTTOM_PAGER' => 'Y',
        'PAGER_TEMPLATE' => 'products_list',
        'PAGER_SHOW_ALWAYS' => 'N',
        'PAGER_DESC_NUMBERING' => 'N',
        'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
        'PAGER_BASE_LINK_ENABLE' => 'N',
    ),
    false
);
