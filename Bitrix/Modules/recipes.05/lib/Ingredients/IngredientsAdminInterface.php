<?php

namespace Recipes_05\Ingredients;

use _PHPStan_d01b91999\Nette\PhpGenerator\Constant;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Dev05\Api\Controllers\Darkstore\ProductsSelection;
use Dev05\Classes\HighLoadBlock;
use Dev05\Classes\SiteHelper;
use DigitalWand\AdminHelper\Helper\AdminInterface;
use DigitalWand\AdminHelper\Widget\FileWidget;
use DigitalWand\AdminHelper\Widget\HLIBlockFieldWidget;
use DigitalWand\AdminHelper\Widget\IblockElementWidget;
use DigitalWand\AdminHelper\Widget\IblockSectionWidget;
use DigitalWand\AdminHelper\Widget\NumberWidget;
use DigitalWand\AdminHelper\Widget\DateTimeWidget;
use DigitalWand\AdminHelper\Widget\StringWidget;
use DigitalWand\AdminHelper\Widget\UserWidget;

/**
 * Описание интерфейса (табок и полей) админки рецептов.
 * {@inheritdoc}
 */
class IngredientsAdminInterface extends AdminInterface
{
    /**
     * @inheritdoc
     */
    public function fields()
    {
        Loc::loadMessages(__FILE__);
        global $USER;
        return [
            'MAIN' => [
                'NAME' => 'Главная',
                'FIELDS' => [
                    'ID' => [
                        'WIDGET' => new NumberWidget(),
                        'READONLY' => true,
                        'FILTER' => true,
                        'HIDE_WHEN_CREATE' => true
                    ],
                    'NAME' => [
                        'WIDGET' => new StringWidget(),
                        'FILTER' => true,
                        'REQUIRED' => true
                    ],
                ]
            ],
            'PRODUCTS' => [
                'NAME' => 'Товары',
                'FIELDS' => [
                    'PRODUCTS' => [
                        'TITLE' => 'Товары ингредиента',
                        'FILTER' => true,
                        'WIDGET' => new IblockElementWidget(),
                        'IBLOCK_ID' => \Dev05\Classes\Constant::INFOBLOCKS('CATALOG_1C', SiteHelper::DARKSTORE_SITE_ID),
                        'MULTIPLE' => 'Y',
                    ],
                ]
            ],
            'SECTIONS' => [
                'NAME' => 'Разделы',
                'FIELDS' => [
                    'SECTIONS' => [
                        'TITLE' => 'Разделы ингредиента',
                        'FILTER' => true,
                        'WIDGET' => new IblockSectionWidget(),
                        'IBLOCK_ID' => \Dev05\Classes\Constant::INFOBLOCKS('CATALOG_1C', SiteHelper::DARKSTORE_SITE_ID),
                        'MULTIPLE' => 'Y',
                    ],
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function helpers(): array
    {
        return [
            '\Recipes_05\Ingredients\IngredientsListHelper' => [
                'BUTTONS' => []
            ],
            '\Recipes_05\Ingredients\IngredientsEditHelper' => [
                'BUTTONS' => []
            ]
        ];
    }
}
