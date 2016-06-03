<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
    <?=$arResult["NAV_STRING"]?><br />
    <?endif;?>
<table class="data-table">
    <tr>
        <td><b>Дата отправления</b></td>
        <td><b>Дата прибытия</b></td>
        <?/*
            <td><b>Направление</b></td>
            */?>
        <td><b>Город</b></td>
        <td><b>Гостиница</b></td>
        <td><b>Номер</b></td>
        <td><b>Заселение с детьми</b></td>
        <td><b>Доступно номеров</b></td>
        <td><b>Цена</b></td>
        <td><b>Доп. место</b></td>
        <td><b>Цена доп. места</b></td>
        <td></td>
    </tr>

    <?foreach($arResult["ITEMS"] as $arItem):?>
        <tr <?if ($arItem["PROPERTIES"]["SPECIAL_OFFER"]["VALUE_ENUM"] == "Да"){?> class="tour_spec_offer"<?}?>>

            <td><?=$arItem["PROPERTIES"]["DATE_FROM"]["VALUE"]?></td>

            <td><?=$arItem["PROPERTIES"]["DATE_TO"]["VALUE"]?></td>

            <?/*
                <td>             
                    <?$direction = CIBLockElement::GetById($arItem["PROPERTIES"]["DIRECTION"]["VALUE"]);
                        $arDirection = $direction->Fetch();
                        echo $arDirection["NAME"];?>
                </td>
                <*/?>

            <td>
                <?$city = CIBLockElement::GetById($arItem["PROPERTIES"]["CITY"]["VALUE"]);
                    $arCity = $city->Fetch();
                    echo $arCity["NAME"];?>
            </td>

            <td>
                <?$hotel = CIBLockElement::GetList(array(), array("ID"=>$arItem["PROPERTIES"]["HOTEL"]["VALUE"]), false, false, array("NAME", "PROPERTY_IS_CHILDREN"));
                    $arHotel = $hotel->Fetch();
                    echo $arHotel["NAME"];?>
            </td>

            <td>
                <?$room = CIBLockElement::GetList(array(),array("ID"=>$arItem["PROPERTIES"]["ROOM"]["VALUE"]),false, false, array("ID","NAME","PROPERTY_IS_ADD_ADDITIONAL_SEATS"));
                    $arRoom = $room->Fetch();
                    echo $arRoom["NAME"];?>
            </td>

            <td>
                <?if($arHotel["PROPERTY_IS_CHILDREN_VALUE"]) {echo $arHotel["PROPERTY_IS_CHILDREN_VALUE"];} else {echo "Нет";}?>
            </td>

            <td><?=$arItem["PROPERTIES"]["NUMBER_ROOM"]["VALUE"]?></td>

            <td><?=$arItem["PROPERTIES"]["PRICE"]["VALUE"]?></td>

            <td><?=$arRoom["PROPERTY_IS_ADD_ADDITIONAL_SEATS_VALUE"]?></td>

            <td><?if($arItem["PROPERTIES"]["PRICE_ADDITIONAL_SEATS"]["VALUE"]> 0){echo $arItem["PROPERTIES"]["PRICE_ADDITIONAL_SEATS"]["VALUE"];}?></td>

            <td><b><a href="<?=$arItem["DETAIL_PAGE_URL"]?>">Бронировать</a></b></td>
        </tr>
        <?endforeach;?>
</table>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <br /><?=$arResult["NAV_STRING"]?>
    <?endif;?>
