<?php

namespace Recipes_05\CookingSteps;

use Dev05\Classes\Orm\Darkstore\Recipe\RecipeCookingStepsTable;
use DigitalWand\AdminHelper\Helper\AdminEditHelper;
use Recipes_05\PermissionTrait;

/**
 * {@inheritdoc}
 */
class CookingStepsEditHelper extends AdminEditHelper
{
    use PermissionTrait;

    protected static $model = RecipeCookingStepsTable::class;
}
