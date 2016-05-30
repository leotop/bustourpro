<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<?   
    $places = explode(";",$_POST["places"]);


    $lock_place = CIBLockElement::GetList(array(), array("IBLOCK_CODE"=>"PLACES_LOCKER","NAME"=>$places, "PROPERTY_USER_ID"=>$USER->GetId()), false, false, array("ID","NAME","PROPERTY_SCHEME_ID","DATE_CREATE"));
    while($arLockPlace = $lock_place->Fetch()){
        //освобождаем место 
        freeBusPlace($arLockPlace["NAME"],$arLockPlace["PROPERTY_SCHEME_ID_VALUE"]);         
        //и затем удаляем элементы из инфоблока блокировки
        CIBlockElement::Delete($arLockPlace["ID"]);
    }


    if ($_POST["room"] > 0) {
        //и из инфоблока с блокировкой номеров тоже нужно удалить запись
        $lock_rooms = CIBLockElement::GetList(array(), array("IBLOCK_CODE"=>"ROOM_LOCKER","NAME"=>$_POST["room"],"PROPERTY_USER_ID"=>$USER->GetId()), false, false, array("ID","NAME"));
        $arLockRoom = $lock_rooms->Fetch();
        //освобождаем номер
        freeRoom($arLockRoom["NAME"]);        
        //и удаляем запись о блокировке
        CIBlockElement::Delete($arLockRoom["ID"]);          
    }
?>