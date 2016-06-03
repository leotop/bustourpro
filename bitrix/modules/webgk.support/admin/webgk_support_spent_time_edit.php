<?
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 

    IncludeModuleLangFile(__FILE__);
    CModule::IncludeModule('webgk.support');


    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("SPENT_TIME_EDIT_TAB_TITLE"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("TRANSACTIONS_EDIT_TAB_TITLE")),
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
        $spentTime = new GKSupportSpentTime;

        // data

        // save data
        if($ID > 0)
        {
            $arFields = Array(    
                "COMMENT"=> $COMMENT,         
            );

            $res = $spentTime->Update($ID, $arFields);
        }   

        else {
            $arFields = Array(   
                "CLIENT_ID"      => $CLIENT_ID,
                "HOURS"          => $HOURS,
                "MINUTES"        => $MINUTES,
                "COMMENT"        => $COMMENT,
                "IS_PAYED"       => $IS_PAYED,
                "TICKET_ID"      => $TICKET_ID,
                "SERVICE_ID"     => $SERVICE_ID      
            );

            $ID = $spentTime->Add($arFields);
            $res = ($ID > 0);
        }             

        if($res)
        {
            // if save complete, make redirect 
            if ($apply != "")
                // if apply
                LocalRedirect("/bitrix/admin/webgk_support_spent_time_edit.php?ID=".$ID."&mess=ok&=".LANG."&".$tabControl->ActiveTabParam());
            else
                // if save
                LocalRedirect("/bitrix/admin/webgk_support_spent_time.php?lang=".LANG);
        }
        else
        {
            // if error
            $bVarsFromForm = true;
        }
    }

    //default values
    $str_CLIENT_ID = "";
    $str_COMMENT = "";
    $str_IS_PAYED = "";
    $str_TICKET_ID = "";
    $str_HOURS = "";
    $str_MINUTES = "";
    $str_SERVICE_ID = "";

    if($ID>0)
    {
        $discount = GKSupportSpentTime::GetList($by,$sort,array("ID"=>$ID));  
        if(!$discount->ExtractFields("str_"))
            $ID=0;
    }

    // if data from form
    if($bVarsFromForm)
        $DB->InitTableVarsForEdit("webgk_support_spent_time", "", "str_");

    $APPLICATION->SetTitle(GetMessage("SPENT_TIME_EDIT")." #".$ID);


    $aMenu = array(
        array(
            "TEXT"  => GetMessage("BTN_TO_SPENT_TIME_LIST"),
            "TITLE" => GetMessage("BTN_TO_SPENT_TIME_LIST"),
            "LINK"  => "webgk_support_spent_time.php?lang=".LANG,
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
    <td ><?echo GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_CLIENT_ID")?></td>
    <td >
        <input <?if ($ID > 0) {?> disabled="disabled"<?}?> type="text" name="CLIENT_ID" id="CLIENT_ID" value="<?echo htmlspecialchars($str_CLIENT_ID)?>" size="5" />     
        <input <?if ($ID > 0) {?> disabled="disabled"<?}?> type="button" value="..." onClick="jsUtils.OpenWindow('/bitrix/admin/webgk_support_users_search.php?lang=<?echo LANGUAGE_ID?>&amp;n=CLIENT_ID&amp;m=n&FN=post_form&FC=CLIENT_ID', 600, 500);">
        <?if ($str_CLIENT_ID){?>
            <?$arUser = GKSupportUsers::GetList($by="ID",$sort="ASC",array("ID"=>$str_CLIENT_ID))->Fetch();?>
            <?=$arUser["PROJECT_NAME"]?>
            <?}?>
    </td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_IS_PAYED")?>:</td>
    <td>
        <?
           if ($ID > 0) {
               $tag = "disabled='disabled'";
           }
        
            $arr = array(
                "reference" => array(
                    GetMessage("POST_NO"),
                    GetMessage("POST_YES"),
                ),
                "reference_id" => array(
                    "N",
                    "Y",
                )
            );
            echo SelectBoxFromArray("IS_PAYED", $arr, $str_IS_PAYED, "", $tag);
        ?>
    </td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_HOURS")?>:</td>
    <td><input <?if ($ID > 0) {?> disabled="disabled"<?}?> type="text" name="HOURS" id="HOURS" value="<?=$str_HOURS?>"/></td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_MINUTES")?>:</td>
    <td><input <?if ($ID > 0) {?> disabled="disabled"<?}?> type="text" name="MINUTES" id="MINUTES" value="<?=$str_MINUTES?>"/></td>
</tr>


<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_COMMENT")?>:</td>
    <td><textarea cols="50" rows="5" name="COMMENT" id="COMMENT"><?=$str_COMMENT?></textarea></td>
</tr> 

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_TICKET_ID")?>:</td>
    <td><input <?if ($ID > 0) {?> disabled="disabled"<?}?> type="text" name="TICKET_ID" id="TICKET_ID" value="<?=$str_TICKET_ID?>"/></td>
</tr>  

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_SPENT_TIME_SERVICE_ID")?>:</td>
    <td>

        <?$service = GKSupportServices::GetList($by="ID",$sort="ASC",array());?>
        <select name="SERVICE_ID" id="SERVICE_ID" <?if ($ID > 0){?> disabled="disabled" <?}?>>                                                                         
            <?while($arService = $service->Fetch()){?>
                <option value="<?=$arService["ID"]?>" <?if ($arService["ID"] == $str_SERVICE_ID){?> selected="selected"<?}?>><?=$arService["NAME"]?></option>
                <?}?>

        </select>
    </td>
</tr> 


<?
    // form buttons
    $tabControl->Buttons(
        array(
            "back_url"=>"webgk_support_spent_time.php?lang=".LANG,
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