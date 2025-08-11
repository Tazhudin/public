<?php

namespace Dev05\Classes\Orm\Darkstore\Order\Evaluation;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\SystemException;

class EvaluationCommentsTable extends DataManager
{
    /**
     * @behavior get user field enum table name
     * @return string|null
     */
    public static function getTableName(): ?string
    {
        return 'b_hl_order_evaluation_comments';
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
            'ORDER_EVALUATION_ID' => new IntegerField(
                'UF_ORDER_EVALUATION_ID',
                [
                    'required' => true,
                    'title' => 'Id оценки заказа'
                ]
            ),
            'ORDER_EVALUATION_COMMENT_ID' => new IntegerField(
                'UF_ORDER_EVALUATION_COMMENT_ID',
                [
                    'required' => true,
                    'title' => 'Id комментария к оценке'
                ]
            ),
            'VARIANT' => new ReferenceField(
                'VARIANT',
                EvaluationCommentVariantsTable::class,
                ['this.ORDER_EVALUATION_COMMENT_ID' => 'ref.ID']
            ),
        ];
    }
}
