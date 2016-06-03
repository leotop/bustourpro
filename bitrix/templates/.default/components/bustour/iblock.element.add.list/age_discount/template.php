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
                <td><b>Название</b></td>
                <td><b>Величина скидки</b></td>
                <td><b>Единицы измерения</b></td>
                <td><b>Тип</b></td>
                <td colspan="2"></td>
            </tr>
        </thead>
        <tbody>
            <?if (count($arResult["ELEMENTS"]) > 0):?>
                <?foreach ($arResult["ELEMENTS"] as $arElement):?>
                    <tr>
                        <td><!--a href="detail.php?CODE=<?=$arElement["ID"]?>"--><?=$arElement["NAME"]?><!--/a--></td>

                        <td>
                            <?=$arElement["PROPERTIES"]["DISCOUNT"]["VALUE"]?>
                        </td>

                        <td>
                            <?=$arElement["PROPERTIES"]["ED_IZM"]["VALUE_ENUM"]?>
                        </td>

                        <td>
                            <?=$arElement["PROPERTIES"]["TYPE"]["VALUE_ENUM"]?>
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
<?if (strlen($arResult["NAV_STRING"]) > 0):?><?=$arResult["NAV_STRING"]?><?endif?>