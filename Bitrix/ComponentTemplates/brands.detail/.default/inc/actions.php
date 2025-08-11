<?php
global $arActionsFilter, $idPrice, $r;

use Dev05\Classes\Constant;

if (!empty($arResult['ACTIONS'])) {

	$dbProp = CIBlockPropertyEnum::GetList(
		[],
		["IBLOCK_ID" => Constant::INFOBLOCKS('ACTIONS'),  "CODE" => "GEO_TARGETING", "EXTERNAL_ID" => ["all", $idPrice, $r['CITY_ID']]]
	);
	while ($arGeoTargeting = $dbProp->GetNext()) {
		$arGeoTargetingFilter[] = $arGeoTargeting['VALUE'];
	}

	$arActionsFilter = [
		"ACTIVE" => "Y",
		"PROPERTY_GEO_TARGETING_VALUE" => $arGeoTargetingFilter,
		"ID" => $arResult['ACTIONS']
	];

	ob_start();

	$tmp = $APPLICATION->IncludeComponent(
		"bitrix:news.list",
		"actions",
		array(
			"ACTIVE_DATE_FORMAT" => "d.m.Y",
			"ADD_SECTIONS_CHAIN" => "N",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
			"AJAX_MODE" => "N",
			"AJAX_OPTION_ADDITIONAL" => "",
			"AJAX_OPTION_HISTORY" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"CACHE_FILTER" => "N",
			"CACHE_GROUPS" => "Y",
			"CACHE_TIME" => "36000000",
			"CACHE_TYPE" => "A",
			"CHECK_DATES" => "Y",
			"COMPONENT_TEMPLATE" => "actions",
			"DETAIL_URL" => "",
			"DISPLAY_BOTTOM_PAGER" => "Y",
			"DISPLAY_TOP_PAGER" => "N",
			"FIELD_CODE" => array(
				0 => "DATE_ACTIVE_TO",
				1 => "",
			),
			"FILTER_NAME" => "arActionsFilter",
			"HIDE_LINK_WHEN_NO_DETAIL" => "N",
			"IBLOCK_ID" => "64",
			"IBLOCK_TYPE" => "services",
			"INCLUDE_SUBSECTIONS" => "Y",
			"MESSAGE_404" => "",
			"NEWS_COUNT" => "30",
			"PAGER_BASE_LINK_ENABLE" => "N",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
			"PAGER_SHOW_ALL" => "N",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_TEMPLATE" => ".default",
			"PAGER_TITLE" => "Новости",
			"PARENT_SECTION" => "",
			"PARENT_SECTION_CODE" => "",
			"PREVIEW_TRUNCATE_LEN" => "",
			"PROPERTY_CODE" => array(
				0 => "",
				1 => "LINK",
				2 => "BGCOLOR",
				3 => "",
			),
			"RESIZER_SET_1200" => "4",
			"RESIZER_SET_991" => "4",
			"RESIZER_SET_FROM_1200" => "10",
			"SET_BROWSER_TITLE" => "N",
			"SET_LAST_MODIFIED" => "N",
			"SET_META_DESCRIPTION" => "N",
			"SET_META_KEYWORDS" => "N",
			"SET_STATUS_404" => "N",
			"SET_TITLE" => "N",
			"SHOW_404" => "N",
			"SORT_BY1" => "ID",
			"SORT_BY2" => "ACTIVE_FROM ",
			"SORT_ORDER1" => "DESC",
			"SORT_ORDER2" => "desc",
			"USE_RESIZER_SET_FROM_1200" => "N",
			"STRICT_SECTION_CHECK" => "N",
			"COMPOSITE_FRAME_MODE" => "A",
			"COMPOSITE_FRAME_TYPE" => "AUTO"
		),
		false
	);



	$content = ob_get_contents();
	ob_end_clean();

	if (!empty($tmp)) :
?>
		<div class="row-slider row-slider_news">
			<div class="row-slider__title">
				<div class="h2 h2_mt">Акции</div>
				<?= $content; ?>
			</div>
		</div>
<?php
	endif;
}
