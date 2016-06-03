<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?foreach ($arResult["DISPLAY_PROPERTIES"] as $prop) {
    // arshow($prop);
}?>
<script>  
    controllerAction = "tour/update"; 
</script>

<?  //  arshow($arItem["PROPERTIES"]);
    //получаем тур, к которому принадлежит автобус
    $bus_direction = $arResult["PROPERTIES"]["BUS_DIRECTION"]["VALUE_XML_ID"];
    $arFilter = array("IBLOCK_CODE"=>"TOUR", "PROPERTY_COMPANY"=>getCurrentCompanyID());
    switch($bus_direction) {
        case "BACK": $direction = "BUS_BACK"; $arFilter["PROPERTY_BUS_BACK"] = $arResult["ID"]; break;
        case "TO": $direction = "BUS_TO"; $arFilter["PROPERTY_BUS_TO"] = $arResult["ID"]; break; 
    }   

    $tour = CIBlockElement::GetList(array(),$arFilter, false, false, array("ID","NAME","PROPERTY_DIRECTION","PROPERTY_CITY","PROPERTY_DATE_FROM","PROPERTY_DATE_TO"));
    $arTour = $tour->Fetch(); 
?>

<?$APPLICATION->SetTitle("Автобус \"".$arResult["NAME"]."\"")?>
<table class="data-table-second" width="500px" style="float:left">
    <tr>
        <td colspan="2">
            <b><a href="/order-management/order_make/?TOUR_ID=<?=$arResult["ID"]?>&TYPE=ONLY_ROAD">Бронировать в одну сторону</a></b>
            <span class="booking_types_separator"> | </span> 
            <b><a href="/order-management/order_make/?TOUR_ID=<?=$arResult["ID"]?>&TYPE=DOUBLE_ROAD">Бронировать туда и обратно</a></b>
        </td>    
    </tr>

    <tr>
        <td width="200">Отправление</td>
        <td>
            <?if ($bus_direction == "TO"){echo $arTour["PROPERTY_DATE_FROM_VALUE"];}?>
        </td>
    </tr>

    <tr>
        <td>Прибытие</td>
        <td>
            <?if ($bus_direction == "BACK"){echo $arTour["PROPERTY_DATE_TO_VALUE"];}?>
        </td>
    </tr>

    <tr>
        <td>Направление</td>
        <td>
            <?=get_iblock_element_name($arTour["PROPERTY_DIRECTION_VALUE"]);?>
        </td>
    </tr>

    <tr>
        <td>Город</td>
        <td>
            <?//собираем города для данного автобуса
                $cities = array();
                $company = getCurrentCompanyID();  
                $arFilter = array("IBLOCK_CODE"=>"TOUR","PROPERTY_COMPANY"=>getCurrentCompanyID());
                switch($bus_direction) {
                    case "TO": $arFilter["PROPERTY_BUS_TO"] = $arResult["ID"]; break;
                    case "BACK": $arFilter["PROPERTY_BUS_BACK"] = $arResult["ID"]; break; 
                }
                $bus_cities = CIBlockElement::GetList(array(), $arFilter, false, false, array("PROPERTY_CITY"));
                while ($arBusCities = $bus_cities->Fetch()) {
                    $cities[] = $arBusCities["PROPERTY_CITY_VALUE"];  
                }
                $cities = array_unique($cities);
            ?>
            <?
                foreach ($cities as $city) {
                    echo get_iblock_element_name($city),"<br>";
                }
            ?>
        </td>
    </tr>   

    <tr>
        <td>Цена</td>
        <td>
            <?  //получаем стоимость только проезда для текущего направления
                $direction = CIBlockElement::GetList(array(), array("ID"=>$arTour["PROPERTY_DIRECTION_VALUE"]), false, false, array("PROPERTY_ROAD_PRICE"));
                $arDirection = $direction->Fetch();
                echo $arDirection["PROPERTY_ROAD_PRICE_VALUE"];
            ?>   
        </td>
    </tr>

  
</table>
     <div >
<? 
    get_bus_scheme($arResult["PROPERTIES"]["P_SCHEME"]["~VALUE"]);
?>
</div>
