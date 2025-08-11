<?php

namespace Dev05\Classes\Orm\Darkstore\Recipe;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\SystemException;

class RecipeIngredientsMappingTable extends DataManager
{
    /**
     * @return string|null
     */
    public static function getTableName(): ?string
    {
        return '05_recipe_to_ingredients';
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
            'INGREDIENT_ID' => new IntegerField(
                'INGREDIENT_ID',
                [
                    'primary' => false,
                    'required' => true,
                    'title' => 'Id ингредиента'
                ]
            ),
            'QUANTITY' => new StringField(
                'QUANTITY',
                [
                    'required' => true,
                    'title' => 'Количество'
                ]
            ),
            new ReferenceField(
                'INGREDIENTS',
                RecipeIngredientsMappingTable::class,
                ['=this.INGREDIENT_ID' => 'ref.ID']
            ),
        ];
    }
}
