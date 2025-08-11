<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Recipes_05\CookingSteps\CookingStepsListHelper;
use Recipes_05\Ingredients\IngredientsListHelper;
use Recipes_05\Recipe\RecipeListHelper;

if (!Loader::includeModule('digitalwand.admin_helper') || !Loader::includeModule('recipes.05')) {
    return;
}

Loc::loadMessages(__FILE__);

return array(
    array(
        'parent_menu' => 'global_menu_05ru',
        'sort' => 10,
        'text' => 'Рецепты даркстора',
        'items' => [
            [
                'text' => 'Список рецептов',
                'title' => 'Список рецептов',
                'url' => RecipeListHelper::getUrl()
            ],
            [
                'text' => 'Ингредиенты',
                'title' => 'Список ингредиентов',
                'url' => IngredientsListHelper::getUrl()
            ],
            [
                'text' => 'Шаги приготовления',
                'title' => 'Шаги приготовления',
                'url' => CookingStepsListHelper::getUrl()
            ],
        ]
    )
);
