<?
    Class GKSupportTicketPayment extends GKSupport { 

        /****
        * @param string $by
        * @param string $sort
        * @param array $arFilter
        * $arFilter: TICKET_ID, IN_PAYMENT
        */

        function GetList($by = "ID", $sort = "asc",$arFilter = array()) {
            if (empty($by)) {
                $by = "TICKET_ID";
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


            $query = 'SELECT * FROM `webgk_support_ticket_payment` '.$where.$order.$limit;
            $res = $DB->query($query);   

            return $res; 
        }   


        /***
        * get ticket payment status
        * 
        * @param mixed $TICKET_ID
        */
        function GetByTicket($TICKET_ID) {
            $res = false;
            $TICKET_ID = intval($TICKET_ID);
            if ($TICKET_ID > 0) {                    
                $info = GKSupportTicketPayment::GetList($by="TICKET_ID",$sort="ASC",array("TICKET_ID"=>$TICKET_ID));
                $arInfo = $info->Fetch();
                if($arInfo["IN_PAYMENT"]) {
                    $res = $arInfo["IN_PAYMENT"];
                }
            }

            return $res;
        }


        /***
        * chenge ticket payment status
        * 
        * @param integer $TICKET_ID
        * @param Y/N $IN_PAYMENT
        */
        function Change($TICKET_ID,$IN_PAYMENT) {

            global $DB;

            $payment = array("Y","N");
            $TICKET_ID = intval($TICKET_ID);

            if ($TICKET_ID > 0 && in_array($IN_PAYMENT,$payment)) {
                $ticket = GKSupportTicketPayment::GetList($by="TICKET_ID",$sort="ASC",array("TICKET_ID"=>$TICKET_ID));
                $arTicket = $ticket->Fetch();
                if ($arTicket["IN_PAYMENT"] != "") {
                    $where = "WHERE `TICKET_ID`=".$TICKET_ID;
                    $rsFields = array();
                    $rsFields["IN_PAYMENT"] = "'".$IN_PAYMENT."'";
                    $res = $DB->Update("webgk_support_ticket_payment", $rsFields, $where); 
                }
                else {                       
                    $rsFields["IN_PAYMENT"] = "'".$IN_PAYMENT."'";      
                    $rsFields["TICKET_ID"] = "'".$TICKET_ID."'";
                    $res = $DB->Insert("webgk_support_ticket_payment", $rsFields); 
                } 

                $arTransFilter = array("TICKET_ID"=>$TICKET_ID);
                $obTransaction = GKSupportTransactions::GetList(($by="id"), ($order="desc"), $arTransFilter);
                while($Transaction = $obTransaction->Fetch()) {
                    $arFields = array("ACTIVE"=>$IN_PAYMENT);
                    //check spent time id. if spent time exists then update transactions
                    $checkSpentTime = GKSupportSpentTime::GetList($by="ID",$sort="ASC",array("ID"=>$Transaction["SPENT_TIME_ID"]))->Fetch();
                    if ($checkSpentTime["ID"] > 0) {
                        GKSupportTransactions::Update($Transaction["ID"], $arFields);   
                    }
                }


                return true;


            }
            else {
                return false;
            }



        }


        function Delete($TICKET_ID) {
            global $DB;
            $query = "DELETE FROM `webgk_support_ticket_payment` WHERE TICKET_ID=".$TICKET_ID;
            $res = $DB->Query($query);
            return $res;
        }  
    }

?>