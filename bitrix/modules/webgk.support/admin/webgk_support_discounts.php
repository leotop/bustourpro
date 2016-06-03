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

    //get services list
    $arServices = array();
    $service = GKSupportServices::GetList($by="ID",$sort="ASC",array());
    while ($arService = $service->Fetch()) {
        $arServices[$arService["ID"]] = $arService["NAME"];
    } 

    //get users list
    $arUsers = array();
    $user = GKSupportUsers::GetList($by="ID",$sort="ASC",array());
    while ($arUser = $user->Fetch()) {
        $arUsers[$arUser["ID"]] = $arUser["PROJECT_NAME"];
    }

    $type = array("R"=>GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_RUB"),"P"=>"%");


    $sTableID = "webgk_support_discounts"; // table ID
    $oSort = new CAdminSorting($sTableID, "ID", "asc"); // sort object
    $lAdmin = new CAdminList($sTableID, $oSort); // list object
    $lAdmin->bMultipart = true;


    // filter fields
    $FilterArr = Array(
        "find_id",
        "find_active",
        "find_name",          
        "find_service_id",
        "find_user_id",
        "find_group_id",
        "find_discount",
        "find_type"
    );

    // init filter
    $lAdmin->InitFilter($FilterArr);

    $arFilter = Array(
        "ID"=> $find_id,  
        "ACTIVE"=> $find_active, 
        "NAME"=> $find_name,         
        "DISCOUNT" => $find_disciount,
        "TYPE" => $find_type,
        "SERVICE_ID"=> $find_service_id,
        "USER_ID" => $find_user_id,
        "GROUP_ID" => $find_group_id,           
    );    


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
            $cData = new GKSupportDiscounts;
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
                    $cData = new GKSupportDiscounts;
                    $cData->Delete($ID);
                    $DB->Commit();
                    break;

                    // activate/deactivate
                case "activate":
                case "deactivate":
                    $cData = new GKSupportDiscounts;
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
    $cData = new GKSupportDiscounts;
    $rsData = $cData->GetList($by,$order, $arFilter);  
    $rsData = new CAdminResult($rsData, $sTableID); 
    //set pagenavigation   
    $rsData->NavStart();

    $lAdmin->NavText($rsData->GetNavPrint(GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_NAV")));    


    $arHeaders = array(  
        array(  
            "id"    =>"ID",
            "content"  => "ID",
            "sort"     =>"ID",
            "default"  =>true,
        ),
        array(  
            "id"    =>"ACTIVE",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_ACTIVE"),
            "sort"     =>"ACTIVE",
            "default"  =>true,
        ),
        array(  
            "id"    =>"NAME",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_NAME"),
            "sort"     =>"NAME",
            "default"  =>true,
        ),           
        array(  
            "id"    =>"DISCOUNT",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_DISCOUNT"),
            "sort"     =>"DISCOUNT",
            "default"  =>true,
        ),
        array(  
            "id"    =>"TYPE",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_TYPE"),
            "sort"     =>"TYPE",
            "default"  =>true,
        ),
        array(  
            "id"    =>"USER_ID",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_USER"),
            "sort"     =>"USER_ID",
            "default"  =>true,
        ),
        array(  
            "id"    =>"GROUP_ID",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_GROUP_ID"),
            "sort"     =>"GROUP_ID",
            "default"  =>true,
        ),
        array(  
            "id"    =>"SERVICE_ID",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_SERVICE_ID"),
            "sort"     =>"SERVICE_ID",
            "default"  =>true,
        ),


    );

    //init headers
    $lAdmin->AddHeaders($arHeaders);     



    while($arRes = $rsData->NavNext(true, "f_")) {

        // add row to result                         
        $row = & $lAdmin->AddRow($f_ID, $arRes); 

        $row->AddViewField("ID", '<a href="webgk_support_discounts_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_ID.'</a>');
        $row->AddViewField("NAME", '<a href="webgk_support_discounts_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_NAME.'</a>');
        $row->AddViewField("TYPE", $type[$f_TYPE]);
        $row->AddViewField("USER_ID", '['.$f_USER_ID.'] <a href="webgk_support_users_edit.php?ID='.$f_USER_ID.'&lang='.LANG.'">'.$arUsers[$f_USER_ID].'</a>');
        $row->AddViewField("GROUP_ID", '['.$f_GROUP_ID.'] <a href="webgk_support_user_groups_edit.php?ID='.$f_GROUP_ID.'&lang='.LANG.'">'.$arGroups[$f_GROUP_ID].'</a>');


        if ($f_SERVICE_ID > 0) {
            $serviceName = $arServices[$f_SERVICE_ID];
            $row->AddViewField("SERVICE_ID", '['.$f_SERVICE_ID.'] <a href="webgk_support_services_edit.php?ID='.$f_SERVICE_ID.'&lang='.LANG.'">'.$serviceName.'</a>');
        }
        //0 = all services
        else {
            $serviceName = GetMessage("POST_ALL");
            $row->AddViewField("SERVICE_ID", $serviceName);
        }



        $row->AddCheckField("ACTIVE"); 

        // create context menu
        $arActions = Array();

        // element edit
        $arActions[] = array(
            "ICON"=>"edit",
            "DEFAULT"=>true,
            "TEXT"=>GetMessage("EDIT_DISCOUNT"),
            "ACTION"=>$lAdmin->ActionRedirect("webgk_support_discounts_edit.php?ID=".$f_ID)
        );    
        // element copy 
        $arActions[] = array(
            "ICON" => "copy",
            "TEXT" => GetMessage("COPY_DISCOUNT"),
            "ACTION" => $lAdmin->ActionRedirect("webgk_support_discounts_edit.php?COPY=".$f_ID)
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
        "activate"=>GetMessage("MAIN_ADMIN_LIST_ACTIVATE"), // activate selected elements
        "deactivate"=>GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"), // deactivate selected elements
    )); 

    //context menu
    $aContext = array(
        array(
            "TEXT"=>GetMessage("POST_ADD_TITLE"),
            "LINK"=>"webgk_support_discounts_edit.php?lang=".LANG,
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
    $APPLICATION->SetTitle(GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_TITLE"));
?>
<?


    // create filter
    $oFilter = new CAdminFilter(
        $sTableID."_filter",
        array(
            "ID",
            GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_NAME"),
            GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_ACTIVE"),
            GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_TYPE"),
            GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_DISCOUNT"),
            GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_GROUP_ID"),
            GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_USER_ID"),
            GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_SERVICE_ID"),
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
        <td><?=GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_NAME").":"?></td>
        <td><input type="text" name="find_name" size="47" value="<?echo htmlspecialchars($find_name)?>"></td>
    </tr>
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_DISCOUNT").":"?></td>
        <td><input type="text" name="find_discount" size="47" value="<?echo htmlspecialchars($find_discount)?>"></td>
    </tr> 
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_ACTIVE")?>:</td>
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
        <td><?=GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_TYPE")?>:</td>
        <td>
            <?
                $arrType = array(
                    "reference" => array(
                        GetMessage("POST_RUB"),
                        GetMessage("POST_PERCENT"),
                    ),
                    "reference_id" => array(
                        "R",
                        "P",
                    )
                );
                echo SelectBoxFromArray("find_type", $arrType, $find_type, GetMessage("POST_ALL"), "");
            ?>
        </td>
    </tr>   

    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_USER_ID").":"?></td>
        <td>
            <input type="text" name="find_user_id"  value="<?echo htmlspecialchars($find_user_id)?>" size="5" />     
            <input type="button" value="..." onClick="jsUtils.OpenWindow('/bitrix/admin/webgk_support_users_search.php?lang=<?echo LANGUAGE_ID?>&amp;n=find_user_id&amp;m=n&FN=find_form&FC=find_user_id', 600, 500);">
        </td>
    </tr>

    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_GROUP_ID").":"?></td>
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

    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_SERVICE_ID").":"?></td>
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