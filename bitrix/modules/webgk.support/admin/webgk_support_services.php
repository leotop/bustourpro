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

   
    $sTableID = "webgk_support_servicer"; // table ID
    $oSort = new CAdminSorting($sTableID, "ID", "asc"); // sort object
    $lAdmin = new CAdminList($sTableID, $oSort); // list object
    $lAdmin->bMultipart = true;


    // filter fields
    $FilterArr = Array(
        "find_id",
        "find_name",
        "find_active",
        "find_hour_price"
    );

    // init filter
    $lAdmin->InitFilter($FilterArr);

    $arFilter = Array(
        "ID"=> $find_id,
        "NAME"=> $find_name,
        "ACTIVE"=> $find_active,
        "HOUR_PRICE"=> $find_hour_price
    );    

    if(!empty($find_hour_price_start))
        $arFilter[">=HOUR_PRICE"] = $find_hour_price_start;
    if(!empty($find_hour_price_end))
        $arFilter["<=HOUR_PRICE"] = $find_hour_price_end;    

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
            $cData = new GKSupportServices;
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
                    $cData = new GKSupportServices;
                    $cData->Delete($ID);
                    $DB->Commit();
                    break;
                
                // activate/deactivate
                case "activate":
                case "deactivate":
                    $cData = new GKSupportServices;
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

    //get services list
    $cData = new GKSupportServices;
    $rsData = $cData->GetList($by,$order, $arFilter);  
    $rsData = new CAdminResult($rsData, $sTableID); 
    //set pagenavigation   
    $rsData->NavStart();

    $lAdmin->NavText($rsData->GetNavPrint(GetMessage("WEBGK_GK_SUPPORT_SERVICES_NAV")));    


    $arHeaders = array(  
        array(  
            "id"    =>"ID",
            "content"  => "ID",
            "sort"     =>"ID",
            "default"  =>true,
        ),
        array(  
            "id"    =>"ACTIVE",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_SERVICES_ACTIVE"),
            "sort"     =>"ACTIVE",
            "default"  =>true,
        ),
        array(  
            "id"    =>"NAME",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_SERVICES_NAME"),
            "sort"     =>"NAME",
            "default"  =>true,
        ),           
        array(  
            "id"    =>"HOUR_PRICE",
            "content"  =>GetMessage("WEBGK_GK_SUPPORT_SERVICES_HOUR_PRICE"),
            "sort"     =>"HOUR_PRICE",
            "default"  =>true,
        ),
  
    );

    //init headers
    $lAdmin->AddHeaders($arHeaders);     



    while($arRes = $rsData->NavNext(true, "f_")) {

        // add row to result                         
        $row = & $lAdmin->AddRow($f_ID, $arRes); 

        $row->AddViewField("ID", '<a href="webgk_support_services_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_ID.'</a>');
        $row->AddViewField("NAME", '<a href="webgk_support_services_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_NAME.'</a>');


        $row->AddCheckField("ACTIVE"); 

        // create context menu
        $arActions = Array();

        // element edit
        $arActions[] = array(
            "ICON"=>"edit",
            "DEFAULT"=>true,
            "TEXT"=>GetMessage("EDIT_SERVICE"),
            "ACTION"=>$lAdmin->ActionRedirect("webgk_support_services_edit.php?ID=".$f_ID)
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
            "LINK"=>"webgk_support_services_edit.php?lang=".LANG,
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
    $APPLICATION->SetTitle(GetMessage("WEBGK_GK_SUPPORT_SERVICES_TITLE"));
?>
<?


    // create filter
    $oFilter = new CAdminFilter(
        $sTableID."_filter",
        array(
            "ID",
            GetMessage("WEBGK_GK_SUPPORT_SERVICES_NAME"),
            GetMessage("WEBGK_GK_SUPPORT_SERVICES_ACTIVE"),
            GetMessage("WEBGK_GK_SUPPORT_SERVICES_HOUR_PRICE"),
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
        <td><?=GetMessage("WEBGK_GK_SUPPORT_SERVICES_NAME").":"?></td>
        <td><input type="text" name="find_name" size="47" value="<?echo htmlspecialchars($find_name)?>"></td>
    </tr>
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_SERVICES_HOUR_PRICE").":"?></td>
        <td>

            <input type="text" name="find_hour_price_start" size="10" value="<?echo htmlspecialcharsex($find_hour_price_start)?>">
            ...
            <input type="text" name="find_hour_price_end" size="10" value="<?echo htmlspecialcharsex($find_hour_price_end)?>">

        </td>
    </tr>
    <tr>
        <td><?=GetMessage("WEBGK_GK_SUPPORT_SERVICES_ACTIVE")?>:</td>
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
    
    <?    
        $oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
        $oFilter->End();
    ?>
</form>
<?

    $lAdmin->DisplayList();
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>