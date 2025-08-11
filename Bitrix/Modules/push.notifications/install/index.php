<?php
// phpcs:ignoreFile

use Bitrix\Main\Localization\Loc;

/**
 * Class PushNotifications_05
 */
class Push_notifications extends CModule
{
    public $MODULE_ID = 'push.notifications';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_CSS;
    public $strError = '';
    public $MODULE_GROUP_RIGHTS = 'Y';
    public $SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';

    public function __construct()
    {
        Loc::loadMessages(__FILE__);
        $this->MODULE_NAME = 'Push-уведомления';
        $this->MODULE_DESCRIPTION = 'Модуль push-уведомлений';
        $this->PARTNER_NAME = '05.ru';
        $this->PARTNER_URI = 'https://05.ru';
        $this->MODULE_SORT = 1;
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        $this->MODULE_GROUP_RIGHTS = 'Y';
    }

    /**
     * @return bool|false
     */
    public function installDB(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function unInstallDB(): bool
    {
        return true;
    }

    /**
     * @return void
     */
    public function installFiles()
    {
        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . $this->MODULE_ID . '/install/admin',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin',
            true,
            true
        );
    }

    /**
     * @return void
     */
    public function unInstallFiles()
    {
        DeleteDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . $this->MODULE_ID . '/install/admin/',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/'
        );
    }

    /**
     * @return void
     */
    public function DoInstall()
    {
        $this->installFiles();
        \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     * @return void
     */
    public function DoUninstall()
    {
        $this->unInstallFiles();
        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @return string[][]
     */
    public function GetModuleRightList(): array
    {
        return [
            'reference_id' => ['D', 'R', 'W'],
            'reference' => [
                '[D] ' . Loc::getMessage('ACCESS_DENIED'),
                '[R] ' . Loc::getMessage('ACCESS_READ'),
                '[W] ' . Loc::getMessage('ACCESS_FULL'),
            ]
        ];
    }
}
