<?  require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"); 

    Cmodule::IncludeModule("webgk.support");

    $ticketID=intval($_GET["ticketID"]);
    $userID=intval($_GET["userID"]);
    
    $arFilter = array();
    //if user has groups
    $groupsInfo = GKSupport::GetBitrixSupportGroupInfo();
    $user = GKSupportUsers::GetList($by="ID",$sort="ASC",array("ID"=>$userID))->Fetch();
    $gID = $groupsInfo["USER_GROUPS"][$user["USER_ID"]];         
    if ($gID > 0) {
        $users = $groupsInfo["GROUPS"][$gID]["USERS"]; 
        $clients = array();
        foreach($users as $uID) {
            $clID = GKSupportUsers::GetClientId($uID);
            if ($clID > 0) {
                $clients[] = $clID;
            }
        }   
        if (count($clients) > 0) {
            $arFilter["CLIENT_ID"] = $clients;
        }
    }

    $yearFilterLow='01.'.$_GET["month"].'.'.$_GET["year"]." 00:00:00";
    $yearFilterHigh='31.'.$_GET["month"].'.'.$_GET["year"]." 23:59:59";
    if ($_GET["checked"]=="false") {
        $checked="N";
    } else if ($_GET["checked"]=="true") {
        $checked="Y";
    }

    if(!empty($ticketID)){
        unset($arFilter["CLIENT_ID"]);
        $arFilter["TICKET_ID"]=$ticketID;
        $arFilter[">=DATE"]=date("Y-m-d H:i:s", strtotime($yearFilterLow));    
        $arFilter["<=DATE"]=date("Y-m-d H:i:s", strtotime($yearFilterHigh));
        $obTime = GKSupportSpentTime::GetList(($by="id"), ($order="desc"), $arFilter); 
        while($time = $obTime->Fetch()) {
            $arFields = array("IS_PAYED"=>$checked);
            GKSupportSpentTime::Update($time["ID"], $arFields);
        }
    } else {
        $arFilter[">=DATE"]=date("Y-m-d H:i:s", strtotime($yearFilterLow));    
        $arFilter["<=DATE"]=date("Y-m-d H:i:s", strtotime($yearFilterHigh));   
        if (!$arFilter["CLIENT_ID"]) {
            $arFilter["CLIENT_ID"]=$userID;
        }
        $obTime = GKSupportSpentTime::GetList(($by="id"), ($order="desc"), $arFilter); 
        while($time = $obTime->Fetch()) {
            $arFields = array("IS_PAYED"=>$checked);
            GKSupportSpentTime::Update($time["ID"], $arFields);
        }
    }
    //    GKSupportTicketPayment::Change($ticketID, $checked);

?>