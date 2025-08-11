<?php

namespace Dev05\Classes\ProductsCollection\Provider;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Order;
use CFile;
use CIBlockElement;
use Dev05\Classes\ProductsCollection\ISelectionProductsProvider;
class BuyingNow implements ISelectionProductsProvider
{
    /**
     * @behavior get products
     * @param $arParams (use sectionId, limit for orders and items count )
     * @return array
     */
    public function getProducts(array $arParams): array
    {
        $arItemIds = [];
        $sectionId = (int) $arParams['SECTION_ID'];
        $arItemIds = $sectionId > 0 ?  $this->getSectionProductIds($sectionId, $arParams) : $this->getAllProductIds($arParams['BUYING_NOW_LIMIT_IN_ORDERS']);
        if (!empty($arItemIds)) {
            $arFilter = ['=IBLOCK_ID' => $arParams['IBLOCK_ID'], '=ID' => $arItemIds, '=ACTIVE' => 'Y', '=AVAILABILITY' => 'Y', '>QUANTITY' => 0];
            $arSelect = ['ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'NAME', 'DETAIL_PAGE_URL', 'DETAIL_PICTURE', 'PREVIEW_PICTURE', 'QUANTITY', 'AVAILABLE', 'PROPERTY_CML2_ELEMENT_PAGE_TITLE'];
            $dbItems = CIBlockElement::GetList([], $arFilter, false, ['nTopCount' => $arParams['SELECT_ITEMS_COUNT']], $arSelect);
            $arItems = [];
            $images = [];
            while ($arRes = $dbItems->fetch()) {
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
        }
        return $arItems;
    }
    /**
     * @behavior get section productIds from last orders
     * @param $arParams (use sectionId, limit for orders and items count )
     * @return array
     */
    public function getSectionProductIds(int $sectionId, array $arParams): array
    {
        $dbOrders = Order::getList([
            'select' => [
                'PRODUCT_ID' => 'BASKET.PRODUCT.ID',
                'IBLOCK_SECTION_ID' => 'BASKET.PRODUCT.IBLOCK.IBLOCK_SECTION_ID',
            ],
            'group' => ['PRODUCT_ID'],
            'filter' => ['=SECTIONS.IBLOCK_SECTION_ID' => $sectionId ],
            'order' => ['ID' => 'DESC'],
            'limit' => $arParams['BUYING_NOW_LIMIT_IN_ORDERS'],
            'runtime' => [
                'SECTIONS' => [
                    'data_type' => SectionTable::class,
                    'reference' => [
                        '=this.IBLOCK_SECTION_ID' => 'ref.ID'
                    ]
                ]
            ]
        ]);
        $arItemIds = [];
        while ($arRes = $dbOrders->fetch()) {
            $arItemIds[] = $arRes['PRODUCT_ID'];
        }
        return $arItemIds;
    }
    /**
     * @behavior get productIds from last orders
     * @param $arParams (limit for orders and items count )
     * @return array
     */
    public function getAllProductIds(int $ordersLimit): array
    {
        $dbOrders = Order::getList([
            'select' => [
                'PRODUCT_ID' => 'BASKET.PRODUCT.ID',
            ],
            'group' => ['PRODUCT_ID'],
            'filter' => [],
            'order' => ['ID' => 'DESC'],
            'limit' => $ordersLimit,
        ]);
        $arItemIds = [];
        while ($arRes = $dbOrders->fetch()) {
            $arItemIds[] = $arRes['PRODUCT_ID'];
        }
        return $arItemIds;
    }
}