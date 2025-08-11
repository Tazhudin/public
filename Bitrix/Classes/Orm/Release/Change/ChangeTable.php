<?php


namespace Dev05\Classes\Orm\Release\Change;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM;
use Bitrix\Main\Entity;

class ChangeTable extends DataManager
{

    /**
     * @behavior get user field enum table name
     * @return string|null
     */
    public static function getTableName()
    {
        return 'hl_changelog_changes_table';
    }

    /**
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [
            'ID' => [
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
            ],
            'NAME' => new Entity\StringField('UF_NAME'),
            'ACTIVE' => new Entity\BooleanField('UF_ACTIVE'),
            'DESCRIPTION' => new Entity\StringField('UF_DESCRIPTION'),
            'TYPE' => new Entity\IntegerField('UF_TYPE'),
            'FILES' => new ORM\Fields\IntegerField(
                'UF_FILES',
                [
                    'fetch_data_modification' => function () {
                        return [
                            function ($value) {
                                return unserialize($value);
                            }
                        ];
                    }
                ]
            ),
            'IMG_TITLES' => new Entity\StringField(
                'UF_IMG_TITLE',
                [
                    'fetch_data_modification' => function () {
                        return [
                            function ($value) {
                                return unserialize($value);
                            }
                        ];
                    }
                ]
            ),
            'RELEASE_ID' => new ORM\Fields\IntegerField('UF_RELEASE'),
            'TYPE_NAME' =>  new ORM\Fields\Relations\Reference(
                'TYPE_NAME',
                'Dev05\Classes\Orm\UserFieldEnum\UserFieldEnumTable',
                ['=this.TYPE' => 'ref.ID'],
                ['join_type' => 'LEFT']
            )
//            'RELEASE' => new ORM\Fields\Relations\Reference(
//                'UF_RELEASE',
//                'Dev05\Classes\Orm\Release\ReleaseTable',
//                ['=this.RELEASE_ID' => 'ref.ID'],
//                ['join_type' => 'LEFT']
//            ),
        ];
    }
}