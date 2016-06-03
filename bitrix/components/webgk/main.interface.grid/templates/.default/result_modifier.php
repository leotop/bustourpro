<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    CModule::IncludeModule("webgk.support");

    foreach($arParams["ROWS"] as $index=>$aRow) {   
        $arParams["ROWS"][$index]["data"]["UF_SPEND_TIME"] = GKSupportSpentTime::GetTicketSpentTime($aRow["data"]["ID"]);
    }
    
    
?>