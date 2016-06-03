<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
  /*  foreach ($arResult["ITEMS"] as $id=>$arItem) {
        //проверяем ID компании
        if ($arItem["PROPERTIES"]["COMPANY"]["VALUE"] != getCurrentCompanyID()) {
            unset($arResult["ITEMS"][$id]);   
        }

        //проверяем дату тура/автобуса
        if ($arItem["PROPERTIES"]["TOUR"]["VALUE"]) {
            //для тура
            $tour_id = $arItem["PROPERTIES"]["TOUR"]["VALUE"];
        } else {
            //для автобуса сначала получаем тур, к которому он относится
            $tour_id = CIBlockElement::GetList(array(),array("PROPERTY_BUS_TO"=>array($arItem["PROPERTIES"]["BUS_ID"]["VALUE"]),"PROPERTY_BUS_BACK"=>array($arItem["PROPERTIES"]["BUS_ID"]["VALUE"])), false, false, array("PROPERTY_DATE_FROM"));
            $arTour_id = $tour_id->Fetch();                    
            $arTour["PROPERTY_DATE_FROM_VALUE"] = $arTour_id["PROPERTY_DATE_FROM_VALUE"];
        }
        $tour = CIBlockElement::GetList(array(), array("ID"=>$tour_id), false, false, array("ID","NAME","PROPERTY_DIRECTION","PROPERTY_CITY","PROPERTY_ROOM","PROPERTY_DATE_FROM","PROPERTY_DATE_TO","PROPERTY_PRICE","PROPERTY_HOTEL"));
        $arTour = $tour->Fetch();
        
        //получаем метку времени даты отправления
        $date_from = explode(".",$arTour["PROPERTY_DATE_FROM_VALUE"]);
        $dateFrom = mktime(0,0,0,$date_from[1],$date_from[0],$date_from[2]); 
        
        //если дата заказа тура, указанного в заказе еще не наступила, убираем заказ из выборки
        if ($dateFrom > date("U") ) {
             unset($arResult["ITEMS"][$id]); 
        }
        
    }



    if ($_GET["set_filter"] == "Y") {



    }   
      */
?>