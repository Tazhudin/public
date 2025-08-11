<?php

namespace Dev05\Classes\Survey;

use Bitrix\Iblock\Iblock;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Entity\ReferenceField;
use CIBlockSection;
use Dev05\Classes\Constant;

class Survey
{

    /**
     * @behavior get syrvey entity
     */
    private static function getSurveyEntity()
    {
        static $entitySurveyClass = null;

        if ($entitySurveyClass === null) {
            $iblockId = IblockTable::getRow([
                'select' => ['ID'],
                'filter' => ['=CODE' => Constant::getInfoBlockCode('SURVEY')]
            ])['ID'];
            $entitySurveyClass = Iblock::wakeUp($iblockId)->getEntityDataClass();
        }

        return $entitySurveyClass;
    }

    public function getSurvey(string $iblockCode, int $surveyId)
    {

        $dbQuestionTypes = CIBlockSection::GetList(
            ['SORT' => 'ASC', 'ID' => 'ASC'],
            ['=IBLOCK_CODE' => $iblockCode, '=IBLOCK_SECTION_ID' => $surveyId, '=DEPTH_LEVEL' => 2],
            [],
            ['ID', 'NAME']
        );

        $questionGroups = [];
        while ($group = $dbQuestionTypes->fetch()) {
            $group['QUESTIONS'] = [];
            $questionGroups[$group['ID']] = $group;
        }

        $arSurvey['GROUPS'] = $questionGroups;

        $dbSurvey = self::getSurveyEntity()::getList([
            'select' => [
                'ID',
                'NAME',
                'GROUP_ID' => 'IBLOCK_SECTION_ID',
                'ANSWER_TYPE' => 'ANSWER.XML_ID',
                'ANSWER_VAR' => 'ANSWERS.VALUE',
                'ANSWER_VAR_ID' => 'ANSWERS.ID',
            ],
            'filter' => [
                'SECTIONS.IBLOCK_SECTION_ID' => $surveyId
            ],
            'runtime' => [
                new ReferenceField(
                    'ANSWER',
                    'Bitrix\Iblock\PropertyEnumerationTable',
                    ['=this.INPUT_ANSWER_TYPE.VALUE' => 'ref.ID']
                ),
            ]
        ]);

        while ($questionData = $dbSurvey->fetch()) {

            $id = (int)$questionData['ID'];

            if (!isset($arSurvey['QUESTIONS'][$questionData['ID']])) {
                $arSurvey['QUESTIONS'][$id] = [
                    'ID' => $id,
                    'NAME' => $questionData['NAME'],
                    'GROUP_ID' => (int)$questionData['GROUP_ID'],
                    'TYPE' => $questionData['ANSWER_TYPE'],
                    'VARS' => [],
                ];

                $arSurvey['GROUPS'][$questionData['GROUP_ID']]['QUESTIONS'][$id] = $id;
            }

            if ($arSurvey['QUESTIONS'][$id]['TYPE'] === 'text') {
                continue;
            }

            if (!empty($questionData['ANSWER_VAR'])) {
                $arSurvey['QUESTIONS'][$id]['VARS'][$questionData['ANSWER_VAR_ID']] = $questionData['ANSWER_VAR'];
            }

        }

        return $arSurvey;
    }
}

