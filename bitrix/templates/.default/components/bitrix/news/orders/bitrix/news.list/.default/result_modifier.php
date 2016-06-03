<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

    //���������, ����� ������� ���� ����� ������ ������� � �����
    $daysCount = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"VARIABLES","PROPERTY_COMPANY"=>getCurrentCompanyID(),"CODE"=>"DAYS_COUNT_FOR_ARCHIVE"),false, false, array("PROPERTY_VALUE"));
    $arDaysCount = $daysCount->Fetch();
    if (!$arDaysCount["PROPERTY_VALUE_VALUE"]) {
        $arDaysCount["PROPERTY_VALUE_VALUE"] = 1;
    }


    foreach ($arResult["ITEMS"] as $id=>$arItem) {
        //��������� ID ��������
        if ($arItem["PROPERTIES"]["COMPANY"]["VALUE"] != getCurrentCompanyID()) {
            unset($arResult["ITEMS"][$id]);   
        }

       //��������� ���� ����/��������
        if ($arItem["PROPERTIES"]["TOUR"]["VALUE"]) {
            //��� ����
            $tour_id = $arItem["PROPERTIES"]["TOUR"]["VALUE"];
        } else {
            //��� �������� ������� �������� ���, � �������� �� ���������
            $tour_id = CIBlockElement::GetList(array(),array("PROPERTY_BUS_TO"=>array($arItem["PROPERTIES"]["BUS_ID"]["VALUE"]),"PROPERTY_BUS_BACK"=>array($arItem["PROPERTIES"]["BUS_ID"]["VALUE"])), false, false, array("PROPERTY_DATE_FROM"));
            $arTour_id = $tour_id->Fetch();                    
            $arTour["PROPERTY_DATE_FROM_VALUE"] = $arTour_id["PROPERTY_DATE_FROM_VALUE"];
        }
        $tour = CIBlockElement::GetList(array(), array("ID"=>$tour_id), false, false, array("ID","NAME","PROPERTY_DIRECTION","PROPERTY_CITY","PROPERTY_ROOM","PROPERTY_DATE_FROM","PROPERTY_DATE_TO","PROPERTY_PRICE","PROPERTY_HOTEL"));
        $arTour = $tour->Fetch();
        
        //�������� ����� ������� ���� �����������
        $date_from = explode(".",$arTour["PROPERTY_DATE_FROM_VALUE"]);
        $dateFrom = mktime(0,0,0,$date_from[1],$date_from[0],$date_from[2]);   

        //���� ���� ������ ����, ���������� � ������ ��� ������, ������� ����� �� �������
        if ($dateFrom < date("U") - $arDaysCount["PROPERTY_VALUE_VALUE"] * 86400 ) {
            unset($arResult["ITEMS"][$id]); 
        }

    }



    if ($_GET["set_filter"] == "Y") {



    }   

?>