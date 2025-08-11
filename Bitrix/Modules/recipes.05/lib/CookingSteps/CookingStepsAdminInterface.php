<?php

namespace Recipes_05\CookingSteps;

use Bitrix\Main\Localization\Loc;
use Dev05\Classes\AdminInterface\DigitalWand\Widget\OrmTable;
use Dev05\Classes\Orm\Darkstore\Recipe\RecipesTable;
use DigitalWand\AdminHelper\Helper\AdminInterface;
use DigitalWand\AdminHelper\Widget\FileWidget;
use DigitalWand\AdminHelper\Widget\NumberWidget;
use DigitalWand\AdminHelper\Widget\StringWidget;
use DigitalWand\AdminHelper\Widget\TextAreaWidget;

/**
 * Описание интерфейса (табок и полей) админки рецептов.
 * {@inheritdoc}
 */
class CookingStepsAdminInterface extends AdminInterface
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
                    'RECIPE_ID' => array(
                        'TITLE' => 'Рецепт',
                        'HELPER' => 'Dev05\Classes\AdminInterface\DigitalWand\OrmTableListHelper',
                        'WIDGET' => new OrmTable(),
                        'CLASS' => RecipesTable::class,
                        'TITLE_FIELD_NAME' => 'NAME',
                        'FILTER' => true,
                        'REQUIRED' => true
                    ),
                    'STEP_NUM' => [
                        'WIDGET' => new StringWidget(),
                        'FILTER' => true,
                        'REQUIRED' => true
                    ],
                    'DESCRIPTION' => [
                        'WIDGET' => new TextAreaWidget(),
                        'FILTER' => false,
                        'COLS' => 40,
                        'ROWS' => 2,
                        'REQUIRED' => true
                    ],
                    'UF_DESK_BANNER' => [
                        'WIDGET' => new FileWidget(),
                        'FILTER' => false,
                        'IMAGE' => true,
                        'REQUIRED' => true
                    ],
                    'UF_MOB_BANNER' => [
                        'WIDGET' => new FileWidget(),
                        'FILTER' => false,
                        'IMAGE' => true,
                        'REQUIRED' => true
                    ],
                    'UF_APP_BANNER' => [
                        'WIDGET' => new FileWidget(),
                        'FILTER' => false,
                        'IMAGE' => true,
                        'REQUIRED' => true
                    ],
                    'USED_INGREDIENTS' => [
                        'WIDGET' => new TextAreaWidget(),
                        'FILTER' => false,
                        'COLS' => 40,
                        'ROWS' => 2,
                        'REQUIRED' => false
                    ]
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    public function helpers(): array
    {
        return [
            '\Recipes_05\CookingSteps\CookingStepsListHelper' => [
                'BUTTONS' => []
            ],
            '\Recipes_05\CookingSteps\CookingStepsEditHelper' => [
                'BUTTONS' => []
            ]
        ];
    }
}
