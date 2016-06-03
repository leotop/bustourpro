<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    //Include requaried modules
    Cmodule::IncludeModule("webgk.support");
    Cmodule::IncludeModule("support");

    //Reset filter
    if($_REQUEST["IS_RESET"]=='Y'){
        header( "Location: ".$APPLICATION->GetCurPage());  
    }

    function calculateTime ($Year, $Month, $user, $time, $arResult, $key) {
        $oneHour = 1;
        $oneHourInMinutes = 60;
        //Calculate user time
        $arResult["STAT_TIME"][$Year][$Month][$user["ID"]]["USER_HOURS".$key]=$arResult["STAT_TIME"][$Year][$Month][$user["ID"]]["USER_HOURS".$key]+$time["HOURS"];
        $arResult["STAT_TIME"][$Year][$Month][$user["ID"]]["USER_MINUTES".$key]=$arResult["STAT_TIME"][$Year][$Month][$user["ID"]]["USER_MINUTES".$key]+$time["MINUTES"];
        if( $arResult["STAT_TIME"][$Year][$Month][$user["ID"]]["USER_MINUTES".$key]>=$oneHourInMinutes) {
            $arResult["STAT_TIME"][$Year][$Month][$user["ID"]]["USER_HOURS".$key]=$arResult["STAT_TIME"][$Year][$Month][$user["ID"]]["USER_HOURS".$key]+$oneHour;
            $arResult["STAT_TIME"][$Year][$Month][$user["ID"]]["USER_MINUTES".$key]=$arResult["STAT_TIME"][$Year][$Month][$user["ID"]]["USER_MINUTES".$key]-$oneHourInMinutes;
        }
        //Calculate ticket time  
        $arResult["STAT_TIME"][$Year][$Month][$user["ID"]][$time["TICKET_ID"]]["TIME"]["TICKET_HOURS".$key]=$arResult["STAT_TIME"][$Year][$Month][$user["ID"]][$time["TICKET_ID"]]["TIME"]["TICKET_HOURS".$key]+$time["HOURS"];
        $arResult["STAT_TIME"][$Year][$Month][$user["ID"]][$time["TICKET_ID"]]["TIME"]["TICKET_MINUTES".$key]=$arResult["STAT_TIME"][$Year][$Month][$user["ID"]][$time["TICKET_ID"]]["TIME"]["TICKET_MINUTES".$key]+$time["MINUTES"];
        if( $arResult["STAT_TIME"][$Year][$Month][$user["ID"]][$time["TICKET_ID"]]["TIME"]["TICKET_MINUTES".$key]>=$oneHourInMinutes) {
            $arResult["STAT_TIME"][$Year][$Month][$user["ID"]][$time["TICKET_ID"]]["TIME"]["TICKET_HOURS".$key]=$arResult["STAT_TIME"][$Year][$Month][$user["ID"]][$time["TICKET_ID"]]["TIME"]["TICKET_HOURS".$key]+$oneHour;
            $arResult["STAT_TIME"][$Year][$Month][$user["ID"]][$time["TICKET_ID"]]["TIME"]["TICKET_MINUTES".$key]=$arResult["STAT_TIME"][$Year][$Month][$user["ID"]][$time["TICKET_ID"]]["TIME"]["TICKET_MINUTES".$key]-$oneHourInMinutes;
        }  
        //Calculate month time
        $arResult["STAT_TIME"][$Year][$Month]["MONTH_HOURS".$key]=$arResult["STAT_TIME"][$Year][$Month]["MONTH_HOURS".$key]+$time["HOURS"];
        $arResult["STAT_TIME"][$Year][$Month]["MONTH_MINUTES".$key]=$arResult["STAT_TIME"][$Year][$Month]["MONTH_MINUTES".$key]+$time["MINUTES"];
        if( $arResult["STAT_TIME"][$Year][$Month]["MONTH_MINUTES".$key]>=$oneHourInMinutes) {
            $arResult["STAT_TIME"][$Year][$Month]["MONTH_HOURS".$key]=$arResult["STAT_TIME"][$Year][$Month]["MONTH_HOURS".$key]+$oneHour;
            $arResult["STAT_TIME"][$Year][$Month]["MONTH_MINUTES".$key]=$arResult["STAT_TIME"][$Year][$Month]["MONTH_MINUTES".$key]-$oneHourInMinutes;  
        } 
        //Calculate client time
        $arResult["STAT_TIME"][$Year][$Month][$time["CLIENT_ID"]]["CLIENT_HOURS".$key]=$arResult["STAT_TIME"][$Year][$Month][$time["CLIENT_ID"]]["CLIENT_HOURS".$key]+$time["HOURS"];
        $arResult["STAT_TIME"][$Year][$Month][$time["CLIENT_ID"]]["CLIENT_MINUTES".$key]=$arResult["STAT_TIME"][$Year][$Month][$time["CLIENT_ID"]]["CLIENT_MINUTES".$key]+$time["MINUTES"];
        if( $arResult["STAT_TIME"][$Year][$Month][$time["CLIENT_ID"]]["CLIENT_MINUTES".$key]>=$oneHourInMinutes) {
            $arResult["STAT_TIME"][$Year][$Month][$time["CLIENT_ID"]]["CLIENT_HOURS".$key]=$arResult["STAT_TIME"][$Year][$Month][$time["CLIENT_ID"]]["CLIENT_HOURS".$key]+$oneHour;
            $arResult["STAT_TIME"][$Year][$Month][$time["CLIENT_ID"]]["CLIENT_MINUTES".$key]=$arResult["STAT_TIME"][$Year][$Month][$time["CLIENT_ID"]]["CLIENT_MINUTES".$key]-$oneHourInMinutes;
        }
        return $arResult;
    }

    global $USER;

    //Get id group of support emloyee's 
    $supportId = GKSupportUsers::GetSupportEmployerGroupID();
    
    if ($USER->IsAdmin() || in_array($supportId, $USER->GetUserGroupArray())) {

        //Filter by year  
        if (!empty($_REQUEST["date_fld"])){
            $yearFilterLow=$_REQUEST["date_fld"];
        }  else {
            $yearFilterLow="01.01.".date("Y");
        }  
        if (!empty($_REQUEST["date_fld_finish"])){
            $yearFilterHigh=$_REQUEST["date_fld_finish"];
        }  else {
            $yearFilterHigh="31.12.".date("Y");
        }  
        $ticketFilter[">=DATE"]=date("Y-m-d H:i:s", strtotime($yearFilterLow));    
        $ticketFilter["<=DATE"]=date("Y-m-d H:i:s", strtotime($yearFilterHigh));  

        if (!empty($_REQUEST["user"])){
            $filter["ID"]=intval($_REQUEST["user"]);
        }

        //Creating a list of all client's
        $client = GKSupportUsers::GetList($by="PROJECT_NAME",$sort="asc",array());

        while($arClient = $client->Fetch()) {
            $arResult["CLIENT_LIST"][$arClient["ID"]]=$arClient; 
        }

        //Get list of all group's support
        $arResult["GROUPS"] = GKSupport::GetBitrixSupportGroup();

        //Getting id group of support employee's  
        $supportId = GKSupportUsers::GetSupportEmployerGroupID();

        //Get list of support employee's for filter
        $arFilt = Array( "GROUPS_ID" => Array($supportId), "ACTIVE"=>"Y");
        $rsFiltUsers = CUser::GetList(($by="name"), ($order="asc"), $arFilt);
        while($filtUsers = $rsFiltUsers->Fetch()) {    
            $arResult["FILT_USERS"][$filtUsers["ID"]]=$filtUsers;
        }

        //Get list of support employee's
        $filter["GROUPS_ID"] = $supportId;
        $rsUsers = CUser::GetList(($by="name"), ($order="asc"), $filter);

        while($user = $rsUsers->Fetch()) { 
            $arResult["USERS"][$user["ID"]]=$user;
            $ticketFilter["USER_ID"]=$user["ID"];
            $obTime = GKSupportSpentTime::GetList(($by="id"), ($order="desc"), $ticketFilter); 
            while($time = $obTime->Fetch()) {
                $inPayment = GKSupportTicketPayment::GetByTicket($time["TICKET_ID"]);
                $time["IN_PAYMENT"]=$inPayment;
                $Month = substr($time["DATE"],5,2); 
                $Year = substr($time["DATE"],0,4); 
                $arResult=calculateTime($Year, $Month, $user, $time, $arResult, '');
                if ($inPayment=="Y") {
                    $arResult=calculateTime($Year, $Month, $user, $time, $arResult, '_IN_PAY');
                    if ($time["IS_PAYED"]=="Y"){
                        $arResult=calculateTime($Year, $Month, $user, $time, $arResult, '_PAYED');
                    }
                } 
                if (!empty($_REQUEST["user"])) {
                    $arResult["STAT"][$Year][$Month][$time["USER_ID"]][$time["CLIENT_ID"]][$time["TICKET_ID"]][]=$time;
                } else {
                    $arResult["STAT"][$Year][$Month][$time["USER_ID"]][$time["TICKET_ID"]][]=$time;
                }
            }

        }
        //Sorting month's
        foreach  ($arResult["STAT"] as $yID => $year) {
            krsort($arResult["STAT"][$yID]);                     
        }

        //Write filter in result array
        $arResult["FILTER"]=$filter;
        $arResult["FILTER"][">=DATE"]=date("d.m.Y", strtotime($ticketFilter[">=DATE"]));
        $arResult["FILTER"]["<=DATE"]=date("d.m.Y", strtotime($ticketFilter["<=DATE"]));

        $this->IncludeComponentTemplate();
} else {
    $arResult='';
}