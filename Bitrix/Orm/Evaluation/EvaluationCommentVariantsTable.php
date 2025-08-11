<?php

namespace Dev05\Classes\Orm\Darkstore\Order\Evaluation;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\SystemException;

class EvaluationCommentVariantsTable extends DataManager
{
    /**
     * @behavior get user field enum table name
     * @return string|null
     */
    public static function getTableName(): ?string
    {
        return 'b_hl_order_evaluation_comment_variants';
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
            'EVALUATION' => new IntegerField(
                'UF_EVALUATION',
                [
                    'required' => true,
                    'title' => 'Оценка заказа'
                ]
            ),
            'COMMENT' => new StringField(
                'UF_COMMENT',
                [
                    'required' => true,
                    'title' => 'Комментарий к оценке'
                ]
            )
        ];
    }
}
