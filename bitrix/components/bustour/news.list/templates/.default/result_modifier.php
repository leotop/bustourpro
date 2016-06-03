<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    foreach ($arResult["ITEMS"] as $id=>$item) {
     
        //проверяем активность номера
        $room = CIBlockElement::GetList(array(), array("ID"=>$item["PROPERTIES"]["ROOM"]["VALUE"],"PROPERTY_ACTIVE_VALUE"=>"Да"));
        if ($room->SelectedRowsCount() <=0) {
            unset($arResult["ITEMS"][$id]); 
        }
      

    }

    if ($_GET["set_filter"] == "Y") {

        foreach ($arResult["ITEMS"] as $id=>$item) { 

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