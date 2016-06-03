<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    //Include required modules
    Cmodule::IncludeModule("webgk.support");
    global $USER;

    if($_REQUEST["IS_RESET"]=='Y'){
        header( "Location: ".$APPLICATION->GetCurPage());  
    }

    function makeResultArray($arFilt){

        $user = GKSupportUsers::GetList($by="PROJECT_NAME",$sort="ASC",array("ACTIVE"=>"Y"));
        while($arUser = $user->Fetch()) {
            $arUsers[$arUser["ID"]]=$arUser;
            $arResult["CLIENTS"][]=$arUser;
        }
        //Getting id group of support employee's  
        $supportId = GKSupportUsers::GetSupportEmployerGroupID();

        //Get list of support employee's
        $filter = Array("GROUPS_ID" => Array($supportId));
        $rsEmpl = CUser::GetList(($by="name"), ($order="asc"), $filter);

        while($empl = $rsEmpl->Fetch()) { 
            $arEmpl[$empl["ID"]]=$empl;
            $arResult["USERS"][$empl["ID"]]=$empl;

        };
        //Creating list of all transactions
       
        $transaction = GKSupportTransactions::GetList($by="id",$sort="desc",$arFilt);
        $arResult["OBJECT"]=$transaction;
        while($arTransaction = $transaction->Fetch()) {
            $arResult["ARRAY"][$arTransaction["ID"]]=$arTransaction;
            $arResult["ARRAY"][$arTransaction["ID"]]["PROJECT_NAME"]=$arUsers[$arTransaction["CLIENT_ID"]]["PROJECT_NAME"];
            $arResult["ARRAY"][$arTransaction["ID"]]["USER"]=$arEmpl[$arTransaction["USER_ID"]]["LOGIN"];
        }   
        return $arResult;
    }
    //$arFilter["ACTIVE"] = "Y";
    //Make array for filter
    $arResult["FILTER"] = array();
    if(!empty($_REQUEST["ID"])){
        $arFilter["ID"] = intval($_REQUEST["ID"]); 
    } 
    if(!empty($_REQUEST["ID_TICKET"])){
        $arFilter["TICKET_ID"]=intval($_REQUEST["ID_TICKET"]);    
    }
    if(!empty($_REQUEST["USER"])){
        $arFilter["USER_ID"]=intval($_REQUEST["USER"]);    
    }
    if(!empty($_REQUEST["CLIENT"])){
        $arFilter["CLIENT_ID"]=intval($_REQUEST["CLIENT"]);    
    }
    if(!empty($_REQUEST["TYPE"])){
        $arFilter["TYPE"]=$_REQUEST["TYPE"];    
    }
    if(!empty($_REQUEST["date_fld"])){
        $arFilter[">=DATE"]=date("Y-m-d H:i:s", strtotime($_REQUEST["date_fld"]." 00:00:00"));    
    }
    if(!empty($_REQUEST["date_fld_finish"])){
        $arFilter["<=DATE"]=date("Y-m-d H:i:s", strtotime($_REQUEST["date_fld_finish"]." 23:59:59"));    
    }  
    if ($USER->IsAdmin()) {
        //Creating list of all transactions for admin
        $arResult=makeResultArray($arFilter);
        $arResult["FILTER"] = $arFilter;
    } else if (GKSupportUsers::CheckUser($USER->GetId())){
        //Creating list of all transactions for client
        $arFilter["CLIENT_ID"]=GKSupportUsers::GetClientId($USER->GetId());
        $arResult=makeResultArray($arFilter);
        $arResult["IS_CLIENT"]="Y";
        $arResult["FILTER"] = $arFilter;
    } else {
        //Null array if user doesn't have access
        $arResult='no-access';
    }    
    
    
    $this->IncludeComponentTemplate();
?>