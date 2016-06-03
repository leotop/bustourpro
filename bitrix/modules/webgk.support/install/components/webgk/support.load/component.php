<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    //Include modules
    Cmodule::IncludeModule("webgk.support");
    Cmodule::IncludeModule("support");

    global $USER;

    //Get id group of support emloyee's 
    $supportId = GKSupportUsers::GetSupportEmployerGroupID();

    if ($USER->IsAdmin() || in_array($supportId, $USER->GetUserGroupArray())) {
        //Filter
        if (!empty($_REQUEST["user"])){
            $arResult["FILTER"]["ID"]=intval($_REQUEST["user"]);
        }                                       

        //Get id group of support emloyee's 
        //$supportId = GKSupportUsers::GetSupportEmployerGroupID();

        //Get list of support employee's for filter
        $arFilt = Array("ACTIVE"=>"Y", "GROUPS_ID" => Array($supportId));
        $rsFiltUsers = CUser::GetList(($by="name"), ($order="asc"), $arFilt);
        while($filtUsers = $rsFiltUsers->Fetch()) {    
            $arResult["FILT_USERS"][$filtUsers["ID"]]=$filtUsers;
        }

        //Get list of support status's on site
        $arFilter = Array("LID" => SITE_ID, "TYPE" => "S");
        //Sorting
        $by = "s_c_sort";
        $sort = "asc";
        $arStatus = CTicketDictionary::GetList($by, $sort, $arFilter, $is_filtered); 
        while($arStat = $arStatus->Fetch()) {
            $arResult["STATUS"][]=$arStat;
        }

        //Get list of support employee's
        $filter = Array("ACTIVE"=>"Y", "GROUPS_ID" => Array($supportId));
        $filter["ID"]=$arResult["FILTER"]["ID"];
        $rsUsers = CUser::GetList(($by="name"), ($order="asc"), $filter);
        while($curUser = $rsUsers->Fetch()) {
            $arResult["ITEMS"][$curUser["ID"]] = $curUser;  
            foreach ($arResult["STATUS"] as $arRes) {
                //Get list ticket's of current employee
                $rs = CTicket::GetList($by="ID", $order="asc", array("RESPONSIBLE_ID"=>$curUser["ID"], "CLOSE"=>"N", "STATUS"=>$arRes["ID"]),  $is_filtered, 'N');
                while($ar = $rs->Fetch()) {
                    $arResult["ITEMS"][$curUser["ID"]]["TICKETS"][$arRes["ID"]]["ID"]=$arRes["ID"];
                    $arResult["ITEMS"][$curUser["ID"]]["TICKETS"][$arRes["ID"]]["NAME"]=$arRes["NAME"];
                    $arResult["ITEMS"][$curUser["ID"]]["TICKETS"][$arRes["ID"]]["TICKETS_LIST"][]=$ar["ID"];
                }
            } 
        }
    }

    $this->IncludeComponentTemplate();
?>