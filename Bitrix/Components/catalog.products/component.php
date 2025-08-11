<?php

use Bitrix\Main\Web\Uri;
use \Dev05\Classes\HighLoadBlock;

global $idPrice, $NavNum;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

/**
 * @var CBitrixComponent $this
 * @var array $arResult
 * @var array $arParams
 */

// Сохраняемые при кэшировании ключи
$this->arResultCacheKeys = [
    'CACHED',
];

$NavNum = 0; // => Обнуление NavNum, для корректной пагинации
$IBLOCK_ID = $arParams['IBLOCK_ID'];

if (empty($arParams['IBLOCK_ID'])) {
	throw new Exception('Не указан инфоблок');
}

// Параметры удаляемые из ссылки
if (!is_array($arParams['PAGER_DEL_PARAMS']) || empty($arParams['PAGER_DEL_PARAMS'])) {
	$arParams['PAGER_DEL_PARAMS'] = [];
} else {
	$tmp = [];
	foreach ($arParams['PAGER_DEL_PARAMS'] as $param) {
		if (is_string($param)) {
			$tmp[$param] = $param;
		}
	}
	$arParams['PAGER_DEL_PARAMS'] = array_values($tmp);
	unset($param);
	unset($tmp);
}

$arItems = [];
$arPropCode = [];
$arGroupBy = false;
$arNavStartParams = false;
$navComponentParameters = [
	'DEL_PARAMS' => $arParams['PAGER_DEL_PARAMS'],
];

