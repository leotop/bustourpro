<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    foreach ($arResult["ITEMS"] as $id=>$item) {
        //проверяем ID компании
        if ($item["PROPERTIES"]["COMPANY"]["VALUE"] != getCurrentCompanyID() || $item["PROPERTIES"]["NUMBER_ROOM"]["VALUE"] < 1) {
            unset($arResult["ITEMS"][$id]);   
        }   

        //проверяем активность номера
        $room = CIBlockElement::GetList(array(), array("ID"=>$item["PROPERTIES"]["ROOM"]["VALUE"],"PROPERTY_ACTIVE_VALUE"=>"Да"));
        if ($room->SelectedRowsCount() <=0) {
            unset($arResult["ITEMS"][$id]); 
        }

        //если дата тура уже прошла, удаляем его из выборки
        $tour_date_begin = explode(".",$item["PROPERTIES"]["DATE_FROM"]["VALUE"]);  //начало тура
        $tour_date_begin_label =  mktime(0,0,0,$tour_date_begin[1],$tour_date_begin[0],$tour_date_begin[2]);   //метка времени начала тура
       
        if ($tour_date_begin_label < date("U")-86400 ) {
           unset($arResult["ITEMS"][$id]);  
        }

    }

    if ($_GET["set_filter"] == "Y") {

        foreach ($arResult["ITEMS"] as $id=>$item) {   

            //проверяем дату начала 
            $tour_date_begin = explode(".",$item["PROPERTIES"]["DATE_FROM"]["VALUE"]);  //начало тура
            $tour_date_begin_label =  mktime(0,0,0,$tour_date_begin[1],$tour_date_begin[0],$tour_date_begin[2]);   //метка времени начала тура
            $tour_date_end = explode(".",$item["PROPERTIES"]["DATE_TO"]["VALUE"]);  //конец тура
            $tour_date_end_label =  mktime(0,0,0,$tour_date_end[1],$tour_date_end[0],$tour_date_end[2]);   //метка времени конца тура

            //длительность тура, переводим в дни
            $days_count = ($tour_date_end_label - $tour_date_begin_label) / 86400;


            //проверяем фильтр 
            if ($_GET["arrival_date_begin"] !="") {
                $arDay = explode(".",$_GET["arrival_date_begin"]);
                $arrivalDateBegin = mktime(0,0,0,$arDay[1],$arDay[0],$arDay[2]);  //метка времени начала промежутка даты отправления по фильтру           
            }

            if ($_GET["arrival_date_end"] !="") {
                $arDay = explode(".",$_GET["arrival_date_end"]);
                $arrivalDateEnd = mktime(0,0,0,$arDay[1],$arDay[0],$arDay[2]);  //метка времени окончания промежутка даты отправления по фильтру            
            }       



            //если задано только начало промежутка
            if ($arrivalDateBegin && !$arrivalDateEnd) {
                //если дата начала тура < даты начала по фильтру, то удаляем этот тур из выборки
                if($tour_date_begin_label < $arrivalDateBegin) {
                    unset($arResult["ITEMS"][$id]); 
                }
            }

            //если задано только окончание промежутка
            else if (!$arrivalDateBegin && $arrivalDateEnd) {
                //если дата начала тура > даты начала по фильтру, то удаляем этот тур из выборки
                if($tour_date_begin_label > $arrivalDateEnd) {
                    unset($arResult["ITEMS"][$id]); 
                }
            }

            //если задано начало и окончание промежутка
            else if ($arrivalDateBegin && $arrivalDateEnd) {
                //если дата начала тура < даты начала по фильтру, то удаляем этот тур из выборки
                if($tour_date_begin_label < $arrivalDateBegin || $tour_date_begin_label > $arrivalDateEnd) {
                    unset($arResult["ITEMS"][$id]); 
                }
            }       


            //проверяем город
            if ($_GET["city"]) {
                if (!in_array($item["PROPERTIES"]["CITY"]["VALUE"],$_GET["city"]) && $_GET["city"][0] !=0){
                    unset($arResult["ITEMS"][$id]); 
                }
            }


            //проверяем гостиницу
            if ($_GET["hotel"]) {
                if (!in_array($item["PROPERTIES"]["HOTEL"]["VALUE"],$_GET["hotel"]) && $_GET["hotel"][0] !=0){
                    unset($arResult["ITEMS"][$id]); 
                }
            }    


            //проверяем количество человек
            if ($_GET["people_quantity"] > 0 || $_GET["children_quantity"] > 0){
                $total_quantity = $_GET["people_quantity"] + $_GET["children_quantity"];
                //проверяем номер
                $room = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"ROOM","ID"=>$item["PROPERTIES"]["ROOM"]["VALUE"]), false, false, array("ID","PROPERTY_NUMBER_SEATS","PROPERTY_IS_ADD_ADDITIONAL_SEATS"));
                $arRoom = $room->Fetch();
                //если в номере недостаточно мест и возможности доп мест нет, удаляем тур из выборки
                if ($arRoom["PROPERTY_NUMBER_SEATS_VALUE"] < $total_quantity && $arRoom["PROPERTY_IS_ADD_ADDITIONAL_SEATS_VALUE"] != "Да") {
                    unset($arResult["ITEMS"][$id]);  
                }

                //если количество мест в номере меньше и при использовании доп места мест все равно не хватает, удаляем тур из выборки
                if ($arRoom["PROPERTY_NUMBER_SEATS_VALUE"] < $total_quantity && $arRoom["PROPERTY_IS_ADD_ADDITIONAL_SEATS_VALUE"] == "Да" && $arRoom["PROPERTY_NUMBER_SEATS_VALUE"] + 1 < $total_quantity) {
                    unset($arResult["ITEMS"][$id]);  
                }

                //если есть дети, то проверяем отель на возможность заселения с детьми 
                if ($_GET["children_quantity"] > 0) {
                    $hotel = CIblockElement::GetList(array(),array("ID"=>$item["PROPERTIES"]["HOTEL"]["VALUE"]), false, false, array("PROPERTY_IS_CHILDREN"));
                    $arHotel = $hotel->Fetch();
                    if ($arHotel["PROPERTY_IS_CHILDREN_VALUE"] != "Да") {
                        unset($arResult["ITEMS"][$id]); 
                    }
                } 
            }    
        }     

    }   

?>