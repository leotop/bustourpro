<?
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 

    IncludeModuleLangFile(__FILE__);
    CModule::IncludeModule('webgk.support');


    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("SERVICES_EDIT_TAB_TITLE"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("SERVICES_EDIT_TAB_TITLE")),
    );
    $tabControl = new CAdminTabControl("tabControl", $aTabs);


    $ID = $_REQUEST["ID"];
    $ID = intval($ID);        
    $message = null;        
    $bVarsFromForm = false; 


    if(
        $REQUEST_METHOD == "POST" // check request method
        &&
        ($save!="" || $apply!="") // check action method      
        &&
        check_bitrix_sessid()     // check session
    )
    {
        $group = new GKSupportServices;

        // data
        $arFields = Array(
            "ACTIVE"=> ($ACTIVE <> "Y" ? "N" : "Y"),
            "NAME"=> $NAME,
            "HOUR_PRICE"=> $HOUR_PRICE,
        );

        // save data
        if($ID > 0)
        {
            $res = $group->Update($ID, $arFields);
        } 
        else {
            $ID = $group->Add($arFields);
            $res = ($ID > 0);
        }            

        if($res)
        {
            // if save complete, make redirect 
            if ($apply != "")
                // if apply
                LocalRedirect("/bitrix/admin/webgk_support_services_edit.php?ID=".$ID."&mess=ok&=".LANG."&".$tabControl->ActiveTabParam());
            else
                // if save
                LocalRedirect("/bitrix/admin/webgk_support_services.php?lang=".LANG);
        }
        else
        {
            // if error
            $bVarsFromForm = true;
        }
    }

    //default values
    $str_ACTIVE= "Y";
    $str_NAME = "";
    $str_HOUR_PRICE = "";


    if($ID>0)
    {
        $group = GKSupportServices::GetList($by,$sort,array("ID"=>$ID));  
        if(!$group->ExtractFields("str_"))
            $ID=0;
    }

    // if data from form
    if($bVarsFromForm)
        $DB->InitTableVarsForEdit("webgk_support_services", "", "str_");

    $APPLICATION->SetTitle(GetMessage("SERVICES_EDIT")." #".$ID);


    $aMenu = array(
        array(
            "TEXT"  => GetMessage("BTN_TO_SERVICES_LIST"),
            "TITLE" => GetMessage("BTN_TO_SERVICES_LIST"),
            "LINK"  => "webgk_support_services.php?lang=".LANG,
            "ICON"  => "btn_list",
        )
    );


?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); // второй общий пролог
?>
<?
    // make menu
    $context = new CAdminContextMenu($aMenu);

    // init menu
    $context->Show();
?>

<form method="POST" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
<?echo bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?=LANG?>">
<?if($ID>0 && !$bCopy):?>
    <input type="hidden" name="ID" value="<?=$ID?>">
    <?endif;?>
<?
    // make tabs title
    $tabControl->Begin();
?>
<?
    //********************
    // first tab
    //********************
    $tabControl->BeginNextTab();
?>
<tr>
    <td width="40%"><?echo GetMessage("WEBGK_GK_SUPPORT_SERVICES_ACTIVE")?></td>
    <td width="60%"><input type="checkbox" name="ACTIVE" id="ACTIVE" value="Y"<?if($str_ACTIVE == "Y") echo " checked"?> /></td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_SERVICES_NAME")?>:</td>
    <td><input type="text" name="NAME" id="NAME" value="<?=$str_NAME?>" size="30"/></td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_SERVICES_HOUR_PRICE")?>:</td>
    <td>
       <input type="text" name="HOUR_PRICE" id="HOUR_PRICE" value="<?=$str_HOUR_PRICE?>" size="30"/></textarea>
    </td>
</tr>


<?
    // form buttons
    $tabControl->Buttons(
        array(
            "back_url"=>"webgk_support_services.php?lang=".LANG,
        )
    );
?>
<?
    // end of tabs
    $tabControl->End();
?>

<?
    // error messages
    $tabControl->ShowWarnings("post_form", $message);
?>


<?echo BeginNote();?>
<span class="required">*</span><?echo GetMessage("REQUIRED_FIELDS")?>
<?echo EndNote();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>