$arSelect = ['ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'NAME', 'DETAIL_PAGE_URL', 'DETAIL_PICTURE', 'PREVIEW_PICTURE', 'QUANTITY', 'AVAILABLE'];
$arFilter = ['IBLOCK_ID' => $IBLOCK_ID] + ($arParams['SHOW_NOT_AVAILABLE'] === 'Y' ? [] : ['=AVAILABLE' => 'Y', '>QUANTITY' => 0]);

if (!empty($arParams['ORDER'])) {
	$arOrder = $arParams['ORDER'];
} else {
	$arOrder = ['ID' => 'DESC'];
}

if (!empty($arParams['SELECT_FIELDS'])) {
	$arSelect = array_merge($arSelect, $arParams['SELECT_FIELDS']);
}

if (!empty($arParams['FILTER'])) {
	$arFilter = array_merge($arFilter, $GLOBALS[$arParams['FILTER']]);
}

if (!empty($arParams['SECTION_ID'])) {
	$arFilter = array_merge($arFilter, [
	    'SECTION_ID' => $arParams['SECTION_ID'],
        'INCLUDE_SUBSECTIONS' => 'Y',
    ]);
}
$arNavigation = false;
if ($arParams['USE_PAGE_NAVIGATION'] == 'Y') {
    $pageItemsCount = (intval($arParams['PAGE_ELEMENT_COUNT']) > 0) ? intval($arParams['PAGE_ELEMENT_COUNT']) : 20;
    $arNavStartParams = [
        'nPageSize' => $arParams['PAGE_ELEMENT_COUNT'],
        'bShowAll' => 'N'
    ];
    $arNavigation = CDBResult::GetNavParams($arNavStartParams);
} elseif (!empty($arParams['ITEMS_COUNT'])) {
    $arNavStartParams = ['nTopCount' => intval($arParams['ITEMS_COUNT'])];
}


$arCacheId = [$arOrder, $arFilter, $arSelect, $arGroupBy, $arNavigation];
if ($this->startResultCache(false, $arCacheId)) {
	try {

        $dbItems = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelect);

        $productsIds = [];
        $arItems = [];
		$images = [];
		$sections = [];

        $arItemsProps = [];
        while ($arRes = $dbItems->GetNext()) {
            $arItems[$arRes['ID']] = $arRes;
            $arItemsProps[$arRes['ID']] = [];
            $productsIds[] = $arRes['ID'];

            if ($arRes['DETAIL_PICTURE'] > 0) {
                $images[$arRes['DETAIL_PICTURE']][] = $arRes['ID'];
			}
			
			if ($arRes['IBLOCK_SECTION_ID'] > 0) {
				$sections[$arRes['IBLOCK_SECTION_ID']] = $arRes['IBLOCK_SECTION_ID'];
			}
        }
		$arResult['CACHED']['ITEMS_IDS'] = $productsIds;
		

		if (!empty($sections)) {
			$sectionIter = CIBlockSection::GetList([], ['ID' => $sections], false, ['ID', 'SECTION_PAGE_URL']);
			while ($section = $sectionIter->GetNext()) {
				$section['SECTION_PAGE_URL'] = trim($section['SECTION_PAGE_URL']);
				$sections[$section['ID']] = $section;
			}
		}
		$arResult['SECTIONS'] = $sections;


        if (count($images) > 0) {
            $filesIter = CFile::GetList([], ['@ID' => array_keys($images)]);
            while ($img = $filesIter->Fetch()) {
                foreach ($images[$img['ID']] as $_id) {
                    $arItems[$_id]['DETAIL_PICTURE'] = $img;
                    $arItems[$_id]['DETAIL_PICTURE']['SRC'] = CFile::GetFileSRC($img);
                }
            }
            unset($filesIter);
            unset($img);
            unset($_id);
            unset($images);
        }

		if (count($arItems) > 0) {
			// свойства по-умолчанию
			$props = ['CML2_STICKERS', 'CML2_ELEMENT_PAGE_TITLE', 'CML2_AMOUNT_IN_PACK'];

			// добавляем новые свойства из параметров
			if (count($arParams['DISPLAY_PROPS']) > 0) {
				$props = array_merge($props, $arParams['DISPLAY_PROPS']);
			}

			if (!is_array($IBLOCK_ID)) {
				$IBLOCK_ID = [$IBLOCK_ID];
			}

			foreach ($IBLOCK_ID as $idIblock) {
				CIBlockElement::GetPropertyValuesArray(
					$arItemsProps,
					$idIblock,
					['ID' => array_keys($arItemsProps)],
					['CODE' => $props]
				);
			}



			$stickersPropId = null;
            $stickers = [];
			if (count($arItemsProps) > 0) {
				foreach ($arItemsProps as $productId => $arProp) {
					
					if ($arItems[$productId]) {
						$arItems[$productId]['PROPERTIES'] = $arItemsProps[$productId];
						if (!empty($arItems[$productId]['PROPERTIES']['CML2_STICKERS']['VALUE'])) {
							$stickersPropId = $arItems[$productId]['PROPERTIES']['CML2_STICKERS']['ID'];
							foreach ($arItems[$productId]['PROPERTIES']['CML2_STICKERS']['VALUE'] as $stickerXmlId) {
								$stickers[$stickerXmlId][] = $productId;
							}
						}
					}
				}
			}

			// Add stickers
			$tmpFilters = HighLoadBlock::getHlEntityByTableName('hl_sticker_table')::getList([
				'filter' => ['=UF_XML_ID' => array_keys($stickers)]
			]);

			while ($arStickers = $tmpFilters->fetch()) {
				foreach ($stickers[$arStickers['UF_XML_ID']] as $id) {
					$uri = (new Uri($sections[$arItems[$id]['IBLOCK_SECTION_ID']]['SECTION_PAGE_URL'] . 'filter/'))->addParams([
						"prop_{$stickersPropId}" => $arStickers['UF_NAME'],
					]);
					$arStickers['LINK'] = $uri->getUri();
					$arItems[$id]['STICKERS'][] = $arStickers;
				}
			}

			$arResult['NAV_STRING'] = $dbItems->GetPageNavStringEx(
				$navComponentObject,
				$arParams['PAGER_TITLE'],
				$arParams['PAGER_TEMPLATE'],
				false,
				$this,
				$navComponentParameters
			);

			$arResult['NAV_RESULT'] = $dbItems;

			$arResult['ITEMS'] = $arItems;
		} else {
			$this->AbortResultCache();
		}
		$this->IncludeComponentTemplate();
	} catch (\Exception $e) {
		die($e->getMessage());
	}
}


// нужно для проверки наличия результата
return $arResult['CACHED']['ITEMS_IDS'];