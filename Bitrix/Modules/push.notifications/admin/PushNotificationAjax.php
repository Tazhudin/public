<?php
// phpcs:ignoreFile

use Bitrix\Main\Request;

class PushNotificationAjax
{
    /**
     * @var \Bitrix\Main\HttpRequest|Request
     */
    private $request;
    /**
     * @var array
     */
    public array $arErrors;
    /**
     * @var string
     */
    private string $gridId;
    /**
     * @var Push_notifications
     */
    private Push_notifications $obPushNotifications;

    /**
     * @param string $gridId
     */
    public function __construct(string $gridId = '')
    {
        $this->request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $this->gridId = $gridId;
    }


    /**
     * @return PushNotificationAjax
     */
    public function ajax(): PushNotificationAjax
    {
        return $this;
    }

    private function addError(string $errorMessage): void
    {
        $this->arErrors['messages'][] = [
            'TYPE' => \Bitrix\Main\Grid\MessageType::ERROR,
            'TITLE' => 'Ошибка',
            'TEXT' => $errorMessage,
        ];
    }

    public function isSuccess(): bool
    {
        return empty($this->arErrors);
    }

    public function getErrors(): array
    {
        return $this->arErrors;
    }
}
