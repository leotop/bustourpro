<? 
    require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');   

    IncludeModuleLangFile(__FILE__);
    CModule::IncludeModule('webgk.support');

    global $USER;
    global $APPLICATION;

    if ( !$USER->IsAdmin() ) {
        $APPLICATION-> Form("");
    }

    IncludeModuleLangFile(__FILE__);


    $sTableID = "webgk_support_ticket_payment"; // table ID
    $oSort = new CAdminSorting($sTableID, "ID", "asc"); // sort object
    $lAdmin = new CAdminList($sTableID, $oSort); // list object
    $lAdmin->bMultipart = true;

    // filter fields
    $FilterArr = Array(
        "find_ticket_id",
        "find_in_payment",
    );


    // init filter
    $lAdmin->InitFilter($FilterArr);

    $arFilter = Array(
        "TICKET_ID"=>$find_ticket_id,          
        "IN_PAYMENT"=>$find_in_payment,
    ); 



    //set sorting field and direction
    $by="TICKET_ID";
    $order="DESC";

    if ($_REQUEST["by"]) {
        $by = $_REQUEST["by"];  
    }
    if ($_REQUEST["order"]) {
        $order = $_REQUEST["order"]; 
    }

    // group actions
    if(($arID = $lAdmin->GroupAction()))
    {
        // if for all
        if($_REQUEST['action_target']=='selected')
        {
            $cData = new GKSupportTicketPayment;
            $rsData = $cData->GetList($by,$order, $arFilter);
            while($arRes = $rsData->Fetch())
                $arID[] = $arRes['ID'];
        }

        // 
        foreach($arID as $ID)
        {
            if(strlen($ID)<=0)
            continue;
            $ID = IntVal($ID);

            // make action for each element
            switch($_REQUEST['action'])
            {   
                //deleting
                case "delete":
                    @set_time_limit(0);
                    $DB->StartTransaction();
                    $cData = new GKSupportTicketPayment;
                    $cData->Delete($ID);
                    $DB->Commit();
                    break;

            }

        }
    }  


    //get group list
    $cData = new GKSupportTicketPayment;
    $rsData = $cData->GetList($by,$order, $arFilter);  
    $rsData = new CAdminResult($rsData, $sTableID); 
    //set pagenavigation   
    $rsData->NavStart();

    $lAdmin->NavText($rsData->GetNavPrint(GetMessage("WEBGK_GK_SUPPORT_TICKET_PAYMENT_NAV"))); 


    $arHeaders = array(        
        array(  
            "id"    =>"TICKET_ID",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_TICKET_PAYMENT_TICKET_ID"),
            "sort"     =>"TICKET_ID",
            "default"  =>true,
        ),
        array(  
            "id"    =>"IN_PAYMENT",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_TICKET_PAYMENT_IN_PAYMENT"),
            "sort"     =>"IN_PAYMENT",
            "default"  =>true,
        ),      

    );



    //init headers
    $lAdmin->AddHeaders($arHeaders);     

    while($arRes = $rsData->NavNext(true, "f_")) {

        // add row to result                         
        $row = & $lAdmin->AddRow($f_ID, $arRes); 

        $row->AddViewField("TICKET_ID", '<a href="webgk_support_ticket_payment_edit.php?TICKET_ID='.$f_TICKET_ID.'&lang='.LANG.'" >'.$f_TICKET_ID.'</a>');

        $row->addCheckField("IN_PAYMENT");
        // create context menu
        $arActions = Array();


        //element delete
        $arActions[] = array(
            "ICON"=>"delete",
            "TEXT"=>GetMessage("DELETE_PAYMENT"),
            "ACTION"=>"if(confirm('".GetMessage('DELETE_PAYMENT_CONFIRM')."')) ".$lAdmin->ActionDoGroup($f_TICKET_ID, "delete")
        );
        
         // element edit
        $arActions[] = array(
            "ICON"=>"edit",
            "DEFAULT"=>true,
            "TEXT"=>GetMessage("EDIT_PAYMENT"),
            "ACTION"=>$lAdmin->ActionRedirect("webgk_support_ticket_payment_edit.php?TICKET_ID=".$f_TICKET_ID)
        );

        // separator
        $arActions[] = array("SEPARATOR"=>true);      


        // deleting last seperator
        if(is_set($arActions[count($arActions)-1], "SEPARATOR"))
            unset($arActions[count($arActions)-1]);

        // init context menu
        $row->AddActions($arActions);

    }   

    $lAdmin->AddFooter(
        array(
            array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()), // element quantity
            array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"), // selected elements counter
        )
    );     
    
    // group actions
    $lAdmin->AddGroupActionTable(Array(
        "delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"), // удалить выбранные элементы      
    )); 


    $aContext = array(
        array(
            "TEXT"=>GetMessage("POST_ADD_TITLE"),
            "LINK"=>"webgk_support_ticket_payment_edit.php?lang=".LANG,
            "TITLE"=>GetMessage("POST_ADD_TITLE"),
            "ICON"=>"btn_new",
        ),
    );

    // create context menu
    $lAdmin->AddAdminContextMenu($aContext);


    $lAdmin->CheckListMode();

?>
<?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); // 
    $APPLICATION->SetTitle(GetMessage("WEBGK_GK_SUPPORT_TICKET_PAYMENT_TITLE"));
?>
<?



    // create filter
    $oFilter = new CAdminFilter(
        $sTableID."_filter",
        array(
            GetMessage("WEBGK_GK_SUPPORT_TICKET_PAYMENT_TICKET_ID"),
            GetMessage("WEBGK_GK_SUPPORT_TICKET_PAYMENT_IN_PAYMENT"),
        )
    );
?>
<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
    <?$oFilter->Begin();?>
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_TICKET_PAYMENT_TICKET_ID").":"?></td>
        <td><input type="text" name="find_ticket_id" size="47" value="<?echo htmlspecialchars($find_ticket_id)?>"></td>
    </tr>  
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_TICKET_PAYMENT_IN_PAYMENT")?>:</td>
        <td>
            <?
                $arr = array(
                    "reference" => array(
                        GetMessage("IN_PAYMENT_YES"),
                        GetMessage("IN_PAYMENT_NO"),
                    ),
                    "reference_id" => array(
                        "Y",
                        "N",
                    )
                );
                echo SelectBoxFromArray("find_in_payment", $arr, $find_active, GetMessage("POST_ALL"), "");
            ?>
        </td>
    </tr>    
    <?    
        $oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
        $oFilter->End();
    ?>
</form>
<?

    $lAdmin->DisplayList();
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>