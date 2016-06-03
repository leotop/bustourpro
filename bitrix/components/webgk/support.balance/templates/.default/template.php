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
<? if ($arResult["OBJECT"]){?>
    <h1><?=GetMessage('SUPPORT_MAIN_TITLE')?></h1>
    <table class="statTable">
        <tr>
            <th><?=GetMessage('SUPPORT_CLIENT')?></th>
            <th><?=GetMessage('SUPPORT_BALANCE')?></th>
        </tr>   
        <?    
            $arResult["OBJECT"]->NavStart($arParams["PAGE_QUANTITY"]);
            echo $arResult["OBJECT"]->NavPrint();
            $viewedGroups = array();
            while($arClient = $arResult["OBJECT"]->NavNext(true, 'f_')){     
                $gID = $arResult["GROUPS_INFO"]["USER_GROUPS"][$arClient["USER_ID"]];
                if (in_array($gID,$viewedGroups) && $gID > 0) {continue;} else {$viewedGroups[] = $gID;}
            ?>
            <? if ($arClient["BALANCE"] > 0 ) {
                    $class= "balancePlus";
                } else if ($arClient["BALANCE"] < 0) {
                    $class= "balanceMinus";
                } else {
                    $class = "";
            }?>
            <tr class="<?=$class?>">
                <?      
                    if ($gID) {?>       
                    <td>
                        <?=$arResult["GROUPS_INFO"]["GROUPS"][$gID]["NAME"]?> <i>(<?=GetMessage("SUPPORT_GROUP")?>)</i>
                    </td>
                    <td> 
                        <?=$arResult["GROUPS_INFO"]["GROUPS"][$gID]["BALANCE"]?>
                    </td>  

                    <?} else {?>  
                    <td>                                
                        <?=$arClient["PROJECT_NAME"]?>   
                    </td>
                    <td><?=$arClient["BALANCE"]?></td>
                    <?}?>    
            </tr>
            <?}
        ?> 

        <? if ($USER->IsAdmin()) {?>
            <tr><td colspan="2"></td></tr>
            <tr>
                <td><b><?=GetMessage('SUPPORT_ALL_SUMM')?></b></td>
                <td><b><?=$arResult["SUMM"]?></b></td>
            </tr>
            <?}?>

    </table>
    <?echo $arResult["OBJECT"]->NavPrint();?>
    <?
    } else {
        echo GetMessage('SUPPORT_ATTENTION');
    }
?>