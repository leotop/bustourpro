<?

    IncludeModuleLangFile(__FILE__);

    Class GKSupport {


        /***
        * get sign for sql query
        * 
        * @param string $key
        */
        function GetFilterSign($key)
        {
            $res = array();

            static $double_char = array(
                "!="=>"!=", //not Identical
                "!%"=>"!%", //not substring
                "><"=>"><",  //between
                ">="=>">=", //greater or equal
                "<="=>"<=", //less or equal
            );
            static $single_char = array(
                "="=>"=", //Identical
                "%"=>"%", //substring
                ">"=>">", //greater
                "<"=>"<", //less
                "!"=>"!=", // not field LIKE val
            );
            $key = (string)$key;
            if ($key == '')
                return $res = array("FIELD"=>$key,"SIGN"=>"=");                    
            $op = substr($key,0,2);
            if($op && isset($double_char[$op]))
                return  $res = array("FIELD"=>substr($key,2), "SIGN"=>$double_char[$op]);
            $op = substr($key,0,1);
            if($op && isset($single_char[$op]))
                return  $res = array("FIELD"=>substr($key,1), "SIGN"=>$single_char[$op]);  

            return array("FIELD"=>$key, "SIGN"=>"="); 

        }


        /***
        * get clients group ID
        * 
        */
        function GetSupportEmployerGroupID() {

            //get full group list
            $arGroups = array();
            $groups = CGroup::GetList($by = "id",$order = "asc",array(),"N");
            while($arGroup = $groups->Fetch()) {
                $arGroups[] = $arGroup["ID"];
            }

            //searching for group with permission = R (this is support clients)
            foreach ($arGroups as $groupID) { 
                $group = array($groupID);
                $res = CMain::GetUserRoles("support", $group, "N", "N", false);
                if ($res[0] == "T") {
                    $supportGroup = $groupID;
                    break;
                }         
            }  

            return $supportGroup;
        }


        /***
        * get service price for client
        * 
        * @param integer $clID - client ID
        * @param integer $sID - service ID
        */
        function GetClientServicePrice($clID,$sID) {     

            $service = GKSupportServices::GetList($by="ID",$sort="ASC",array("ID"=>$sID));
            $arService = $service->Fetch();
            if (!$arService["ID"]) {
                return false;
            }   

            $price = intval($arService["HOUR_PRICE"]);  

            //check user discounts
            $userDiscounts = GKSupportDiscounts::getUserDiscounts($clID);
            if (is_array($userDiscounts) && count($userDiscounts) > 0) {
                foreach ($userDiscounts as $discount) {
                    $servicePrice = "";
                    switch($discount["TYPE"]) {
                        case "R": //rouble discount
                            if ($discount["SERVICE_ID"] == $arService["ID"] || $discount["SERVICE_ID"] == 0) {
                                $servicePrice = intval($arService["HOUR_PRICE"] - $discount["DISCOUNT"]); 
                                if ($servicePrice < $price) {
                                    $price = $servicePrice; 
                                } 
                            }
                            break;
                        case "P": //percent discount
                            if ($discount["SERVICE_ID"] == $arService["ID"] || $discount["SERVICE_ID"] == 0) {
                                $servicePrice = intval($arService["HOUR_PRICE"] - $arService["HOUR_PRICE"]*$discount["DISCOUNT"]/100);   
                                if ($servicePrice < $price) {
                                    $price = $servicePrice; 
                                }
                            }
                            break;
                    }       
                }
            }

            return $price; 

        }


        /**
        * get bitrix support group users (from /bitrix/admin/ticket_group_list.php) uID=>group name
        * 
        */
        function GetBitrixSupportGroup(){

            CModule::IncludeModule("support");
            $userByGroup = array();

            $group = CSupportUserGroup::GetList();
            while($arGroup = $group->Fetch()) {   
                $list = CSupportUserGroup::GetUserGroupList(array(),array("GROUP_ID"=>$arGroup["ID"]));
                while($arList = $list->Fetch()) {
                    $userByGroup[$arList["USER_ID"]] = $arGroup["NAME"];
                }      
            }

            return $userByGroup;
        }



        /**
        * get full support groups info (users by groups, balance)
        * 
        */

        function GetBitrixSupportGroupInfo() {

            CModule::IncludeModule("support");
            $res = array();

            $group = CSupportUserGroup::GetList();
            while($arGroup = $group->Fetch()) {   

                $users = array();

                
                
                $list = CSupportUserGroup::GetUserGroupList(array(),array("GROUP_ID"=>$arGroup["ID"]));
                while($arList = $list->Fetch()) {
                    $users[] = $arList["USER_ID"];
                    $res["USER_GROUPS"][$arList["USER_ID"]] = $arGroup["ID"];
                }  
                
                $balance = 0;
                foreach ($users as $user) {
                    $clID = GKSupportUsers::GetClientId($user);
                    $clientBalance = GKSupportUsers::GetClientBalance($clID);
                    $balance += $clientBalance; 
                }       

                $res["GROUPS"][$arGroup["ID"]] = array(
                    "NAME" => $arGroup["NAME"],
                    "USERS" => $users,
                    "BALANCE" => $balance  
                );     
            }

            return $res;

        } 



    }




?>