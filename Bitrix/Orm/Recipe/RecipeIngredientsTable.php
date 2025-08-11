<?php

namespace Dev05\Classes\Orm\Darkstore\Recipe;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\SystemException;

class RecipeIngredientsTable extends DataManager
{
    /**
     * @return string|null
     */
    public static function getTableName(): ?string
    {
        return '05_recipe_ingredients';
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function getMap()
    {
        return [
            'ID' => new IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true,
                    'title' => 'ID'
                ]
            ),
            'NAME' => new StringField(
                'NAME',
                [
                    'primary' => false,
                    'required' => true,
                    'title' => 'Наименование'
                ]
            ),
            'PRODUCTS' => new StringField(
                'PRODUCTS',
                [
                    'primary' => false,
                    'required' => false,
                    'title' => 'Товары',
                    'save_data_modification' => function () {
                        return [
                            function ($value) {
                                return serialize($value);
                            }
                        ];
                    },
                    'fetch_data_modification' => function () {
                        return array(
                            function ($value) {
                                return unserialize($value);
                            }
                        );
                    }
                ]
            ),
            'SECTIONS' => new StringField(
                'SECTIONS',
                [
                    'primary' => false,
                    'required' => false,
                    'title' => 'Разделы',
                    'save_data_modification' => function () {
                        return [
                            function ($value) {
                                return serialize($value);
                            }
                        ];
                    },
                    'fetch_data_modification' => function () {
                        return array(
                            function ($value) {
                                return unserialize($value);
                            }
                        );
                    }
                ]
            ),
        ];
    }
}
