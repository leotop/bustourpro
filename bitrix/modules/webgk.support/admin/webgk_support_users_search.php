<?
    ##############################################
    # Bitrix Site Manager                        #
    # Copyright (c) 2002-2007 Bitrix             #
    # http://www.bitrixsoft.com                  #
    # mailto:admin@bitrixsoft.com                #
    ##############################################
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/prolog.php");

    if(!($USER->CanDoOperation('view_subordinate_users') || $USER->CanDoOperation('view_all_users')))
        $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

    IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/user_admin.php");
    IncludeModuleLangFile(__FILE__);   

    CModule::IncludeModule('webgk.support');    

    $FN = preg_replace("/[^a-z0-9_\\[\\]:]/i", "", $_REQUEST["FN"]);
    $FC = preg_replace("/[^a-z0-9_\\[\\]:]/i", "", $_REQUEST["FC"]);
    if($FN == "")
        $FN = "find_form";
    if($FC == "")
        $FC = "find_user_id";

    if (isset($_REQUEST['JSFUNC']))
    {
        $JSFUNC = preg_replace("/[^a-z0-9_\\[\\]:]/i", "", $_REQUEST['JSFUNC']);
    }
    else
    {
        $JSFUNC = '';
    }
    // идентификатор таблицы
    $sTableID = "tbl_user_popup";

    // инициализация сортировки
    $oSort = new CAdminSorting($sTableID, "ID", "asc");
    // инициализация списка
    $lAdmin = new CAdminList($sTableID, $oSort);

    // инициализация параметров списка - фильтры
    $arFilterFields = Array(
        "find_id",
        "find_user_id",   
        "find_project_name" 
    );

    $lAdmin->InitFilter($arFilterFields);


    $arFilter = Array(
        "ID"=> $find_id,
        "USER_ID"=>$find_user_id,
        "PROJECT_NAME"=>$find_project_name
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
      
    );

    //init headers
    $lAdmin->AddHeaders($arHeaders); 

    // построение списка
    while($arRes = $rsData->GetNext())
    {
        $f_ID = $arRes['ID'];
        $row =& $lAdmin->AddRow($f_ID, $arRes);
        $row->AddViewField("ID", $f_ID);
        $row->AddCheckField("ACTIVE", false);
        $row->AddViewField("PROJECT_NAME", "<a href=\"javascript:SetValue('".$f_ID."');\" title=\"".GetMessage("MAIN_CHANGE")."\">".$arRes["PROJECT_NAME"]."</a>");

        $arActions = array();
        $arActions[] = array(
            "ICON"=>"",
            "TEXT"=>GetMessage("MAIN_CHANGE"),
            "DEFAULT"=>true,
            "ACTION"=>"SetValue('".$f_ID."');"
        );
        $row->AddActions($arActions);
    }

    $lAdmin->AddAdminContextMenu(array());

    // проверка на вывод только списка (в случае списка, скрипт дальше выполняться не будет)
    $lAdmin->CheckListMode();

    $APPLICATION->SetTitle(GetMessage("MAIN_PAGE_TITLE"));
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php")
?>
<script language="JavaScript">
    <!--
    function SetValue(id)
    {
        <?if ($JSFUNC <> ''){?>
            window.opener.SUV<?=$JSFUNC?>(id);
            <?}else{?>
            window.opener.document.<?echo $FN;?>["<?echo $FC;?>"].value=id;
            if (window.opener.BX)
                window.opener.BX.fireEvent(window.opener.document.<?echo $FN;?>["<?echo $FC;?>"], 'change');
            window.close();
            <?}?>
    }
    //-->
</script>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
    <?
        // create filter
        $oFilter = new CAdminFilter(
            $sTableID."_filter",
            array(
                "ID",
                GetMessage("WEBGK_GK_SUPPORT_USERS_USER_ID"),
                GetMessage("WEBGK_GK_SUPPORT_USERS_PROJECT_NAME"),
            )
        );

        $oFilter->Begin();
    ?>
    
    <tr>
        <td>ID</td>
        <td><input type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"><?=ShowFilterLogicHelp()?></td>
    </tr>
    <tr>
        <td><?echo GetMessage("WEBGK_GK_SUPPORT_USERS_USER_ID")?></td>
        <td><input type="text" name="find_user_id" size="47" value="<?echo htmlspecialcharsbx($find_user_id)?>"><?=ShowFilterLogicHelp()?></td>
    </tr>
    <tr>
        <td><?echo GetMessage("WEBGK_GK_SUPPORT_USERS_PROJECT_NAME")?></td>
        <td><input type="text" name="find_project_name" size="47" value="<?echo htmlspecialcharsbx($find_project_name)?>"><?=ShowFilterLogicHelp()?></td>
    </tr>
   
    <input type="hidden" name="FN" value="<?echo htmlspecialcharsbx($FN)?>">
    <input type="hidden" name="FC" value="<?echo htmlspecialcharsbx($FC)?>">
    <input type="hidden" name="JSFUNC" value="<?echo htmlspecialcharsbx($JSFUNC)?>">
    <?
        $oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
        $oFilter->End();
    ?>
</form>
<?
    // место для вывода списка
    $lAdmin->DisplayList();

    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");
?>
