<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true){
    die();
}

use Bitrix\Main\Localization\Loc;
use Dev05\Classes\Brand;
use Dev05\Classes\Component\Component;
use Dev05\Classes\GlobalFactory;

class BrandDetail extends Component
{
    private $brandData;
    private $sections;
    private $promoBrands;
    private $actionIds;

    /**
     * @behavior prepare component params
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TIME'] = ($arParams['CACHE_TIME']) ? intval($arParams['CACHE_TIME']) : 3600;
        $arParams['BRAND_LINK'] = htmlspecialchars($arParams['BRAND_LINK']);
        return $arParams;
    }
    
     /**
     * @behavior execute component
     */
    public function executeComponent()
    {
        $this->initComponentVariables();
        $this->setComponentResult();
        $this->initMetaData();
        $this->includeComponentTemplate();
    }


    /**
     * @behavior set component result variables
     */
    protected function setComponentResult() 
    {
        $this->arResult['INFO'] = $this->brandData;
        $this->arResult['SECTIONS'] = $this->sections;
        $this->arResult['ACTIONS'] = $this->actionIds;
        $this->arResult['RESIZER'] = GlobalFactory::getInstance()->getImageResizer();
        if (!empty($this->promoBrands)) {
            $this->arResult['PROMO'] = $this->promoBrands;
        }
    }

    /**
     * @behavior init component variables
     * @throws Exception
     */
    protected function initComponentVariables() 
    {
        $obBrand = new Brand($this->arParams['BRAND_LINK']);
        /** Проверяем наличие промо-брендов **/
        if (\Bitrix\Main\Loader::includeModule('params.05') && in_array($this->arParams['BRAND_LINK'], \Params05\Helper::getInstance()->getPromoBrandsContent())) {
            $this->promoBrands = $obBrand->getPromoBrands();
        } else {
            $this->brandData = $obBrand->getData();
            if (!$this->brandData) {
                \Bitrix\Iblock\Component\Tools::process404(Loc::getMessage('BRANDS_DETAIL_BRAND_NOT_FOUND'), true, true, true);
            }
            $this->sections = $obBrand->getSections([$this->brandData['UF_XML_ID']], [$this->brandData['ID']]);
            $this->actionIds = $obBrand->getActionsIds();
        }
    }

    protected function initMetaData(){
        if($this->arResult['INFO']['UF_NAME']){
            global $APPLICATION;
            $MetaTitle = Loc::getMessage('BRANDS_TITLE',['#BRAND#' => $this->arResult['INFO']['UF_NAME']]);
            $MetaDescription = Loc::getMessage('BRANDS_DESCRIPTION',['#BRAND#' => $this->arResult['INFO']['UF_NAME']]);
            $APPLICATION->setTitle($MetaTitle);
            $APPLICATION->setPageProperty('description',$MetaDescription);
        }
    }
}