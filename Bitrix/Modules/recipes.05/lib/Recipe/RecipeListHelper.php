<?php

namespace Recipes_05\Recipe;

use Dev05\Classes\Orm\Darkstore\Recipe\RecipesTable;
use DigitalWand\AdminHelper\Helper\AdminListHelper;
use Recipes_05\PermissionTrait;

/**
 * Хелпер описывает интерфейс, выводящий список рецептов.
 *
 * {@inheritdoc}
 */
class RecipeListHelper extends AdminListHelper
{
    use PermissionTrait;

    protected static $model = "\Dev05\Classes\Orm\Darkstore\Recipe\RecipesTable";
}
