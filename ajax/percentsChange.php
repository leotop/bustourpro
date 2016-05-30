<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<?
    //проверяем, есть ли связка тип/направление и добавляем/изменяем/удаляем ее
    if (intval($_POST["direction"]) > 0 && intval($_POST["type"]) > 0) {
        
         $iblock = CIBlock::GetList(array(),array("CODE"=>"PERCENTS"));
         $arIblock = $iblock->Fetch();
        
        $percent = CIBLockElement::GetList(array(), array("IBLOCK_CODE"=>"PERCENTS","PROPERTY_COMPANY"=>getCurrentCompanyID(),"PROPERTY_TYPE_BOOKING"=>$_POST["type"], "PROPERTY_DIRECTION"=>$_POST["direction"]), false, false, array("ID","PROPERTY_DIRECTION","PROPERTY_DISCOUNT" ,"PROPERTY_TYPE_BOOKING" ,"NAME"));
        $arPercent = $percent->Fetch();  
        

        //если задано пустое значение, удаляем запись
        if ($_POST["value"] == "") {
            CIBLockElement::Delete($arPercent["ID"]);
            echo "1";
        }

        //если значение не пустое и есть элемент, его надо обновить
        else if ($_POST["value"] != "" && $arPercent["ID"] > 0) {
          
            $el = new CIBlockElement;

            $PROP = array();
            $PROP["COMPANY"] = getCurrentCompanyID();  // свойству с кодом 12 присваиваем значение "Белый"
            $PROP["DIRECTION"] = $_POST["direction"];        // свойству с кодом 3 присваиваем значение 38
            $PROP["TYPE_BOOKING"] = $_POST["type"];
            $PROP["DISCOUNT"] = $_POST["value"];

            $arLoadProductArray = Array(
                "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                "PROPERTY_VALUES"=> $PROP,
                "NAME"           => $arPercent["NAME"],
                "ACTIVE"         => "Y",            // активен    
                "IBLOCK_ID"      => $arIblock["ID"]      
            );

            $PRODUCT_ID = $arPercent["ID"];  // изменяем элемент с кодом (ID) 2
            $res = $el->Update($PRODUCT_ID, $arLoadProductArray);              
        } 

        //иначе надо добавить элемент
        else {
            $el = new CIBlockElement;

            $PROP = array();
            $PROP["COMPANY"] = getCurrentCompanyID();  // свойству с кодом 12 присваиваем значение "Белый"
            $PROP["DIRECTION"] = $_POST["direction"];        // свойству с кодом 3 присваиваем значение 38
            $PROP["TYPE_BOOKING"] = $_POST["type"];
            $PROP["DISCOUNT"] = $_POST["value"];

            $arLoadProductArray = Array(
                "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                "PROPERTY_VALUES"=> $PROP,
                "NAME"           => getCurrentCompanyID()."-".$_POST["direction"]."-".$_POST["type"],
                "ACTIVE"         => "Y",            // активен    
                "IBLOCK_ID"      => $arIblock["ID"]            
            );

            
            $res = $el->Add($arLoadProductArray);             
        }             

    }
?>