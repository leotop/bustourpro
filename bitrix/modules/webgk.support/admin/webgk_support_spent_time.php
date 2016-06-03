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

    //get services list
    $arServices = array();
    $service = GKSupportServices::GetList($by="ID",$sort="ASC",array());
    while ($arService = $service->Fetch()) {
        $arServices[$arService["ID"]] = $arService["NAME"];
    } 

    $payed = array("Y"=>GetMessage("YES"),"N"=>GetMessage("NO"));


    $sTableID = "webgk_support_spent_time"; // table ID
    $oSort = new CAdminSorting($sTableID, "ID", "DESC"); // sort object
    $lAdmin = new CAdminList($sTableID, $oSort); // list object
    $lAdmin->bMultipart = true;


    // filter fields
    $FilterArr = Array(
        "find_id",       
        "find_user_id",
        "find_client_id",
        "find_date_from",
        "find_date_to",
        "find_is_payed",
        "find_ticket_id",
        "find_service_id"
    );

    // init filter
    $lAdmin->InitFilter($FilterArr);

    $arFilter = Array(
        "ID"=> $find_id,   
        "USER_ID" => $find_user_id,   
        "CLIENT_ID"=> $find_client_id,
        "DATE" => $find_date,
        "IS_PAYED" => $find_is_payed,
        "TICKET_ID" => $find_ticket_id,  
        "SERVICE_ID" => $find_service_id
    );  

    if(!empty($find_date_from)){
        $arrDate = ParseDateTime($find_date_from, "DD.MM.YYYY");
        $arFilter[">=DATE"] = $arrDate["YYYY"].".".$arrDate["MM"].".".$arrDate["DD"]." 00:00:00";  
    }
    if(!empty($find_date_to)) {
        $arrDate = ParseDateTime($find_date_to, "DD.MM.YYYY");
        $arFilter["<=DATE"] = $arrDate["YYYY"].".".$arrDate["MM"].".".$arrDate["DD"]." 23:59:59";
    }

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
            $cData = new GKSupportSpentTime;
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
                    $cData = new GKSupportSpentTime;
                    $cData->Delete($ID);
                    $DB->Commit();
                    break;                 
            }

        }
    }             



    //get discounts list
    $cData = new GKSupportSpentTime;
    $rsData = $cData->GetList($by,$order, $arFilter);  
    $rsData = new CAdminResult($rsData, $sTableID); 
    //set pagenavigation   
    $rsData->NavStart();

    $lAdmin->NavText($rsData->GetNavPrint(GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_NAV")));    


    $arHeaders = array(  
        array(  
            "id"    =>"ID",
            "content"  => "ID",
            "sort"     =>"ID",
            "default"  =>true,
        ),           
        array(  
            "id"    =>"USER_ID",
            "content"  => GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_USER_ID"),
            "sort"     =>"USER_ID",
            "default"  =>true,
        ),
        array(  
            "id"    =>"CLIENT_ID",
            "content"  => GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_CLIENT_ID"),
            "sort"     =>"CLIENT_ID",
            "default"  =>true,
        ), 
        array(  
            "id"    =>"DATE",
            "content"  => GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_DATE"),
            "sort"     =>"DATE",
            "default"  =>true,
        ), 
        array(  
            "id"    =>"HOURS",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_HOURS"),
            "sort"     =>"HOURS",
            "default"  =>true,
        ),
        array(  
            "id"    =>"MINUTES",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_MINUTES"),
            "sort"     =>"MINUTES",
            "default"  =>true,
        ),        
        array(  
            "id"    =>"IS_PAYED",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_IS_PAYED"),
            "sort"     =>"IS_PAYED",
            "default"  =>true,
        ), 
        array(  
            "id"    =>"COMMENT",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_COMMENT"),
            "sort"     =>"COMMENT",
            "default"  =>true,
        ), 
        array(  
            "id"    =>"TICKET_ID",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_TICKET_ID"),
            "sort"     =>"TICKET_ID",
            "default"  =>true,
        ), 
        array(  
            "id"    =>"SERVICE_ID",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_SERVICE_ID"),
            "sort"     =>"SERVICE_ID",
            "default"  =>true,
        ),       
    );

    //init headers
    $lAdmin->AddHeaders($arHeaders);     



    while($arRes = $rsData->NavNext(true, "f_")) {

        // add row to result                         
        $row = & $lAdmin->AddRow($f_ID, $arRes); 

        $row->AddViewField("ID", '<a href="webgk_support_spent_time_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_ID.'</a>');
        $row->AddViewField("IS_PAYED", $payed[$f_IS_PAYED]);
        $row->AddViewField("USER_ID", '['.$f_USER_ID.'] <a href="user_edit.php?ID='.$f_USER_ID.'&lang='.LANG.'" target="_blank">'.$arUsers[$f_USER_ID].'</a>');
        $row->AddViewField("CLIENT_ID", '['.$f_CLIENT_ID.'] <a href="webgk_support_users_edit.php?ID='.$f_CLIENT_ID.'&lang='.LANG.'" target="_blank">'.$arClients[$f_CLIENT_ID].'</a>');

        if ($f_TICKET_ID > 0) {
            $row->AddViewField("TICKET_ID", '<a href="ticket_edit.php?ID='.$f_TICKET_ID.'&lang='.LANG.'" target="_blank">'.$f_TICKET_ID.'</a>');
        } else {
            $row->AddViewField("TICKET_ID", '');    
        }

        $row->AddViewField("SERVICE_ID", '['.$f_SERVICE_ID.'] <a href="webgk_support_services_edit.php?ID='.$f_SERVICE_ID.'&lang='.LANG.'">'.$arServices[$f_SERVICE_ID].'</a>');

        $arrDate = ParseDateTime($f_DATE, "YYYY.MM.DD HH:MI:SS");
        $row->AddViewField("DATE",$arrDate["DD"].".".$arrDate["MM"].".".$arrDate["YYYY"]." ".$arrDate["HH"].":".$arrDate["MI"].":".$arrDate["SS"]);


        // create context menu
        $arActions = Array();

        // element edit
        $arActions[] = array(
            "ICON"=>"edit",
            "DEFAULT"=>true,
            "TEXT"=>GetMessage("EDIT_SPENT_TIME"),
            "ACTION"=>$lAdmin->ActionRedirect("webgk_support_spent_time_edit.php?ID=".$f_ID)
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
        "delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"), // delete checked items      
    )); 


    //context menu
    $aContext = array(
        array(
            "TEXT"=>GetMessage("POST_ADD_TITLE"),
            "LINK"=>"webgk_support_spent_time_edit.php?lang=".LANG,
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
    $APPLICATION->SetTitle(GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_TITLE"));
?>
<?       

    // create filter
    $oFilter = new CAdminFilter(
        $sTableID."_filter",
        array(
            "ID",
            GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_USER_ID"),
            GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_CLIENT_ID"),
            GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_DATE"),
            GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_YS_PAYED"),
            GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_TICKET_ID"),
            GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_SERVICE_ID"),

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
        <td><?=GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_USER_ID").":"?></td>
        <td>
            <input type="text" name="find_user_id" value="<?=$find_user_id?>" size="5"/>     
            <input type="button" value="..." onClick="jsUtils.OpenWindow('/bitrix/admin/user_search.php?lang=<?echo LANGUAGE_ID?>&FN=find_form&FC=find_user_id', 600, 500);">
        </td>
    </tr>
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_CLIENT_ID").":"?></td>
        <td>
            <input type="text" name="find_client_id" value="<?=$find_user_id?>" size="5"/>     
            <input type="button" value="..." onClick="jsUtils.OpenWindow('/bitrix/admin/webgk_support_users_search.php?lang=<?echo LANGUAGE_ID?>&FN=find_form&FC=find_client_id', 600, 500);">
        </td>
    </tr>

    <tr>
        <td><?echo GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_DATE")?>:</td>
        <td><?echo CalendarPeriod("find_date_from", htmlspecialcharsex($find_date_from), "find_date_to", htmlspecialcharsex($find_date_to), "find_form", "Y")?></td>
    </tr>


    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_IS_PAYED")?>:</td>
        <td>
            <?
                $arrType = array(
                    "reference" => array(
                        GetMessage("YES"),
                        GetMessage("NO"),
                    ),
                    "reference_id" => array(
                        "Y",
                        "N",
                    )
                );
                echo SelectBoxFromArray("find_is_payed", $arrType, $find_is_payed, GetMessage("POST_ALL"), "");
            ?>
        </td>
    </tr>    

    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_TICKET_ID").":"?></td>
        <td>
            <input type="text" name="find_ticket_id" size="5" value="<?echo htmlspecialchars($find_ticket_id)?>">
        </td>
    </tr>  

    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_SERVICE_ID").":"?></td>
        <td>
            <?$services = GKSupportServices::GetList($by="ID",$sort="ASC",array());?>
            <select name="find_group_id">                                                                         
                <option value=""><?=GetMessage("POST_ALL")?></option>
                <?while($arService = $services->Fetch()){?>
                    <option value="<?=$arService["ID"]?>" <?if ($arService["ID"] == $find_service_id){?> selected="selected"<?}?>><?=$arService["NAME"]?></option>
                    <?}?>

            </select>
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