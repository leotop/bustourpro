<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Направление");
?><?$APPLICATION->IncludeComponent("bustour:iblock.element.add", "direction", array(
	"IBLOCK_TYPE" => "tour",
	"IBLOCK_ID" => "3",
	"NAV_ON_PAGE" => "50",
	"USE_CAPTCHA" => "N",
	"USER_MESSAGE_ADD" => "",
	"USER_MESSAGE_EDIT" => "",
	"DEFAULT_INPUT_SIZE" => "30",
	"RESIZE_IMAGES" => "Y",
	"PROPERTY_CODES" => array(
		0 => "NAME",
		1 => "140",
		2 => "159",
		3 => "116",
		4 => "87",
		5 => "85",
		6 => "86",
		7 => "95",
		8 => "110",
		9 => "158",
		10 => "122",
		11 => "96",
		12 => "97",
		13 => "103",
		14 => "98",
		15 => "104",
		16 => "99",
		17 => "105",
		18 => "100",
		19 => "106",
		20 => "101",
		21 => "107",
		22 => "102",
		23 => "108",
	),
	"PROPERTY_CODES_REQUIRED" => array(
		0 => "NAME",
		1 => "159",
	),
	"GROUPS" => array(
		0 => "6",
	),
	"STATUS" => "ANY",
	"STATUS_NEW" => "N",
	"ALLOW_EDIT" => "Y",
	"ALLOW_DELETE" => "Y",
	"ELEMENT_ASSOC" => "PROPERTY_ID",
	"ELEMENT_ASSOC_PROPERTY" => "12",
	"MAX_USER_ENTRIES" => "100000",
	"MAX_LEVELS" => "100000",
	"LEVEL_LAST" => "Y",
	"MAX_FILE_SIZE" => "0",
	"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
	"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
	"SEF_MODE" => "Y",
	"SEF_FOLDER" => "/formation-tours/direction/",
	"USE_FILTER" => "N",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CUSTOM_TITLE_NAME" => "",
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
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>