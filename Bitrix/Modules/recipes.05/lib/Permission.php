<?php

namespace Recipes_05;

class Permission
{
    /**
     * @return bool
     */
    public static function canWrite(): bool
    {
        global $APPLICATION;
        return $APPLICATION->GetGroupRight('recipes.05') >= 'W';
    }

    /**
     * @return bool
     */
    public static function canRead(): bool
    {
        global $APPLICATION;
        return $APPLICATION->GetGroupRight('recipes.05') >= 'R';
    }
}
