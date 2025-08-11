<?php

namespace Evaluation_05\Evaluation;

use Dev05\Classes\Orm\Darkstore\Order\Evaluation\EvaluationsTable;
use DigitalWand\AdminHelper\Helper\AdminEditHelper;
use DigitalWand\AdminHelper\Helper\AdminListHelper;

/**
 * Хелпер описывает интерфейс, выводящий список новостей.
 *
 * {@inheritdoc}
 */
class EvaluationEditHelper extends AdminEditHelper
{
    protected static $model = EvaluationsTable::class;
}
