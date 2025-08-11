<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Dev05\Classes\Component\Component;
use Dev05\Classes\Changelog;

class ReleaseList extends Component
{
    protected $obReleaseHelper;
    protected $arReleases;
    protected $componentPage;

    /**
     * @behavior prepare component params
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TIME'] = ($arParams['CACHE_TIME']) ? intval($arParams['CACHE_TIME']) : 3600;
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
        $this->arResult['RELEASES'] = $this->arReleases;
    }

    /**
     * @behavior init component variables
     * @throws Exception
     */
    protected function initComponentVariables()
    {
        $cacheId = md5('changelog' . $this->arParams['PAGE']);
        $cacheTime = $this->arParams['CACHE_TIME'];
        $cachePath = '/hlBlock/changelog/';
        $phpCache = new CPHPCache();

        if (!$phpCache->InitCache($cacheTime, $cacheId, $cachePath)) {
            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cachePath);
            $CACHE_MANAGER->RegisterTag('releases_cache_tag');

            $this->obReleaseHelper = new Changelog\Release\Helper();
            $this->getReleases();

            $CACHE_MANAGER->EndTagCache();
            if ($phpCache->StartDataCache()) {
                $phpCache->EndDataCache($this->arReleases);
            }
        } else {
            $this->arReleases = $phpCache->GetVars();
        }
    }

    /**
     * @behavior get releases
     */
    protected function getReleases()
    {
        $arNavParams = [
            'PAGE_ITEMS_COUNT' => $this->arParams['PAGE_ITEMS_COUNT'],
            'LIMIT' => $this->arParams['LIMIT'],
            'PAGE' => $this->arParams['PAGE'],
            'OFFSET' => ($this->arParams['PAGE'] - 1) * $this->arParams['PAGE_ITEMS_COUNT'],
        ];
        $this->arReleases = $this->obReleaseHelper->getReleases($arNavParams);
    }
}