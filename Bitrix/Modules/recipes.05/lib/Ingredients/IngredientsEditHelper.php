<?php

namespace Recipes_05\Ingredients;

use Dev05\Classes\Orm\Darkstore\Recipe\RecipeIngredientsTable;
use DigitalWand\AdminHelper\Helper\AdminEditHelper;
use Recipes_05\PermissionTrait;

/**
 * {@inheritdoc}
 */
class IngredientsEditHelper extends AdminEditHelper
{
    use PermissionTrait;

    protected static $model = RecipeIngredientsTable::class;
}
