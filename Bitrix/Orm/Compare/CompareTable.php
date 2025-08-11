<?php

namespace Dev05\Classes\Orm\Compare;

use
    Bitrix\Main\Entity,
    Bitrix\Main\UserTable,
    Bitrix\Main\ORM\Data\DataManager;

class CompareTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'compare_items_table';
    }

    /**
     * @return array
     */
    public static function getMap()
    {
        return [
            new Entity\IntegerField('USER_ID', ['primary' => true]),
            new Entity\ReferenceField('USER', UserTable::class, ['=this.USER_ID' => 'ref.ID']),
            new Entity\StringField('ITEMS', ['serialized' => true]),
        ];
    }
}
