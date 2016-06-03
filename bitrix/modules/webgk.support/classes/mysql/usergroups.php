<?

    Class GKSupportUserGroups extends GKSupport {

        /****
        * @param string $by
        * @param string $sort
        * @param array $arFilter
        * $arFilter: ID, ACTIVE, NAME, DESCRIPTION
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


            $query = 'SELECT * FROM `webgk_support_groups` '.$where.$order.$limit;
            $res = $DB->query($query);   

            return $res;       
        }      


        /****
        * $arFields: ACTIVE, NAME, DESCRIPTION
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

            if (!empty($arFields["NAME"])) {
                $rsFields["NAME"] = "'".$arFields["NAME"]."'";
            }  

            else {
                $rsFields["NAME"] = "Group ".date("U");
            } 

            if (!empty($arFields["DESCRIPTION"])) {
                $rsFields["DESCRIPTION"] = "'".$arFields["DESCRIPTION"]."'";
            }         

            $rsFields["ID"] = "NULL";   

            $ID = $DB->Insert("webgk_support_groups", $rsFields);

            return $ID;
        }   


        /****
        * $arFields: ID, ACTIVE, NAME, DESCRIPTION
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


            if (!empty($arFields["NAME"])) {
                $rsFields["NAME"] = "'".$arFields["NAME"]."'";
            }  

            if ($arFields["DESCRIPTION"]) {
                $rsFields["DESCRIPTION"] = "'".$arFields["DESCRIPTION"]."'";
            }                 

            $where = "WHERE `ID`=".$ID;       

            $res = $DB->Update("webgk_support_groups", $rsFields, $where);

            return $res;
        } 




        function Delete($ID) {
            global $DB;
            $query = "DELETE FROM `webgk_support_groups` where ID=".$ID;
            $res = $DB->Query($query);
            return $res;
        }

    }

?>