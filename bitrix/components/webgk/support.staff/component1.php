<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    //Include requaried modules
    Cmodule::IncludeModule("webgk.support");
    Cmodule::IncludeModule("support");

    if($_REQUEST["IS_RESET"]=='Y'){
        header( "Location: ".$APPLICATION->GetCurPage());  
    }
    global $USER;
    if ($USER->IsAdmin()) {
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
        $ticketFilter["DATE_LOW"] = strtotime($yearFilterLow);
        $ticketFilter["DATE_HIGH"] = strtotime($yearFilterHigh);

        //Get list of all group's support
        $arResult["GROUPS"] = GKSupport::GetBitrixSupportGroup();

        //Creating a list of all client's
        $client = GKSupportUsers::GetList($by="PROJECT_NAME",$sort="asc",array());

        while($arClient = $client->Fetch()) {
            $arResult["CLIENT_LIST"][$arClient["ID"]]=$arClient;               
        }
        //Getting id group of support employee's  
        $supportId = GKSupportUsers::GetSupportEmployerGroupID();

        //Get list of support employee's
        $filter = Array("GROUPS_ID" => Array($supportId));
        $rsUsers = CUser::GetList(($by="name"), ($order="asc"), $filter);

        while($user = $rsUsers->Fetch()) {   
            $arResult["USER"][$user["ID"]]=$user;   
            $filt_time = Array("USER_ID" => $user["ID"]);
            $obTime = GKSupportSpentTime::GetList(($by="id"), ($order="desc"), $filt_time); 
            while($time = $obTime->Fetch()) {
                if (strtotime($time["DATE"])>=$ticketFilter["DATE_LOW"] && strtotime($time["DATE"])<=$ticketFilter["DATE_HIGH"]) {
                    $Month = substr($time["DATE"],5,2); 
                    $Year = substr($time["DATE"],0,4); 
                    $arTime[$Year][$Month][$user["ID"]]["TIME"][$time["TICKET_ID"]][]=$time;
                    $arResult["USER"][$user["ID"]]["DETAIL"][$Year][$Month][$arResult["CLIENT_LIST"][$time["CLIENT_ID"]]["PROJECT_NAME"]]["TIME"][$time["TICKET_ID"]][]=$time;
                    $arResult["USER"][$user["ID"]]["DETAIL"][$Year][$Month][$arResult["CLIENT_LIST"][$time["CLIENT_ID"]]["PROJECT_NAME"]]["CLIENT_ID"]=$arResult["CLIENT_LIST"][$time["CLIENT_ID"]]["USER_ID"];
                    //If user add time, then add him in result array
                    if (!empty($arTime[$Year][$Month][$user["ID"]])) {
                        $arResult["ITEMS"][$Year][$Month][$user["ID"]]=$user;
                        $arResult["ITEMS"][$Year][$Month][$user["ID"]]["TIME"]=$arTime[$Year][$Month][$user["ID"]]["TIME"];

                    }
                    $inPayment = GKSupportTicketPayment::GetByTicket($time["TICKET_ID"]);
                    $arResult["ITEMS"][$Year][$Month][$user["ID"]]["HOURS"]=$arResult["ITEMS"][$Year][$Month][$user["ID"]]["HOURS"]+$time["HOURS"];
                    $arResult["ITEMS"][$Year][$Month][$user["ID"]]["MINUTES"]=$arResult["ITEMS"][$Year][$Month][$user["ID"]]["MINUTES"]+$time["MINUTES"];
                    if ($arResult["ITEMS"][$Year][$Month][$user["ID"]]["MINUTES"]>=60) {
                        $arResult["ITEMS"][$Year][$Month][$user["ID"]]["HOURS"]=$arResult["ITEMS"][$Year][$Month][$user["ID"]]["HOURS"]+1;
                        $arResult["ITEMS"][$Year][$Month][$user["ID"]]["MINUTES"]=$arResult["ITEMS"][$Year][$Month][$user["ID"]]["MINUTES"]-60;
                    }

                }
            }  

            arshow($arResult["ITEMS"][$Year][$Month][$user["ID"]]);


            //Ticket list from certain month
            $rs = CTicket::GetList($by="s_id", $order="asc", array("RESPONSIBLE_ID"=>$user["ID"], "DATE_CREATE_1"=>"01.".$Month.".".$yearFilter, "DATE_CREATE_2"=>"31.".$Month.".".$yearFilter));
            while($ar = $rs->Fetch()) {
                $arResult["ITEMS"][$Year][$Month][$user["ID"]]["TICKETS"]["TICKETS_LIST"][]=$ar["ID"];
            }
        }
        //Sorting month's
        foreach  ($arResult["ITEMS"] as $yID => $year) {
            krsort($arResult["ITEMS"][$yID]);                     
        }
    }
    arshow($arResult["ITEMS"]);
    $this->IncludeComponentTemplate();
?>