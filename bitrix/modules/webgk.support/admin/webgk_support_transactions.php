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


    //get clients list
    $arClients = array();
    $client = GKSupportUsers::GetList($by="ID",$sort="ASC",array());
    while ($arClient = $client->Fetch()) {
        $arClients[$arClient["ID"]] = $arClient["PROJECT_NAME"];
    }

    //get user list
    $arUsers = array();
    $user = CUser::GetList($by="ID",$sort="ASC",array());
    while ($arUser = $user->Fetch()) {
        $arUsers[$arUser["ID"]] = $arUser["LOGIN"];
    }

    $type = array("P"=>GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_PLUS"),"M"=>GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_MINUS"));


    $sTableID = "webgk_support_transactions"; // table ID
    $oSort = new CAdminSorting($sTableID, "ID", "DESC"); // sort object
    $lAdmin = new CAdminList($sTableID, $oSort); // list object
    $lAdmin->bMultipart = true;


    // filter fields
    $FilterArr = Array(
        "find_id",   
        "find_active",    
        "find_user_id",
        "find_client_id",
        "find_date_from",
        "find_date_to",
        "find_summ",
        "find_type",
        "find_ticket_id",
        "find_spent_time_id",
    );

    // init filter
    $lAdmin->InitFilter($FilterArr);

    $arFilter = Array(
        "ID"=> $find_id,   
        "ACTIVE"=> $find_active,
        "USER_ID" => $find_user_id,   
        "CLIENT_ID"=> $find_client_id,
        "DATE" => $find_date,
        "TYPE" => $find_type,
        "TICKET_ID" => $find_ticket_id,   
        "SPENT_TIME_ID" => $find_spent_time_id,        
    );  

    if(!empty($find_date_from)){
        $arrDate = ParseDateTime($find_date_from, "DD.MM.YYYY");
        $arFilter[">=DATE"] = $arrDate["YYYY"].".".$arrDate["MM"].".".$arrDate["DD"]." 00:00:00";  
    }
    if(!empty($find_date_to)) {
        $arrDate = ParseDateTime($find_date_to, "DD.MM.YYYY");
        $arFilter["<=DATE"] = $arrDate["YYYY"].".".$arrDate["MM"].".".$arrDate["DD"]." 23:59:59";
    }

    if(!empty($find_summ_from))
        $arFilter[">=SUMM"] = $find_summ_from;
    if(!empty($find_summ_to))
        $arFilter["<=SUMM"] = $find_summ_to;



    //set sorting field and direction
    $by="ID";
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
            $cData = new GKSupportTransactions;
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
               
                case "activate":
                case "deactivate":
                    $cData = new GKSupportTransactions;
                    $arFields = array();
                    $arFields = $cData->GetList($by,$order,array("ID"=>$ID))->Fetch();
                    if(is_array($arFields) && $arFields["ID"] > 0)
                    {
                        $rsFields["ACTIVE"]=($_REQUEST['action']=="activate"?"Y":"N");            
                        $cData->Update($arFields["ID"], $rsFields);
                    }   
                    break;
            }

        }
    }           



    //get discounts list
    $cData = new GKSupportTransactions;
    $rsData = $cData->GetList($by,$order, $arFilter);  
    $rsData = new CAdminResult($rsData, $sTableID); 
    //set pagenavigation   
    $rsData->NavStart();

    $lAdmin->NavText($rsData->GetNavPrint(GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_NAV")));    


    $arHeaders = array(  
        array(  
            "id"    =>"ID",
            "content"  => "ID",
            "sort"     =>"ID",
            "default"  =>true,
        ), 
        array(  
            "id"    =>"ACTIVE",
            "content"  => GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_ACTIVE"),
            "sort"     =>"ACTIVE",
            "default"  =>true,
        ),          
        array(  
            "id"    =>"USER_ID",
            "content"  => GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_USER_ID"),
            "sort"     =>"USER_ID",
            "default"  =>true,
        ),
        array(  
            "id"    =>"CLIENT_ID",
            "content"  => GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_CLIENT_ID"),
            "sort"     =>"CLIENT_ID",
            "default"  =>true,
        ), 
        array(  
            "id"    =>"DATE",
            "content"  => GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_DATE"),
            "sort"     =>"DATE",
            "default"  =>true,
        ), 
        array(  
            "id"    =>"SUMM",
            "content"  => GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_SUMM"),
            "sort"     =>"SUMM",
            "default"  =>true,
        ), 
        array(  
            "id"    =>"TYPE",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_TYPE"),
            "sort"     =>"TYPE",
            "default"  =>true,
        ), 
        array(  
            "id"    =>"COMMENT",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_COMMENT"),
            "sort"     =>"COMMENT",
            "default"  =>true,
        ), 
        array(  
            "id"    =>"TICKET_ID",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_TICKET_ID"),
            "sort"     =>"TICKET_ID",
            "default"  =>true,
        ),
        array(  
            "id"    =>"SPENT_TIME_ID",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_SPENT_TIME_ID"),
            "sort"     =>"SPENT_TIME_ID",
            "default"  =>true,
        ),            


    );

    //init headers
    $lAdmin->AddHeaders($arHeaders);     



    while($arRes = $rsData->NavNext(true, "f_")) {

        // add row to result                         
        $row = & $lAdmin->AddRow($f_ID, $arRes); 

        $row->AddViewField("ID", '<a href="webgk_support_transactions_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_ID.'</a>');
        $row->AddViewField("TYPE", $type[$f_TYPE]);
        $row->AddViewField("USER_ID", '['.$f_USER_ID.'] <a href="user_edit.php?ID='.$f_USER_ID.'&lang='.LANG.'" target="_blank">'.$arUsers[$f_USER_ID].'</a>');
        $row->AddViewField("CLIENT_ID", '['.$f_CLIENT_ID.'] <a href="webgk_support_users_edit.php?ID='.$f_CLIENT_ID.'&lang='.LANG.'" target="_blank">'.$arClients[$f_CLIENT_ID].'</a>');

        if ($f_TICKET_ID > 0) {
            $row->AddViewField("TICKET_ID", '<a href="ticket_edit.php?ID='.$f_TICKET_ID.'&lang='.LANG.'" target="_blank">'.$f_TICKET_ID.'</a>');
        } else {
            $row->AddViewField("TICKET_ID", '');    
        }

        if ($f_SPENT_TIME_ID > 0) {
            $row->AddViewField("SPENT_TIME_ID", '<a href="webgk_support_spent_time_edit.php?ID='.$f_SPENT_TIME_ID.'&lang='.LANG.'" target="_blank">'.$f_SPENT_TIME_ID.'</a>');
        } else {
            $row->AddViewField("SPENT_TIME_ID", '');    
        }

        $arrDate = ParseDateTime($f_DATE, "YYYY.MM.DD HH:MI:SS");
        $row->AddViewField("DATE",$arrDate["DD"].".".$arrDate["MM"].".".$arrDate["YYYY"]." ".$arrDate["HH"].":".$arrDate["MI"].":".$arrDate["SS"]);

        $row->AddCheckField("ACTIVE");

        // create context menu
        $arActions = Array();

        // element edit
        $arActions[] = array(
            "ICON"=>"edit",
            "DEFAULT"=>true,
            "TEXT"=>GetMessage("EDIT_TRANSACTION"),
            "ACTION"=>$lAdmin->ActionRedirect("webgk_support_transactions_edit.php?ID=".$f_ID)
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
        "activate"=>GetMessage("MAIN_ADMIN_LIST_ACTIVATE"), // activate selected elements
        "deactivate"=>GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"), // deactivate selected elements
    )); 


    //context menu
    $aContext = array(
        array(
            "TEXT"=>GetMessage("POST_ADD_TITLE"),
            "LINK"=>"webgk_support_transactions_edit.php?lang=".LANG,
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
    $APPLICATION->SetTitle(GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_TITLE"));
?>
<?


    // create filter
    $oFilter = new CAdminFilter(
        $sTableID."_filter",
        array(
            "ID",                          
            GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_ACTIVE"),
            GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_USER_ID"),
            GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_CLIENT_ID"),
            GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_SUMM"),
            GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_DATE"),
            GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_TYPE"),
            GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_TICKET_ID"),

        )
    );
?>
<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
    <?$oFilter->Begin();?>
    <tr>
        <td>ID:</td>
        <td>
            <input type="text" name="find_id" size="5" value="<?echo htmlspecialchars($find_id)?>">
        </td>
    </tr>
    <tr>
    <td><?=GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_ACTIVE")?>:</td>
    <td>
        <?
            $arr = array(
                "reference" => array(
                    GetMessage("POST_YES"),
                    GetMessage("POST_NO"),
                ),
                "reference_id" => array(
                    "Y",
                    "N",
                )
            );
            echo SelectBoxFromArray("find_active", $arr, $find_active, GetMessage("POST_ALL"), "");
        ?>
    </td>
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_USER_ID").":"?></td>
        <td>
            <input type="text" name="find_user_id" value="<?=$find_user_id?>" size="5"/>     
            <input type="button" value="..." onClick="jsUtils.OpenWindow('/bitrix/admin/user_search.php?lang=<?echo LANGUAGE_ID?>&FN=find_form&FC=find_user_id', 600, 500);">
        </td>
    </tr>
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_CLIENT_ID").":"?></td>
        <td>
            <input type="text" name="find_client_id" value="<?=$find_user_id?>" size="5"/>     
            <input type="button" value="..." onClick="jsUtils.OpenWindow('/bitrix/admin/webgk_support_users_search.php?lang=<?echo LANGUAGE_ID?>&FN=find_form&FC=find_client_id', 600, 500);">
        </td>
    </tr>

    <tr>
        <td><?echo GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_DATE")?>:</td>
        <td><?echo CalendarPeriod("find_date_from", htmlspecialcharsex($find_date_from), "find_date_to", htmlspecialcharsex($find_date_to), "find_form", "Y")?></td>
    </tr>

    <tr>  
        <td><?=GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_SUMM").":"?></td>
        <td>
            <input type="text" name="find_summ_from" size="10" value="<?echo htmlspecialcharsex($find_summ_from)?>">
            ...
            <input type="text" name="find_summ_to" size="10" value="<?echo htmlspecialcharsex($find_summ_to)?>">
        </td>
    </tr>       

    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_TYPE")?>:</td>
        <td>
            <?
                $arrType = array(
                    "reference" => array(
                        GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_PLUS"),
                        GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_MINUS"),
                    ),
                    "reference_id" => array(
                        "P",
                        "M",
                    )
                );
                echo SelectBoxFromArray("find_type", $arrType, $find_type, GetMessage("POST_ALL"), "");
            ?>
        </td>
    </tr>   


    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_TRANSACTIONS_TICKET_ID").":"?></td>
        <td>
            <input type="text" name="find_ticket_id" value="<?=$find_ticket_id?>" size="5"/>     
            <?/* <input type="button" value="..." onClick="jsUtils.OpenWindow('/bitrix/admin/ticket_list.php?lang=<?echo LANGUAGE_ID?>&FN=find_form&FC=find_ticket_id', 600, 500);">*/?>
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