<?php

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Dev05\Classes\Survey\SurveyResult;


class SurveyAjaxController extends Controller
{

    private $componentParams;
    private $formData;
    private $userId;

    /**
     * @behavior ajax request handler
     * @param array|string $formData
     * @param string $csrfToken
     * @param array|null $componentParams
     * @return string[]
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\NotImplementedException
     */
    public function ajaxRequestHandlerAction($formData, string $csrfToken, array $componentParams = null)
    {
        if (!Dev05\Classes\Security\Helper::checkCsrfToken($csrfToken)) {
            throw new CSecurityRequirementsException('Ошибка проверки csrf токена');
        } else {
            $this->prepareFields($formData, $componentParams, uniqid ('RESPONDENT_'));
            $this->saveUserAnswers($this->formData, $this->componentParams['SURVEY_ID'], $this->userId);

            return [
                'message' => 'Cпасибо за пройденный опрос!<br>Ваше мнение будет учтено.'
            ];
        }
    }

    /**
     * @behavior prepared ajax params
     * @param array $formData
     * @param array $componentParams
     * @param array $userId
     */
    private function prepareFields($formData, array $componentParams, string $userId)
    {
        if (is_string($formData)){
            $formData = urldecode($formData);
            parse_str($formData, $formData);
        }

        $formData = $formData['QUESTIONS'];

        if (empty($formData)) {
            throw new \Bitrix\Main\ArgumentNullException('ответы не получены !');
        } else {
            $this->formData = $formData;
        }

        if (empty($componentParams)) {
            throw new \Bitrix\Main\ArgumentNullException('отсутствуют параметры компоненты!');
        } else {
            $this->componentParams = $componentParams;
        }

        if (!$userId) {
            throw new \Bitrix\Main\ArgumentNullException('отсутствуeт ip адресс пользователя');
        } else {
            $this->userId = $userId;
        }
    }

    /**
     * @behavior save user answers
     * @param array $formData
     * @param array $userId
     * @param array $sirveyId
     * @return
     */
    private function saveUserAnswers(array $formData, int $surveyId, string $userId)
    {
        $res = (new SurveyResult)->add($formData,$surveyId, $userId);
        if (!$res) {
            throw new \Bitrix\Main\NotImplementedException('ошибка сохранения результатов опроса');
        }
    }

    /**
     * @behavior configure request filters
     * @return array
     */
    public function configureActions(): array
    {
        return [
            'ajaxRequestHandler' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_POST])
                ],
                'postfilters' => []
            ]
        ];
    }
}