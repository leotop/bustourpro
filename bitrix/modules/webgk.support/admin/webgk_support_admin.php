<?require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');   
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

IncludeModuleLangFile(__FILE__);
CModule::IncludeModule('webgk.support');
/** @global \CMain $APPLICATION */
/** @global \CUser $USER */
/** @global CDatabase $DB */

global $USER;
global $APPLICATION;

if ( !$USER->IsAdmin() ) {
	$APPLICATION->AuthForm("");
}

IncludeModuleLangFile(__FILE__);
  
 echo GetMessage("GK_SUPPORT_SETTINGS_TITLE");
 
 