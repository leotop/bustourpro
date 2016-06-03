<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
   //проверяем город
    
        foreach ($arResult["ITEMS"] as $id=>$arItem) {

            $bus_direction = $arItem["PROPERTIES"]["BUS_DIRECTION"]["VALUE_XML_ID"];
            //собираем города для данного автобуса
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
           
           if( ( !in_array($_GET["city"],$cities) && $_GET["set_filter"] == "Y" && $_GET["city"])|| count($cities) == 0) {
               unset($arResult["ITEMS"][$id]);
           } 
        }

    
?>