<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();



    // $COMPANY_ID = null;
    if (!getCurrentCompanyID()) {
        $APPLICATION->AuthForm("");
    }

    $BusTo = array();
    $BusBack = array();


    //собираем автобусы, которые еще не уехали, "ТУДА"
    $busToFilter = array(
        "PROPERTY_COMPANY"=>getCurrentCompanyID(),
        "IBLOCK_CODE"=>"BUS_ON_TOUR",
        "PROPERTY_BUS_DIRECTION_VALUE" => "Туда",
        "ID"=>CIBlockElement::SubQuery("PROPERTY_BUS_TO", array("IBLOCK_CODE" => "TOUR",">=PROPERTY_DATE_FROM" => date("Y-m-d 00:00:00")))
    );
    $busSelect = array(
        "PROPERTY_BUS_DIRECTION",
        "PROPERTY_P_SCHEME",
        "NAME",
        "ID"
    );
    $busesTo = CIBlockElement::GetList(array(), $busToFilter, false, false, $busSelect);
    while($arBusTo = $busesTo->Fetch()) {
        //получаем первый тур, к которому относится автобус, чтобы получить даты начала и окончания
        $tour = CIBlockElement::GetList(array(),array("PROPERTY_BUS_TO"=>$arBusTo["ID"]), false, array("nTopCount"=>1), array("PROPERTY_DATE_FROM","PROPERTY_DATE_TO","ID","NAME"));
        $arTour = $tour->Fetch();
        $arBusTo["TOUR"] = $arTour;
        $BusTo[$arBusTo["ID"]] = $arBusTo;
    }           


    //собираем автобусы, которые еще не уехали, "ОБРАТНО"
    $busBackFilter = array(
        "PROPERTY_COMPANY"=>getCurrentCompanyID(),
        "IBLOCK_CODE"=>"BUS_ON_TOUR",
        "PROPERTY_BUS_DIRECTION_VALUE" => "Обратно",
        "ID"=>CIBlockElement::SubQuery("PROPERTY_BUS_BACK", array("IBLOCK_CODE" => "TOUR",">=PROPERTY_DATE_TO" => date("Y-m-d 00:00:00")))
    );

    $busesBack = CIBlockElement::GetList(array(), $busBackFilter, false, false, $busSelect);
    while($arBusBack = $busesBack->Fetch()) {
        //получаем первый тур, к которому относится автобус, чтобы получить даты начала и окончания
        $tour = CIBlockElement::GetList(array(),array("PROPERTY_BUS_BACK"=>$arBusBack["ID"]), false, array("nTopCount"=>1), array("PROPERTY_DATE_FROM","PROPERTY_DATE_TO","ID","NAME"));
        $arTour = $tour->Fetch();
        $arBusBack["TOUR"] = $arTour;
        $BusBack[$arBusBack["ID"]] = $arBusBack; 
    }
    
   
    
    //сливаем массивы в один
    
    $arResult["ITEMS"] = $BusTo + $BusBack;
    ksort($arResult["ITEMS"]);



    $this->IncludeComponentTemplate();
?>