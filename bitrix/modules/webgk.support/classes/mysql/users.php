<?

    Class GKSupportUsers extends GKSupport {


        /***
        * get user list from database
        * 
        * @param string $by
        * @param string $sort
        * @param array $arFilter
        * @param array $navParams
        * @param array $arSelect
        * $arFilter: ID, USER_ID, GROUP_ID, PROJECT_NAME, BALANCE, ACTIVE
        */
        function GetList($by = "ID", $sort = "ASC",$arFilter = array(),$navParams = false,$arSelect = false) {

            if (empty($by)) {
                $by = "ID";
            }
            if (empty($sort)) {
                $sort = "ASC";
            }

            global $DB;
            $res = "";

            $select = $where = $order = $limit = ""; 

            //selected fields
            if (!is_array($arSelect) || count($arSelect) <= 0) {
                $select = "*";  
            }
            else if (is_array($arSelect) && count($arSelect) > 0) {                
                $select = implode(",",$arSelect);  
            }

            //filter
            if (is_array($arFilter) && count($arFilter) > 0) {    

                $whereAr = array();
                $orArr = array();
                foreach ($arFilter as $orig_key=>$val) {

                    $res = GKSupport::GetFilterSign($orig_key);                       

                    if (is_array($val) && count($val) > 0) {
                        foreach ($val as $v) {
                           $orArr[] = $res["FIELD"].$res["SIGN"].'"'.$v.'"';  
                        }
                    } 
                    
                    elseif (!empty($val) || strlen($val) > 0){
                        $whereAr[] = $res["FIELD"].$res["SIGN"].'"'.$val.'"'; 
                    }  
                }
                if (count ($whereAr) > 0) {
                    $where = ' WHERE ';
                    $where .= implode(" AND ",$whereAr);
                }
                
                if (count ($orArr) > 0) {
                    $where .= "AND (".implode(" OR ",$orArr).")";
                }

            }

            //order
            $order = ' ORDER BY '.$by.' '.$sort.' ';   

            //limit    

            $query = 'SELECT '.$select.' FROM `webgk_support_users` '.$where.$order.$limit;  
            global $USER;                
            $res = $DB->query($query);   

            return $res;                   
        }   

        /****
        * @param mixed $arFields
        * $arFields: USER_ID (is_required), PROJECT_NAME (is_required), BALANCE , ACTIVE, GROUP_ID
        */
        function Add($arFields = array()) {
            global $DB;

            $active = array("Y","N");
            if (!empty($arFields["ACTIVE"]) && in_array($arFields["ACTIVE"],$active)) {
                $rsFields["ACTIVE"] = "'".$arFields["ACTIVE"]."'";  
            }  
            else {
                $rsFields["ACTIVE"] = "'Y'"; 
            }

            $rsFields["USER_ID"] = intval($arFields["USER_ID"]);

            if (!$arFields["PROJECT_NAME"] || !($arFields["USER_ID"] > 0)) {
                return false;
            }

            if (!$arFields["BALANCE"]) {
                $rsFields["BALANCE"] = 0;
            }
            else {
                $rsFields["BALANCE"] = "'".floatval($arFields["BALANCE"])."'";
            }   

            $rsFields["PROJECT_NAME"] = "'".$arFields["PROJECT_NAME"]."'"; 
            $rsFields["ID"] = "NULL";   

            $ID = $DB->Insert("webgk_support_users", $rsFields);

            return $ID;

        }

        /****
        * @param integer $ID (is_required), from webgk_support_users
        * @param array $arFields
        * $arFields: PROJECT_NAME, BALANCE, GROUP_ID, USER_ID, ACTIVE
        */

        function Update($ID, $arFields = array()) {
            global $DB;
            $ID = intval($ID);

            if (!($ID > 0)) {
                return false;
            }

            $active = array("Y","N");
            if (!empty($arFields["ACTIVE"]) && in_array($arFields["ACTIVE"],$active)) {
                $rsFields["ACTIVE"] = "'".$arFields["ACTIVE"]."'";  
            }  

            if ($arFields["BALANCE"]) {
                $arFields["BALANCE"] = floatval($arFields["BALANCE"]);       
            }  
            
            if (strlen($arFields["BALANCE"]) > 0) {
            $rsFields["BALANCE"] = $arFields["BALANCE"];
            }
            

            if (strlen($arFields["PROJECT_NAME"]) > 0) {
                $rsFields["PROJECT_NAME"] = "'".$arFields["PROJECT_NAME"]."'";
            }    

            if (intval($arFields["USER_ID"]) > 0) {
                $rsFields["USER_ID"] = intval($arFields["USER_ID"]);
            }

           if ($arFields["GROUP_ID"] != "") {
                $rsFields["GROUP_ID"] = intval($arFields["GROUP_ID"]);
           } 

            $where = "WHERE `ID`=".$ID;       
            $res = $DB->Update("webgk_support_users", $rsFields, $where);

            return $res;

        } 

        /****
        * delete support client from database
        * 
        * @param integer $ID
        */

        function Delete($ID) {
            global $DB;
            $query = "DELETE FROM `webgk_support_users` WHERE USER_ID=".$ID;
            $res = $DB->query($query);
            return $res;
        }


        /****
        * check user in database. if user exists - return true, else - false
        * 
        * @param integer $ID - USER_ID from table "b_user"
        */

        function CheckUser($ID) {
            $res = false;
            $ID = intval($ID);           
            $check = mysql_fetch_assoc(mysql_query("SELECT `ID` FROM `webgk_support_users` WHERE `USER_ID`=".$ID));
            if ($check["ID"] > 0) {
                $res = true;  
            } 

            return $res;
        }

        /****
        * check user active (ACTIVE=Y/N)
        * 
        * @param integer $ID - USER_ID from table "webgk_support_users"
        */
        function CheckUserActive($ID) {
            $res = false;
            $ID = intval($ID);           
            $check = mysql_fetch_assoc(mysql_query("SELECT `ACTIVE` FROM `webgk_support_users` WHERE `USER_ID`=".$ID));
            if ($check["AVTIVE"] == "Y") {
                $res = true;  
            }       
            return $res; 
        }


        /****
        * update client balance
        * 
        * @param mixed $clientID - ID from webgk_support_users
        * @param mixed $summ - summ to update
        * @param mixed $type - P - plus, M - minus
        */
        function UpdateClientBalance($clientID,$summ,$type) {
            $res = false;

            $clientID = intval($clientID);
            $summ = abs(floatval($summ));


            if (!$clientID || $clientID <=0) {
                return false;
            }

            $arType = array("P","M");
            if (!in_array($type,$arType)) {
                return false; 
            }

            if($summ == "") {
                return false;
            }   

            $curBalance = GKSupportUsers::GetClientBalance($clientID);
            switch ($type) {
                case "P": $newBalance = $curBalance + $summ; break;
                case "M": $newBalance = $curBalance - $summ; break;
            }

            $res = GKSupportUsers::Update($clientID,array("BALANCE"=>$newBalance));

            return $res;

        }


        /****
        * get client balance
        * 
        * @param mixed $clientID - ID from webgk_support_users 
        */
        function GetClientBalance($clientID) {
            $clientID = intval($clientID);
            $summ = abs(floatval($summ));

            $res = false;

            if (!$clientID || $clientID <=0) {
                return false;
            }
            else {
                $client = GKSupportUsers::GetList($by = "ID",$sort = "ASC",array("ID"=>$clientID),false,array("BALANCE"));
                $arClient = $client->Fetch();
                if ($arClient["BALANCE"] != "") {
                    $res = $arClient["BALANCE"];
                }
            }

            return $res;
        }

        /***
        * get client ID
        * 
        * @param mixed $userID - ID from b_user. if empty, ID = current authorized user ID
        */
        function GetClientId($userID) {
            global $USER;
            $res = false;

            $ID = intval($userID);
            if (empty($ID)) {
                $ID = $USER->GetId();
            }
            if ($ID > 0) {
                $client = GKSupportUsers::GetList($by = "ID",$sort = "ASC",array("USER_ID"=>$ID),false,array("ID"));
                $arClient = $client->Fetch();
                if (!empty($arClient["ID"])) {
                    $res = $arClient["ID"];
                }
            } else {
                return false;
            }


            return $res;
        }

    }

?>