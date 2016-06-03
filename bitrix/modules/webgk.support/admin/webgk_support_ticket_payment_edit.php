<?
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 

    IncludeModuleLangFile(__FILE__);
    CModule::IncludeModule('webgk.support');


    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("TICKET_PAYMENT_EDIT_TAB_TITLE"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("TICKET_PAYMENT_EDIT_TAB_TITLE")),
    );
    $tabControl = new CAdminTabControl("tabControl", $aTabs);


    $TICKET_ID = $_REQUEST["TICKET_ID"];
    $TICKET_ID = intval($TICKET_ID);        
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
        $payment = new GKSupportTicketPayment;

        // data
        $arFields = Array(
            "IN_PAYMENT"         => ($IN_PAYMENT <> "Y" ? "N" : "Y"),
            "TICKET_ID"           => $TICKET_ID,     
        );
        
       
        // save data

        $res = $payment->Change($arFields["TICKET_ID"],$arFields["IN_PAYMENT"]);

        if($res)
        {
            // if save complete, make redirect 
            if ($apply != "")
                // if apply
                LocalRedirect("/bitrix/admin/webgk_support_ticket_payment_edit.php?TICKET_ID=".$TICKET_ID."&mess=ok&=".LANG."&".$tabControl->ActiveTabParam());
            else
                // if save
                LocalRedirect("/bitrix/admin/webgk_support_ticket_payment.php?lang=".LANG);
        }
        else
        {
            // if error
            $bVarsFromForm = true;
        }
    }

    //default values
    $str_IN_PAYMENT = "Y";
    $str_TICKET_ID = "";



    if($TICKET_ID>0)
    {
        $payment = GKSupportTicketPayment::GetList($by,$sort,array("TICKET_ID"=>$TICKET_ID));  
        if(!$payment->ExtractFields("str_"))
            $TICKET_ID=0;
    }

    // if data from form
    if($bVarsFromForm)
        $DB->InitTableVarsForEdit("webgk_support_ticket_payment", "", "str_");

    $APPLICATION->SetTitle(GetMessage("TICKET_EDIT")." #".$TICKET_ID);


    $aMenu = array(
        array(
            "TEXT"  => GetMessage("BTN_TO_TICKET_PAYMENT_LIST"),
            "TITLE" => GetMessage("BTN_TO_TICKET_PAYMENT_LIST"),
            "LINK"  => "webgk_support_ticket_payment.php?lang=".LANG,
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
    <td ><?echo GetMessage("WEBGK_GK_SUPPORT_TICKET_PAYMENT_IN_PAYMENT")?></td>
    <td ><input type="checkbox" name="IN_PAYMENT" id="IN_PAYMENT" value="Y"<?if($str_IN_PAYMENT == "Y") echo " checked"?> /></td>
</tr>

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_TICKET_PAYMENT_TICKET_ID")?>:</td>
    <td><input type="text" name="TICKET_ID" id="TICKET_ID" value="<?=$str_TICKET_ID?>"/></td>
</tr>


<?
    // form buttons
    $tabControl->Buttons(
        array(
            "back_url"=>"webgk_support_ticket_payment.php?lang=".LANG,
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