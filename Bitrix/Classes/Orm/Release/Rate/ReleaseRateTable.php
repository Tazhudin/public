<?php


namespace Dev05\Classes\Orm\Release\Rate;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM;
use Bitrix\Main\Entity;

/**
 * Описание класса
 * Class ReleaseRateTable
 * @package Dev05\Classes\Orm\Release\Rate
 */
class ReleaseRateTable extends DataManager
{

    /**
     * @behavior get releases evaluation table name
     * @return string|null
     */
    public static function getTableName()
    {
        return 'hl_changelog_releases_evaluation_table';
    }

    /**
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [
            'ID' => array('data_type' => 'integer', 'primary' => true, 'autocomplete' => true,),
            'RELEASE_ID' => new Entity\IntegerField('UF_RELEASE_ID'),
            'USER_ID' => new Entity\IntegerField('UF_USER_ID'),
            'VALUE' => new Entity\IntegerField('UF_EVALUATION'),

            // REF fields
            'RELEASE' => new ORM\Fields\Relations\Reference(
                'UF_RELEASE_ID',
                \Dev05\Classes\Orm\Release\ReleaseTable::class,
                ['=this.UF_RELEASE_ID' => 'ref.ID'],
                ['join_type' => 'LEFT']
            ),
        ];
    }
}