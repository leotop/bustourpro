<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    //Include requaried modules
    Cmodule::IncludeModule("webgk.support");
    Cmodule::IncludeModule("support");

    if($_REQUEST["IS_RESET"]=='Y'){
        header( "Location: ".$APPLICATION->GetCurPage());  
    }

    global $USER;

    $oneHour = 1;
    $oneHourInMinutes = 60;


    function calculateTimePrice ($Year, $Month, $user, $time, $arResult, $key) {
        $oneHour = 1;
        $oneHourInMinutes = 60;
        //Calculate client time
        $arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["HOURS".$key]=$arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["HOURS".$key]+$time["HOURS"];
        $arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["MINUTES".$key]=$arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["MINUTES".$key]+$time["MINUTES"];
        $arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["PRICE".$key]=$arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["PRICE".$key]+$time["PRICE"];
        if( $arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["MINUTES".$key]>=$oneHourInMinutes) {
            $arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["HOURS".$key]=$arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["HOURS".$key]+$oneHour;
            $arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["MINUTES".$key]=$arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["MINUTES".$key]-$oneHourInMinutes;
        }
        //Calculate ticket time  
        $arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["TIME"][$time["TICKET_ID"]]["HOURS".$key]=$arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["TIME"][$time["TICKET_ID"]]["HOURS".$key]+$time["HOURS"];
        $arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["TIME"][$time["TICKET_ID"]]["MINUTES".$key]=$arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["TIME"][$time["TICKET_ID"]]["MINUTES".$key]+$time["MINUTES"];
        $arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["TIME"][$time["TICKET_ID"]]["PRICE".$key]=$arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["TIME"][$time["TICKET_ID"]]["PRICE".$key]+$time["PRICE"];
        if( $arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["TIME"][$time["TICKET_ID"]]["MINUTES".$key]>=$oneHourInMinutes) {
            $arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["TIME"][$time["TICKET_ID"]]["HOURS".$key]=$arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["TIME"][$time["TICKET_ID"]]["HOURS".$key]+$oneHour;
            $arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["TIME"][$time["TICKET_ID"]]["MINUTES".$key]=$arResult["STAT"][$Year][$Month][$user["PROJECT_NAME"]]["TIME"][$time["TICKET_ID"]]["MINUTES".$key]-$oneHourInMinutes;
        } 
        //Calculate all time 
        $arResult["STAT_TIME"][$Year][$Month]["HOURS".$key]=$arResult["STAT_TIME"][$Year][$Month]["HOURS".$key]+$time["HOURS"];
        $arResult["STAT_TIME"][$Year][$Month]["MINUTES".$key]=$arResult["STAT_TIME"][$Year][$Month]["MINUTES".$key]+$time["MINUTES"];
        $arResult["STAT_TIME"][$Year][$Month]["PRICE".$key]=$arResult["STAT_TIME"][$Year][$Month]["PRICE".$key]+$time["PRICE"];
        if( $arResult["STAT_TIME"][$Year][$Month]["MINUTES".$key]>=$oneHourInMinutes) {
            $arResult["STAT_TIME"][$Year][$Month]["HOURS".$key]=$arResult["STAT_TIME"][$Year][$Month]["HOURS".$key]+$oneHour;
            $arResult["STAT_TIME"][$Year][$Month]["MINUTES".$key]=$arResult["STAT_TIME"][$Year][$Month]["MINUTES".$key]-$oneHourInMinutes;
        }

        return $arResult;
    }


    $arResult["IS_CLIENT"]=GKSupportUsers::GetClientId($USER->GetId());
    if (!empty($arResult["IS_CLIENT"])) {
        $_REQUEST["user"]=$arResult["IS_CLIENT"];
    }

    //Filter by year  
    if (!empty($_REQUEST["date_fld"])){
        $yearFilterLow=$_REQUEST["date_fld"]." 00:00:00";
    }  else {
        $yearFilterLow="01.01.".date("Y");
    }  
    if (!empty($_REQUEST["date_fld_finish"])){
        $yearFilterHigh=$_REQUEST["date_fld_finish"]." 23:59:59";;
    }  else {
        $yearFilterHigh="31.12.".date("Y");
    } 
    $arFilter[">=DATE"]=date("Y-m-d H:i:s", strtotime($yearFilterLow));    
    $arFilter["<=DATE"]=date("Y-m-d H:i:s", strtotime($yearFilterHigh));    


    //Get list of all group's support
    $arResult["GROUPS"] = GKSupport::GetBitrixSupportGroup();

    //get full groups info
    $arResult["GROUPS_INFO"] = GKSupport::GetBitrixSupportGroupInfo();
    //Filter for detail client
    if (!empty($_REQUEST["user"])){   
        $arFilterClient["ID"]=intval($_REQUEST["user"]);
        //check for user groups

        $uID = GKSupportUsers::GetList($by="ID",$sort="ASC",array("ID"=>$arFilterClient["ID"]))->Fetch();         
        $gID = $arResult["GROUPS_INFO"]["USER_GROUPS"][$uID["USER_ID"]];                
        if ($gID > 0){
            unset($arFilterClient["ID"]);
            $arFilterClient["USER_ID"] = $arResult["GROUPS_INFO"]["GROUPS"][$gID]["USERS"];
        }
    }
    $arFilterClient["ACTIVE"] = "Y";


    $groupsInFilter = array();

    $users = GKSupportUsers::GetList($by="PROJECT_NAME",$sort="asc", array("ACTIVE"=>"Y"));
    while($arUser = $users->Fetch()) {

        //check for user groups
        $projectName = $arUser["PROJECT_NAME"];
        $groupName = $arResult["GROUPS"][$arUser["USER_ID"]];             


        $arResult["CLIENT"][$arUser["ID"]]=$arUser;
        $arResult["FILTER_CLIENT"][$arUser["PROJECT_NAME"]]=$arUser;   


        //if user has group
        if ($groupName) {
            if ($groupsInFilter[$groupName]) {                   
                unset($arResult["CLIENT"][$groupsInFilter[$groupName]]);                   
            } 
            
            $projectName = $groupName." (".GetMessage("GROUP").")"; 
            $arUser["PROJECT_NAME"] = $projectName;
            $arResult["FILTER_CLIENT"][$arUser["PROJECT_NAME"]]=$arUser;
            $arResult["CLIENT"][$arUser["ID"]] = $arUser;
            $groupsInFilter[$groupName] = $arUser["ID"];  
        }    

      $userForFilter = $arUser["ID"];
    }

    $groupsInFilter = array();

    //Creating a list of all client's
    $client = GKSupportUsers::GetList($by="PROJECT_NAME",$sort="asc", $arFilterClient);
    while($user = $client->Fetch()) { 

        $arFilter["CLIENT_ID"]=$user["ID"];
        $obTime = GKSupportSpentTime::GetList(($by="id"), ($order="desc"), $arFilter); 
        $payed="N";
        while($time = $obTime->Fetch()) {
            $Month = substr($time["DATE"],5,2); 
            $Year = substr($time["DATE"],0,4);

            //check for user groups
            $projectName = $user["PROJECT_NAME"];
            $groupName = $arResult["GROUPS"][$user["USER_ID"]];
            //if user has group
            if ($groupName) {
                $projectName = $groupName." (".GetMessage("GROUP").")"; 
                $user["PROJECT_NAME"] = $projectName;
                $arResult["FILTER_CLIENT"][$user["PROJECT_NAME"]]=$user;
            } 

            if (empty($arResult["STAT"][$Year][$Month][$projectName]["TIME"][$time["TICKET_ID"]]["TITLE"])){
                $obTicket = CTicket::GetByID($time["TICKET_ID"]);
                $ticket = $obTicket->Fetch();
                $arResult["STAT"][$Year][$Month][$projectName]["TIME"][$time["TICKET_ID"]]["TITLE"]=$ticket["TITLE"];
            }
            //Calculate ticket price
            $inPayment = GKSupportTicketPayment::GetByTicket($time["TICKET_ID"]);
            if ($inPayment=="Y"){
                if (empty($arResult["STAT"][$Year][$Month][$projectName]["TIME"][$time["TICKET_ID"]]["IN_PAYMENT"])){
                    $arResult["STAT"][$Year][$Month][$projectName]["TIME"][$time["TICKET_ID"]]["IN_PAYMENT"]="Y";         
                }
                $filt=array("SPENT_TIME_ID"=>$time["ID"]);
                $transaction = GKSupportTransactions::GetList($by="id",$sort="desc",$filt);
                while($arTransaction = $transaction->Fetch()) {
                    $time["PRICE"]=$arTransaction["SUMM"];
                } 
                //Check payed time or not 
                $arResult=calculateTimePrice ($Year, $Month, $user, $time, $arResult, "_TO_PAY");
                if ($time["IS_PAYED"]=="Y") {
                    $arResult=calculateTimePrice ($Year, $Month, $user, $time, $arResult, "_PAYED");
                    $arResult["STAT"][$Year][$Month][$projectName]["TIME"][$time["TICKET_ID"]]["IS_PAYED"]="Y";
                }    else {
                    $arResult["STAT"][$Year][$Month][$projectName]["TIME"][$time["TICKET_ID"]]["IS_PAYED"]="N";
                    $arResult["STAT"][$Year][$Month][$projectName]["IS_PAYED"]="N";     
                }


            }  else {
                $arResult["STAT"][$Year][$Month][$projectName]["TIME"][$time["TICKET_ID"]]["IN_PAYMENT"]="N";
            }  

            $arResult=calculateTimePrice ($Year, $Month, $user, $time, $arResult, '');
            
            if ($arParams["TICKET_DETAIL_PAGE"] && substr_count($arParams["TICKET_DETAIL_PAGE"],"#ID#") > 0) {                  
                $arResult["STAT"][$Year][$Month][$projectName]["TIME"][$time["TICKET_ID"]]["TICKET_DETAIL_PAGE"] = str_replace("#ID#",$time["TICKET_ID"],$arParams["TICKET_DETAIL_PAGE"]);
            }
            
            $arResult["STAT"][$Year][$Month][$projectName]["TIME"][$time["TICKET_ID"]][]=$time;
            $arResult["STAT"][$Year][$Month][$projectName]["USER_ID"] = $user["USER_ID"]; 
            $arResult["STAT"][$Year][$Month][$projectName]["TICKETS"][] = $time["TICKET_ID"];
            $arResult["STAT"][$Year][$Month][$projectName]["TICKETS"] = array_unique($arResult["STAT"][$Year][$Month][$projectName]["TICKETS"]);
            $arResult["STAT"][$Year][$Month][$projectName]["TICKET_TOTAL"] = count($arResult["STAT"][$Year][$Month][$projectName]["TICKETS"]); 
            $arResult["TICKET_TOTAL_ALL"][$Month][$time["TICKET_ID"]] = $time["TICKET_ID"];

        }  

    }
    //Write filter in result array
    if (intval($_REQUEST["user"]) > 0) {$arFilterClient["ID"] = intval($_REQUEST["user"]);} 
    $arResult["FILTER"]["ID"]=$arFilterClient["ID"];  
    $arResult["FILTER"][">=DATE"]=date("d.m.Y", strtotime($arFilter[">=DATE"]));
    $arResult["FILTER"]["<=DATE"]=date("d.m.Y", strtotime($arFilter["<=DATE"])); 
    
    //arshow($arResult);

    $this->IncludeComponentTemplate();
?>