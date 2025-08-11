<?php

namespace Recipes_05;

trait PermissionTrait
{
    protected function hasReadRights(): bool
    {
        return Permission::canRead();
    }

    /**
     * @return bool
     */
    protected function hasWriteRights(): bool
    {
        return Permission::canWrite();
    }

    /**
     * @return bool
     */
    protected function hasDeleteRights(): bool
    {
        return Permission::canWrite();
    }
}
