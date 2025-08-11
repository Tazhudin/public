<?php

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Dev05\Classes\Changelog\Release;
use Dev05\Classes\UserRate;

class ReleaseRateAjaxController extends Controller
{
    /**
     * @var array
     */
    private $params;


    /**
     * @behavior ajax request handler
     * @param int $userId
     * @param int $releaseId
     * @param int $userRate
     * @throws
     */
    public function ajaxRequestHandlerAction($userId, $userRate, $releaseId)
    {
        $this->prepareFields($userId, $userRate, $releaseId);
        $this->obReleaseHelper = new Release\Helper();

        $res = $this->setReleaseRate();
         if ($res->isSuccess()) {
             $newReleaseRate = $this->obReleaseHelper::calcReleaseRate($releaseId);

         } else {
             throw new Exception('Не удалось записать вашу оценку !');
         }
         return [
             'newRate' => round($newReleaseRate['RATE'], 1)
         ];
    }

    /**
     * @behavior prepared params
     * @param int $userId
     * @param int $releaseId
     * @param int $userRate
     * @throws Exception
     */
    private function prepareFields($userId, $userRate, $releaseId)
    {
        if (empty($userId)) {
            throw new Exception('EMPTY_USER_ID');
        }
        if (empty($userRate)) {
            throw new Exception('EMPTY_RATE');
        }
        if (empty($releaseId)) {
            throw new Exception('EMPTY_RELEASE_ID');
        }

        $this->params['USER_ID'] = (int)$userId;
        $this->params['USER_RATE'] = (int)$userRate;
        $this->params['RELEASE_ID'] = (int)$releaseId;
    }

    /**
     * @behavior set user rate
     * @return array
     */
    private function setReleaseRate()
    {
        $setReleaseRateResult = $this->obReleaseHelper->setReleaseRateById(
            $this->params['USER_ID'],
            $this->params['RELEASE_ID'],
            $this->params['USER_RATE']
        );

        return $setReleaseRateResult;
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