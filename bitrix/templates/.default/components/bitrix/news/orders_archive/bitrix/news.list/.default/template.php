<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
    <?=$arResult["NAV_STRING"]?><br />
    <?endif;?>
<table class="data-table">
    <tr>
        <td><b>#</b></td>
        <td><b>Тип</b></td>
        <td><b>Тур</b></td>
        <td><b>Направление</b></td>
        <td><b>Город</b></td>
        <td><b>Гостиница</b></td>
        <td><b>Номер</b></td>
        <td><b>Дата отправления</b></td>
        <td><b>Дата прибытия</b></td>
        <td><b>Дата создания</b></td>
        <td><b>Туристы</b></td>       
        <td><b>Статус</b></td>
        <td><b>Агентство</b></td>
        <td><b>Стоимость</b></td>
        <td><b>К оплате</b></td>
        <td><b>Примечание</b></td>

    </tr>


    <?foreach($arResult["ITEMS"] as $arItem):?>
        <tr>

            <td><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["ID"]?></a></td>


            <td>
                <?=$arItem["PROPERTIES"]["TYPE_BOOKING"]["VALUE"]?>
            </td>

            <td>
                <?
                    if ($arItem["PROPERTIES"]["TOUR"]["VALUE"]) {$tour_id = $arItem["PROPERTIES"]["TOUR"]["VALUE"];} else {$tour_id = $arItem["PROPERTIES"]["BUS_ID"]["VALUE"];}
                    $tour = CIBlockElement::GetList(array(), array("ID"=>$tour_id), false, false, array("ID","NAME","PROPERTY_DIRECTION","PROPERTY_CITY","PROPERTY_ROOM","PROPERTY_DATE_FROM","PROPERTY_DATE_TO","PROPERTY_PRICE","PROPERTY_HOTEL"));
                    $arTour = $tour->Fetch();
                    echo $arTour["NAME"];   
                ?>
                <?//arshow($arItem["PROPERTIES"])?>
            </td>

            <td>
                <?
                    $direction = CIBLockElement::GetById($arTour["PROPERTY_DIRECTION_VALUE"]);
                    $arDirection = $direction->Fetch();
                    echo $arDirection["NAME"]; 
                ?>
            </td>

            <td>
                <?
                    $city = CIBLockElement::GetById($arTour["PROPERTY_CITY_VALUE"]);
                    $arCity = $city->Fetch();
                    echo $arCity["NAME"]; 
                ?>
            </td>

            <td>
                <?
                    $hotel = CIBLockElement::GetById($arTour["PROPERTY_HOTEL_VALUE"]);
                    $arHotel = $hotel->Fetch();
                    echo $arHotel["NAME"]; 
                ?>
            </td>

            <td>
                <?
                    $room = CIBLockElement::GetById($arTour["PROPERTY_ROOM_VALUE"]);
                    $arRoom = $room->Fetch();
                    echo $arRoom["NAME"]; 
                ?>
            </td>

            <td>
                <?=$arTour["PROPERTY_DATE_FROM_VALUE"]?>
            </td>

            <td>
                <?=$arTour["PROPERTY_DATE_TO_VALUE"]?>
            </td>

            <td>
                <?=$arItem["DATE_CREATE"];?>
            </td>

            <td>
                <?
                    $tourist = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"TOURIST","PROPERTY_ORDER"=>$arItem["ID"]), false, false, array("NAME"));
                    while($arTourist = $tourist->Fetch()) {
                        echo $arTourist["NAME"]."<br>";
                    }
                ?>
            </td>


            <td>
                <?=$arItem["PROPERTIES"]["STATUS"]["VALUE"]?>
            </td>

            <td>
                <?
                    $company = CUser::GetById($arItem["CREATED_BY"]);
                    $arCompany = $company->Fetch();
                    echo $arCompany["NAME"];
                ?>
            </td>

            <td>
                <?=$arItem["PROPERTIES"]["PRICE"]["VALUE"]?>
            </td>

            <td>

            </td>
            
            <td>
               <?=$arItem["PROPERTIES"]["NOTES"]["VALUE"]?>
            </td>

        </tr>
        <?endforeach;?>

</table>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <br /><?=$arResult["NAV_STRING"]?>
    <?endif;?>
