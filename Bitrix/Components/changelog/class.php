<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Dev05\Classes\Component\Component;
use Dev05\Classes\Changelog;

class ReleasesController extends Component
{
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
        $this->IncludeComponentTemplate($this->componentPage);
    }



    /**
     * @behavior set component result variables
     */
    protected function setComponentResult()
    {
        $this->arResult = ['RELEASE_ID' => $this->arResult['VARIABLES']['ID']];
    }

    /**
     * @behavior init component variables
     * @throws Exception
     */
    protected function initComponentVariables()
    {
        $this->componentPage = $this->makeFriendlyUrl();
    }

    private function makeFriendlyUrl(): string
    {
        $componentVariables = ['ID'];
        $variables = [];
        $defaultUrlTemplates = $this->getDefaultUrlTemplates();
        $templatesUrls = CComponentEngine::makeComponentUrlTemplates(
            $defaultUrlTemplates,
            $this->arParams['SEF_URL_TEMPLATES']
        );

        foreach ($templatesUrls as $url => $value) {
            $this->arResult['PATH_TO_' . ToUpper($url)] = $this->arParams['SEF_FOLDER'] . $value;
        }

        $variableAliases = CComponentEngine::makeComponentVariableAliases([], $this->arParams['VARIABLE_ALIASES']);

        $componentPage = CComponentEngine::parseComponentPath(
            $this->arParams['SEF_FOLDER'],
            $templatesUrls,
            $variables
        );

        CComponentEngine::initComponentVariables($componentPage, $componentVariables, $variableAliases, $variables);
        if (empty($componentPage)) {
            $componentPage = 'list';
        }

        $this->arResult = array_merge(
            [
                'SEF_FOLDER' => $this->arParams['SEF_FOLDER'],
                'URL_TEMPLATES' => $templatesUrls,
                'VARIABLES' => $variables,
                'ALIASES' => $variableAliases,
            ],
            $this->arResult
        );
        return $componentPage;
    }

    private function getDefaultUrlTemplates()
    {
        return  [
            'list' => 'index.php',
            'detail' => 'release/'
        ];
    }
}
