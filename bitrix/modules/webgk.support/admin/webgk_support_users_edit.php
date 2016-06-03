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
        $client = new GKSupportUsers;

        // data
        $arFields = Array(
            "ACTIVE"         => ($ACTIVE <> "Y" ? "N" : "Y"),
            "PROJECT_NAME"   => $PROJECT_NAME,
            "USER_ID"        => $USER_ID,
            "GROUP_ID"       => $GROUP_ID
        );

        // save data
        if($ID > 0)
        {
            $res = $client->Update($ID, $arFields);
        }             

        if($res)
        {
            // if save complete, make redirect 
            if ($apply != "")
                // if apply
                LocalRedirect("/bitrix/admin/webgk_support_users_edit.php?ID=".$ID."&mess=ok&=".LANG."&".$tabControl->ActiveTabParam());
            else
                // if save
                LocalRedirect("/bitrix/admin/webgk_support_users.php?lang=".LANG);
        }
        else
        {
            // if error
            $bVarsFromForm = true;
        }
    }

    //default values
    $str_ACTIVE        = "Y";
    $str_PROJECT_NAME  = "";
    $str_USER_ID = "";
    $str_BALANCE = "";
    $str_GROUP_ID = "";


    if($ID>0)
    {
        $client = GKSupportUsers::GetList($by,$sort,array("ID"=>$ID));  
        if(!$client->ExtractFields("str_"))
            $ID=0;
    }

    // if data from form
    if($bVarsFromForm)
        $DB->InitTableVarsForEdit("webgk_support_users", "", "str_");

    $APPLICATION->SetTitle(GetMessage("USER_EDIT")." #".$ID);


    $aMenu = array(
        array(
            "TEXT"  => GetMessage("BTN_TO_USER_LIST"),
            "TITLE" => GetMessage("BTN_TO_USER_LIST"),
            "LINK"  => "webgk_support_users.php?lang=".LANG,
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
    <td width="40%"><?echo GetMessage("WEBGK_GK_SUPPORT_USERS_ACTIVE")?></td>
    <td width="60%"><input type="checkbox" name="ACTIVE" id="ACTIVE" value="Y"<?if($str_ACTIVE == "Y") echo " checked"?> /></td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_USERS_PROJECT_NAME")?>:</td>
    <td><input type="text" name="PROJECT_NAME" id="PROJECT_NAME" value="<?=$str_PROJECT_NAME?>"/></td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_USERS_USER_ID")?>:</td>
    <td>
    <input type="text" name="USER_ID" id="USER_ID" value="<?=$str_USER_ID?>" size="5"/>     
    <input type="button" value="..." onClick="jsUtils.OpenWindow('/bitrix/admin/user_search.php?lang=<?echo LANGUAGE_ID?>&amp;n=USER_ID&amp;m=n&FN=post_form', 600, 500);">
        
    <?$arUser = CUser::GetById($str_USER_ID)->Fetch();?>
    (<?=$arUser["LOGIN"]?>) <?=$arUser["NAME"]?> <?=$arUser["LAST_NAME"]?>
    </td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_USERS_BALANCE")?>:</td>
    <td><input type="text" name="" value="<?=$str_BALANCE?>" disabled="disabled"/></td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_USERS_GROUP_ID")?>:</td>
    <td>
    
    <?$groups = GKSupportUserGroups::GetList($by="ID",$sort="ASC",array());?>
            <select name="GROUP_ID">                                                                         
                <option value="0"><?=GetMessage("NO")?></option>
                <?while($arGroup = $groups->Fetch()){?>
                    <option value="<?=$arGroup["ID"]?>" <?if ($arGroup["ID"] == $str_GROUP_ID){?> selected="selected"<?}?>><?=$arGroup["NAME"]?></option>
                    <?}?>

            </select>
    </td>
</tr>

<?
    // form buttons
    $tabControl->Buttons(
        array(
            "back_url"=>"webgk_support_users.php?lang=".LANG,
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