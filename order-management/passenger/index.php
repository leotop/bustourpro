<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Пассажиры");
?>

<?/*$APPLICATION->IncludeComponent("bustour:iblock.element.add", "tourist", array(
	"IBLOCK_TYPE" => "order_management",
	"IBLOCK_ID" => "12",
	"NAV_ON_PAGE" => "10",
	"USE_CAPTCHA" => "N",
	"USER_MESSAGE_ADD" => "",
	"USER_MESSAGE_EDIT" => "",
	"DEFAULT_INPUT_SIZE" => "30",
	"RESIZE_IMAGES" => "Y",
	"PROPERTY_CODES" => array(
		0 => "NAME",
		1 => "144",
		2 => "49",
		3 => "50",
	),
	"PROPERTY_CODES_REQUIRED" => array(
		0 => "NAME",
	),
	"GROUPS" => array(
		0 => "6",
	),
	"STATUS" => "ANY",
	"STATUS_NEW" => "N",
	"ALLOW_EDIT" => "Y",
	"ALLOW_DELETE" => "Y",
	"ELEMENT_ASSOC" => "PROPERTY_ID",
	"ELEMENT_ASSOC_PROPERTY" => "45",
	"MAX_USER_ENTRIES" => "100000",
	"MAX_LEVELS" => "100000",
	"LEVEL_LAST" => "Y",
	"MAX_FILE_SIZE" => "0",
	"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
	"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
	"SEF_MODE" => "Y",
	"SEF_FOLDER" => "/order-management/passenger/",
	"USE_FILTER" => "N",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CUSTOM_TITLE_NAME" => "Имя",
	"CUSTOM_TITLE_TAGS" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
	"CUSTOM_TITLE_IBLOCK_SECTION" => "",
	"CUSTOM_TITLE_PREVIEW_TEXT" => "",
	"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
	"CUSTOM_TITLE_DETAIL_TEXT" => "",
	"CUSTOM_TITLE_DETAIL_PICTURE" => "",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);*/?>

<?$APPLICATION->IncludeComponent("bustour:iblock.element.add", "tourist", array(
	"IBLOCK_TYPE" => "order_management",
	"IBLOCK_ID" => "12",
	"NAV_ON_PAGE" => "100",
	"USE_CAPTCHA" => "N",
	"USER_MESSAGE_ADD" => "",
	"USER_MESSAGE_EDIT" => "",
	"DEFAULT_INPUT_SIZE" => "30",
	"RESIZE_IMAGES" => "N",
	"PROPERTY_CODES" => array(
		0 => "NAME",
		1 => "49",
		2 => "50",
	),
	"PROPERTY_CODES_REQUIRED" => array(
	),
	"GROUPS" => array(
		0 => "1",
		1 => "6",
		2 => "7",
	),
	"STATUS" => "ANY",
	"STATUS_NEW" => "N",
	"ALLOW_EDIT" => "Y",
	"ALLOW_DELETE" => "Y",
	"ELEMENT_ASSOC" => "PROPERTY_ID",
	"ELEMENT_ASSOC_PROPERTY" => "45",
	"MAX_USER_ENTRIES" => "100000",
	"MAX_LEVELS" => "100000",
	"LEVEL_LAST" => "Y",
	"MAX_FILE_SIZE" => "0",
	"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
	"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
	"SEF_MODE" => "N",
	"SEF_FOLDER" => "/order-management/passenger/",
	"USE_FILTER" => "N",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CUSTOM_TITLE_NAME" => "ФИО",
	"CUSTOM_TITLE_TAGS" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
	"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
	"CUSTOM_TITLE_IBLOCK_SECTION" => "",
	"CUSTOM_TITLE_PREVIEW_TEXT" => "",
	"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
	"CUSTOM_TITLE_DETAIL_TEXT" => "",
	"CUSTOM_TITLE_DETAIL_PICTURE" => "",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>