<?php


namespace Dev05\Classes\Orm\Brand;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM;

class BrandTable extends DataManager
{
    /**
     * @behavior get user field enum table name
     * @return string|null
     */
    public static function getTableName()
    {
        return 'rz_bitronic2_brand_reference';
    }

    /**
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [
            'ID' => new ORM\Fields\IntegerField(
                'ID',
                ['primary' => true,]
            ),
            'NAME' => new ORM\Fields\StringField(
                'UF_NAME',
                []
            ),
            'XML_ID' => new ORM\Fields\StringField(
                'UF_XML_ID',
                []
            )
        ];
    }
}