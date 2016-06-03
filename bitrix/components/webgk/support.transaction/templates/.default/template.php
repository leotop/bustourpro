<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent("bitrix:menu", "deskmanMenu", Array(
        "ROOT_MENU_TYPE" => "deskman",    
        "MENU_CACHE_TYPE" => "N",    
        "MENU_CACHE_TIME" => "3600",    
        "MENU_CACHE_USE_GROUPS" => "Y",   
        "MENU_CACHE_GET_VARS" => "",   
        "MAX_LEVEL" => "1",    
        "CHILD_MENU_TYPE" => "",    
        "USE_EXT" => "Y",   
        ),
        false
    ); 
?>
<? if ($arResult!="no-access"){?>
    <form id="filter-form" method="get">
        <?=GetMessage('SUPPORT_ID')?>: <input type="text" name="ID" id="ID" value="<?=$arResult["FILTER"]["ID"]?>"/><br><br>
        <?=GetMessage('SUPPORT_ID_TICKET')?>: <input type="text" name="ID_TICKET" id="ID_TICKET" value="<?=$arResult["FILTER"]["TICKET_ID"]?>"/><br><br>
        <?=GetMessage('SUPPORT_ID_USER')?>:
        <select id="USER" name="USER">
            <option value=""><?=GetMessage('SUPPORT_STAFF_ALL')?></option>
            <?  foreach ($arResult["USERS"] as $arUser) {?>
                <option value="<?=$arUser["ID"]?>" <? if($arResult["FILTER"]["USER_ID"]==$arUser["ID"]) {?>selected="selected"<?}?>><?=$arUser["NAME"]." ".$arUser["LAST_NAME"]?></option>
                <?}?>
        </select><br><br>
        <? if ($arResult["IS_CLIENT"]!="Y") {?>
            <?=GetMessage('SUPPORT_ID_CLIENT')?>:
            <select id="CLIENT" name="CLIENT">
                <option value=""><?=GetMessage('SUPPORT_STAFF_ALL')?></option>
                <?  foreach ($arResult["CLIENTS"] as $arClient) {?>
                    <option value="<?=$arClient["ID"]?>" <? if($arResult["FILTER"]["CLIENT_ID"]==$arClient["ID"]) {?>selected="selected"<?}?>><?=$arClient["PROJECT_NAME"]?></option>
                    <?}?>
            </select>
            <br><br>
            <?} ?>
        <?=GetMessage('SUPPORT_INTERVAL_TIME')?>:
        <div class="date-input">
            <?
                if (!empty($_REQUEST["date_fld"])){
                    $inputValue=$arResult["FILTER"][">=DATE"];
                }  
                if (!empty($_REQUEST["date_fld_finish"])){
                    $inputValueFinish=$arResult["FILTER"]["<=DATE"];
                } 
            ?>
            <?$APPLICATION->IncludeComponent(
                    "bitrix:main.calendar", 
                    ".default", 
                    array(
                        "COMPONENT_TEMPLATE" => ".default",
                        "SHOW_INPUT" => "Y",
                        "FORM_NAME" => "date_filter",
                        "INPUT_NAME" => "date_fld",
                        "INPUT_NAME_FINISH" => "date_fld_finish",
                        "INPUT_VALUE" => $inputValue,
                        "INPUT_VALUE_FINISH" => $inputValueFinish,
                        "SHOW_TIME" => "N",
                        "HIDE_TIMEBAR" => "N"
                    ),
                    false
                );?> 
        </div>
        &nbsp; <br><br>
        <?=GetMessage('SUPPORT_TYPE')?>:
        <select id="TYPE" name="TYPE">
            <option value=""><?=GetMessage('SUPPORT_STAFF_ALL')?></option>
            <option value="P" <? if($arResult["FILTER"]["TYPE"]=="P") {?>selected="selected"<?}?>><?=GetMessage('SUPPORT_TYPE_P')?></option>
            <option value="M" <? if($arResult["FILTER"]["TYPE"]=="M") {?>selected="selected"<?}?>><?=GetMessage('SUPPORT_TYPE_M')?></option>
        </select><br><br>

        <button type="submit"><?=GetMessage('SUPPORT_FIND')?></button>
        <button type="submit" name="IS_RESET" value="Y"><?=GetMessage('SUPPORT_RESET')?></button><br><br>

    </form>
    <table class="statTable">
        <tr>
            <th><?=GetMessage('SUPPORT_ID')?></th>
            <th><?=GetMessage('SUPPORT_ACTIVE')?></th>
            <th><?=GetMessage('SUPPORT_ID_USER')?></th>
            <? if ($arResult["IS_CLIENT"]!='Y') { ?>
                <th><?=GetMessage('SUPPORT_ID_CLIENT')?></th>
                <? } ?>
            <th><?=GetMessage('SUPPORT_DATE')?></th>
            <th><?=GetMessage('SUPPORT_SUMM')?></th>
            <th><?=GetMessage('SUPPORT_TYPE')?></th>
            <th><?=GetMessage('SUPPORT_ID_TICKET')?></th>
            <th><?=GetMessage('SUPPORT_COMMENT')?></th>
        </tr>  
        <?    
            $arResult["OBJECT"]->NavStart($arParams["PAGE_QUANTITY"]);
            echo $arResult["OBJECT"]->NavPrint();
            while($arTransaction = $arResult["OBJECT"]->NavNext(true, 'f_')){?>

            <?switch ($arTransaction["TYPE"]) {
                case "P" : $class= "Plus"; break;
                case "M" : $class= "Minus"; break;
            }
            if ($arTransaction["ACTIVE"] == "N") {
                $class = "unactive";
            }
            ?>
            <tr class="<?=$class?>">
                <td align="center"><?=$arTransaction["ID"]?></td>
                <td align="center"><?=GetMessage("SUPPORT_ACTIVE_".$arTransaction["ACTIVE"])?></td>
                <td><?=$arResult["USERS"][$arTransaction["USER_ID"]]["NAME"].' '.$arResult["USERS"][$arTransaction["USER_ID"]]["LAST_NAME"]?></td>
                <? if ($arResult["IS_CLIENT"]!='Y') { ?>
                    <td><?=$arResult["ARRAY"][$arTransaction["ID"]]["PROJECT_NAME"]?></td>
                    <? } ?>
                <td align="center"><?=date("H:i:s d.m.Y", strtotime($arTransaction["DATE"]))?></td>
                <td align="center"><?=$arTransaction["SUMM"]?></td>
                <td><?=GetMessage('SUPPORT_TYPE_'.$arTransaction["TYPE"])?></td>
                <td align="center"><? if (!empty($arTransaction["TICKET_ID"])) { echo $arTransaction["TICKET_ID"];} else {echo '-'; }?></td>
                <td><?=$arTransaction["COMMENT"]?></td>
            </tr>
            <?}
        ?> 
    </table>
    <?
        echo $arResult["OBJECT"]->NavPrint();
    ?>
    <?
    } else if ($arResult=="no-access") {
        echo GetMessage('SUPPORT_ATTENTION');
    }
?>