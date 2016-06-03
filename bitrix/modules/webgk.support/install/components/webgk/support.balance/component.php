<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    //Include module
    Cmodule::IncludeModule("webgk.support");

    global $USER;
    $uID = $USER->GetId();
    
    $summ = 0;
    
   
    $supportStaffGroupID = GKSupport::GetSupportEmployerGroupID();
    $supportStaff = in_array($supportStaffGroupID,CUser::GetUserGroup($uID));
    
    
    
    $arResult["GROUPS_INFO"] = GKSupport::GetBitrixSupportGroupInfo();
     
    
    //If  user is support client, then show him him data
    if(GKSupportUsers::CheckUser($uID)){
        $arFilter["ID"]=GKSupportUsers::GetClientId($uID);        
    } 
     
    
    //Get list all clients 
    if ($USER->IsAdmin() || GKSupportUsers::CheckUser($uID)) {
        $arFilter["ACTIVE"] ="Y";
        
        if ($arParams["HIDE_NULL_BALANCE"] == "Y" && ($USER->IsAdmin() || $supportStaff)) {
             $arFilter["!BALANCE"] = 0;
        }
        
        $user = GKSupportUsers::GetList($by="PROJECT_NAME",$sort="ASC",$arFilter);
        $arResult["OBJECT"]=$user;
        
        while($data = $user->Fetch()) {
           $summ += $data["BALANCE"]; 
        }
        
        $arResult["SUMM"] = $summ;
    }  

    $this->IncludeComponentTemplate();
?>