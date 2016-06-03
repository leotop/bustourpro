<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    Cmodule::IncludeModule("support"); 
    Cmodule::IncludeModule("webgk.support");  

    $arResult = array();

    $arParams["TICKET_ID"] = intval($arParams["TICKET_ID"]); 

    if (!($arParams["TICKET_ID"] > 0)) {
        return;
    }

    $arResult["TICKET_ID"] = $arParams["TICKET_ID"];

    $ticket = CTicket::GetList($by="ID",$order="ASC",array("ID"=>$arParams["TICKET_ID"]),$filtered,"N");
    if ($ticket->SelectedRowsCount() > 0) {
        $arTicket = $ticket->Fetch();
    }

    $arResult["TICKET"] = $arTicket;

    $filter = array("TICKET_ID"=>$arParams["TICKET_ID"]);

    if (!$USER->IsAdmin() && !in_array($USER->GetId(),GKSupport::GetSupportEmployerGroupID())) {
        $filter["CLIENT_ID"] = GKSupportUsers::GetClientId();
    }    


    $spentTime = GKSupportSpentTime::GetList($by="ID",$sort="ASC",$filter);

    while($arSpentTime = $spentTime->Fetch()) {
        $date = MakeTimeStamp($arSpentTime["DATE"], "YYYY-MM-DD HH:MI:SS");
        $date = date('d.m.Y H:i:s', $date); 
        $arResult["ITEMS"][$arSpentTime["ID"]] = $arSpentTime; 
        $arResult["ITEMS"][$arSpentTime["ID"]]["DATE"] = $date;
    }


    $services = GKSupportServices::GetList($by="ID",$sort="ASC",array());
    while($arService = $services->Fetch()) {
        $arResult["SERVICES"][$arService["ID"]] = $arService;
    }   

    $totalHours = 0;
    $totalMinutes = 0;
    foreach ($arResult["ITEMS"] as $iID=>$arItem){  
        $totalHours += $arItem['HOURS'];
        $totalMinutes += $arItem['MINUTES'];
    }

    $minutes = $totalMinutes%60;
    if (strlen($minutes) == 1) {$minutes = "0".$minutes;}
    $arResult["TOTAL_TIME"] = ($totalHours + intval($totalMinutes/60)).":".$minutes;



    $this->IncludeComponentTemplate();
?>