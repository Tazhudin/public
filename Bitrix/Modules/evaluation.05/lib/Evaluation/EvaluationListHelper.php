<?php

namespace Evaluation_05\Evaluation;

use Dev05\Classes\Orm\Darkstore\Order\Evaluation\EvaluationsTable;
use DigitalWand\AdminHelper\Helper\AdminListHelper;

/**
 * Хелпер описывает интерфейс, выводящий список новостей.
 *
 * {@inheritdoc}
 */
class EvaluationListHelper extends AdminListHelper
{
    protected static $model = EvaluationsTable::class;
}
