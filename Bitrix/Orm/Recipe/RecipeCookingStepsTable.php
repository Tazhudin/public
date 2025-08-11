<?php

namespace Dev05\Classes\Orm\Darkstore\Recipe;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\FileTable;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\SystemException;

class RecipeCookingStepsTable extends DataManager
{
    /**
     * @return string|null
     */
    public static function getTableName(): ?string
    {
        return '05_recipe_cooking_steps';
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
            'RECIPE_ID' => new IntegerField(
                'RECIPE_ID',
                [
                    'primary' => false,
                    'required' => true,
                    'title' => 'Id рецепта'
                ]
            ),
            'STEP_NUM' => new IntegerField(
                'STEP_NUM',
                [
                    'primary' => false,
                    'required' => true,
                    'title' => 'Номер шага'
                ]
            ),
            'DESCRIPTION' => new StringField(
                'DESCRIPTION',
                [
                    'primary' => false,
                    'required' => true,
                    'title' => 'Описание'
                ]
            ),
            'UF_DESK_BANNER' => new IntegerField(
                'UF_DESK_BANNER',
                [
                    'primary' => false,
                    'required' => true,
                    'title' => 'Баннер для десктопа'
                ]
            ),
            'UF_MOB_BANNER' => new IntegerField(
                'UF_MOB_BANNER',
                [
                    'primary' => false,
                    'required' => true,
                    'title' => 'Баннер для моб. версии'
                ]
            ),
            'UF_APP_BANNER' => new IntegerField(
                'UF_APP_BANNER',
                [
                    'primary' => false,
                    'required' => true,
                    'title' => 'Баннер для приложений'
                ]
            ),
            'DESK_BANNER' => [
                'data_type' => FileTable::class,
                'reference' => ['=this.UF_DESK_BANNER' => 'ref.ID'],
                'join_type' => 'LEFT'
            ],
            'MOB_BANNER' => [
                'data_type' => FileTable::class,
                'reference' => ['=this.UF_MOB_BANNER' => 'ref.ID'],
                'join_type' => 'LEFT'
            ],
            'APP_BANNER' => [
                'data_type' => FileTable::class,
                'reference' => ['=this.UF_APP_BANNER' => 'ref.ID'],
                'join_type' => 'LEFT'
            ],
            new ReferenceField(
                'RECIPE',
                RecipesTable::class,
                ['=this.RECIPE_ID' => 'ref.ID']
            ),
            'USED_INGREDIENTS' => new StringField(
                'USED_INGREDIENTS',
                [
                    'primary' => false,
                    'required' => false,
                    'title' => 'Использованные ингредиенты',
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
