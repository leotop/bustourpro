<?
    require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');   

    IncludeModuleLangFile(__FILE__);
    CModule::IncludeModule('main');
    CModule::IncludeModule('webgk.support');

    global $USER;
    global $APPLICATION;

    if ( !$USER->IsAdmin() ) {
        $APPLICATION-> Form("");
    }

    IncludeModuleLangFile(__FILE__);


    $sTableID = "webgk_support_files"; // table ID
    $oSort = new CAdminSorting($sTableID, "ID", "asc"); // sort object
    $lAdmin = new CAdminList($sTableID, $oSort); // list object
    $lAdmin->bMultipart = true;


    //get clients list
    $arClients = array();
    $client = GKSupportUsers::GetList($by="ID",$sort="ASC",array());
    while ($arClient = $client->Fetch()) {
        $arClients[$arClient["ID"]] = $arClient["PROJECT_NAME"];
    }


    // filter fields
    $FilterArr = Array(
        "find_id",
        "find_name",
        "find_client_id",
        "find_date_from",
        "find_date_to"
    );

    // init filter
    $lAdmin->InitFilter($FilterArr);

    $arFilter = Array(
        "ID"=> $find_id,
        "NAME"=> $find_name,
        "CLIENT_ID"=>$find_client_id,
        "DATE" => $find_date,
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
            $cData = new GKSupportFiles;
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
                    $cData = new GKSupportFiles;
                    $cData->Delete($ID);
                    $DB->Commit();
                    break;

            }

        }
    }         

    //get services list
    $cData = new GKSupportFiles;
    $rsData = $cData->GetList($by,$order, $arFilter);  
    $rsData = new CAdminResult($rsData, $sTableID); 
    //set pagenavigation   
    $rsData->NavStart();

    $lAdmin->NavText($rsData->GetNavPrint(GetMessage("WEBGK_GK_SUPPORT_FILES_NAV")));    


    $arHeaders = array(  
        array(  
            "id"    =>"ID",
            "content"  => "ID",
            "sort"     =>"ID",
            "default"  =>true,
        ),          
        array(  
            "id"    =>"NAME",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_FILES_NAME"),
            "sort"     =>"NAME",
            "default"  =>true,
        ),    

        array(  
            "id"    =>"DATE",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_FILES_DATE"),
            "sort"     =>"DATE",
            "default"  =>true,
        ), 

        array(  
            "id"    =>"COMMENT",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_FILES_COMMENT"),
            "sort"     =>"COMMENT",
            "default"  =>true,
        ),         
        array(  
            "id"    =>"FILE_ID",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_FILES_FILE_ID"),
            "sort"     =>"FILE_ID",
            "default"  =>true,
        ), 
        array(  
            "id"    =>"CLIENT_ID",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_FILES_CLIENT_ID"),
            "sort"     =>"CLIENT_ID",
            "default"  =>true,
        ),      


    );

    //init headers
    $lAdmin->AddHeaders($arHeaders);     



    while($arRes = $rsData->NavNext(true, "f_")) {

        // add row to result                         
        $row = & $lAdmin->AddRow($f_ID, $arRes); 

        $row->AddViewField("ID", '<a href="webgk_support_files_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_ID.'</a>');
        $row->AddViewField("NAME", '<a href="webgk_support_files_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_NAME.'</a>');
        $row->AddViewField("CLIENT_ID", '['.$f_CLIENT_ID.'] <a href="webgk_support_users_edit.php?ID='.$f_CLIENT_ID.'&lang='.LANG.'" target="_blank">'.$arClients[$f_CLIENT_ID].'</a>');
        $row->AddViewField("FILE_ID", '<a href="'.CFile::GetPath($f_FILE_ID).'" target="_blank">'.$f_FILE_ID.'</a>');



        $row->AddCheckField("ACTIVE"); 

        // create context menu
        $arActions = Array();

        // element edit
        $arActions[] = array(
            "ICON"=>"edit",
            "DEFAULT"=>true,
            "TEXT"=>GetMessage("EDIT_FILE"),
            "ACTION"=>$lAdmin->ActionRedirect("webgk_support_files_edit.php?ID=".$f_ID)
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

    //context menu
    $aContext = array(
        array(
            "TEXT"=>GetMessage("POST_ADD_TITLE"),
            "LINK"=>"webgk_support_files_edit.php?lang=".LANG,
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
    $APPLICATION->SetTitle(GetMessage("WEBGK_GK_SUPPORT_FILES_TITLE"));
?>
<?


    // create filter
    $oFilter = new CAdminFilter(
        $sTableID."_filter",
        array(
            "ID",
            GetMessage("WEBGK_GK_SUPPORT_FILES_NAME"),
            GetMessage("WEBGK_GK_SUPPORT_FILES_DATE"),
            GetMessage("WEBGK_GK_SUPPORT_FILES_CLIENT_ID"),  
        )
    );
?>
<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
    <?$oFilter->Begin();?>
    <tr>
        <td>ID:</td>
        <td>
            <input type="text" name="find_id" size="10" value="<?echo htmlspecialchars($find_id)?>">
        </td>
    </tr>
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_FILES_NAME").":"?></td>
        <td><input type="text" name="find_name" size="47" value="<?echo htmlspecialchars($find_name)?>"></td>
    </tr>
    <tr>
        <td><?echo GetMessage("WEBGK_GK_SUPPORT_FILES_DATE")?>:</td>
        <td><?echo CalendarPeriod("find_date_from", htmlspecialcharsex($find_date_from), "find_date_to", htmlspecialcharsex($find_date_to), "find_form", "Y")?></td>
    </tr>
    
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_FILES_CLIENT_ID").":"?></td>
        <td>
            <input type="text" name="find_client_id" value="<?=$find_user_id?>" size="5"/>     
            <input type="button" value="..." onClick="jsUtils.OpenWindow('/bitrix/admin/webgk_support_users_search.php?lang=<?echo LANGUAGE_ID?>&FN=find_form&FC=find_client_id', 600, 500);">
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