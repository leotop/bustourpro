<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    //echo "<pre>"; print_r($arParams); echo "</pre>";
    //echo "<pre>"; print_r($arResult); echo "</pre>";
    $colspan = 2;
    if ($arResult["CAN_EDIT"] == "Y") $colspan++;
    if ($arResult["CAN_DELETE"] == "Y") $colspan++;
?>  
<script>
    function updateScheme(schemeID,directionID) {
        $.post('/ajax/update_scheme.php', {
            scheme : schemeID,
            direction: directionID
            }, function(data) {
              //alert(data);
        })
    }

</script>

<?//arshow($arResult["ELEMENTS"])?>
<?if (strlen($arResult["MESSAGE"]) > 0):?>
    <?=ShowNote($arResult["MESSAGE"])?>
    <?endif?>   
<?if ($arParams["MAX_USER_ENTRIES"] > 0 && $arResult["ELEMENTS_COUNT"] < $arParams["MAX_USER_ENTRIES"]):?><a class="fancybox add_button" href="<?=$arParams["EDIT_URL"]?>?edit=Y"><?=GetMessage("IBLOCK_ADD_LINK_TITLE")?></a><?else:?><?=GetMessage("IBLOCK_LIST_CANT_ADD_MORE")?><?endif?>    

<?
    //получаем список направления
    $directions = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"DIRECTION","PROPERTY_COMPANY"=>getCurrentCompanyID()),false, false, array("ID","NAME"));
    $directions_copy = $directions;

?>

<table class="data-table">
    <?if($arResult["NO_USER"] == "N"):?>

        <tr>
            <td rowspan="2"><b>Название</b></td>
            <td rowspan="2"><b>Возраст ОТ</b></td>
            <td rowspan="2"><b>Возраст ДО</b></td>
            <td colspan="<?=$directions->SelectedRowsCount()?>" align="center"><b>Направления</b></td>
            <td colspan="2" rowspan="2"></td>

        </tr>
        <tr>
            <?while($arDirection = $directions->Fetch()){?>
                <td><?=$arDirection["NAME"]?></td>
                <?}?>

        </tr>
        <?if (count($arResult["ELEMENTS"]) > 0):?>
            <?foreach ($arResult["ELEMENTS"] as $arElement):?>
                <tr>
                    <td>
                        <?=$arElement["NAME"]?>
                    </td>
                    <td><?=$arElement["PROPERTIES"]["MATH_AGE_FROM"]["VALUE_ENUM"]?></td>
                    <td><?=$arElement["PROPERTIES"]["MATH_AGE_TO"]["VALUE_ENUM"]?></td>
                    <?
                        //получаем список направления
                        $directions = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"DIRECTION","PROPERTY_COMPANY"=>getCurrentCompanyID()),false, false, array("ID","NAME"));
                    ?>
                    <?while($arDirection = $directions->Fetch()){?>
                        <td align="center">
                            <input type="checkbox" <?if (in_array($arDirection["ID"],$arElement["PROPERTIES"]["DIRECTION"]["VALUE"])) {?>checked="checked" <?}?> onchange="updateScheme(<?=$arElement["ID"]?>,<?=$arDirection["ID"]?>)">
                        </td>
                        <?}?>
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

        <?endif?>
</table>
<a class="save_button" href="javascript:void(0)" onclick="document.location.reload()">Сохранить</a>
<?if (strlen($arResult["NAV_STRING"]) > 0):?><?=$arResult["NAV_STRING"]?><?endif?>