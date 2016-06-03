<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
    //получаем город для каждой гостиницы
    foreach ($arResult["ELEMENTS"] as &$arElement) {
        if ($arElement["PROPERTIES"]["HOTEL"]["VALUE"][0] > 0) {
            $directions = CIBlockElement::GetList(array(), array("ID"=>$arElement["PROPERTIES"]["HOTEL"]["VALUE"][0]), false, false, array("PROPERTY_CITY"));
            while ($arDirection = $directions->Fetch()) {
                $arElement["PROPERTIES"]["CITY"]["VALUE"][] = $arDirection["PROPERTY_CITY_VALUE"]; 
            }
        }
    }


    //получаем направления для каждого номера
    foreach ($arResult["ELEMENTS"] as &$arElement) {
        if ($arElement["PROPERTIES"]["CITY"]["VALUE"][0] > 0) {
            $directions = CIBlockElement::GetList(array(), array("ID"=>$arElement["PROPERTIES"]["CITY"]["VALUE"][0]), false, false, array("PROPERTY_DIRECTION"));
            while ($arDirection = $directions->Fetch()) {
                $arElement["PROPERTIES"]["DIRECTION"]["VALUE"][] = $arDirection["PROPERTY_DIRECTION_VALUE"]; 
            }
        }
    }

?>