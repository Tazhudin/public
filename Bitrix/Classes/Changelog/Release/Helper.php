<?php

namespace Dev05\Classes\Changelog\Release;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Dev05\Classes\Changelog\Interfaces\IHelper;
use Dev05\Classes\Orm\Release\Rate\ReleaseRateTable;
use Dev05\Classes\Orm\Release\ReleaseTable;
use Exception;

class Helper implements IHelper
{
    private static $entity = null;
    private $releaseIds;

    use Entity;

    /**
     * Storage constructor.
     */
    public function __construct()
    {
        $this->setHlEntity($this->hlTableName);
    }

    public static function getInstance()
    {
        if (self::$entity === null) {
            self::$entity = new self;
        }
        return self::$entity;
    }


    /**
     * @behavior get releases
     * @param array $arNavParams
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function getReleases($arNavParams = [])
    {
        if (empty($arNavParams)) {
            $arNavParams = [
                'PAGE_ITEMS_COUNT' => 3,
                'PAGE' => 1,
                'OFFSET' => 0,
            ];
        }

        $dbReleaseIds = $this->hlBlock::getList([
            'order' => ['ID' => 'DESC'],
            'select' => ['ID'],
            'filter' => [
                'UF_ACTIVE' => '1',
            ],
            'offset' => $arNavParams['OFFSET'],
            'limit' => $arNavParams['PAGE_ITEMS_COUNT'],
            'count_total' => true,
        ]);

        while ($id = $dbReleaseIds->fetch()) {
            $this->releaseIds[] = $id['ID'];
        }


        $filter = [
            'UF_ACTIVE' => true,
            'CHANGES.ACTIVE' => true,
            'ID' => $this->releaseIds,
        ];
        if (isset($params['filter']) && is_array($params['filter'])) {
            $filter = $params['filter'] + $filter;
            unset($params['filter']);
        }

        $arReleases = [];
        $dbReleases = $this->hlBlock::getList([
                'order' => ['ID' => 'DESC'],
                'select' => [
                    '*',
                    'CHANGE_NAME' => 'CHANGES.NAME',
                    'CHANGE_DESCRIPTION' => 'CHANGES.DESCRIPTION',
                    'CHANGE_FILES' => 'CHANGES.FILES',
                    'CHANGE_IMG_TITLES' => 'CHANGES.IMG_TITLES',
                    'CHANGE_TYPE' => 'CHANGES.TYPE',
                    'TYPE_NAME' => 'CHANGE_TYPE_NAME.VALUE',
                ],
                'filter' => $filter,
                'runtime' => [
                    new ReferenceField(
                        'CHANGES',
                        \Dev05\Classes\Orm\Release\Change\ChangeTable::class,
                        ['=this.ID' => 'ref.RELEASE_ID']
                    ),
                    new ReferenceField(
                        'CHANGE_TYPE_NAME',
                        \Dev05\Classes\Orm\UserFieldEnum\UserFieldEnumTable::class,
                        [
                            '=this.CHANGE_TYPE' => 'ref.ID'
                        ]
                    ),
                ]
            ]);

        $change = null;
        while ($arRes = $dbReleases->fetch()) {
            if (!array_key_exists($arRes['ID'], $arReleases)) {
                $change = [];
            }
            $arReleases[$arRes['ID']] = $arRes;
            if (!empty($arRes['TYPE_NAME']))
                $change[$arRes['TYPE_NAME']][] = [
                    'CHANGE_NAME' => $arRes['CHANGE_NAME'],
                    'CHANGE_DESCRIPTION' => $arRes['CHANGE_DESCRIPTION'],
                    'CHANGE_FILES' => $arRes['CHANGE_FILES'],
                    'CHANGE_IMG_TITLES' => $arRes['CHANGE_IMG_TITLES'],
                ];
            $arReleases[$arRes['ID']]['CHANGES'] = $change;
        }

        $arReleases['RELEASES_CNT'] =  $dbReleaseIds->getCount();
        return $arReleases;
    }

    /**
     * @behavior get releases by params
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function getList(array $params)
    {
        if (empty($params)) {
            throw new Exception('Invalid params');
        }
        return $this->hlBlock::getList($params);
    }

    /**
     * @behavior get all release records
     * @param
     * @return array
     * @throws
     */
    public function getAll()
    {
        return $this->hlBlock::getList(array(
            'select' => array('*')
        ));
    }

    /**
     * @behavior get release record by id
     * @param int $id
     * @return array
     * @throws Exception
     */
    public function getById($id)
    {
        $id = (int)$id;
        if ($id <= 0) {
            throw new Exception('Invalid id');
        }
        $tmp = $this->getReleases([
            'filter' => ['=ID' => $id],
        ]);
        return $tmp[$id];
    }

    /**
     * @behavior calculate release rate by id
     * @param int $releaseId
     */
    public static function calcReleaseRate($releaseId)
    {
        $row = ReleaseRateTable::getRow([
            'select' => [
                'AVG_RATE' => new ExpressionField('AVG_RATE', 'AVG(%s)', [
                    'VALUE',
                ]),
            ],
            'filter' => [
                '=RELEASE_ID' => $releaseId,
            ],
        ]);
        $rate = round($row['AVG_RATE'], 1);
        $res = ReleaseTable::update($releaseId, [
            'RATE' => $rate,
        ]);
        if ($res->isSuccess()) {
            $res->setData(['RATE' => $rate]);

            global $CACHE_MANAGER;
            $CACHE_MANAGER->ClearByTag('release_detail_cache_tag'. $releaseId);
        }
        return $res->getData();
    }

    /**
     * @behavior get release rate by user id and release id
     * @param int $userId
     * @param int $releaseId
     * @return array
     */
    public function getReleaseRateById (int $userId, int $releaseId) {
        return $res = ReleaseRateTable::getList([
            'select' => ['ID'],
            'filter' => [
                '=USER_ID' => $userId,
                '=RELEASE_ID' => $releaseId
            ],
        ])->fetch();
    }

    /**
     * @behavior set release rate by user id and release id
     * @param int $userId
     * @param int $releaseId
     * @param int $userRate
     * @return \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result
     */
    public function setReleaseRateById (int $userId, int $releaseId, int $userRate) {
        $releaseRateData = array(
            'USER_ID' => $userId,
            'RELEASE_ID' => $releaseId,
            'VALUE' => $userRate,
        );

        // проверка на наличие оценки от пользователя по релизу
        $userReleaseRate = $this->getReleaseRateById($releaseRateData['USER_ID'], $releaseRateData['RELEASE_ID']);

        if ($userReleaseRate) {
            $result = ReleaseRateTable::update($userReleaseRate['ID'], $releaseRateData);
        } else {
            $result = ReleaseRateTable::add($releaseRateData);
        }
        return $result;
    }

    public function bind()
    {
        $entityName = $this->hlBlock::getHighloadBlock()['NAME'];
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->addEventHandler('', $entityName . 'OnAfterAdd', function () {
            global $CACHE_MANAGER;
            $CACHE_MANAGER->ClearByTag('releases_cache_tag');
        });
        $eventManager->addEventHandler('', $entityName . 'OnAfterDelete', function (\Bitrix\Main\Event $event) {
            global $CACHE_MANAGER;
            $CACHE_MANAGER->ClearByTag('releases_cache_tag');
        });
        $eventManager->addEventHandler('', $entityName . 'OnAfterUpdate', function (\Bitrix\Main\Event $event) {
            global $CACHE_MANAGER;
            $CACHE_MANAGER->ClearByTag('releases_cache_tag');
        });
    }
}
