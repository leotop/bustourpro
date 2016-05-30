<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>      
<?
    //выбираем из инфоблока блокировки мест, все элементы, которые существуют более 15 минут

    $last_date = date("U") - 15*60; //метка времени, которая была 15 минут назад      
    $places = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"PLACES_LOCKER","<DATE_CREATE"=>date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), $last_date)), false, false, array("ID","NAME","PROPERTY_SCHEME_ID","DATE_CREATE"));

    //получаем список мест, которые нужно разблокировать
    while($arPlaces = $places->Fetch()) {              
       //освобождаем место 
        freeBusPlace($arPlaces["NAME"],$arPlaces["PROPERTY_SCHEME_ID_VALUE"]);         
        //и затем удаляем элементы из инфоблока блокировки
        CIBlockElement::Delete($arPlaces["ID"]);
    }       


    //разблокировка номеров
    //получаем список номеров, которые нужно разблокировать
    $rooms = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"ROOM_LOCKER","<DATE_CREATE"=>date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), $last_date)), false, false, array("ID", "NAME"));
    while($arRoom = $rooms->Fetch()) {              
        //освобождаем номер
        freeRoom($arRoom["NAME"]);        
        //и удаляем запись о блокировке
        CIBlockElement::Delete($arRoom["ID"]);
    }

?>