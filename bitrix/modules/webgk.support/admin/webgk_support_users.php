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

    //get group list
    $arGroups = array();
    $group = GKSupportUserGroups::GetList($by="ID",$sort="ASC",array());
    while ($arGroup = $group->Fetch()) {
        $arGroups[$arGroup["ID"]] = $arGroup["NAME"];
    } 


    $sTableID = "webgk_support_users"; // table ID
    $oSort = new CAdminSorting($sTableID, "ID", "asc"); // sort object
    $lAdmin = new CAdminList($sTableID, $oSort); // list object
    $lAdmin->bMultipart = true;


    // filter fields
    $FilterArr = Array(
        "find_user_id",
        "find_project_name",
        "find_balance",
        "find_active",
        "find_group_id"
    );

    // init filter
    $lAdmin->InitFilter($FilterArr);

    $arFilter = Array(
        "USER_ID"=> $find_user_id,
        "PROJECT_NAME"=> $find_project_name,
        "BALANCE"=> $find_balance,
        "ACTIVE"=> $find_active,
        "GROUP_ID"=> $find_group_id
    );    

    if(!empty($find_balance_start))
        $arFilter[">=BALANCE"] = $find_balance_start;
    if(!empty($find_balance_end))
        $arFilter["<=BALANCE"] = $find_balance_end;    

    //set sorting field and direction
    $by="ID";
    $order="ASC";

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
            $cData = new GKSupportUsers;
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

                // activate/deactivate
                case "activate":
                case "deactivate":
                    $cData = new GKSupportUsers;
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

    //get user list
    $cData = new GKSupportUsers;
    $rsData = $cData->GetList($by,$order, $arFilter);  
    $rsData = new CAdminResult($rsData, $sTableID); 
    //set pagenavigation   
    $rsData->NavStart();

    $lAdmin->NavText($rsData->GetNavPrint(GetMessage("WEBGK_GK_SUPPORT_USERS_NAV")));    


    $arHeaders = array(  
        array(  
            "id"    =>"ID",
            "content"  => "ID",
            "sort"     =>"ID",
            "default"  =>true,
        ),
        array(  
            "id"    =>"ACTIVE",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_USERS_ACTIVE"),
            "sort"     =>"ACTIVE",
            "default"  =>true,
        ),
        array(  
            "id"    =>"PROJECT_NAME",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_USERS_PROJECT_NAME"),
            "sort"     =>"PROJECT_NAME",
            "default"  =>true,
        ),      
        array(  
            "id"    =>"BALANCE",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_USERS_BALANCE"),
            "sort"     =>"BALANCE",
            "default"  =>true,
        ),
        array(  
            "id"    =>"USER_ID",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_USERS_USER_ID"),
            "sort"     =>"USER_ID",
            "default"  =>true,
        ),
        array(  
            "id"    =>"GROUP_ID",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_USERS_GROUP_ID"),
            "sort"     =>"GROUP_ID",
            "default"  =>true,
        ),

    );

    //init headers
    $lAdmin->AddHeaders($arHeaders);     



    while($arRes = $rsData->NavNext(true, "f_")) {

        // add row to result                         
        $row = & $lAdmin->AddRow($f_ID, $arRes); 

        // set view for field "USER_ID"
        $row->AddViewField("USER_ID", '<a href="user_edit.php?ID='.$f_USER_ID.'&lang='.LANG.'" target="_blank">'.$f_USER_ID.'</a>');
        $row->AddViewField("ID", '<a href="webgk_support_users_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_ID.'</a>');
        $row->AddViewField("PROJECT_NAME", '<a href="webgk_support_users_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_PROJECT_NAME.'</a>');

        if ($f_GROUP_ID > 0) {
            $row->AddViewField("GROUP_ID", '['.$f_GROUP_ID.'] <a href="webgk_support_user_groups_edit.php?ID='.$f_GROUP_ID.'&lang='.LANG.'" target="_blank">'.$arGroups[$f_GROUP_ID].'</a>');
        } 
        else {
            $row->AddViewField("GROUP_ID", '');    
        }

        $row->AddCheckField("ACTIVE"); 

        // create context menu
        $arActions = Array();

        // element edit
        $arActions[] = array(
            "ICON"=>"edit",
            "DEFAULT"=>true,
            "TEXT"=>GetMessage("EDIT_USER"),
            "ACTION"=>$lAdmin->ActionRedirect("webgk_support_users_edit.php?ID=".$f_ID)
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


    $lAdmin->CheckListMode();


?>
<?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); // 
    $APPLICATION->SetTitle(GetMessage("WEBGK_GK_SUPPORT_USERS_TITLE"));
?>
<?


    // create filter
    $oFilter = new CAdminFilter(
        $sTableID."_filter",
        array(
            GetMessage("WEBGK_GK_SUPPORT_USERS_USER_ID"),
            GetMessage("WEBGK_GK_SUPPORT_USERS_PROJECT_NAME"),
            GetMessage("WEBGK_GK_SUPPORT_USERS_BALANCE"),
            GetMessage("WEBGK_GK_SUPPORT_USERS_ACTIVE"),
            GetMessage("WEBGK_GK_SUPPORT_USERS_GROUP_ID"),
        )
    );
?>
<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
    <?$oFilter->Begin();?>
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_USERS_USER_ID")?>:</td>
        <td>
            <input type="text" name="find_user_id" value="<?=$find_user_id?>" size="5"/>     
            <input type="button" value="..." onClick="jsUtils.OpenWindow('/bitrix/admin/user_search.php?lang=<?echo LANGUAGE_ID?>&FN=find_form&FC=find_user_id', 600, 500);">
        </td>
    </tr>
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_USERS_PROJECT_NAME").":"?></td>
        <td><input type="text" name="find_project_name" size="47" value="<?echo htmlspecialchars($find_project_name)?>"></td>
    </tr>
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_USERS_BALANCE").":"?></td>
        <td>

            <input type="text" name="find_balance_start" size="10" value="<?echo htmlspecialcharsex($find_balance_start)?>">
            ...
            <input type="text" name="find_balance_end" size="10" value="<?echo htmlspecialcharsex($find_balance_end)?>">

        </td>
    </tr>
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_USERS_ACTIVE")?>:</td>
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
    </tr>    
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_USERS_GROUP_ID").":"?></td>
        <td>
            <?$groups = GKSupportUserGroups::GetList($by="ID",$sort="ASC",array());?>
            <select name="find_group_id">                                                                         
                <option value=""><?=GetMessage("POST_ALL")?></option>
                <?while($arGroup = $groups->Fetch()){?>
                    <option value="<?=$arGroup["ID"]?>" <?if ($arGroup["ID"] == $find_group_id){?> selected="selected"<?}?>><?=$arGroup["NAME"]?></option>
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