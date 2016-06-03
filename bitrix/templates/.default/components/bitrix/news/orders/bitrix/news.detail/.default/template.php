<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
    //получаем данные о туре
    $arSelect = array(
        "ID",
        "NAME",
        "PROPERTY_COMPANY",
        "PROPERTY_DIRECTION",
        "PROPERTY_CITY",
        "PROPERTY_ROOM",
        "PROPERTY_DATE_FROM",
        "PROPERTY_DATE_TO",
        "PROPERTY_PRICE",
        "PROPERTY_DISCOUNT",  
        "PROPERTY_DISCONT_ON_ROOM_AND_DATE_TOUR",
        "PROPERTY_HOTEL",
        "PROPERTY_PRICE_ADDITIONAL_SEATS",
        "PROPERTY_BUS_TO",
    );

    $arFilter = array("IBLOCK_CODE"=>"TOUR","ID"=>$arResult["PROPERTIES"]["TOUR"]["VALUE"]);

    $tour = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    $arTour = $tour->Fetch();
    ////////////////////////

    $order = array();

    //получаем информацию о направлении
    $direction = CIBlockElement::GetById($arTour["PROPERTY_DIRECTION_VALUE"]);
    $arDirection = $direction->Fetch();
    $order[] = array("NAME"=>"Направление", "VALUE"=>$arDirection["NAME"]);

    //получаем информацию о городе
    $city = CIBlockElement::GetById($arTour["PROPERTY_CITY_VALUE"]);
    $arCity = $city->Fetch();
    $order[] = array("NAME"=>"Город", "VALUE"=>$arCity["NAME"]);

    //получаем информацию о гостинице
    $hotel = CIBlockElement::GetById($arTour["PROPERTY_HOTEL_VALUE"]);
    $arHotel = $hotel->Fetch();
    $order[] = array("NAME"=>"Гостиница", "VALUE"=>$arHotel["NAME"]);

    //получаем информацию о гостинице
    $room = CIBlockElement::GetById($arTour["PROPERTY_ROOM_VALUE"]);
    $arRoom = $room->Fetch();
    $order[] = array("NAME"=>"Номер", "VALUE"=>$arRoom["NAME"]);

    //получаем информацию об автобусе для данного тура BUS_ON_TOUR
    $bus = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"BUS_ON_TOUR","ID"=>$arTour["PROPERTY_BUS_TO_VALUE"]), false, false, array("NAME","ID","PROPERTY_P_SCHEME"));
    $arBus = $bus->Fetch();

    //получаем информацию о туристах
    $touristSelect = array("NAME","ID","PROPERTY_PASSPORT","PROPERTY_PHONE","PROPERTY_PLACE","PROPERTY_BIRTHDAY","PROPERTY_PRICE");
    $tourist = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"TOURIST","PROPERTY_ORDER"=>$arResult["ID"]), false, false, $touristSelect);

?>
<?foreach ($order as $prop){ ?>
    <p><b><?=$prop["NAME"]?>:</b> <?=$prop["VALUE"]?></p>
    <?}?>
<p><b>Туристы</b>:</p>
<table class="data-table">
    <tr>
        <th>#</th>
        <th>ФИО</th>
        <th>Пасспорт</th>
        <th>Телефон</th> 
        <th>Дата рождения</th>
        <th>Стоимость тура</th>
    </tr>

    <?
        $places = array();
        $i = 1;
        while ($arTourist = $tourist->Fetch()){
            $places[] = $arTourist["PROPERTY_PLACE_VALUE"];
        ?>
        <tr> 
            <td><?=$i?></td>
            <td><?=$arTourist["NAME"]?></td>
            <td><?=$arTourist["PROPERTY_PASSPORT_VALUE"]?></td>
            <td><?=$arTourist["PROPERTY_PHONE_VALUE"]?></td>
            <td><?=$arTourist["PROPERTY_BIRTHDAY_VALUE"]?></td>
            <td><?=$arTourist["PROPERTY_PRICE_VALUE"]?></td>    
        </tr>
        <?$i++;}?>
    <tr>
        <td colspan="5">Общая стоимость:</td>
        <td><?=$arResult["PROPERTIES"]["PRICE"]["VALUE"]?></td>
    </tr>
    <tr>
        <td colspan="6">Примечание</td>
    </tr>
    <tr>
        <td colspan="6">
            <?=$arResult["PROPERTIES"]["NOTES"]["VALUE"]?>
        </td>
    </tr>
</table>
<?
    //обрабатываем схему 
    //преобразуем схему в ассоциативный массив
    $scheme = json_decode($arBus["PROPERTY_P_SCHEME_VALUE"], true);
    //перебираем схему, чтобы убрать с нее все лишние места, и оставить только места текущего заказа
    foreach($scheme as $n=>$val) {
        foreach ($val as $i=>$place){
            if (!in_array($i,$places) && $place == "PP") {
                $scheme[$n][$i] = "FP"; 
            }
        }
    }
    //кодируем схему обратно    
    $scheme_new = json_encode($scheme);   
?>

<div class="bookingBusSchemeTop">
    <div class="bookingBusScheme">
        <div class="twoBus">                        
            <div class="busTable_0">
                <?get_bus_scheme($scheme_new); ?>                          
            </div>  
        </div>          
    </div>
</div>
   