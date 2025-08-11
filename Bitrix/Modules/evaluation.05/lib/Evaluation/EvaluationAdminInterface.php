<?php

namespace Evaluation_05\Evaluation;

use Bitrix\Main\Localization\Loc;
use DigitalWand\AdminHelper\Helper\AdminInterface;
use DigitalWand\AdminHelper\Widget\FileWidget;
use DigitalWand\AdminHelper\Widget\NumberWidget;
use DigitalWand\AdminHelper\Widget\DateTimeWidget;
use DigitalWand\AdminHelper\Widget\StringWidget;
use DigitalWand\AdminHelper\Widget\UserWidget;

/**
 * Описание интерфейса (табок и полей) админки новостей.
 * {@inheritdoc}
 */
class EvaluationAdminInterface extends AdminInterface
{
    /**
     * @inheritdoc
     */
    public function fields()
    {
        Loc::loadMessages(__FILE__);
        global $USER;
        return array(
            'MAIN' => array(
                'NAME' => 'Главная',
                'FIELDS' => array(
                    'ID' => array(
                        'WIDGET' => new NumberWidget(),
                        'READONLY' => true,
                        'FILTER' => true,
                        'HIDE_WHEN_CREATE' => true
                    ),
                    'DATE' => array(
                        'WIDGET' => new DateTimeWidget(),
                        'HEADER' => true,
                        'READONLY' => true,
                        'HIDE_WHEN_CREATE' => true
                    ),
                    'USER_COMMENT' => array(
                        'WIDGET' => new StringWidget(),
                        'HEADER' => false,
                    ),
                    'USER_ID' => array(
                        'WIDGET' => new UserWidget(),
                        'HEADER' => false,
                    ),
                    'ORDER_ID' => array(
                        'WIDGET' => new NumberWidget(),
                        'HEADER' => false,
                    ),
                    'USER.PERSONAL_PHONE' => array(
                        'REF_NAME' => 'USER_PHONE',
                        'WIDGET' => new NumberWidget(),
                        'HEADER' => false,
                        'FORCE_SELECT' => true,
                        'FILTER' => true
                    ),
                    'ORDER.DATE_INSERT' => array(
                        'REF_NAME' => 'ORDER_DATE_INSERT',
                        'WIDGET' => new NumberWidget(),
                        'HEADER' => false,
                        'FORCE_SELECT' => true,
                        'FILTER' => false
                    ),
                    'IMAGES' => array(
                        'WIDGET' => new FileWidget(),
                        'HEADER' => false,
                        'IMAGE' => true,
                        'MULTIPLE' => true,
                        'SERIALIZED' => true
                    ),
                    'EVALUATION_COMMENT.VARIANT.COMMENT' => array(
                        'REF_NAME' => 'EVALUATION_COMMENT_VARIANT',
                        'WIDGET' => new StringWidget(),
                        'HEADER' => false,
                        'FORCE_SELECT' => true,
                        'FILTER' => true
                    ),
                )
            ),
        );
    }

    /**
     * @return array
     */
    public function helpers(): array
    {
        return array(
            '\Evaluation_05\Evaluation\EvaluationListHelper' => array(
                'BUTTONS' => array(
                )
            ),
            '\Evaluation_05\Evaluation\EvaluationEditHelper' => array(
                'BUTTONS' => array(
                )
            )
        );
    }
}
