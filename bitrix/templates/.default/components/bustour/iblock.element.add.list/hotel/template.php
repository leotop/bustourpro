<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    //echo "<pre>"; print_r($arParams); echo "</pre>";
    //echo "<pre>"; print_r($arResult); echo "</pre>";
    $colspan = 2;
    if ($arResult["CAN_EDIT"] == "Y") $colspan++;
    if ($arResult["CAN_DELETE"] == "Y") $colspan++;
?>
<?//arshow($arResult["ELEMENTS"])?>
<script>
    function check_directions(){
        $(".city_id").attr("disabled","disabled");

        $(".direction").each(function(){
            var id = $(this).attr("id");
            if ($(this).attr("checked") == "checked") {
                $("." + id).removeAttr("disabled");
                $("." + id + " + label").css("color","");
            }                
        })
    }

    $(function(){   

        $(".city_id + label").css("color","#ddd")

        check_directions();

        $(".direction").change(function(){           
            var id = $(this).attr("id");
            if ($(this).attr("checked") == "checked") {
                $("." + id).removeAttr("disabled");
                $("." + id + " + label").css("color","");
            }
            else {
                $("." + id).removeAttr("checked");
                $("." + id).attr("disabled","disabled");
                $("." + id + " + label").css("color","#ddd");
            }
            check_directions();
        })
    })
</script>
<?if (strlen($arResult["MESSAGE"]) > 0):?>
    <?=ShowNote($arResult["MESSAGE"])?>
    <?endif?>   

<form method="get" action=""> 
    <table class="data-table">
        <tr>
            <td><b>Направление</b></td>
        </tr>
        <tr>
            <td>  
                <?
                    $directions = CIBlockElement::GetList(array("NAME"=>"ASC"), array("PROPERTY_COMPANY"=>getCurrentCompanyID(),"IBLOCK_CODE"=>"DIRECTION", "PROPERTY_ACTIVE_VALUE"=>"Да"));
                    while($arDirection = $directions->Fetch()) {?>
                    <div class="city_direction_list">
                        <input class="direction" type="checkbox" name="filter[DIRECTION][<?=$arDirection["ID"]?>]" id="direction_<?=$arDirection["ID"]?>" value="Y" <?if ($_REQUEST["filter"]["DIRECTION"][$arDirection["ID"]] == "Y"){?> checked="checked"<?}?>>
                        <label for="direction_<?=$arDirection["ID"]?>"><?=$arDirection["NAME"]?></label>
                    </div>   
                    <?}?> 
            </td>
        </tr>

        <tr>
            <td><b>Город</b></td>
        </tr>
        <tr>
            <td>  
                <?
                    $cities = CIBlockElement::GetList(array("NAME"=>"ASC"), array("PROPERTY_COMPANY"=>getCurrentCompanyID(),"IBLOCK_CODE"=>"CITY", "PROPERTY_ACTIVE_VALUE"=>"Да"), false, false, array("ID","NAME","IBLOCK_ID"));
                    while($arCity = $cities->Fetch()) {?>
                    <div class="city_direction_list ">
                        <?//для каждого города получаем набор направлений
                            $city_directions = CIBlockElement::GetProperty($arCity["IBLOCK_ID"], $arCity["ID"],Array(), Array("CODE"=>"DIRECTION"));
                            $city_directions_ids = "";
                            while($arCityDirection = $city_directions->Fetch()){
                                $city_directions_ids .= "direction_".$arCityDirection["VALUE"]." ";
                            }
                        ?>
                        <input class="city_id <?=$city_directions_ids?>" type="checkbox" name="filter[CITY][<?=$arCity["ID"]?>]" id="direction_<?=$arCity["ID"]?>" value="Y" <?if ($_REQUEST["filter"]["CITY"][$arCity["ID"]] == "Y"){?> checked="checked"<?}?>>
                        <label for="direction_<?=$arCity["ID"]?>"><?=$arCity["NAME"]?></label>
                    </div>   
                    <?}?> 
            </td>
        </tr>

        <tr>
            <td>
                <input type="hidden" name="setFilter" value="Y">
                <input type="submit" value="Показать">
                <input type="button" value="Сбросить" onclick="document.location.href='<?=$APPLICATION->GetCurPage()?>'">
            </td>
        </tr>

    </table> 
</form>   
<br>    

