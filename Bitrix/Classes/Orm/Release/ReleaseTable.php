<?php
namespace Dev05\Classes\Orm\Release;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\Entity;
use Dev05\Classes\Messages;

class ReleaseTable extends DataManager
{
    /**
     * @behavior get user field enum table name
     * @return string|null
     */
    public static function getTableName()
    {
        return 'hl_changelog_releases_table';
    }

    /**
     * @return array
     */
    public static function getMap()
    {
        return [
            'ID' => new Entity\IntegerField('ID', [
                'primary' => true,
                'title' => Messages\Orm\Release\ReleaseTable::getMessages()['RELEASE_ID']
            ]),
            'NAME' => new Entity\StringField('UF_NAME'),
            'RATE' => new Entity\IntegerField('UF_RATE'),
            'DESCRIPTION' => new Entity\StringField('UF_DESCRIPTION'),
            'IMPORTANT' => new Entity\BooleanField('UF_IMPORTANT')
        ];
    }
}