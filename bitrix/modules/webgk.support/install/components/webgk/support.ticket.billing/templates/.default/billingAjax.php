<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>   
<?CModule::IncludeModule("webgk.support");?>   
<? 
    IncludeTemplateLangFile(__FILE__);

    global $USER;
    $uID = $USER->GetId();
    if (in_array(GKSupport::GetSupportEmployerGroupID(),CUser::GetUserGroup($uID)) || $USER->IsAdmin()) {


        $ticketID = intval($_POST["ticket_id"]);
        $ticket = CTicket::GetList($by="ID",$order="ASC",array("ID"=>$ticketID),$filtered,"N");
        if ($ticket->SelectedRowsCount() > 0) {
            $arTicket = $ticket->Fetch();
        }   

        $itemID = intval($_POST["item_id"]);           

        switch($_POST['action']){
            case 'add':
                $arFields = array(
                    "TICKET_ID" => $ticketID,
                    "CLIENT_ID" => $arTicket['OWNER_USER_ID'],
                    "HOURS" => $_POST['hours'],
                    "MINUTES" => $_POST['minutes'],
                    "COMMENT" => $_POST['comment'],
                    "SERVICE_ID" => $_POST['service_id']
                );
                addNewBillingTimeRecord($arFields);
                break;
            case 'delete':
                $item = GKSupportSpentTime::GetList($by="ID",$sort="ASC",array("ID"=>$itemID));
                $arItem = $item->Fetch();
                if ($arItem["TICKET_ID"] == $ticketID){
                    deleteBillingTimeRecord($itemID);
                }
                break;
        }      

    }

    /***
    * add spen time record
    * 
    * @param array $arFields (TICKET_ID, CLIENT_ID (from b_user), HOURS, MINUTES, COMMENT, SERVICE_ID)
    */
    function addNewBillingTimeRecord($arFields){
        global $USER;
        global $DB;
        $requires = array("TICKET_ID", "CLIENT_ID", "HOURS", "MINUTES", "COMMENT", "SERVICE_ID");

        foreach ($requires as $field) {
            if ($arFields[$field] == "") {
                break;
                return false; 
            }
        }            

        $re = CTicket::GetByID($arFields['TICKET_ID']);
        if($arF = $re->GetNext()){
            $ticketTitle = $arF['TITLE'];
        }

        $userID = $USER->GetId();                        

        $timeFields = array(
            "USER_ID" => $userID,
            "CLIENT_ID" => GKSupportUsers::GetClientId($arFields["CLIENT_ID"]), 
            "IS_PAYED" => "N", 
            "COMMENT" => iconv("UTF-8", "WINDOWS-1251",$arFields["COMMENT"]), 
            "TICKET_ID" => $arFields["TICKET_ID"], 
            "SERVICE_ID" => $arFields["SERVICE_ID"],
            "HOURS" => $arFields['HOURS'],
            "MINUTES" => $arFields['MINUTES']
        );
        $ID = GKSupportSpentTime::Add($timeFields);  

        $services = GKSupportServices::GetList($by="ID",$sort="ASC",array("ID"=>$arFields["SERVICE_ID"]));
        $arService = $services->Fetch();

        $rsUser = CUser::GetByID($userID);
        $arUser = $rsUser->Fetch();
        echo "<tr>
        <td>".date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), mktime())."</td>
        <td>".$arUser['NAME']." ".$arUser['LAST_NAME']."</td>
        <td class='tableHours'>
        <p>".$arFields['HOURS'].":".$arFields['MINUTES']."</p>
        </td>
        <td>".$arService["NAME"]."</td>
        <td>
        <p>".iconv("UTF-8", "WINDOWS-1251",$arFields["COMMENT"])."</p>
        </td>             
        <td>
        <input type='hidden' name='recordID' value='".$ID."'>
        <input class='deleteBillingPosition' data-action='delete' value='X' type='submit' rel='".$ID."'/>
        </td>
        </tr>";


    }

    function deleteBillingTimeRecord($ID){
        GKSupportSpentTime::Delete($ID);
    }
?>
