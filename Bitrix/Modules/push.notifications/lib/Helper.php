<?php

namespace PushNotifications;

use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Main\Type\Collection;
use CSite;
use Dev05\Classes\Logger\Context\OrderIdContext;
use Dev05\Classes\Logger\Context\UserIdContext;
use Dev05\Classes\Notification\Push\Message;
use Dev05\Classes\Notification\Push\Providers\FireBase;
use Dev05\Classes\Notification\Push\PushNotificationService;
use Dev05\Classes\Sale\Order;
use Dev05\Classes\Sentry\Event;
use Dev05\Classes\Tester\Tester;
use Dev05\Classes\Personal;
use Exception;

final class Helper
{
    private const MODULE_ID = 'push.notifications';
    private static array $arOrderStatuses;
    private array $params = [];
    protected static ?array $arInstance = null;
    private string $siteId = '';
    private array $testingSendPushParams = [
        'SERVER_API_KEY'
    ];

    private function __construct(string $siteId)
    {
        $this->siteId = $siteId;
    }

    public static function getInstance(string $siteId): Helper
    {
        return self::$arInstance[$siteId] ?: (self::$arInstance[$siteId] = new Helper($siteId));
    }

    public function setConf(array $params): bool
    {
        if (!Collection::isAssociative($params)) {
            return false;
        }

        foreach ($params as $name => $value) {
            $value = is_array($value) ? serialize($value) : $value;
            $this->params[$name] = $value;
        }
        return true;
    }

    public function get($name): string
    {
        return Option::get(self::MODULE_ID, $name, "", $this->siteId);
    }

    /**
     * @throws ArgumentOutOfRangeException
     */
    public function save()
    {
        foreach ($this->params as $name => $value) {
            Option::set(self::MODULE_ID, $name, $value, $this->siteId);
        }
    }

    public function getOrderStatuses(string $paramName = ''): array
    {
        $paramValue = $this->get($paramName);
        $selectedOrderStatusId = unserialize($paramValue) ?: $paramValue;
        if (empty(self::$arOrderStatuses)) {
            self::$arOrderStatuses = \Bitrix\Sale\Internals\StatusTable::getList([
                'filter' => ['=TYPE' => 'O', '=STATUS_LANG.LID' => 'ru'],
                'select' => ['NAME' => 'STATUS_LANG.NAME', 'ID']
            ])->fetchAll();
        }
        $arStatuses = [];
        foreach (self::$arOrderStatuses as $arStatus) {
            $arStatus['NAME'] = sprintf('[%s] %s', $arStatus['ID'], $arStatus['NAME']);
            $selected = is_array($selectedOrderStatusId) ? in_array(
                $arStatus['ID'],
                $selectedOrderStatusId
            ) : $arStatus['ID'] == $selectedOrderStatusId;
            $arStatus['SELECTED'] = $selected;
            $arStatuses[] = $arStatus;
        }
        return $arStatuses;
    }

    public function getActiveSites(): array
    {
        $arSites = [];
        $obSites = CSite::GetList('sort', 'asc', ['ACTIVE' => 'Y']);
        while ($arRes = $obSites->fetch()) {
            $arSites[$arRes['ID']] = $arRes;
        }
        return $arSites;
    }

    /**
     * @param $param
     * @return array
     */
    public function getSelectedStatusesTemplates($param): array
    {
        return (array)unserialize(self::get($param));
    }

    public function needToSendPushWithStatus(string $statusId): bool
    {
        $arStatusIdsWithSend = (array)unserialize($this->get('PUSH_NOTIFICATION_SETTINGS_SENDING_WITH_STATUSES'));
        $arStatusTextTemplates = (array)unserialize(
            $this->get('PUSH_NOTIFICATION_SETTINGS_SENDING_STATUSES_BODY_TEMPLATES')
        );
        $arStatusTextTemplates = array_combine($arStatusIdsWithSend, $arStatusTextTemplates);
        return !empty($arStatusTextTemplates[$statusId]);
    }

    public function needToSendPushWithOrderChange(): bool
    {
        return $this->get('PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED') == 'Y';
    }

