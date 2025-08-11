<?php
// phpcs:ignoreFile

use Bitrix\Main\ModuleManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);

class Recipes_05 extends CModule
{
    public $MODULE_ID = 'recipes.05';

    function __construct()
    {
        $this->MODULE_NAME = 'Рецепты';
        $this->MODULE_DESCRIPTION = 'Модуль для работы с рецептами';
        $this->PARTNER_NAME = '05ru';
        $this->PARTNER_URI = 'https://05.ru';
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        $this->MODULE_GROUP_RIGHTS = 'Y';
    }

    public function DoInstall()
    {
        $this->installFiles();
        ModuleManager::registerModule($this->MODULE_ID);
        Loader::includeModule($this->MODULE_ID);
    }

    public function DoUninstall()
    {
        $this->unInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);
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
