<?php


namespace Dev05\Classes\Survey;

use Dev05\Classes\HighLoadBlock;
use Exception;

class SurveyResult
{
    private static $surveyUserAnswersTableName = 'survey_user_answers';
    private static $iblockCode = 'surveys';
    private static $surveyUserAnswersEntity ;


    /**
     * constructor.
     */
    public function __construct()
    {
        self::$surveyUserAnswersEntity = HighLoadBlock::getHlEntityByTableName(self::$surveyUserAnswersTableName);
    }
    /**
     * @behavior add survey user answers
     * @param array $surveyId
     * @return int $formData
     * @return string $userIp
     */
    public static function add(array $formData, int $surveyId, string $userIp ) {
        try {
            foreach ($formData as $questionId=>$answer) {
                if (is_array($answer)) {
                    foreach ($answer as $index => $answerId) {
                        $data = [
                            'UF_QUESTION_ID' => $questionId,
                            'UF_ANSWER' => $answerId,
                            'UF_USER_IP' => $userIp,
                            'UF_SURVEY_ID' => $surveyId,
                        ];
                        $res = self::$surveyUserAnswersEntity::add($data);
                    }
                } else {
                    $data = [
                        'UF_QUESTION_ID' => $questionId,
                        'UF_ANSWER' => $answer,
                        'UF_USER_IP' => $userIp,
                        'UF_SURVEY_ID' => $surveyId,
                    ];
                    $res = self::$surveyUserAnswersEntity::add($data);
                }
            }
            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @behavior get survey results
     * @param array $surveyId
     * @return array $surveyResults
     */
    public static function getSurveyResults(int $surveyId) {
        if ((int)$surveyId <= 0) {
            return false;
        }

        $survey = (new Survey())->getSurvey(self::$iblockCode, $surveyId);

        $surveyAnswersDb = self::$surveyUserAnswersEntity::getList(array(
            'filter' => ['UF_SURVEY_ID' => $surveyId],
            'select' => array('*'),
        ));

        while($el = $surveyAnswersDb->fetch()){
            $surveyResults[$el['UF_USER_IP']][$el['UF_QUESTION_ID']][] = $el['UF_ANSWER'];
        }
        foreach ($survey['QUESTIONS'] as $questionId=>$question) {
            if ($question['TYPE'] != 'text') {
                foreach ($question['VARS'] as $answerId=>$answer) {
                    $answerId['VALUE'] = $answer;
                    unset($survey['QUESTIONS'][$questionId]['VARS'][$answerId]);
                    $survey['QUESTIONS'][$questionId]['VARS'][$answerId]['USERS_CHECKED'] = self::howManyUsersChecked($questionId, $answerId, $surveyResults);
                    $survey['QUESTIONS'][$questionId]['VARS'][$answerId]['PERCENT'] = self::getCheckedPercent($survey['QUESTIONS'][$questionId]['VARS'][$answerId]['USERS_CHECKED'], $surveyResults);
                    $survey['QUESTIONS'][$questionId]['VARS'][$answerId]['VALUE'] = $answer;
                }
            } else {
                $survey['QUESTIONS'][$questionId]['VARS'] = self::getUserTextAnswers($questionId, $surveyResults);
            }
        }
        return $survey;
    }

    /**
     * @behavior calculate haw many users checked current answer
     * @param int $questionId
     * @param int $answerId
     * @param array $surveyResults
     * @return int $checked
     */
    private static function howManyUsersChecked(int $questionId, int $answerId, array $surveyResults) {
        $checked = 0;
        foreach ($surveyResults as $userId=>$userAnswer) {
            if (in_array($answerId, $userAnswer[$questionId])) {
                $checked++;
            }
        }
        return $checked;
    }

    /**
     * @behavior returned all users entered answers
     * @param int $questionId
     * @param array $surveyResults
     * @return int $userTextAnswers
     */
    private static function getUserTextAnswers(int $questionId, array $surveyResults) {
        $userTextAnswers = [];
        foreach ($surveyResults as $userId=>$userAnswer) {
            if ($userAnswer[$questionId])
            $userTextAnswers [] = $userAnswer[$questionId][0];
        }
        return $userTextAnswers;
    }

    /**
     * @behavior returned percent
     * @param int $usersCheckedCount
     * @param array $surveyResults
     * @return float $userTextAnswers
     */
    private static function getCheckedPercent(int $usersCheckedCount, array $surveyResults) {
        $percent = 0;
        $allUsers = count($surveyResults);
        $percent = round($usersCheckedCount / $allUsers * 100, 2);
        return $percent;
    }
}