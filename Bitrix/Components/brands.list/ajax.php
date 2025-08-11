<?php

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;

\CBitrixComponent::includeComponentClass('05:brands.list');

class BrandsSectionsFilterAjaxController extends Controller
{

    /**
     * @var array
     */
    private $arBrands;

    /**
     * @var BrandsList
     */
    private $component;

    /**
     * @behavior ajax request handler
     * @param int $sectionId
     * @return array
     * @throws Error
     */
    public function ajaxRequestHandlerAction($sectionId)
    {
        if ((int)$sectionId > 0 || $sectionId === 'all') {
            $this->component = new BrandsList();
            $this->component->arParams['SECTION_ID'] = (int)$sectionId;
            $this->arBrands = $this->component->getBrands();
        } else {
            throw new Error('Неверный id раздела');
        }

        if (!empty($this->arBrands)) {
            return $this->component->splitToCharacterGroups($this->arBrands);
        } else {
            throw new Error('Не удалось получить брэнды раздела');
        }
    }

    /**
     * @behavior configure request filters
     * @return array
     */
    public function configureActions(): array
    {
        return [
            'ajaxRequestHandler' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_POST])
                ],
                'postfilters' => []
            ]
        ];
    }
}