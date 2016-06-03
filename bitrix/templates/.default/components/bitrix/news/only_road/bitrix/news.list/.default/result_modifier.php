<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

   

    if ($_GET["set_filter"] == "Y") {

        foreach ($arResult["ITEMS"] as $id=>$item) {   

            //проверяем дату начала 
            $tour_date_begin = explode(".",$item["PROPERTIES"]["DATE_FROM"]["VALUE"]);  //начало тура
            $tour_date_begin_label =  mktime(0,0,0,$tour_date_begin[1],$tour_date_begin[0],$tour_date_begin[2]);   //метка времени начала тура
            $tour_date_end = explode(".",$item["PROPERTIES"]["DATE_TO"]["VALUE"]);  //конец тура
            $tour_date_end_label =  mktime(0,0,0,$tour_date_end[1],$tour_date_end[0],$tour_date_end[2]);   //метка времени конца тура

            //длительность тура, переводим в дни
            $days_count = ($tour_date_end_label - $tour_date_begin_label) / 86400;

            //проверяем длительность
            if ($_GET["days_quantity"] > 0) {
                //если количество длительность тура не совпадает с выбранной, удаляем тур из выборки
                if ($days_count != $_GET["days_quantity"]){
                    unset($arResult["ITEMS"][$id]); 
                }
            } 


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
            if ($_GET["city"] > 0) {
                $arFilter = array("IBLOCK_CODE"=>"TOUR","PROPERTY_COMPANY"=>getCurrentCompanyID(),);
                $bus_direction = $item["PROPERTIES"]["BUS_DIRECTION"]["VALUE_XML_ID"];
                switch($bus_direction) {
                    case "BACK": $direction = "BUS_BACK"; $arFilter["PROPERTY_BUS_BACK"] = $item["ID"]; break;
                    case "TO": $direction = "BUS_TO"; $arFilter["PROPERTY_BUS_TO"] = $item["ID"]; break; 
                }

                //собираем города для данного автобуса
                $cities = array();


                $bus_cities = CIBlockElement::GetList(array(), $arFilter, false, false, array("PROPERTY_CITY"));
                while ($arBusCities = $bus_cities->Fetch()) {
                    $cities[] = $arBusCities["PROPERTY_CITY_VALUE"];  
                }
                $cities = array_unique($cities);

                if (!in_array($_GET["city"],$cities)){
                    unset($arResult["ITEMS"][$id]); 
                }
            }


            //проверяем количество человек
            if ($_GET["people_quantity"] > 0){ 

                //получаем ряд, ближе которого нельзя сажать при "только проезде"
                $min_row = CIBlockElement::GetList(array(), array("CODE"=>"MIN_ROW","PROPERTY_COMPANT"=>getCurrentCompanyID()),false,false,array("PROPERTY_VALUE"));
                $arMinRow = $min_row->Fetch();
                $min_row_number = $arMinRow["PROPERTY_VALUE_VALUE"];

                //перебираем схему, проверяем наличие нужного количества мест, причем не ближе ряда, указанного в переменных
                $scheme = json_decode($item["PROPERTIES"]["P_SCHEME"]["~VALUE"], true);
                $fp_count = 0;
                foreach ($scheme as $row=>$places) {
                    if ($row>=$min_row_number) {
                        $pp = array_count_values($places); //массив вида тип_места=>количество для текущего ряда
                        //суммируем общее количество свободных мест
                        $fp_count += $pp["FP"];    
                    }
                }

                //если свободных мест меньше чем нужно для человек, то удаляем автобус из выборки
                if ($fp_count < $_GET["people_quantity"]) {
                    unset($arResult["ITEMS"][$id]);  
                }


            }  

            //проверяем направление туда/обратно
            if ($_GET["bus_direction"]) {
                if ($item["PROPERTIES"]["BUS_DIRECTION"]["VALUE_XML_ID"] != $_GET["bus_direction"]){
                    unset($arResult["ITEMS"][$id]); 
                } 
            }

        }      

    }   

?>