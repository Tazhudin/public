<?php

namespace Dev05\Classes\Orm\Darkstore\Order\Evaluation;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Bitrix\Sale\OrderTable;

class EvaluationsTable extends DataManager
{
    /**
     * @behavior get user field enum table name
     * @return string|null
     */
    public static function getTableName(): ?string
    {
        return 'b_hl_order_evaluations';
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
                ['primary' => true,]
            ),
            'USER_EVALUATION' => new IntegerField(
                'UF_USER_EVALUATION',
                [
                    'required' => true,
                    'title' => 'Оценка заказа'
                ]
            ),
            'USER_COMMENT' => new StringField(
                'UF_USER_COMMENT',
                [
                    'title' => 'Комментарий пользователя'
                ]
            ),
            'USER_ID' => new IntegerField(
                'UF_USER_ID',
                [
                    'required' => true,
                    'title' => 'Id пользователя'
                ]
            ),
            'ORDER_ID' => new IntegerField(
                'UF_ORDER_ID',
                [
                    'required' => true,
                    'title' => 'Id заказа'
                ]
            ),
            'DATE' => new DatetimeField(
                'UF_DATE',
                [
                    'required' => true,
                    'title' => 'Время добавления',
                    'default_value' => new DateTime(),
                ]
            ),
            'USER' => new ReferenceField(
                'USER',
                UserTable::class,
                ['this.USER_ID' => 'ref.ID']
            ),
            'ORDER' => new ReferenceField(
                'ORDER',
                OrderTable::class,
                ['this.ORDER_ID' => 'ref.ID']
            ),
            'EVALUATION_COMMENT' => new ReferenceField(
                'EVALUATION_COMMENT',
                EvaluationCommentsTable::class,
                ['this.ID' => 'ref.ORDER_EVALUATION_ID']
            ),
            'IMAGES' => new IntegerField(
                'UF_PHOTO',
                [
                    'title' => 'Картинки',
                    'save_data_modification' => function () {
                        return [
                            function ($value) {
                                return serialize($value);
                            }
                        ];
                    }
                ]
            )
        ];
    }
}
