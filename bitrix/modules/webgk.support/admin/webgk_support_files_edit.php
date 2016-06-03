<?
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 

    IncludeModuleLangFile(__FILE__);
    CModule::IncludeModule('webgk.support');


    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("FILES_EDIT_TAB_TITLE"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("FILES_EDIT_TAB_TITLE")),
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
        $file = new GKSupportFiles;              

         

        // data
        $arFields = Array(    
            "COMMENT"=> $COMMENT,
            "CLIENT_ID" =>$CLIENT_ID,
            "NAME" => $NAME,
        ); 
        //saving file
        if (is_array($FILE_ID)) {
            $NEW_FILE_ID = CFile::SaveFile($FILE_ID, "/webgk.support/");   
             if ($NEW_FILE_ID > 0) {
               $arFields["FILE_ID"] = $NEW_FILE_ID;   
             } 
        } 

            
          arshow($arFields); 


        // save data
        if($ID > 0)
        {               
            $res = $file->Update($ID, $arFields);
        }   

        else {              
            $ID = $file->Add($arFields);
            $res = ($ID > 0);
        }             

        if($res)
        {
            // if save complete, make redirect 
            if ($apply != "")
                // if apply
                LocalRedirect("/bitrix/admin/webgk_support_files_edit.php?ID=".$ID."&mess=ok&=".LANG."&".$tabControl->ActiveTabParam());
            else
                // if save
                LocalRedirect("/bitrix/admin/webgk_support_files.php?lang=".LANG);
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
    $str_NAME = "";
    $str_FILE_ID = "";

    if($ID>0)
    {
        $discount = GKSupportFiles::GetList($by,$sort,array("ID"=>$ID));  
        if(!$discount->ExtractFields("str_"))
            $ID=0;
    }

    // if data from form
    if($bVarsFromForm)
        $DB->InitTableVarsForEdit("webgk_support_files", "", "str_");

    $APPLICATION->SetTitle(GetMessage("FILE_EDIT")." #".$ID);


    $aMenu = array(
        array(
            "TEXT"  => GetMessage("BTN_TO_FILES_LIST"),
            "TITLE" => GetMessage("BTN_TO_FILES_LIST"),
            "LINK"  => "webgk_support_files.php?lang=".LANG,
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
    <td ><?echo GetMessage("WEBGK_GK_SUPPORT_FILES_CLIENT_ID")?></td>
    <td >
        <input type="text" name="CLIENT_ID" id="CLIENT_ID" value="<?echo htmlspecialchars($str_CLIENT_ID)?>" size="5" />     
        <input type="button" value="..." onClick="jsUtils.OpenWindow('/bitrix/admin/webgk_support_users_search.php?lang=<?echo LANGUAGE_ID?>&amp;n=CLIENT_ID&amp;m=n&FN=post_form&FC=CLIENT_ID', 600, 500);">
        <?if ($str_CLIENT_ID){?>
            <?$arUser = GKSupportUsers::GetList($by="ID",$sort="ASC",array("ID"=>$str_CLIENT_ID))->Fetch();?>
            <?=$arUser["PROJECT_NAME"]?>
            <?}?>
    </td>
</tr>  

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_FILES_NAME")?>:</td>
    <td><input type="text" name="NAME" id="NAME" value="<?=$str_NAME?>"/></td>
</tr>   

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_FILES_FILE_ID")?>:</td>
    <td>
        <?echo CFileInput::Show("FILE_ID", ($ID > 0 && !$bCopy? $str_FILE_ID: 0),
                array(
                    "IMAGE" => "Y",
                    "PATH" => "Y",
                    "FILE_SIZE" => "Y",
                    "DIMENSIONS" => "Y",
                    "IMAGE_POPUP" => "Y",
                    "MAX_SIZE" => array(
                        "W" => COption::GetOptionString("iblock", "detail_image_size"),
                        "H" => COption::GetOptionString("iblock", "detail_image_size"),
                    ),
                ), array(
                    'upload' => true,
                    'medialib' => true,
                    'file_dialog' => true,
                    'cloud' => true,
                    'del' => true,
                    'description' => true,
                )
            );
        ?>
    </td>
</tr>  

<tr>
    <td><?echo GetMessage("WEBGK_GK_SUPPORT_FILES_COMMENT")?>:</td>
    <td><textarea cols="50" rows="5" name="COMMENT" id="COMMENT"><?=$str_COMMENT?></textarea></td>
</tr> 


<?
    // form buttons
    $tabControl->Buttons(
        array(
            "back_url"=>"webgk_support_files.php?lang=".LANG,
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