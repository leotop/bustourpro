<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<?
    //активировать/деактивировать элемент  ID - элемента, VAL - значение которое нужно становить (да или нет), используется для рекурсивного вызова 
    function setElementActive($ID, $VAL) {
        //получаем элемент
        $element = CIBlockElement::GetById($ID);
        $arElement = $element->Fetch();        
        //проверяем инфоблок
        $iblock = CIBlock::GetById($arElement["IBLOCK_ID"]);
        $arIblock = $iblock->Fetch();

        //проверяем свойство "ACTIVE" у элемента
        $active =  CIBlockElement::GetProperty($arElement["IBLOCK_ID"], $arElement["ID"],array(), Array("CODE"=>"ACTIVE"));
        $arActive = $active->Fetch();

        //получаем значения свойства ACTIVE
        $value = CIBlockPropertyEnum::GetList(Array(), Array("CODE"=>"ACTIVE","IBLOCK_ID"=>$arElement["IBLOCK_ID"]));
        $arValue = $value->Fetch();
        // arshow($arValue);



        //если обновляется направление, город или гостиница то нужно обновить все нижележащие по иерархии объекты:
        //для направлений: города->гостиницы->номера
        //для городов: гостиницы->номера
        //для гостиниц: номера


        //рекурсивный вызов для номеров не нужен, так как у них нет потомков    
        if ($arIblock["CODE"] != "ROOM"){

            //обновляем потомков
            $arFilter = array("PROPERTY_COMPANY"=>getCurrentCompanyID());
            switch($arIblock["CODE"]) {
                case "DIRECTION": $childrenIblock = "CITY"; $arFilter["PROPERTY_DIRECTION"] = $ID; break;
                case "CITY": $childrenIblock = "HOTEL"; $arFilter["PROPERTY_CITY"] = $ID; break;
                case "HOTEL": $childrenIblock = "ROOM"; $arFilter["PROPERTY_HOTEL"] = $ID;break;
            }

            $arFilter["IBLOCK_CODE"]=$childrenIblock;

            $children = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID"));
        }




        //if ($arActive["PROPERTY_VALUE_ID"] > 0 && $VAL == "Y")  {} 

        //если значение есть, его надо удалить
        if (($arActive["PROPERTY_VALUE_ID"] > 0 && $VAL != "Y") || $VAL == "Y") {
            mysql_query("DELETE FROM `b_iblock_element_property` WHERE id=".$arActive["PROPERTY_VALUE_ID"]);     
            //рекурсивный вызов для номеров не нужен, так как у них нет потомков                 

            if ($arIblock["CODE"] != "ROOM"){ 
                while ($arChildren = $children->Fetch()) {    
                    setElementActive($arChildren["ID"],"Y");
                }      
            }


        }
        else { //если нет - то добавить
            if (($arValue["ID"] > 0 && $VAL != "N") || $VAL == "N") {
                mysql_query("INSERT INTO `b_iblock_element_property` (`ID`,`IBLOCK_PROPERTY_ID`,`IBLOCK_ELEMENT_ID`, `VALUE`,`VALUE_TYPE`,`VALUE_ENUM`) VALUES (NULL,'".$arActive["ID"]."', '".$arElement["ID"]."', '".$arValue["ID"]."',  'text', '".$arValue["ID"]."')");
                //рекурсивный вызов для номеров не нужен, так как у них нет потомков 


                //необходимость обновления (только для города)
                $update = "Y";

                //для городов нужно проверять, к скольким направлениям он принадлежит, и если у него есть хотя бы одно активное направление, то город не деактивируется
                if ($arIblock["CODE"] == "CITY") {
                    //получаем текущий город
                    $city = CIBlockElement::GetList(array(), array("ID"=>$ID), false, false, array("PROPERTY_DIRECTION"));  
                    while ($arCity = $city->Fetch()){
                        //получаем список направлений, к которым принадлежит город
                        $direction = CIBlockElement::GetList(array(), array("ID"=>$arCity["PROPERTY_DIRECTION_VALUE"]), false, false, array("PROPERTY_ACTIVE"));
                        while($arDirection = $direction->Fetch()) {
                            arshow($arDirection);
                            //если хотя бы одно направление активно, то изменять город не нужно
                            if ($arDirection["PROPERTY_ACTIVE_VALUE"] == "Да") {
                                $update = "N";  
                            }
                        }
                    }      
                }    

                if ($arIblock["CODE"] != "ROOM" && $update == "Y"){
                    while ($arChildren = $children->Fetch()) {   
                        setElementActive($arChildren["ID"],"N");
                    }
                }  
            }
        }
    }

    $val = "";

  //  if (!$_POST["ID"]) {$_POST["ID"] = 44;}

    if ($_POST["ID"]) {
        setElementActive($_POST["ID"],$val); 
    }
?>