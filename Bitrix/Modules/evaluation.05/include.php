<?php

Bitrix\Main\Loader::registerAutoloadClasses(
    'evaluation.05',
    [
        'Evaluation_05\\Evaluation\\EvaluationListHelper' => 'lib/Evaluation/EvaluationListHelper.php',
        'Evaluation_05\\Evaluation\\EvaluationEditHelper' => 'lib/Evaluation/EvaluationEditHelper.php',
        'Evaluation_05\\Evaluation\\EvaluationAdminInterface' => 'lib/Evaluation/EvaluationAdminInterface.php',
        'Evaluation_05\\Permission' => 'lib/Permission.php',
    ]
);
