<?php

use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\Localization\Loc;

use \Bitrix\Main\Application;
use \Bitrix\Main\IO;

Loc::loadMessages(__FILE__);

Class o2_title extends CModule {

	var $MODULE_ID = 'o2.title';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $PARTNER_NAME;
	var $PARTNER_URI;
	var $errors;
	var $exclusionAdminFiles;

	function __construct()
	{
		$this->exclusionAdminFiles=array(
			'..',
			'.',
			'menu.php',
			'operation_description.php',
			'task_description.php'
		);

		include(__DIR__ . '/version.php');

		$this->MODULE_ID = str_replace("_", ".", get_class($this));
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = Loc::getMessage("O2_TITLE_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("O2_TITLE_MODULE_DESCRIPTION");
		$this->PARTNER_NAME = Loc::getMessage("O2_TITLE_PARTNER_NAME");
		$this->PARTNER_URI = Loc::getMessage("O2_TITLE_PARTNER_URI");
	}

	public function isVersionD7()
	{
		return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
	}

	public function GetPath($notDocumentRoot = false)
	{
		if ($notDocumentRoot) {
			return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
		} else {
			return dirname(__DIR__);
		}
	}

	function SetNumChmod ($chmod_num) {
		$type = gettype($chmod_num);
		if($type == "string") return octdec($chmod_num);
		return $chmod_num;
	}

	function DoInstall()
	{
		global $APPLICATION;
		if ($this->isVersionD7()) {
			ModuleManager::registerModule($this->MODULE_ID);
			$this->InstallDB();
			$this->InstallEvents();
			$this->InstallFiles();
			$APPLICATION->IncludeAdminFile(Loc::getMessage("O2_TITLE_INSTALL_TITLE"), __DIR__ . "/step.php");
		} else {
			$APPLICATION->ThrowException(Loc::getMessage("O2_TITLE_INSTALL_ERROR_VERSION"));
		}
		return true;
	}

	function DoUninstall()
	{
		global $APPLICATION;
		$this->UnInstallDB();
		$this->UnInstallEvents();
		$this->UnInstallFiles();

		ModuleManager::unRegisterModule($this->MODULE_ID);

		$APPLICATION->IncludeAdminFile(Loc::getMessage("O2_TITLE_UNINSTALL_TITLE") . " \"" . Loc::getMessage("O2_TITLE_MODULE_NAME") . "\"", __DIR__ . "/unstep.php");
		return true;
	}


	function InstallDB()
	{
		return true;
	}

	function UnInstallDB()
	{
		return true;
	}
}