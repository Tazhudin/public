<?php

use Dev05\Classes\Component\Component;
use Dev05\Classes\Constant;
use Dev05\Classes\ProductsCollection\SelectionProducts;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class ProductsCollectionComponent extends Component
{
    /**
     * @var
     */
    private $obSelectionProducts;
    private $collectionProducts = [];
    private $productSections = [];

    /**
     * @behavior prepare component params
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TIME'] = ($arParams['CACHE_TIME']) ? (int)$arParams['CACHE_TIME'] : 3600;
        $arParams['IBLOCK_ID'] = ($arParams['IBLOCK_ID']) ? (int)$arParams['IBLOCK_ID'] : Constant::INFOBLOCKS('CATALOG_1C');
        $arParams['SECTION_ID'] = ($arParams['SECTION_ID']) ? (int)$arParams['SECTION_ID'] : 0;
        $arParams['BUYING_NOW_LIMIT_IN_ORDERS'] = ($arParams['BUYING_NOW_LIMIT']) ? (int)$arParams['BUYING_NOW_LIMIT_IN_ORDERS'] : 100;
        $arParams['SELECT_ITEMS_COUNT'] = ($arParams['ELEMENTS_COUNT']) ? (int)$arParams['ELEMENTS_COUNT'] : 20;
        $arParams['COLLECTION_TYPE'] = ($arParams['COLLECTION_TYPE']) ? $arParams['COLLECTION_TYPE'] : 'BESTSELLERS';
        $arParams['ACTIVE_COLLECTION_TYPE'] = (in_array($arParams['ACTIVE_COLLECTION_TYPE'], (array)$arParams['COLLECTION_TYPE'])) ? $arParams['ACTIVE_COLLECTION_TYPE'] :  $arParams['COLLECTION_TYPE'];
        return $arParams;
    }

    /**
     * @behavior execute component
     */
    public function executeComponent()
    {
        try {
            $this->initComponentVariables();
            $this->setComponentResult();
            $this->includeComponentTemplate();
        } catch (Exception $exception) {
            $this->AbortResultCache();
        }
    }


    /**
     * @behavior set component result variables
     */
    protected function setComponentResult()
    {
        $this->arResult['ITEMS'] = $this->collectionProducts;

        if (count($this->productSections) > 0) {
            $this->arResult['SECTIONS'] = $this->productSections;
        }
    }


    /**
     * @behavior init component variables
     */
    protected function initComponentVariables()
    {
        $this->arTypes = Constant::getProductCollectionTypes();
        $currentCollectionClass = $this->arTypes[$this->arParams['ACTIVE_COLLECTION_TYPE']]['CLASS'];

        $this->obSelectionProducts = new SelectionProducts(new $currentCollectionClass);
        $this->collectionProducts = $this->getProducts();
        if ($this->arParams['USE_FILTER_BY_SECTIONS'] === 'Y') {
            $this->productSections = $this->getCollectionSections();
        }
    }

    /**
     * @behavior get products by selected collection-type and section
     */
    protected function getProducts()
    {

        return $this->obSelectionProducts->getProducts($this->arParams);
    }

    /**
     * @behavior get sections with property 'UF_USE_IN_COLLECTIONS_FILTER = Y'
     */
    protected function getCollectionSections()
    {
        $sections = [];
        $dbSections = CIBlockSection::GetList([], ['IBLOCK_ID' => $this->arParams['IBLOCK_ID'], 'UF_USE_IN_COLLECTIONS_FILTER' => '1'], false, ['ID', 'NAME', 'UF_USE_IN_COLLECTIONS_FILTER']);
        while ($section = $dbSections->GetNext()) {
            $sections[$section['ID']] = $section;
        }

        return $sections;
    }
}
