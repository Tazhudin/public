<?php

namespace Recipes_05\Recipe;

use Dev05\Classes\Orm\Darkstore\Recipe\RecipesTable;
use DigitalWand\AdminHelper\Helper\AdminEditHelper;
use Recipes_05\PermissionTrait;

/**
 * {@inheritdoc}
 */
class RecipeEditHelper extends AdminEditHelper
{
    use PermissionTrait;

    protected static $model = RecipesTable::class;
}
