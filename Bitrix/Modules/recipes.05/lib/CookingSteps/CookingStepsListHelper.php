<?php

namespace Recipes_05\CookingSteps;

use Dev05\Classes\Orm\Darkstore\Recipe\RecipeCookingStepsTable;
use DigitalWand\AdminHelper\Helper\AdminListHelper;
use Recipes_05\PermissionTrait;

/**
 * Хелпер описывает интерфейс, выводящий список рецептов.
 *
 * {@inheritdoc}
 */
class CookingStepsListHelper extends AdminListHelper
{
    use PermissionTrait;

    protected static $model = RecipeCookingStepsTable::class;
}
