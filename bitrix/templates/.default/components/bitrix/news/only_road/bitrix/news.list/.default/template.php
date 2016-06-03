<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
    <?=$arResult["NAV_STRING"]?><br />
    <?endif;?>
<table class="data-table">
    <tr>
        <td><b>Название</b></td>
        <td><b>Дата отправления</b></td>
        <td><b>Дата прибытия</b></td>
        <td><b>Направление</b></td>
        <td><b>Город</b></td>
        <td><b>Цена</b></td> 
        <td></td>      
    </tr>

    <?foreach($arResult["ITEMS"] as $arItem):?>

        <?
            //  arshow($arItem["PROPERTIES"]);
            //получаем тур, к которому принадлежит автобус
            $bus_direction = $arItem["PROPERTIES"]["BUS_DIRECTION"]["VALUE_XML_ID"];
            $arFilter = array("IBLOCK_CODE"=>"TOUR", "PROPERTY_COMPANY"=>getCurrentCompanyID());
            switch($bus_direction) {
                case "BACK": $direction = "BUS_BACK"; $arFilter["PROPERTY_BUS_BACK"] = $arItem["ID"]; break;
                case "TO": $direction = "BUS_TO"; $arFilter["PROPERTY_BUS_TO"] = $arItem["ID"]; break; 
            }   

            $tour = CIBlockElement::GetList(array(),$arFilter, false, false, array("ID","NAME","PROPERTY_DIRECTION","PROPERTY_CITY","PROPERTY_DATE_FROM","PROPERTY_DATE_TO"));
            $arTour = $tour->Fetch(); 
        ?>

        <tr>
            <td>  
                <b><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></b>
            </td>

            <td>
                <?if ($bus_direction == "TO"){echo $arTour["PROPERTY_DATE_FROM_VALUE"];}?>
            </td>

            <td>
                <?if ($bus_direction == "BACK"){echo $arTour["PROPERTY_DATE_TO_VALUE"];}?>
            </td>

            <td>             
                <?=get_iblock_element_name($arTour["PROPERTY_DIRECTION_VALUE"]);?>
            </td>

            <td>
                <?//собираем города для данного автобуса
                    $cities = array();
                    $company = getCurrentCompanyID();  
                    $arFilter = array("IBLOCK_CODE"=>"TOUR","PROPERTY_COMPANY"=>getCurrentCompanyID());
                    switch($bus_direction) {
                        case "TO": $arFilter["PROPERTY_BUS_TO"] = $arItem["ID"]; break;
                        case "BACK": $arFilter["PROPERTY_BUS_BACK"] = $arItem["ID"]; break; 
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


            <td>
                <?
                    //получаем стоимость только проезда для текущего направления
                    $direction = CIBlockElement::GetList(array(), array("ID"=>$arTour["PROPERTY_DIRECTION_VALUE"]), false, false, array("PROPERTY_ROAD_PRICE"));
                    $arDirection = $direction->Fetch();
                    echo $arDirection["PROPERTY_ROAD_PRICE_VALUE"];
                ?>    
            </td>

            <td>
                <b><a href="<?=$arItem["DETAIL_PAGE_URL"]?>">бронировать</a></b>
            </td>



        </tr>
        <?endforeach;?>
</table>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <br /><?=$arResult["NAV_STRING"]?>
    <?endif;?>
