<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Evaluation_05\Evaluation\EvaluationListHelper;

if (!Loader::includeModule('digitalwand.admin_helper') || !Loader::includeModule('evaluation.05')) {
    return;
}

Loc::loadMessages(__FILE__);

return array(
    array(
        'parent_menu' => 'global_menu_05ru',
        'sort' => 10,
        'text' => 'Оценки заказов даркстора',
        'items' => [
            [
                'text' => 'Оценки',
                'title' => 'Оценки',
                'url' => EvaluationListHelper::getUrl(),
            ]
        ]
    )
);
