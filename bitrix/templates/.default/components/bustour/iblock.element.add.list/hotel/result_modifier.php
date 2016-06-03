<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
    //получаем направления для каждой гостиницы
    foreach ($arResult["ELEMENTS"] as &$arElement) {
        if (is_array($arElement["PROPERTIES"]["CITY"]["VALUE"]) && count($arElement["PROPERTIES"]["CITY"]["VALUE"]) > 0 && $arElement["PROPERTIES"]["CITY"]["VALUE"][0] > 0) {
            $directions = CIBlockElement::GetList(array(), array("ID"=>$arElement["PROPERTIES"]["CITY"]["VALUE"][0]), false, false, array("PROPERTY_DIRECTION"));
            while ($arDirection = $directions->Fetch()) {
                $arElement["PROPERTIES"]["DIRECTION"]["VALUE"][] = $arDirection["PROPERTY_DIRECTION_VALUE"]; 
            }
        }
    }

?>