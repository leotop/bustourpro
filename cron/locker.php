<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>      
<?
    //�������� �� ��������� ���������� ����, ��� ��������, ������� ���������� ����� 15 �����

    $last_date = date("U") - 15*60; //����� �������, ������� ���� 15 ����� �����      
    $places = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"PLACES_LOCKER","<DATE_CREATE"=>date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), $last_date)), false, false, array("ID","NAME","PROPERTY_SCHEME_ID","DATE_CREATE"));

    //�������� ������ ����, ������� ����� ��������������
    while($arPlaces = $places->Fetch()) {              
       //����������� ����� 
        freeBusPlace($arPlaces["NAME"],$arPlaces["PROPERTY_SCHEME_ID_VALUE"]);         
        //� ����� ������� �������� �� ��������� ����������
        CIBlockElement::Delete($arPlaces["ID"]);
    }       


    //������������� �������
    //�������� ������ �������, ������� ����� ��������������
    $rooms = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"ROOM_LOCKER","<DATE_CREATE"=>date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), $last_date)), false, false, array("ID", "NAME"));
    while($arRoom = $rooms->Fetch()) {              
        //����������� �����
        freeRoom($arRoom["NAME"]);        
        //� ������� ������ � ����������
        CIBlockElement::Delete($arRoom["ID"]);
    }

?>