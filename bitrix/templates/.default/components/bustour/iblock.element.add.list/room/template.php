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


<form action="" method="get">
    <table class="data-table">
        <?if($arResult["NO_USER"] == "N"):?>
            <thead>
                <tr>
                    <td colspan="9">Гостиница:
                        <select name="filter[HOTEL]">
                            <?//собираем города, гостиницы
                                $cities = CIBlockElement::GetList(array("NAME"=>"ASC"), array("IBLOCK_CODE"=>"CITY","PROPERTY_COMPANY"=>getCurrentCompanyID(), "PROPERTY_ACTIVE_VALUE"=>"Да"),false,false,array("ID","NAME"));
                                while($arCity = $cities->Fetch()) { ?>                               
                                <?//собираем гостиницы для текущего города
                                    $hotels = CIBLockElement::GetList(array("NAME"=>"ASC"), array("IBLOCK_CODE"=>"HOTEL","PROPERTY_CITY"=>$arCity["ID"], "PROPERTY_COMPANY"=>getCurrentCompanyID(), "PROPERTY_ACTIVE_VALUE"=>"Да"), false, false, array("ID","NAME"));
                                    if ($hotels->SelectedRowsCount()> 0) {?>
                                    <option disabled="disabled">-<?=$arCity["NAME"]?></option>
                                    <?
                                        while($arHotel = $hotels->Fetch()) {?>
                                        <option value="<?=$arHotel["ID"]?>" <?if ($_REQUEST["filter"]["HOTEL"] == $arHotel["ID"]){?> selected="selected"<?}?>><?=$arHotel["NAME"]?></option>   
                                        <?}
                                }?>  
                                <?}?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td colspan="9">
                        <input type="hidden" name="setFilter" value="Y">
                        <input type="submit" value="Показать">
                        <input type="button" value="Сбросить" onclick="document.location.href='<?=$APPLICATION->GetCurPage()?>'">
                    </td>
                </tr>

                <tr>
                    <td><b>Активность</b></td>
                    <td><b>Номер</b></td>
                    <td><b>Мест в номере</b></td>
                    <td><b>Количество номеров данного типа</b></td>
                    <td><b>Гостиница</b></td>
                    <td><b>Город</b></td>
                    <td><b>Направление</b></td>
                    <td colspan="2"></td>
                </tr>
            </thead>
            <tbody>
                <?if (count($arResult["ELEMENTS"]) > 0):?>
                    <?foreach ($arResult["ELEMENTS"] as $arElement):?>
                        <tr <?if (!$arElement["PROPERTIES"]["ACTIVE"]["VALUE_ENUM"]){?> class="item_unactive"<?}?> >  
                            <td align="center">
                                <input type="checkbox" <?if ($arElement["PROPERTIES"]["ACTIVE"]["VALUE_ENUM"] == "Да") {?>checked="checked" <?}?> onchange="setActive(<?=$arElement["ID"]?>)" <?if (checkParentActivity($arElement["ID"]) != "Y") {?>disabled="disabled" title="чтобы активировать номер, сначала активируйте гостиницу"<?}?>>
                            </td>

                            <td><?=$arElement["NAME"]?></td>
                            <td>
                                <?=$arElement["PROPERTIES"]["NUMBER_SEATS"]["VALUE"]?>
                            </td>

                            <td>
                                <?=$arElement["PROPERTIES"]["NUMBER_ROOM"]["VALUE"]?>
                            </td>

                            <td>
                                <?foreach ($arElement["PROPERTIES"]["HOTEL"]["VALUE"] as $i=>$id) {
                                    $hotel = CIBlockElement::GetById($id);
                                    $arHotel = $hotel->Fetch();
                                    echo $arHotel["NAME"];
                                    if ($i < count($arElement["PROPERTIES"]["HOTEL"]["VALUE"]) - 1) {echo "<br>";};
                                }?>
                            </td>

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
</form>
<?if (strlen($arResult["NAV_STRING"]) > 0):?><?=$arResult["NAV_STRING"]?><?endif?>