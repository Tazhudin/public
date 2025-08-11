<?php

namespace Dev05\Classes\Orm\Darkstore\Recipe;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\FileTable;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\SystemException;

class RecipesTable extends DataManager
{
    /**
     * @return string|null
     */
    public static function getTableName(): ?string
    {
        return '05_recipes';
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
            'ACTIVE' => new IntegerField(
                'ACTIVE',
                [
                    'title' => 'Активность',
                    'values' => ['0', '1']
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
            'DESCRIPTION' => new StringField(
                'DESCRIPTION',
                [
                    'primary' => false,
                    'required' => true,
                    'title' => 'Описание'
                ]
            ),
            'COOKING_TIME' => new StringField(
                'COOKING_TIME',
                [
                    'primary' => false,
                    'required' => true,
                    'title' => 'Общее время приготовления'
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
                'INGREDIENTS',
                RecipeIngredientsMappingTable::class,
                ['=this.ID' => 'ref.RECIPE_ID']
            ),
            new ReferenceField(
                'COOKING_STEPS',
                RecipeCookingStepsTable::class,
                ['=this.ID' => 'ref.RECIPE_ID']
            )
        ];
    }
}
