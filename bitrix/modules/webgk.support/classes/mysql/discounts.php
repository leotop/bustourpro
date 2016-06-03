<?

    Class GKSupportDiscounts extends GKSupport {


        /****
        * @param string $by
        * @param string $sort
        * @param array $arFilter
        * $arFilter: ID, ACTIVE, NAME, TYPE, DISCOUNT, USER_ID, GROUP_ID, SERVICE_ID, 
        */
        function GetList($by = "ID", $sort = "asc",$arFilter = array()) {
            if (empty($by)) {
                $by = "ID";
            }
            if (empty($sort)) {
                $sort = "ASC";
            }

            global $DB;
            $res = "";

            //filter
            if (is_array($arFilter) && count($arFilter) > 0) {    

                $whereAr = array();
                foreach ($arFilter as $orig_key=>$val) {

                    $res = GKSupport::GetFilterSign($orig_key);  

                    if (!empty($val) || strlen($val) > 0){
                        $whereAr[] = $res["FIELD"].$res["SIGN"].'"'.$val.'"'; 
                    }  
                }
                if (count ($whereAr) > 0) {
                    $where = ' WHERE ';
                    $where .= implode(" AND ",$whereAr);
                }

            }


            //order
            $order = ' ORDER BY '.$by.' '.$sort.' ';   


            $query = 'SELECT * FROM `webgk_support_discounts` '.$where.$order.$limit;
            $res = $DB->query($query);   

            return $res; 
        }   


        /****
        * $arFields: ACTIVE, NAME, DISCOUNT, USER_ID, GROUP_ID, SERVICE_ID, TYPE 
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

            $type = array("R","P"); //RUB or PERCENT
            if (!empty($arFields["TYPE"]) && in_array($arFields["TYPE"],$type)) {
                $rsFields["TYPE"] = "'".$arFields["TYPE"]."'";  
            }  
            else {
                $rsFields["TYPE"] = "'R'"; 
            }      

            if (!empty($arFields["NAME"])) {
                $rsFields["NAME"] = "'".$arFields["NAME"]."'";
            }  
            else {
                $rsFields["NAME"] = "Discount ".date("U");
            } 


            if (!empty($arFields["DISCOUNT"])) {
                $rsFields["DISCOUNT"] = "'".floatval($arFields["DISCOUNT"])."'";
            }  
            else {
                $rsFields["DISCOUNT"] = 0;
            } 

            if (!empty($arFields["USER_ID"])) {
                $rsFields["USER_ID"] = "'".intval($arFields["USER_ID"])."'";
            } 

            if ($arFields["GROUP_ID"] != "") {
                $rsFields["GROUP_ID"] = intval($arFields["GROUP_ID"]);;
            } 

            if (!empty($arFields["SERVICE_ID"])) {
                $rsFields["SERVICE_ID"] = "'".intval($arFields["SERVICE_ID"])."'";
            }   
            
       
            $rsFields["ID"] = "NULL";   

            $ID = $DB->Insert("webgk_support_discounts", $rsFields);

            return $ID;
        }


        /****
        * $arFields: ACTIVE, NAME, DISCOUNT, USER_ID, GROUP_ID, SERVICE_ID, TYPE 
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

            $type = array("R","P"); //RUB or PERCENT
            if (!empty($arFields["TYPE"]) && in_array($arFields["TYPE"],$type)) {
                $rsFields["TYPE"] = "'".$arFields["TYPE"]."'";  
            }              

            if (!empty($arFields["NAME"])) {
                $rsFields["NAME"] = "'".$arFields["NAME"]."'";
            }  

            if ($arFields["DISCOUNT"]) {
                $rsFields["DISCOUNT"] = "'".floatval($arFields["DISCOUNT"])."'";
            }  

            if ($arFields["USER_ID"]) {
                $rsFields["USER_ID"] = "'".intval($arFields["USER_ID"])."'";
            } 

            if ($arFields["SERVICE_ID"]) {
                $rsFields["SERVICE_ID"] = "'".intval($arFields["SERVICE_ID"])."'";
            }

            if ($arFields["GROUP_ID"] != "") {
                $rsFields["GROUP_ID"] = intval($arFields["GROUP_ID"]);;
            }            

            $where = "WHERE `ID`=".$ID;       

            $res = $DB->Update("webgk_support_discounts", $rsFields, $where);

            return $res;
        } 

        function Delete($ID) {
            global $DB;
            $query = "DELETE FROM `webgk_support_discounts` WHERE ID=".$ID;
            $res = $DB->Query($query);
            return $res;
        }


        /***
        * get all discounts for user
        * 
        * @param integer $clID - client ID
        * 
        * result - array of arrays:
        * array( 
        * [DISCOUNT_ID] => Array
                    (
                    [ID] => 1  //discount ID
                    [ACTIVE] => Y   //discount active
                    [SERVICE_ID] => 0
                    [USER_ID] => 38
                    [GROUP_ID] => 0
                    [DISCOUNT] => 10
                    [NAME] => Для любимых клиентов
                    [TYPE] => P
                    )
                )
        */
        
        function getUserDiscounts($clID) {

            $userDiscounts = array();

            $user = GKSupportUsers::GetList($by = "ID", $sort = "ASC",array("ID"=>$clID));
            $arUser = $user->Fetch();

            $dicsounts = GKSupportDiscounts::GetList($by="ID",$sort="ASC",array("USER_ID"=>$clID, "ACTIVE"=>"Y"));
            while($arDiscount = $dicsounts->Fetch()) {
                $userDiscounts[$arDiscount["ID"]] = $arDiscount; 
            }

            if ($arUser["GROUP_ID"] > 0) {
                $groupDicsounts = GKSupportDiscounts::GetList($by="ID",$sort="ASC",array("GROUP_ID"=>$arUser["GROUP_ID"],"ACTIVE"=>"Y"));
                while($arGroupDiscount = $groupDicsounts->Fetch()) {
                    $userDiscounts[$arGroupDiscount["ID"]] = $arGroupDiscount; 
                }

            }

            return $userDiscounts;      

        }


    }

?>