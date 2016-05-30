<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<?
    //получаем нужную схему
    if ($_POST["scheme"] && $_POST["direction"]) {
        $arSelect = array(
            "PROPERTY_COMPANY",
            "PROPERTY_MATH_AGE_FROM",
            "PROPERTY_MATH_AGE_TO",
            "PROPERTY_MATH_TOUR",
            "PROPERTY_MATH_TOUR_DISCOUNT",
            "PROPERTY_MATH_ROAD",
            "PROPERTY_MATH_ROAD_DISCOUNT",
            "PROPERTY_DIRECTION",
            "NAME",
            "ID"
        );

        $PROPS = array();

        $scheme = CIBlockElement::GetList(array(),array("ID"=>$_POST["scheme"]), false, false, $arSelect);
        while($arScheme = $scheme->Fetch()){
            //arshow($arScheme);
            $PROPS["COMPANY"] = $arScheme["PROPERTY_COMPANY_VALUE"];  
            $PROPS["MATH_AGE_FROM"] = $arScheme["PROPERTY_MATH_AGE_FROM_ENUM_ID"];
            $PROPS["MATH_AGE_TO"] = $arScheme["PROPERTY_MATH_AGE_TO_ENUM_ID"];
            $PROPS["MATH_TOUR"] = $arScheme["PROPERTY_MATH_TOUR_ENUM_ID"];
            $PROPS["MATH_TOUR_DISCOUNT"] = $arScheme["PROPERTY_MATH_TOUR_DISCOUNT_VALUE"];
            $PROPS["MATH_ROAD"] = $arScheme["PROPERTY_MATH_ROAD_ENUM_ID"];
            $PROPS["MATH_ROAD_DISCOUNT"] = $arScheme["PROPERTY_MATH_ROAD_DISCOUNT_VALUE"];
            if ($arScheme["PROPERTY_DIRECTION_VALUE"] > 0) {
                $PROPS["DIRECTION"][] = $arScheme["PROPERTY_DIRECTION_VALUE"];
            }
            $NAME = $arScheme["NAME"];
            $ID = $arScheme["ID"];
        }
        echo array_search($_POST["direction"],$PROPS["DIRECTION"]);
        //проверяем у текущей схемы наличие выбранного направления. если есть - удаляем, если нет - добавляем
        if (in_array($_POST["direction"],$PROPS["DIRECTION"])) {          
            unset($PROPS["DIRECTION"][array_search($_POST["direction"],$PROPS["DIRECTION"])]);
        }
        else {
            $PROPS["DIRECTION"][] = $_POST["direction"];   
        }


        //обновляем схему рассчета
        $el = new CIBlockElement;

      

        $arLoadProductArray = Array(
            "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
            "PROPERTY_VALUES"=> $PROPS,
            "NAME"           => $NAME,
            "ACTIVE"         => "Y",            // активен           
        );

        //arshow($arLoadProductArray);
        $res = $el->Update($ID, $arLoadProductArray);


    }
?>
