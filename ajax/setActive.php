<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<?
    //������������/�������������� �������  ID - ��������, VAL - �������� ������� ����� ��������� (�� ��� ���), ������������ ��� ������������ ������ 
    function setElementActive($ID, $VAL) {
        //�������� �������
        $element = CIBlockElement::GetById($ID);
        $arElement = $element->Fetch();        
        //��������� ��������
        $iblock = CIBlock::GetById($arElement["IBLOCK_ID"]);
        $arIblock = $iblock->Fetch();

        //��������� �������� "ACTIVE" � ��������
        $active =  CIBlockElement::GetProperty($arElement["IBLOCK_ID"], $arElement["ID"],array(), Array("CODE"=>"ACTIVE"));
        $arActive = $active->Fetch();

        //�������� �������� �������� ACTIVE
        $value = CIBlockPropertyEnum::GetList(Array(), Array("CODE"=>"ACTIVE","IBLOCK_ID"=>$arElement["IBLOCK_ID"]));
        $arValue = $value->Fetch();
        // arshow($arValue);



        //���� ����������� �����������, ����� ��� ��������� �� ����� �������� ��� ����������� �� �������� �������:
        //��� �����������: ������->���������->������
        //��� �������: ���������->������
        //��� ��������: ������


        //����������� ����� ��� ������� �� �����, ��� ��� � ��� ��� ��������    
        if ($arIblock["CODE"] != "ROOM"){

            //��������� ��������
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

        //���� �������� ����, ��� ���� �������
        if (($arActive["PROPERTY_VALUE_ID"] > 0 && $VAL != "Y") || $VAL == "Y") {
            mysql_query("DELETE FROM `b_iblock_element_property` WHERE id=".$arActive["PROPERTY_VALUE_ID"]);     
            //����������� ����� ��� ������� �� �����, ��� ��� � ��� ��� ��������                 

            if ($arIblock["CODE"] != "ROOM"){ 
                while ($arChildren = $children->Fetch()) {    
                    setElementActive($arChildren["ID"],"Y");
                }      
            }


        }
        else { //���� ��� - �� ��������
            if (($arValue["ID"] > 0 && $VAL != "N") || $VAL == "N") {
                mysql_query("INSERT INTO `b_iblock_element_property` (`ID`,`IBLOCK_PROPERTY_ID`,`IBLOCK_ELEMENT_ID`, `VALUE`,`VALUE_TYPE`,`VALUE_ENUM`) VALUES (NULL,'".$arActive["ID"]."', '".$arElement["ID"]."', '".$arValue["ID"]."',  'text', '".$arValue["ID"]."')");
                //����������� ����� ��� ������� �� �����, ��� ��� � ��� ��� �������� 


                //������������� ���������� (������ ��� ������)
                $update = "Y";

                //��� ������� ����� ���������, � �������� ������������ �� �����������, � ���� � ���� ���� ���� �� ���� �������� �����������, �� ����� �� ��������������
                if ($arIblock["CODE"] == "CITY") {
                    //�������� ������� �����
                    $city = CIBlockElement::GetList(array(), array("ID"=>$ID), false, false, array("PROPERTY_DIRECTION"));  
                    while ($arCity = $city->Fetch()){
                        //�������� ������ �����������, � ������� ����������� �����
                        $direction = CIBlockElement::GetList(array(), array("ID"=>$arCity["PROPERTY_DIRECTION_VALUE"]), false, false, array("PROPERTY_ACTIVE"));
                        while($arDirection = $direction->Fetch()) {
                            arshow($arDirection);
                            //���� ���� �� ���� ����������� �������, �� �������� ����� �� �����
                            if ($arDirection["PROPERTY_ACTIVE_VALUE"] == "��") {
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