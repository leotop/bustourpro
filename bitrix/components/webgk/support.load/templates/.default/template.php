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
<? if (count($arResult)>=1){?>
    <h1><?=GetMessage('SUPPORT_MAIN_TITLE')?></h1><br>
     <form id="staff-form" method="get">
        <?=GetMessage('SUPPORT_STAFF')?> :
        <select id="user" name="user">
            <option value=""><?=GetMessage('SUPPORT_STAFF_ALL')?></option>
            <?  foreach ($arResult["FILT_USERS"] as $arUser) {?>
                <option value="<?=$arUser["ID"]?>" <? if($arResult["FILTER"]["ID"]==$arUser["ID"]) {?>selected="selected"<?}?>><?=$arUser["NAME"]." ".$arUser["LAST_NAME"]?></option>
                <?}?>
        </select><br><br>
        <button type="submit"><?=GetMessage('SUPPORT_FIND')?></button>
        <button type="submit" name="IS_RESET" value="Y"><?=GetMessage('SUPPORT_RESET')?></button><br><br>

    </form><br>
    
    <table class="statTable">
        <tr>
            <th><?=GetMessage('SUPPORT_EMPLOYEE')?></th>
            <?foreach ($arResult["STATUS"] as $status){?>
                <th><?=$status["NAME"]?></th>
                <?}?>
            <th><?=GetMessage('SUPPORT_TOTAL')?></th>
        </tr>
        <?foreach ($arResult["ITEMS"] as $arUser){?>
            <?
                $style="";
                if ($arUser["ACTIVE"] != "Y") {
                    $style= 'style="background: #FFA8C1;"';
                }
                else if ($i%2 != 0) {
                    $style = 'style="background: #efefef;"';   
                }
            ?>                                                
            <tr <?=$style?>>
                <td><?=$arUser["NAME"]." ".$arUser["LAST_NAME"]?></td>
                <?$total = 0;?>
                <?foreach ($arResult["STATUS"] as $status){?>       
                    <td align="center" >
                        <?$count = count($arUser["TICKETS"][$status["ID"]]["TICKETS_LIST"]);?>
                        <?if ($count >= $arParams["SUPPORT_CRITICALY"]){?><b style="font-size:16px; color: red"><?}?>
                            <?echo $count?>
                        <?if ($count >= $arParams["SUPPORT_CRITICALY"]){?></b><?}?>
                    </td>
                    <?$total +=  $count;}?>

                <td align="center">
                    <b <?if ($total >= $arParams["SUPPORT_CRITICALY_SUM"]){?> style="font-size:16px; color:red"<?}?>><?=$total?></b>
                </td>
            </tr>
            <?}?>
    </table>
    <?
    } else {
        echo GetMessage('SUPPORT_ATTENTION');
    }
?>

