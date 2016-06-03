<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?foreach ($arResult["DISPLAY_PROPERTIES"] as $prop) {
    // arshow($prop);
}?>
<script>  
    controllerAction = "tour/update"; 
</script>
<?$APPLICATION->SetTitle("Просмотр тура \"".$arResult["NAME"]."\"")?>
<?//arshow($arResult)?>   
<h4>Схема для ознакомления с рассадкой. <br> Для бронирования нажмите на один из вариантов слева над датой отправления!</h4>
<table class="data-table-second" width="500px" style="float:left; margin: 0 30px 0 0">
    <tr>
        <td colspan="2">
            <b><a href="/order-management/order_make/?TOUR_ID=<?=$arResult["ID"]?>">Стандартное бронирование</a></b>
            <?//проверяем направление на возможность "только проживания" и "двойного тура"
                $direction = CIBlockElement::GetList(array(),array("ID"=>$arResult["PROPERTIES"]["DIRECTION"]["VALUE"]), false, false, array("PROPERTY_ONLY_ROOM","PROPERTY_DOUBLE_TOUR", "NAME"));
                $arDirection = $direction->Fetch();
            ?>
            <?if ($arDirection["PROPERTY_ONLY_ROOM_VALUE"] == "Да"){?>
                <span class="booking_types_separator"> | </span>
                <b><a href="/order-management/order_make/?TOUR_ID=<?=$arResult["ID"]?>&TYPE=ONLY_ROOM">Только проживание</a></b>
                <?}?> 

            <?if ($arDirection["PROPERTY_DOUBLE_TOUR_VALUE"] == "Да" && checkDoubleTour($arResult["ID"],"Y") > 0){?>
                <span class="booking_types_separator"> | </span>
                <b><a href="/order-management/order_make/?TOUR_ID=<?=$arResult["ID"]?>&TYPE=DOUBLE_TOUR">Двойной тур</a></b>
                <?}?>   

        </td>   
    </tr>
    
    
    <?if ($arResult["DISPLAY_PROPERTIES"]["INFO"]["VALUE"]){?>
        <tr>
            <td><b>ВАЖНАЯ ИНФОРМАЦИЯ</b></td>
            <td style="background: #FCC500;"><?=$arResult["DISPLAY_PROPERTIES"]["INFO"]["VALUE"]?></td>
        </tr>   
        <?}?>
        
        
    <tr>
        <td width="200">Отправление</td>
        <td><?=$arResult["PROPERTIES"]["DATE_FROM"]["VALUE"]?></td>
    </tr>

    <tr>
        <td>Прибытие</td>
        <td><?=$arResult["PROPERTIES"]["DATE_TO"]["VALUE"]?></td>
    </tr>

    <tr>
        <td>Направление</td>
        <td><?=$arDirection["NAME"]?></td>
    </tr>

    <tr>
        <td>Город</td>
        <td><?=get_iblock_element_name($arResult["PROPERTIES"]["CITY"]["VALUE"])?></td>
    </tr>

    <tr>
        <td>Гостиница</td>
        <td><?=get_iblock_element_name($arResult["PROPERTIES"]["HOTEL"]["VALUE"])?></td>
    </tr>

    <tr>
        <td>Номер</td>
        <td>
            <?
                $room = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"ROOM","ID"=>$arResult["PROPERTIES"]["ROOM"]["VALUE"]),false, false, array("ID","NAME","PROPERTY_NUMBER_SEATS", "PROPERTY_IS_ADD_ADDITIONAL_SEATS"));
                $arRoom = $room->Fetch();
            ?>
            <?=$arRoom["NAME"]?>
        </td>
    </tr>

    <tr>
        <td>Количество мест в номере</td>
        <td><?=$arRoom["PROPERTY_NUMBER_SEATS_VALUE"]?></td>
    </tr>

    <tr>
        <td>Цена</td>
        <td><?=$arResult["PROPERTIES"]["PRICE"]["VALUE"]?></td>
    </tr> 

    <tr>
        <td>Дополнительное место</td>
        <td>
            <?if (!$arRoom["PROPERTY_IS_ADD_ADDITIONAL_SEATS_VALUE"]){
                $arRoom["PROPERTY_IS_ADD_ADDITIONAL_SEATS_VALUE"] = "Нет";
            }?>
            <?=$arRoom["PROPERTY_IS_ADD_ADDITIONAL_SEATS_VALUE"]?>
        </td>
    </tr>   

    <?if ($arResult["DISPLAY_PROPERTIES"]["PRICE_ADDITIONAL_SEATS"]["VALUE"] > 0){?>
        <tr>
            <td>Цена за доп место</td>
            <td><?=$arResult["DISPLAY_PROPERTIES"]["PRICE_ADDITIONAL_SEATS"]["VALUE"]?></td>
        </tr>   
        <?}?>
        
          

</table>
<?//arshow($arResult)?>
<div >
    <?
        //получаем ID инфоблока со схемами
        $bus_iblock = CIBlock::GetList(array(),array("CODE"=>"BUS_ON_TOUR"));
        $arBusIblock = $bus_iblock->Fetch();

        //получаем схему автобуса туда
        $scheme = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>$arBusIblock["ID"],"ID"=>$arResult["PROPERTIES"]["BUS_TO"]["VALUE"]), false, false, array("PROPERTY_P_SCHEME","ID"));
        $arScheme = $scheme->Fetch(); 
        $bus_scheme_to = $arScheme["PROPERTY_P_SCHEME_VALUE"];

        //получаем схему автобуса обратно
        $scheme = CIBlockElement::GetList(array(), array("IBLOCK_ID"=>$arBusIblock["ID"],"ID"=>$arResult["PROPERTIES"]["BUS_BACK"]["VALUE"]), false, false, array("PROPERTY_P_SCHEME","ID"));
        $arScheme = $scheme->Fetch(); 
        $bus_scheme_back = $arScheme["PROPERTY_P_SCHEME_VALUE"];

        $scheme_to = json_decode($bus_scheme_to, true);
        $scheme_back = json_decode($bus_scheme_back, true);

        $view_scheme = array(); //объединенная схема
        foreach ($scheme_to as $row=>$place) {
            foreach ($place as $number=>$status) {
                if ($status == "PP" or $scheme_back[$row][$number] == "PP") { //если место занято в автобусе "туда" или "обратно", то оно помечается как занятое
                    $view_scheme[$row][$number] = "PP";  
                } 
                else {
                    $view_scheme[$row][$number] = $status;  
                }
            }
        }

        //схема рассадки с учетом занятых мест в обоих автобусах (туда и обратно)
        $bus_scheme =  json_encode($view_scheme);

        get_bus_scheme($bus_scheme);
    ?>      

</div>            
