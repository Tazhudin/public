<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = [
	"PARAMETERS" => [
		"IBLOCK_ID" => [
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_ID"),
			"TYPE" => "STRING"
		],
		"FILTER" => [
			"PARENT" => "BASE",
			"NAME" => GetMessage("FILTER"),
			"TYPE" => "STRING"
		],		
		"SECTION_ID" => [
			"PARENT" => "BASE",
			"NAME" => GetMessage("SECTION_ID"),
			"TYPE" => "STRING",
			"MULTIPLE" => "Y"		
		],
		"ITEMS_COUNT" => [
			"PARENT" => "BASE",
			"NAME" => GetMessage("ITEMS_COUNT"),
			"TYPE" => "STRING"
		],
		"DISPLAY_PRICES" => [
			"PARENT" => "BASE",
			"NAME" => GetMessage("DISPLAY_PRICES"),
			"TYPE" => "CHECKBOX"
		],
		"DISPLAY_PROPS" => [
			"PARENT" => "BASE",
			"NAME" => GetMessage("DISPLAY_PROPS"),
			"TYPE" => "STRING",
			"MULTIPLE" => "Y"
		],
		"USE_PAGE_NAVIGATION" => [
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("USE_PAGE_NAVIGATION"),
			"TYPE" => "CHECKBOX",
			"REFRESH" => "Y"
		],
		"PAGE_ELEMENT_COUNT" => [
			"PARENT" => "BASE",
			"NAME" => GetMessage("PAGE_ELEMENT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "20"
		],
		"PAGER_SHOW_ALL" => [
			"PARENT" => "BASE",
			"NAME" => GetMessage("PAGER_SHOW_ALL"),
			"TYPE" => "GetMessage",
		],
		"SET_TITLE" => [],
		"CACHE_TIME" => [
			"DEFAULT" => 36000000
		],
	]
];

if($arCurrentValues['USE_PAGE_NAVIGATION'] == 'Y') {
	CIBlockParameters::AddPagerSettings(
		$arComponentParameters,
		GetMessage('PAGER_NAVIGATION_TITLE'), //$pager_title
		true, //$bDescNumbering
		true, //$bShowAllParam
		true, //$bBaseLink
		$arCurrentValues['PAGER_BASE_LINK_ENABLE'] === 'Y' //$bBaseLinkEnabled
	);
}
?>