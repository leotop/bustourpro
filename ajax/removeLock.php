<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<?   
    $places = explode(";",$_POST["places"]);


    $lock_place = CIBLockElement::GetList(array(), array("IBLOCK_CODE"=>"PLACES_LOCKER","NAME"=>$places, "PROPERTY_USER_ID"=>$USER->GetId()), false, false, array("ID","NAME","PROPERTY_SCHEME_ID","DATE_CREATE"));
    while($arLockPlace = $lock_place->Fetch()){
        //����������� ����� 
        freeBusPlace($arLockPlace["NAME"],$arLockPlace["PROPERTY_SCHEME_ID_VALUE"]);         
        //� ����� ������� �������� �� ��������� ����������
        CIBlockElement::Delete($arLockPlace["ID"]);
    }


    if ($_POST["room"] > 0) {
        //� �� ��������� � ����������� ������� ���� ����� ������� ������
        $lock_rooms = CIBLockElement::GetList(array(), array("IBLOCK_CODE"=>"ROOM_LOCKER","NAME"=>$_POST["room"],"PROPERTY_USER_ID"=>$USER->GetId()), false, false, array("ID","NAME"));
        $arLockRoom = $lock_rooms->Fetch();
        //����������� �����
        freeRoom($arLockRoom["NAME"]);        
        //� ������� ������ � ����������
        CIBlockElement::Delete($arLockRoom["ID"]);          
    }
?>