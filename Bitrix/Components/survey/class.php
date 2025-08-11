<?php

use Bitrix\Main\Data\Cache;
use Dev05\Classes\Component\Component;
use Dev05\Classes\Survey;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class SurveyComponent extends Component
{
    /**
     * @var Survey\Survey
     */
    private $obSurvey;
    private $arSurvey = [];

    private function getIdByCode($iblock, $code)
    {
        return (int)\Bitrix\Iblock\SectionTable::getRow([
            'filter' => [
                '=IBLOCK.CODE' => $iblock,
                '=CODE' => $code,
                '=IBLOCK_SECTION_ID' => false,
            ],
            'select' => ['ID'],
        ])['ID'] ?: 0;
    }

    /**
     * @behavior prepare component params
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TIME'] = ($arParams['CACHE_TIME']) ? (int)$arParams['CACHE_TIME'] : 3600;
        $arParams['SURVEY_ID'] = $this->getIdByCode($arParams['IBLOCK_CODE'], $arParams['SURVEY_CODE']);
        $arParams['QUESTIONS_TYPE'] = ($arParams['QUESTIONS_TYPE']) ? (int)$arParams['QUESTIONS_TYPE'] : 0;
        return $arParams;
    }

    /**
     * @behavior return name for input
     * @param array $guestion
     * @return string
     */
    public function getInputName(array $question) :string
    {
        if (!empty($question)) {
            return "QUESTIONS[{$question['ID']}]" . ($question['TYPE'] === 'checkbox' ? '[]' : '');
        }
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
        $this->arResult['SURVEY'] = $this->arSurvey;
        $this->arResult['CSRF_TOKEN'] = Dev05\Classes\Security\Helper::getOnceCsrfToken('survey');
        $this->arResult['AJAX_REQUEST_HANDLER_NAME'] = self::AJAX_REQUEST_HANDLER_NAME;
    }


    /**
     * @behavior init component variables
     */
    protected function initComponentVariables()
    {
        $cacheId = md5('survey' . $this->arParams['IBLOCK_CODE'] . $this->arParams['SURVEY_ID']);
        $cacheTime = $this->arParams['CACHE_TIME'];
        $cachePath = '/iblock/' . $this->arParams['IBLOCK_CODE'] . '/' . $this->arParams['SURVEY_ID'];
        $phpCache = new CPHPCache();

        if (!$phpCache->InitCache($cacheTime, $cacheId, $cachePath)) {
            global $CACHE_MANAGER;

            $CACHE_MANAGER->StartTagCache($cachePath);
            $CACHE_MANAGER->RegisterTag('survey_tag_' . $this->arParams['SURVEY_ID']);

            $this->obSurvey = new Survey\Survey();
            $this->arSurvey = $this->obSurvey->getSurvey($this->arParams['IBLOCK_CODE'], $this->arParams['SURVEY_ID']);

            foreach ($this->arSurvey['QUESTIONS'] as &$quest) {
                $quest['INPUT_NAME'] = $this->getInputName($quest);
            }

            $CACHE_MANAGER->EndTagCache();
            if ($phpCache->StartDataCache()) {
                $phpCache->EndDataCache($this->arSurvey);
            }
        } else {
            $this->arSurvey = $phpCache->GetVars();
        }
    }
}