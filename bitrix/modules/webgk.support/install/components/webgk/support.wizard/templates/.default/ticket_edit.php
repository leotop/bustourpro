<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();   ?>   
<?$APPLICATION->IncludeComponent("bitrix:menu", "deskmanMenu", Array(
    "ROOT_MENU_TYPE" => "deskman",    
    "MENU_CACHE_TYPE" => "N",   
    "MENU_CACHE_TIME" => "3600",    
    "MENU_CACHE_USE_GROUPS" => "Y",    
    "MENU_CACHE_GET_VARS" => "",   
    "MAX_LEVEL" => "1",    
    "CHILD_MENU_TYPE" => "",    
    "USE_EXT" => "Y",    
        ),
    false
); 
?>   
<?if ($arParams['SHOW_RESULT']=='Y' && $arResult['DISPLAY_MESSAGE'])
	echo '<font class=text>'.GetMessage('WZ_RESULT').'</font>'.
		'<div class="wizard-result">'.$arResult['DISPLAY_MESSAGE'].'</div><br>';
        

$APPLICATION->IncludeComponent(
	"webgk:support.ticket.edit", 
	"", 
	Array(
		"ID" => $arResult["VARIABLES"]["ID"],
		"TICKET_LIST_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["ticket_list"],
		"TICKET_EDIT_TEMPLATE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["ticket_edit"],
		"MESSAGES_PER_PAGE" => $arParams["MESSAGES_PER_PAGE"],
		"MESSAGE_SORT_ORDER" => $arParams["MESSAGE_SORT_ORDER"],
		"MESSAGE_MAX_LENGTH" => $arParams["MESSAGE_MAX_LENGTH"],
		"SET_PAGE_TITLE" =>$arParams["SET_PAGE_TITLE"],
		'SHOW_COUPON_FIELD' => $arParams['SHOW_COUPON_FIELD'],
		"SET_SHOW_USER_FIELD" => $arParams["SET_SHOW_USER_FIELD"],
	),
	$component,
	array('HIDE_ICONS' => 'Y')
);
?>
