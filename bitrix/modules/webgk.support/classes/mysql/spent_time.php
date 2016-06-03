<?
    IncludeModuleLangFile(__FILE__);

    Class GKSupportSpentTime extends GKSupport {

        /****
        * @param string $by
        * @param string $sort
        * @param array $arFilter
        * $arFilter: ID, USER_ID (from `b_user`), CLIENT_ID (from `webgk_support_users`), DATE (timestamp), IS_PAYED (Y/N),  COMMENT, TICKET_ID, SERVICE_ID
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


            $query = 'SELECT * FROM `webgk_support_spent_time` '.$where.$order.$limit;
            $res = $DB->query($query);   

            return $res; 
        }   


        //$arFields:  USER_ID (from `b_user`), CLIENT_ID (from `webgk_support_users`), DATE (timestamp), IS_PAYED (Y/N), COMMENT, TICKET_ID, SERVICE_ID, HOURS, MINUTES, USER_ID
        function Add($arFields = array()) {
            global $DB; 
            global $USER;              

            $payed = array("Y","N"); //Y or N
            if (!empty($arFields["IS_PAYED"]) && in_array($arFields["IS_PAYED"],$payed)) {
                $rsFields["IS_PAYED"] = "'".$arFields["IS_PAYED"]."'";  
            }  
            else {
                $rsFields["IS_PAYED"] = "'N'"; 
            } 

            if (!empty($arFields["USER_ID"])) {
                $rsFields["USER_ID"] = "'".intval($arFields["USER_ID"])."'";
            }                 
            else {
                $rsFields["USER_ID"] = "'".$USER->GetId()."'"; 
            }    

            if (!empty($arFields["CLIENT_ID"])) {
                $rsFields["CLIENT_ID"] = "'".intval($arFields["CLIENT_ID"])."'";
            }  
            else {
                return false;
            } 

            if (!empty($arFields["HOURS"])) {
                $rsFields["HOURS"] = "'".intval($arFields["HOURS"])."'";
            }

            if (!empty($arFields["MINUTES"])) {
                $rsFields["MINUTES"] = "'".intval($arFields["MINUTES"])."'";
            }  

            if (!empty($arFields["SERVICE_ID"])) {
                $rsFields["SERVICE_ID"] = "'".intval($arFields["SERVICE_ID"])."'";
            } 
            else {
                return false;
            }        

            if (!empty($arFields["COMMENT"])) {
                $rsFields["COMMENT"] = "'".$arFields["COMMENT"]."'";
            }  

            if (!empty($arFields["TICKET_ID"])) {
                $rsFields["TICKET_ID"] = "'".intval($arFields["TICKET_ID"])."'";
            }  

            if ($arFields["DATE"]) {
                $rsFields["DATE"] = "'".ConvertDateTime($arFields["DATE"], "YYYY-MM-DD HH:MI:SS")."'";
            }         

            $rsFields["ID"] = "NULL";  



            $ID = $DB->Insert("webgk_support_spent_time", $rsFields);

            if ($ID > 0) {

                //add transaction 
                $hourPrice = GKSupport::GetClientServicePrice(intval($arFields["CLIENT_ID"]),intval($arFields["SERVICE_ID"]));
                $hours = $arFields["HOURS"] + round($arFields["MINUTES"]/60,2);
                if ($hourPrice && $hours) {
                    $summ = round($hourPrice * $hours,2);
                }      
                    
                $message = GetMessage("AUTOMATIC_MINUS");
                if (intval($arFields["SERVICE_ID"]) > 0) {
                    $service = GKSupportServices::GetList($by="ID",$sort="ASC",array("ID"=>intval($arFields["SERVICE_ID"])))->Fetch();
                    $message .= " (".$service["NAME"].")"; 
                }

                $arTransact  = array(
                    "USER_ID"=>$USER->GetId(),
                    "CLIENT_ID"=>intval($arFields["CLIENT_ID"]),
                    "TYPE"=>"M",
                    "SUMM"=>$summ,
                    "COMMENT"=>$message,
                    "TICKET_ID"=>intval($arFields["TICKET_ID"]),
                    "SPENT_TIME_ID"=>$ID
                );                  


                if ($summ) {
                    GKSupportTransactions::Add($arTransact);
                }
            }

            return $ID;
        }


        //$arFields: IS_PAYED (Y/N), COMMENT
        function Update($ID, $arFields = array()) {
            global $DB;

            $ID = intval($ID);

            if (!($ID > 0)) {
                return false;
            }   

            $payed = array("Y","N"); //Y or N
            if (!empty($arFields["IS_PAYED"]) && in_array($arFields["IS_PAYED"],$payed)) {
                $rsFields["IS_PAYED"] = "'".$arFields["IS_PAYED"]."'";  
            }  

            if (!empty($arFields["COMMENT"])) {
                $rsFields["COMMENT"] = "'".$arFields["COMMENT"]."'";
            }                             

            $where = "WHERE `ID`=".$ID;       

            $res = $DB->Update("webgk_support_spent_time", $rsFields, $where);

            return $res;
        } 



        function Delete($ID) {
            global $DB;      

            //deactivate transaction before spent time record deleting
            $transaction = GKSupportTransactions::GetList($by="ID",$sort="ASC",array("SPENT_TIME_ID"=>$ID));
            $arTransaction = $transaction->Fetch();
            if ($arTransaction["ID"] > 0) {
                GKSupportTransactions::Update($arTransaction["ID"],array("ACTIVE"=>"N"));
            }

            $query = "DELETE FROM `webgk_support_spent_time` WHERE ID=".$ID;
            $res = $DB->Query($query);
            return $res;
        }

        /***
        * get full ticket spent time
        * 
        * @param integer $tID - ticket ID
        */

        function GetTicketSpentTime($tID) {
            $res = "0:00";
            $ID = intval($tID);
            $minutes = 0;
            $time = GKSupportSpentTime::GetList($by="ID",$sort="ASC",array("TICKET_ID"=>$ID));
            while ($arTime = $time->Fetch()) {
                $minutes += ($arTime["HOURS"]*60 + $arTime["MINUTES"]);
            }
            if ($minutes > 0) {
                $totalHours = floor($minutes/60);
                $totalMinutes = $minutes%60;
                if (strlen($totalMinutes) == 1) {$totalMinutes = "0".$totalMinutes;}
                $res = $totalHours.":".$totalMinutes;  
            }

            return $res;
        }

    }

?>