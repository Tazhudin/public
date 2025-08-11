<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Dev05\Classes\Brand;
use Dev05\Classes\Component\Component;

class BrandsList extends Component
{
    protected $componentPage;
    private $brands;
    private $sectionId;
    private $brandSections;
    private $sortedBrands;

    /**
     * @behavior prepare component params
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TIME'] = ($arParams['CACHE_TIME']) ? intval($arParams['CACHE_TIME']) : 3600;
        $arParams['SECTION_ID'] = $_GET['SECTION_ID'] > 0 ? (int)$_GET['SECTION_ID'] : 0;

        return $arParams;
    }

    /**
     * @behavior execute component
     */
    public function executeComponent()
    {
        $this->initComponentVariables();
        $this->setComponentResult();
        $this->includeComponentTemplate();
    }

    /**
     * @behavior set component result variables
     */
    protected function setComponentResult()
    {
        $this->arResult['BRANDS'] = $this->brands;
        $this->arResult['SORTED_BRANDS'] = $this->sortedBrands;
        $this->arResult['SECTIONS'] = $this->brandSections;
        $this->arResult['AJAX_REQUEST_HANDLER_NAME'] = self::AJAX_REQUEST_HANDLER_NAME;
    }

    /**
     * @behavior init component variables
     * @throws Exception
     */
    protected function initComponentVariables()
    {
        $this->brands = $this->getBrands();
        $this->sortedBrands = $this->splitToCharacterGroups($this->brands);
        $this->brandSections = $this->getBrandSections();
    }

    /**
     * @behavior get brands
     */
    public function getBrands()
    {
        $arBrands = [];
        $sectionId = (int) $this->arParams['SECTION_ID'];
        $arBrands = $sectionId > 0 ? Brand::getSectionBrands($sectionId) : Brand::getAllBrands();

        return $arBrands;
    }

    /**
     * @behavior sort brands by symbols
     * @return array
     */
    public function splitToCharacterGroups($arBrands): array
    {
        $sortedBrands = [];
        foreach ($arBrands as $id => $brand) {
            $firstLetter = strtoupper(mb_substr(trim($brand['UF_NAME']), 0, 1));
            if (!isset($sortedBrands[$firstLetter])) {
                $sortedBrands[$firstLetter] = [];
            }
            $sortedBrands[$firstLetter][$id] = $brand;
        }

        return $sortedBrands;
    }

    /**
     * @behavior get sections with brands
     */
    protected function getBrandSections()
    {
        $arXmlIDs = [];
        foreach ($this->brands as $brand) {
            $arXmlIDs[] = $brand['UF_XML_ID'];
        }
        return Brand::getSections($arXmlIDs, [], true);
    }
}