<?php

namespace Recipes_05\Ingredients;

use Dev05\Classes\Orm\Darkstore\Recipe\RecipeIngredientsTable;
use DigitalWand\AdminHelper\Helper\AdminListHelper;
use Recipes_05\PermissionTrait;

/**
 * Хелпер описывает интерфейс, выводящий список рецептов.
 *
 * {@inheritdoc}
 */
class IngredientsListHelper extends AdminListHelper
{
    use PermissionTrait;

    protected static $model = RecipeIngredientsTable::class;
}