<?if ($arParams["MAX_USER_ENTRIES"] > 0 && $arResult["ELEMENTS_COUNT"] < $arParams["MAX_USER_ENTRIES"]):?><a class="fancybox add_button" href="<?=$arParams["EDIT_URL"]?>?edit=Y"><?=GetMessage("IBLOCK_ADD_LINK_TITLE")?></a><?else:?><?=GetMessage("IBLOCK_LIST_CANT_ADD_MORE")?><?endif?>    


<table class="data-table">
    <?if($arResult["NO_USER"] == "N"):?>
        <thead>

            <tr>
                <td><b>Активность</b></td>
                <td><b>Гостиница</b></td>
                <td><b>Город</b></td>
                <td><b>Направление</b></td>
                <td colspan="2"></td>
            </tr>
        </thead>
        <tbody>
            <?if (count($arResult["ELEMENTS"]) > 0):?>
                <?foreach ($arResult["ELEMENTS"] as $arElement):?>
                    <?//arshow($arElement["PROPERTIES"]["DIRECTION"]["VALUE"])?>
                    <tr <?if (!$arElement["PROPERTIES"]["ACTIVE"]["VALUE_ENUM"]){?> class="item_unactive"<?}?>>
                    
                        <td align="center">
                            <input type="checkbox" <?if ($arElement["PROPERTIES"]["ACTIVE"]["VALUE_ENUM"] == "Да") {?>checked="checked" <?}?> onchange="setActive(<?=$arElement["ID"]?>)" <?if (checkParentActivity($arElement["ID"]) != "Y") {?>disabled="disabled" title="чтобы активировать гостиницу, сначала активируйте город"<?}?>>
                        </td>
                        
                        <td><?=$arElement["NAME"]?></td>
                        <td>
                            <?foreach ($arElement["PROPERTIES"]["CITY"]["VALUE"] as $i=>$id) {
                                $city = CIBlockElement::GetById($id);
                                $arCity = $city->Fetch();
                                echo $arCity["NAME"];
                                if ($i < count($arElement["PROPERTIES"]["CITY"]["VALUE"]) - 1) {echo "<br>";};
                            }?>
                        </td>
                        <td>
                            <?foreach ($arElement["PROPERTIES"]["DIRECTION"]["VALUE"] as $i=>$id) {
                                $direction = CIBlockElement::GetById($id);
                                $arDirection = $direction->Fetch();
                                echo $arDirection["NAME"];
                                if ($i < count($arElement["PROPERTIES"]["DIRECTION"]["VALUE"]) - 1) {echo "<br>";};
                            }?>
                        </td>

                        <?if ($arResult["CAN_EDIT"] == "Y"):?>
                            <td><?if ($arElement["CAN_EDIT"] == "Y"):?><a class="fancybox" href="<?=$arParams["EDIT_URL"]?>?edit=Y&amp;CODE=<?=$arElement["ID"]?>"><?=GetMessage("IBLOCK_ADD_LIST_EDIT")?><?else:?>&nbsp;<?endif?></a></td>
                            <?endif?>
                        <?if ($arResult["CAN_DELETE"] == "Y"):?>
                            <td><?if ($arElement["CAN_DELETE"] == "Y"):?><a href="?delete=Y&amp;CODE=<?=$arElement["ID"]?>&amp;<?=bitrix_sessid_get()?>" onClick="return confirm('<?echo CUtil::JSEscape(str_replace("#ELEMENT_NAME#", $arElement["NAME"], GetMessage("IBLOCK_ADD_LIST_DELETE_CONFIRM")))?>')"><?=GetMessage("IBLOCK_ADD_LIST_DELETE")?></a><?else:?>&nbsp;<?endif?></td>
                            <?endif?>
                    </tr>
                    <?endforeach?>
                <?else:?>
                <tr>
                    <td<?=$colspan > 1 ? " colspan=\"".$colspan."\"" : ""?>><?=GetMessage("IBLOCK_ADD_LIST_EMPTY")?></td>
                </tr>
                <?endif?>
        </tbody>
        <?endif?>
</table>
<a class="save_button" href="javascript:void(0)" onclick="document.location.reload()">Сохранить</a>
<?if (strlen($arResult["NAV_STRING"]) > 0):?><?=$arResult["NAV_STRING"]?><?endif?>