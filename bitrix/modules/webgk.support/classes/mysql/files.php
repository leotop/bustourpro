<?

    Class GKSupportFiles extends GKSupport {

        /****
        * @param string $by
        * @param string $sort
        * @param array $arFilter
        * $arFilter: ID, FILE_ID (from `b_file`), CLIENT_ID (from `webgk_support_users`), DATE (timestamp), NAME
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


            $query = 'SELECT * FROM `webgk_support_files` '.$where.$order.$limit;
            $res = $DB->query($query);   

            return $res; 
        }  

        // $arFilter:  FILE_ID (from `b_file`), CLIENT_ID (from `webgk_support_users`), DATE (timestamp), NAME
        function Add($arFields = array()) {
            global $DB; 

            if (!empty($arFields["NAME"])) {
                $rsFields["NAME"] = "'".$arFields["NAME"]."'";
            } 
            else {
                $rsFields["NAME"] = "'file".date("U")."'";
            }

            if (!empty($arFields["FILE_ID"])) {
                $rsFields["FILE_ID"] = "'".intval($arFields["FILE_ID"])."'";
            }  

            if (!empty($arFields["CLIENT_ID"])) {
                $rsFields["CLIENT_ID"] = "'".intval($arFields["CLIENT_ID"])."'";
            }  


            if (!empty($arFields["COMMENT"])) {
                $rsFields["COMMENT"] = "'".$arFields["COMMENT"]."'";
            }     


            $rsFields["ID"] = "NULL";   

            $ID = $DB->Insert("webgk_support_files", $rsFields);

            return $ID;
        }



        function Update($ID, $arFields = array()) {
            global $DB;
            CModule::IncludeModule("main");

            $ID = intval($ID);

            if (!($ID > 0)) {
                return false;
            }   

            if (!empty($arFields["NAME"])) {
                $rsFields["NAME"] = "'".$arFields["NAME"]."'";
            } 

            if (!empty($arFields["FILE_ID"])) {
                $rsFields["FILE_ID"] = "'".intval($arFields["FILE_ID"])."'";
                $oldFileId = GKSupportFiles::GetList($by="ID",$sort="ASC",array("ID"=>$ID));
                $arOldFile = $oldFileId->Fetch();
                if ($rsFields["FILE_ID"] == $arOldFile["FILE_ID"]) {
                    $arOldFile["FILE_ID"] = 0; 
                }
            } 

            if (!empty($arFields["CLIENT_ID"])) {
                $rsFields["CLIENT_ID"] = "'".intval($arFields["CLIENT_ID"])."'";
            }


            if (!empty($arFields["COMMENT"])) {
                $rsFields["COMMENT"] = "'".$arFields["COMMENT"]."'";
            }                             

            $where = "WHERE `ID`=".$ID;       

            $res = $DB->Update("webgk_support_files", $rsFields, $where);

            if ($res && $arOldFile["FILE_ID"] > 0 ) {
                //delete old file from DB
                CFile::Delete($arOldFile["FILE_ID"]);
            }

            return $res;
        } 

        function Delete($ID) {
            global $DB;    
            CModule::IncludeModule("main");

            $oldFileId = GKSupportFiles::GetList($by="ID",$sort="ASC",array("ID"=>$ID));
            $arOldFile = $oldFileId->Fetch();


            $query = "DELETE FROM `webgk_support_files` WHERE ID=".$ID;
            $res = $DB->Query($query);

            if ($res && $rsFields["FILE_ID"] > 0) {
                CFile::Delete($arOldFile["FILE_ID"]);
            }

            return $res;
        }

    }

?>