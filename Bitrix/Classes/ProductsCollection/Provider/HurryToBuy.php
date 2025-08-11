<?php

namespace Dev05\Classes\ProductsCollection\Provider;

use CFile;
use CIBlockElement;
use CIBlockPropertyEnum;
use Dev05\Classes\Constant;
use Dev05\Classes\ProductsCollection\ISelectionProductsProvider;
use Exception;

class HurryToBuy implements ISelectionProductsProvider
{
    /**
     * @behavior get products by sort 'rand' => 'asc'
     * @param $arParams (use sectionId, limit and items count)
     * @return array
     */
    public function getProducts(array $arParams)
    {
        $actionProductsFilter = $this->getActionProductsFilter($arParams['IBLOCK_ID']);

        $arSelect = ['ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'NAME', 'DETAIL_PAGE_URL', 'DETAIL_PICTURE', 'PREVIEW_PICTURE', 'QUANTITY', 'AVAILABLE', 'PROPERTY_CML2_ELEMENT_PAGE_TITLE'];
        $arFilter = ['=IBLOCK_ID' => $arParams['IBLOCK_ID'], '=ACTIVE' => 'Y', '=AVAILABILITY' => 'Y', '>QUANTITY' => 0];

        $arFilter = array_merge($arFilter, $actionProductsFilter);


        if ($arParams['SECTION_ID'] > 0) {
            $arFilter['IBLOCK_SECTION_ID'] = $arParams['SECTION_ID'];
            $arFilter['INCLUDE_SUBSECTIONS'] = 'Y';
        }

        $dbItems = CIBlockElement::GetList(['rand' => 'asc'], $arFilter, false, ['nTopCount' => $arParams['SELECT_ITEMS_COUNT']], $arSelect);

        $arItems = [];
        $images = [];

        while ($arRes = $dbItems->GetNext()) {
            $arItems[$arRes['ID']] = $arRes;

            if ($arRes['DETAIL_PICTURE'] > 0) {
                $images[$arRes['DETAIL_PICTURE']][] = $arRes['ID'];
            }
        }

        if (count($images) > 0) {
            $filesIter = CFile::GetList([], ['@ID' => array_keys($images)]);
            while ($img = $filesIter->Fetch()) {
                foreach ($images[$img['ID']] as $imgId) {
                    $arItems[$imgId]['DETAIL_PICTURE'] = $img;
                    $arItems[$imgId]['DETAIL_PICTURE']['SRC'] = CFile::GetFileSRC($img);
                }
            }
            unset($filesIter);
            unset($img);
            unset($imgId);
            unset($images);
        }

        return $arItems;
    }

    /**
     * @behavior get filter for actions
     * @param int $iBlockId
     * @return array
     */
    public function getActionProductsFilter(int $iBlockId)
    {
        global $idPrice;
        $iBlockActionsId = Constant::INFOBLOCKS('ACTIONS');
        $locationData = \Dev05\Classes\GlobalFactory::getInstance()->getCurrentLocation()->getData();

        $dbProp = CIBlockPropertyEnum::GetList(
            [],
            ['IBLOCK_ID' => $iBlockActionsId, 'CODE' => 'GEO_TARGETING', 'EXTERNAL_ID' => ['all', $idPrice, $locationData['CITY_ID'] ?: []]]
        );

        while ($arRes = $dbProp->GetNext()) {
            $arGeoTargetingFilter[] = $arRes['VALUE'];
        }

        $dbActions = CIBlockElement::GetList(
            ['ID' => 'DESC'],
            ['IBLOCK_ID' => $iBlockActionsId, 'ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y', 'PROPERTY_GEO_TARGETING_VALUE' => $arGeoTargetingFilter],
            false,
            false,
            ['ID']
        );

        while ($arRes = $dbActions->GetNext()) {
            $arActions['IDS'][] = $arRes['ID'];
        }

        if (!empty($arActions['IDS'])) {
            $filter = ['IBLOCK_ID' => $iBlockId, 'ACTIVE' => 'Y', 'AVAILABLE' => 'Y', 'PROPERTY_' . 'CML2_PROMOLINK' => $arActions['IDS']];

        }

        return $filter;
    }
}