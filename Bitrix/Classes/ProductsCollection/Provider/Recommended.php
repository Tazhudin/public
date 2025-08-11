<?php
namespace Dev05\Classes\ProductsCollection\Provider;

use CFile;
use CIBlockElement;
use Dev05\Classes\ProductsCollection\ISelectionProductsProvider;

class Recommended implements ISelectionProductsProvider
{

    /**
     * @behavior get products by sort 'shows' => 'desc'
     * @param $arParams (use sectionId and limit for orders)
     * @return array
     */
    public function getProducts(array $arParams)
    {
        $arFilter = ['IBLOCK_ID' => $arParams['IBLOCK_ID'], '=ACTIVE' => 'Y', '=AVAILABILITY' => 'Y', '>QUANTITY' => 0];

        if ($arParams['SECTION_ID'] > 0) {
            $arFilter['SECTION_ID'] = $arParams['SECTION_ID'];
            $arFilter['INCLUDE_SUBSECTIONS'] = 'Y';
        }

        $arSelect = ['ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'NAME', 'DETAIL_PAGE_URL', 'DETAIL_PICTURE', 'PREVIEW_PICTURE', 'QUANTITY', 'AVAILABLE', 'PROPERTY_CML2_ELEMENT_PAGE_TITLE'];

        $dbItems = CIBlockElement::GetList(['shows' => 'desc'], $arFilter, false, ['nTopCount' => $arParams['SELECT_ITEMS_COUNT']], $arSelect);

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
}