    public function needToSendPushToOrderEvaluate(string $statusId = ''): bool
    {
        return $this->get('PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE') == 'Y' &&
            in_array(
                $statusId,
                (array)unserialize($this->get('PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_IN_STATUS'))
            );
    }

    public function getStatusChangeTitleTmp(): string
    {
        return $this->get('PUSH_NOTIFICATION_SETTINGS_SENDING_STATUSES_TITLE_TEMPLATE') ?: '';
    }

    public function getStatusChangeTextTmp(string $statusId, Order $obOrder): string
    {
        $arStatusIdsWhithSend = (array)unserialize($this->get('PUSH_NOTIFICATION_SETTINGS_SENDING_WITH_STATUSES'));
        $arStatusTextTemplates = (array)unserialize(
            $this->get('PUSH_NOTIFICATION_SETTINGS_SENDING_STATUSES_BODY_TEMPLATES')
        );
        $arStatusTextTemplates = array_combine($arStatusIdsWhithSend, $arStatusTextTemplates);
        return str_replace(
            ['#ORDER_ID#'],
            [$obOrder->getField('ACCOUNT_NUMBER')],
            $arStatusTextTemplates[$statusId]
        ) ?: '';
    }

    public function orderChangePushTitle(): string
    {
        return $this->get(
            'PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED_TITLE_TEMPLATE'
        ) ?: '';
    }

    public function orderChangePushText(Order $obOrder): string
    {
        $orderChangeNotificationText = $this->get(
            'PUSH_NOTIFICATION_SETTINGS_SENDING_WHEN_ORDER_CHANGED_BODY_TEMPLATE'
        );
        return str_replace(
            ['#ORDER_ID#'],
            [
                $obOrder->getField('ACCOUNT_NUMBER'),
            ],
            $orderChangeNotificationText
        ) ?: '';
    }

    public function orderEvaluatePushTitle(): string
    {
        return $this->get(
            'PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_TITLE_TEMPLATE'
        ) ?: '';
    }

    public function orderEvaluatePushText(Order $obOrder): string
    {
        $orderChangeNotificationText = $this->get(
            'PUSH_NOTIFICATION_SETTINGS_SENDING_TO_ORDER_EVALUATE_BODY_TEMPLATE'
        );
        return str_replace(
            ['#ORDER_ID#'],
            [
                $obOrder->getField('ACCOUNT_NUMBER'),
            ],
            $orderChangeNotificationText
        ) ?: '';
    }

    public function sendTestingPushNotification(array $arParams): Result
    {
        $obResult = new Result();
        if (empty($arParams['SITE_ID']) || !in_array($arParams['SITE_ID'], array_keys($this->getActiveSites()))) {
            $obResult->addError(new Error('Невалидный id сайта!'));
            return $obResult;
        }
        if (!is_array($arParams['DATA'])) {
            $obResult->addError(new Error('Невалидный json для полезной нагрузки!'));
            return $obResult;
        }

        $personalHelper = new Personal\Helper($arParams['USER_ID']);
        if (!(new Tester())->isTester($personalHelper->getUserPhone())) {
            $obResult->addError(new Error('Пользователь не является тестером!'));
            return $obResult;
        }

        try {
            $obPushSender = new PushNotificationService(
                new FireBase($arParams['SITE_ID']),
                PushNotificationService::getLogger()
            );
            $obMessage = (new Message())
                ->setTitle($arParams['TITLE'])
                ->setText($arParams['TEXT'])
                ->setImage($arParams['IMAGE'])
                ->setIcon($arParams['ICON'])
                ->setData($arParams['DATA']);
            $obSendResult = $obPushSender->send($obMessage, $arParams['USER_ID']);
        } catch (Exception $exception) {
            $obResult->addError(new Error($exception->getMessage()));
            return $obResult;
        }

        $obResult->setData(
            [
                'IS_SUCCESS' => $obSendResult->isSuccess(),
                'REQUEST_DATA' => $arParams,
                'RESPONSE_DATA' => $obSendResult->getData(),
            ]
        );
        return $obResult;
    }
}
