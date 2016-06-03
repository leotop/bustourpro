<?
    global $MESS;
    IncludeModuleLangFile( __FILE__ );   
    /**
    * Идентификатор модуля
    */
    $sModuleId  = 'webgk.support';

    /**
    * Подключаем модуль (выполняем код в файле include.php)
    */     
    
    if (!CModule::IncludeModule("support"))  {
        echo GetMessage('GK_SUP_SUPPORT_ERROR');            
    }
    
    else {
    
    CModule::IncludeModule( $sModuleId );  


    /**
    * Языковые константы (файл lang/ru/options.php)
    */
        

    $optionStatuses = array();
    $statuses = CTicketDictionary::GetList($by="id",$order="asc",array("TYPE"=>"S"),$is_filtered);
    while($arStatus = $statuses->Fetch()) {
        $optionStatuses[] = $arStatus; 
    }

    if( $REQUEST_METHOD == 'POST' && $_POST['Update'] == 'Y' ) {
        /**
        * Если форма была сохранена, устанавливаем значение опций модуля
        */

        foreach ($_POST["gkSupport"] as $key=>$param) {
            COption::SetOptionString($sModuleId,$key,$param);
        }
        
        foreach ($optionStatuses as $status) {
            if ($_POST["status"][$status["SID"]] == "Y") {
               COption::SetOptionString($sModuleId,"status_".$status["SID"],"Y"); 
            }
            else {
              COption::SetOptionString($sModuleId,"status_".$status["SID"],"N");  
            }
        }            
    }

    /**
    * Описываем табы административной панели битрикса
    */
    $aTabs = array(
        array(
            'DIV'   => 'edit1',
            'TAB'   => GetMessage('MAIN_TAB_SET'),
            'ICON'  => 'fileman_settings',
            'TITLE' => GetMessage('MAIN_TAB_TITLE_SET' )
        ),
    );

    /**
    * Инициализируем табы
    */
    $oTabControl = new CAdmintabControl( 'tabControl', $aTabs );
    $oTabControl->Begin();

?>
<form method="POST" enctype="multipart/form-data" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars( $sModuleId )?>&lang=<?echo LANG?>">
    <?=bitrix_sessid_post()?>
    <?$oTabControl->BeginNextTab();?>
    <tr class="heading">
        <td colspan="2"><?=GetMessage( 'GK_SUP_GROUP_TITLE' )?></td>
    </tr>

    <tr>               
        <td><?=GetMessage( 'GK_SUP_INCLUDE_JQUERY' )?></td>  
        <td>
            <?$includeJquery = COption::GetOptionString( $sModuleId, 'includeJquery');?>
            <select name="gkSupport[includeJquery]">
                <option value="N" <?if ($includeJquery == "N"){?> selected="selected"<?}?>><?=GetMessage("GK_SUP_NO")?></option>
                <option value="Y" <?if ($includeJquery == "Y"){?> selected="selected"<?}?>><?=GetMessage("GK_SUP_YES")?></option>
            </select>
        </td>
    </tr>

    <tr class="heading">
        <td colspan="2"><?=GetMessage( 'GK_SUP_STATUS_OPTIONS' )?></td>
    </tr>

    <td><?=GetMessage( 'GK_SUP_REQUIRED_STATUS' )?></td>  
    <?

    ?>
    <td>
        <?foreach ($optionStatuses as $status) {?>
            <?$checked = COption::GetOptionString( $sModuleId, 'status_'.$status["SID"]);?>
            <label><input type="checkbox" value="Y" name="status[<?=$status["SID"]?>]" <?if ($checked == "Y"){?> checked="checked"<?}?>> <?=$status["NAME"]?></label><br /><br />
            <?}?>
    </td>



    <?$oTabControl->Buttons();?>
    <input type="submit" name="Update" value="<?=GetMessage( 'GK_SUP_BUTTON_SAVE' )?>" />
    <input type="reset" name="reset" value="<?= GetMessage( 'GK_SUP_BUTTON_RESET' )?>" />
    <input type="hidden" name="Update" value="Y" />
    <?$oTabControl->End();?>
</form>

<?}?>