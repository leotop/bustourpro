<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    //echo "<pre>"; print_r($arParams); echo "</pre>";
    //echo "<pre>"; print_r($arResult); echo "</pre>";
    $colspan = 2;
    if ($arResult["CAN_EDIT"] == "Y") $colspan++;
    if ($arResult["CAN_DELETE"] == "Y") $colspan++;
?>
<?//arshow($arResult["ELEMENTS"])?>
<?if (strlen($arResult["MESSAGE"]) > 0):?>
    <?=ShowNote($arResult["MESSAGE"])?>
    <?endif?>   

<?if ($arParams["MAX_USER_ENTRIES"] > 0 && $arResult["ELEMENTS_COUNT"] < $arParams["MAX_USER_ENTRIES"]):?><a class="fancybox add_button" href="<?=$arParams["EDIT_URL"]?>?edit=Y"><?=GetMessage("IBLOCK_ADD_LINK_TITLE")?></a><?else:?><?=GetMessage("IBLOCK_LIST_CANT_ADD_MORE")?><?endif?>    

<table class="data-table">
    <?if($arResult["NO_USER"] == "N"):?>
        <thead>
            <tr>
                <td colspan="2"></td>
                <td colspan="3" align="center"><b>Доступные типы бронирования</b></td>
                <td colspan="2" align="center"><b>Стоимость проезда для типов бронирования:</b></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td><b>Активность</b></td>
                <td><b>Название</b></td>
                <td><b>Только проезд</b></td>
                <td><b>Только проживание</b></td>
                <td><b>Двойной тур</b></td>
                <td><b>Только проезд</b></td>
                <td><b>Только проживание</b></td>
                <td><b>Город отправления</b></td>
                <td colspan="2"></td>
            </tr>
        </thead>
        <tbody>
            <?if (count($arResult["ELEMENTS"]) > 0):?>
                <?foreach ($arResult["ELEMENTS"] as $arElement):?>
                    <tr <?if (!$arElement["PROPERTIES"]["ACTIVE"]["VALUE_ENUM"]){?> class="item_unactive"<?}?>>
                        <td align="center">
                            <input type="checkbox" <?if ($arElement["PROPERTIES"]["ACTIVE"]["VALUE_ENUM"] == "Да") {?>checked="checked" <?}?> onchange="setActive(<?=$arElement["ID"]?>)" >
                        </td>
                        <td><?=$arElement["NAME"]?></td>
                        <td><?=$arElement["PROPERTIES"]["ONLY_ROAD"]["VALUE_ENUM"]?></td>
                        <td><?=$arElement["PROPERTIES"]["ONLY_ROOM"]["VALUE_ENUM"]?></td>
                        <td><?=$arElement["PROPERTIES"]["DOUBLE_TOUR"]["VALUE_ENUM"]?></td>

                        <td><?=$arElement["PROPERTIES"]["ROAD_PRICE"]["VALUE"]?></td>     
                        <td><?=$arElement["PROPERTIES"]["ONLY_ROOM_ROAD_PRICE"]["VALUE"]?></td>
                        <td>
                            <?
                                $d_city = CIBlockElement::GetById($arElement["PROPERTIES"]["DEPARTURE_CITY"]["VALUE"][0]);
                                $arD_city = $d_city->Fetch();
                                echo $arD_city["NAME"]; 

                                //если город не задан, то выбираем тот, который по умолчанию
                                if (!$arD_city["NAME"]) {
                                    $d_city_default = CIBLockElement::GetList(array(), array("IBLOCK_CODE"=>"DEPARTURE_CITY","PROPERTY_DEFAULT_VALUE"=>"Да","PROPERTY_COMPANY"=>getCurrentCompanyID()), false, false, array("NAME"));
                                    $arD_city_default = $d_city_default->Fetch();
                                    echo $arD_city_default["NAME"];
                                } 
                            ?>
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