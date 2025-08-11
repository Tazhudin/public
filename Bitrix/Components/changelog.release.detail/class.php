<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true){
    die();
}

use Dev05\Classes\Component\Component;
use Dev05\Classes\Changelog;
class ReleaseDetail extends Component
{
    protected $obReleaseHelper;
    protected $arRelease;

    /**
     * @behavior prepare component params
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TIME'] = ($arParams['CACHE_TIME']) ? intval($arParams['CACHE_TIME']) : 3600;
        $arParams['RELEASE_ID'] = ($arParams['RELEASE_ID']) ? intval($arParams['RELEASE_ID']) : 'null';
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
        $this->arResult['RELEASE'] = $this->arRelease;
        $this->arResult['AJAX_REQUEST_HANDLER_NAME'] = self::AJAX_REQUEST_HANDLER_NAME;

    }

    /**
     * @behavior init component variables
     * @throws Exception
     */
    protected function initComponentVariables() 
    {
        $releaseId = (int)$this->arParams['RELEASE_ID'];
        $cacheId = md5('changelog/release/' . $releaseId);
        $cacheTime = $this->arParams['CACHE_TIME'];
        $cachePath = '/hlBlock/changelog/release/';
        $phpCache = new CPHPCache();

        if (!$phpCache->InitCache($cacheTime, $cacheId, $cachePath)) {
            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cachePath);
            $CACHE_MANAGER->RegisterTag('release_detail_cache_tag'. $releaseId);

            $this->obReleaseHelper = new Changelog\Release\Helper();
            $this->getReleaseById($releaseId);

            $CACHE_MANAGER->EndTagCache();
            if ($phpCache->StartDataCache()) {
                $phpCache->EndDataCache($this->arReleases);
            }
        } else {
            $this->arReleases = $phpCache->GetVars();
        }
    }

    protected function getReleaseById(int $releaseId)
    {
        if ($releaseId <= 0) {
            \Bitrix\Iblock\Component\Tools::process404("", true, true, true );
        }

        $this->arRelease = $this->obReleaseHelper->getById($releaseId);

        if ($this->arRelease == '') {
            \Bitrix\Iblock\Component\Tools::process404("", true, true, true );
        }
    }
}