<?php

namespace Recipes_05\Recipe;

use _PHPStan_d01b91999\Nette\PhpGenerator\Constant;
use Bitrix\Main\Entity\BooleanField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Dev05\Api\Controllers\Darkstore\ProductsSelection;
use Dev05\Classes\AdminInterface\DigitalWand\Widget\OrmTable;
use Dev05\Classes\HighLoadBlock;
use Dev05\Classes\Orm\Darkstore\Recipe\RecipeCookingStepsTable;
use Dev05\Classes\Orm\Darkstore\Recipe\RecipeIngredientsTable;
use Dev05\Classes\Orm\Darkstore\Recipe\RecipeIngredientsMappingTable;
use Dev05\Classes\SiteHelper;
use DigitalWand\AdminHelper\Helper\AdminInterface;
use DigitalWand\AdminHelper\Widget\CheckboxWidget;
use DigitalWand\AdminHelper\Widget\FileWidget;
use DigitalWand\AdminHelper\Widget\HLIBlockFieldWidget;
use DigitalWand\AdminHelper\Widget\IblockElementWidget;
use DigitalWand\AdminHelper\Widget\NumberWidget;
use DigitalWand\AdminHelper\Widget\DateTimeWidget;
use DigitalWand\AdminHelper\Widget\StringWidget;
use DigitalWand\AdminHelper\Widget\TextAreaWidget;
use DigitalWand\AdminHelper\Widget\UserWidget;

/**
 * Описание интерфейса (табок и полей) админки рецептов.
 * {@inheritdoc}
 */
class RecipeAdminInterface extends AdminInterface
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
                    'ACTIVE' => [
                        'WIDGET' => new CheckboxWidget(),
                        'FILTER' => true,
                        'FIELD_TYPE' => 'boolean'
                    ],
                    'NAME' => [
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
                    'COOKING_TIME' => [
                        'WIDGET' => new StringWidget(),
                        'FILTER' => false,
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
                    'COOKING_STEPS' => [
                        'WIDGET' => new HLIBlockFieldWidget(),
                        'FILTER' => false,
                    ],
                ]
            ],
            'INGREDIENTS' => [
                'NAME' => 'Ингредиенты',
                'FIELDS' => [
                    'INGREDIENTS' => [
                        'TITLE' => 'Ингредиенты',
                        'HELPER' => 'Dev05\Classes\AdminInterface\DigitalWand\OrmTableListHelper',
                        'WIDGET' => new OrmTable(),
                        'MULTIPLE_FIELDS' => ['ID', 'VALUE' => 'INGREDIENT_ID', 'ENTITY_ID' => 'RECIPE_ID'],
                        'CLASS' => RecipeIngredientsTable::class,
                        'HEADER' => false,
                        'FORCE_SELECT' => true,
                        'FILTER' => true,
                        'TITLE_FIELD_NAME' => 'NAME',
                        'DELETE_REFERENCED_DATA' => true,
                        'ADDITIONAL_URL_PARAMS' => ['model' => RecipeIngredientsTable::class],
                        'ADDITIONAL_INPUTS' => "<br>Количество: " .
                            "<input type=\"text\" name=\"INGREDIENTS[{{field_id}}][QUANTITY]\" " .
                            "value=\"{{REFERENCE.QUANTITY}}\">",
                        'MULTIPLE' => true
                    ],
                    'INGREDIENTS.QUANTITY' => array(
                        'WIDGET' => new StringWidget(),
                        'HEADER' => false,
                        'FORCE_SELECT' => true,
                        'FILTER' => false,
                        'HIDDEN' => true,
                    ),
                ]
            ],
            'COOKING_STEPS' => [
                'NAME' => 'Шаги приготовления',
                'FIELDS' => [
                    'COOKING_STEPS' => [

                        'TITLE' => 'Шаги приготовления',
                        'HELPER' => 'Dev05\Classes\AdminInterface\DigitalWand\OrmTableListHelper',
                        'WIDGET' => new OrmTable(),
                        'CLASS' => RecipeCookingStepsTable::class,
                        'TITLE_FIELD_NAME' => 'STEP_NUM',
                        'MULTIPLE_FIELDS' => ['ID' => 'VALUE', 'VALUE' => 'ID', 'ENTITY_ID' => 'RECIPE_ID'],
                        'FILTER' => true,
                        'ADDITIONAL_URL_PARAMS' => ['model' => RecipeCookingStepsTable::class],
                        'MULTIPLE' => true
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
        return array(
            '\Recipes_05\Recipe\RecipeListHelper' => array(
                'BUTTONS' => array(
                )
            ),
            '\Recipes_05\Recipe\RecipeEditHelper' => array(
                'BUTTONS' => array(
                )
            )
        );
    }
}
