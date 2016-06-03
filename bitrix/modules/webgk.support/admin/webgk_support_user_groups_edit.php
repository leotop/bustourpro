<?
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 

    IncludeModuleLangFile(__FILE__);
    CModule::IncludeModule('webgk.support');


    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("USER_EDIT_TAB_TITLE"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("USER_EDIT_TAB_TITLE")),
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
        $group = new GKSupportUserGroups;

        // data
        $arFields = Array(
            "ACTIVE"=> ($ACTIVE <> "Y" ? "N" : "Y"),
            "NAME"=> $NAME,
            "DESCRIPTION"=> $DESCRIPTION,
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
                LocalRedirect("/bitrix/admin/webgk_support_user_groups_edit.php?ID=".$ID."&mess=ok&=".LANG."&".$tabControl->ActiveTabParam());
            else
                // if save
                LocalRedirect("/bitrix/admin/webgk_support_user_groups.php?lang=".LANG);
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
    $str_DESCRIPTION = "";


    if($ID>0)
    {
        $group = GKSupportUserGroups::GetList($by,$sort,array("ID"=>$ID));  
        if(!$group->ExtractFields("str_"))
            $ID=0;
    }

    // if data from form
    if($bVarsFromForm)
        $DB->InitTableVarsForEdit("webgk_support_user_groups", "", "str_");

    $APPLICATION->SetTitle(GetMessage("GROUP_EDIT")." #".$ID);


    $aMenu = array(
        array(
            "TEXT"  => GetMessage("BTN_TO_GROUP_LIST"),
            "TITLE" => GetMessage("BTN_TO_GROUP_LIST"),
            "LINK"  => "webgk_support_user_groups.php?lang=".LANG,
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
    <td width="40%"><?echo GetMessage("WEBGK_GK_SUPPORT_GROUPS_ACTIVE")?></td>
    <td width="60%"><input type="checkbox" name="ACTIVE" id="ACTIVE" value="Y"<?if($str_ACTIVE == "Y") echo " checked"?> /></td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_GROUPS_NAME")?>:</td>
    <td><input type="text" name="NAME" id="NAME" value="<?=$str_NAME?>"/></td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_GROUPS_DESCRIPTION")?>:</td>
    <td>
        <textarea cols="50" rows="5" name="DESCRIPTION"><?=$str_DESCRIPTION?></textarea>
    </td>
</tr>


<?
    // form buttons
    $tabControl->Buttons(
        array(
            "back_url"=>"webgk_support_user_groups.php?lang=".LANG,
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