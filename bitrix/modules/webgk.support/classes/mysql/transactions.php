<?

    Class GKSupportTransactions extends GKSupport {

        /****
        * @param string $by
        * @param string $sort
        * @param array $arFilter
        * $arFilter: ID, USER_ID (from `b_user`), CLIENT_ID (from `webgk_support_users`), DATE (timestamp), TYPE (P - plus, M - minus), SUMM, COMMENT, TICKET_ID, SPENT_TIME_ID, ACTIVE
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


            $query = 'SELECT * FROM `webgk_support_transactions` '.$where.$order.$limit;
            $res = $DB->query($query);   

            return $res; 
        }     


        //$arFields: USER_ID (from `b_user`), CLIENT_ID (from `webgk_support_users`), TYPE (P - plus, M - minus), SUMM, COMMENT, TICKET_ID, SPENT_TIME_ID
        function Add($arFields = array()) {
            global $DB; 
            global $USER;   



            $type = array("P","M"); //PLUS or MINUS
            if (!empty($arFields["TYPE"]) && in_array($arFields["TYPE"],$type)) {
                $rsFields["TYPE"] = "'".$arFields["TYPE"]."'";  
            }  
            else {
                return false; 
            }   


            $rsFields["ACTIVE"] = "'Y'";  

            //checking ticket payment status
            $ticketPayment = GKSupportTicketPayment::GetByTicket(intval($arFields["TICKET_ID"]));
            if ($ticketPayment == "N") {
                $rsFields["ACTIVE"] = "'N'"; 
            }


            $rsFields["USER_ID"] = "'".$USER->GetId()."'";     

            if (!empty($arFields["CLIENT_ID"])) {
                $rsFields["CLIENT_ID"] = "'".intval($arFields["CLIENT_ID"])."'";
            }  
            else {
                return false;
            } 

            if (!empty($arFields["SUMM"])) {
                $rsFields["SUMM"] = "'".floatval($arFields["SUMM"])."'";
            }  
            else {
                return false;
            } 

            //$arFields["DATE"] = date("U");             


            if (!empty($arFields["COMMENT"])) {
                $rsFields["COMMENT"] = "'".$arFields["COMMENT"]."'";
            }  

            if (!empty($arFields["TICKET_ID"])) {
                $rsFields["TICKET_ID"] = "'".intval($arFields["TICKET_ID"])."'";
            } 

            if (!empty($arFields["SPENT_TIME_ID"])) {
                $rsFields["SPENT_TIME_ID"] = "'".intval($arFields["SPENT_TIME_ID"])."'";
            }          

            $rsFields["ID"] = "NULL";   



            $ID = $DB->Insert("webgk_support_transactions", $rsFields);


            //update balance
            if ($ID > 0 && $ticketPayment != "N") {
                GKSupportUsers::UpdateClientBalance($arFields["CLIENT_ID"],floatval($arFields["SUMM"]),$arFields["TYPE"]);
            }

            return $ID;
        }



        function Update($ID, $arFields = array()) {
            global $DB;

            $ID = intval($ID);     


            if (!($ID > 0)) {
                return false;
            }     

            $active = array("Y","N"); //
            if (!empty($arFields["ACTIVE"]) && in_array($arFields["ACTIVE"],$active)) {
                $rsFields["ACTIVE"] = "'".$arFields["ACTIVE"]."'";  

                //check active/unactive
                $transact = GKSupportTransactions::GetList($by="ID",$sort="ASC",array("ID"=>$ID));
                $arTransact = $transact->Fetch();
                if ($arTransact["ACTIVE"] != $arFields["ACTIVE"]) {
                    switch ($arFields["ACTIVE"]) {
                        case "Y":                               
                            GKSupportUsers::UpdateClientBalance($arTransact["CLIENT_ID"],floatval($arTransact["SUMM"]),$arTransact["TYPE"]);  
                            break;    
                        case "N":    
                            if ($arTransact["TYPE"] == "M") {$arTransact["TYPE"] = "P";}
                            else if ($arTransact["TYPE"] == "P") {$arTransact["TYPE"] = "M";}
                                GKSupportUsers::UpdateClientBalance($arTransact["CLIENT_ID"],floatval($arTransact["SUMM"]),$arTransact["TYPE"]);
                            break;
                    }
                }


            }  
            else {
                return false; 
            }  

            if (!empty($arFields["COMMENT"])) {
                $rsFields["COMMENT"] = "'".$arFields["COMMENT"]."'";
            }       



            $where = "WHERE `ID`=".$ID;       

            $res = $DB->Update("webgk_support_transactions", $rsFields, $where);

            return $res;
        } 

        function Delete($ID) {
            global $DB;
            $query = "DELETE FROM `webgk_support_transactions` where ID=".$ID;
            $res = $DB->Query($query);
            return $res;
        }

    }

?>