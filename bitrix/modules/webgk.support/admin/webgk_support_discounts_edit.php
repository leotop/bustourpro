<?
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 

    IncludeModuleLangFile(__FILE__);
    CModule::IncludeModule('webgk.support');


    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("DISCOUNT_EDIT_TAB_TITLE"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("DISCOUNT_EDIT_TAB_TITLE")),
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
        $discount = new GKSupportDiscounts;

        // data
        $arFields = Array(
            "ACTIVE"         => ($ACTIVE <> "Y" ? "N" : "Y"),
            "NAME"           => $NAME,
            "TYPE"           => $TYPE,
            "DISCOUNT"       => $DISCOUNT,
            "USER_ID"        => $USER_ID,
            "GROUP_ID"       => $GROUP_ID,           
            "SERVICE_ID"     => $SERVICE_ID,
        );

        // save data
        if($ID > 0)
        {
            $res = $discount->Update($ID, $arFields);
        }   

        else {
            $ID = $discount->Add($arFields);
            $res = ($ID > 0);
        }             

        if($res)
        {
            // if save complete, make redirect 
            if ($apply != "")
                // if apply
                LocalRedirect("/bitrix/admin/webgk_support_discounts_edit.php?ID=".$ID."&mess=ok&=".LANG."&".$tabControl->ActiveTabParam());
            else
                // if save
                LocalRedirect("/bitrix/admin/webgk_support_discounts.php?lang=".LANG);
        }
        else
        {
            // if error
            $bVarsFromForm = true;
        }
    }

    //default values
    $str_ACTIVE = "Y";
    $str_NAME = "";
    $str_TYPE = "";
    $str_DISCOUNT = "";
    $str_USER_ID = "";
    $str_GROUP_ID = "";
    $str_SERVICE_ID = "";


    if($ID>0)
    {
        $discount = GKSupportDiscounts::GetList($by,$sort,array("ID"=>$ID));  
        if(!$discount->ExtractFields("str_"))
            $ID=0;
    }

    // if data from form
    if($bVarsFromForm)
        $DB->InitTableVarsForEdit("webgk_support_discounts", "", "str_");

    $APPLICATION->SetTitle(GetMessage("DISCOUNT_EDIT")." #".$ID);


    $aMenu = array(
        array(
            "TEXT"  => GetMessage("BTN_TO_DISCOUNT_LIST"),
            "TITLE" => GetMessage("BTN_TO_DISCOUNT_LIST"),
            "LINK"  => "webgk_support_discounts.php?lang=".LANG,
            "ICON"  => "btn_list",
        )
    );
    //Actions for copy 
    if (!empty($_REQUEST["COPY"])){
        $arDiscountFilter["ID"]=intval($_REQUEST["COPY"]);
        $obDiscount = GKSupportDiscounts::GetList($by="id",$sort="ASC", $arDiscountFilter);
        $arDiscount = $obDiscount->Fetch();
        $str_ACTIVE = $arDiscount["ACTIVE"];
        $str_NAME = $arDiscount["NAME"];
        $str_TYPE = $arDiscount["TYPE"];
        $str_DISCOUNT = $arDiscount["DISCOUNT"];
        $str_USER_ID = $arDiscount["USER_ID"];
        $str_GROUP_ID = $arDiscount["GROUP_ID"];
        $str_SERVICE_ID = $arDiscount["SERVICE_ID"];
    }


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
    <td ><?echo GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_ACTIVE")?></td>
    <td ><input type="checkbox" name="ACTIVE" id="ACTIVE" value="<?=$str_ACTIVE?>" <?if($str_ACTIVE == "Y") echo " checked"?> /></td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_NAME")?>:</td>
    <td><input type="text" name="NAME" id="NAME" value="<?=$str_NAME?>"/></td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_TYPE")?>:</td>
    <td>
        <?
            $arr = array(
                "reference" => array(
                    GetMessage("POST_RUB"),
                    GetMessage("POST_PERCENT"),
                ),
                "reference_id" => array(
                    "R",
                    "P",
                )
            );
            echo SelectBoxFromArray("TYPE", $arr, $str_TYPE, "", "");
        ?>
    </td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_DISCOUNT")?>:</td>
    <td><input type="text" name="DISCOUNT" id="DISCOUNT" value="<?=$str_DISCOUNT?>"/></td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_USER_ID")?>:</td>
    <td>
        <input type="text" name="USER_ID" id="USER_ID" value="<?echo htmlspecialchars($str_USER_ID)?>" size="5" />     
        <input type="button" value="..." onClick="jsUtils.OpenWindow('/bitrix/admin/webgk_support_users_search.php?lang=<?echo LANGUAGE_ID?>&amp;n=USER_ID&amp;m=n&FN=post_form&FC=USER_ID', 600, 500);">
        <?if ($str_USER_ID){?>
            <?$arUser = GKSupportUsers::GetList($by="ID",$sort="ASC",array("ID"=>$str_USER_ID))->Fetch();?>
            <?=$arUser["PROJECT_NAME"]?>
            <?}?>
    </td>
</tr>


<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_GROUP_ID")?>:</td>
    <td>

        <?$groups = GKSupportUserGroups::GetList($by="ID",$sort="ASC",array());?>
        <select name="GROUP_ID" id="GROUP_ID">                                                                         
            <option value="0"><?=GetMessage("NO")?></option>
            <?while($arGroup = $groups->Fetch()){?>
                <option value="<?=$arGroup["ID"]?>" <?if ($arGroup["ID"] == $str_GROUP_ID){?> selected="selected"<?}?>><?=$arGroup["NAME"]?></option>
                <?}?>

        </select>
    </td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_DISCOUNTS_SERVICE_ID")?>:</td>
    <td>

        <?$service = GKSupportServices::GetList($by="ID",$sort="ASC",array());?>
        <select name="SERVICE_ID" id="SERVICE_ID">                                                                         
            <option value=""><?=GetMessage("ALL")?></option>
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
            "back_url"=>"webgk_support_discounts.php?lang=".LANG,
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