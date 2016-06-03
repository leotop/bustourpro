<?php
    /**
    * Created by JetBrains PhpStorm.
    * User: Дмитрий
    * Date: 06.11.13
    * Time: 21:57
    * To change this template use File | Settings | File Templates.
    */

    /**
    * CompanyID auth user
    *
    * @return null|int
    */



    CModule::IncludeModule("main");
    CModule::IncludeModule("iblock");

    require_once dirname(__FILE__) .'/bustour/init.php'; 

    AddEventHandler("iblock", "OnBeforeIBlockElementDelete", "itemsDelete");     //удаление/деактивация связанных элементов из БД при удалении головных элементов


    //если перехд по несуществующему урлу
    if ($_SERVER["SERVER_NAME"] != "bustourpro.ru") {
        if (!is_object($USER)) {$USER = new CUser;}
        if ($APPLICATION->GetCurPage() != "/personal/"){
            define("NEED_AUTH", true);
        }   

        //если переходим по несуществующему урлу
        $companyID = getCompanyIdByName(getCompanyNameFromURL());  
        if (!$companyID) {
            header("location:http://bustourpro.ru");
        }

        //ID компании по умолчанию
        $GLOBALS["DEFAULT_COMPANY_ID"] = $companyID;       
    }

    //добавляем новые события для журнала событий
    AddEventHandler('main', 'OnEventLogGetAuditTypes', 'ASD_OnEventLogGetAuditTypes');
    function ASD_OnEventLogGetAuditTypes()
    {
        return array(
            'BUSTOURPRO_EVENT_NEW_ORDER' => '[BUSTOURPRO] Новый заказ',
            'BUSTOURPRO_EVENT_NEW_TOURIST' => '[BUSTOURPRO] Новый пассажир',
            'BUSTOURPRO_EVENT_PLACES_BOOKING' => '[BUSTOURPRO] Забронированы места',
            'BUSTOURPRO_EVENT_ROOM_BOOKING' => '[BUSTOURPRO] Забронирован номер', 
            'BUSTOURPRO_EVENT_PLACES_LOCKED' => '[BUSTOURPRO] Заблокированы места',
            'BUSTOURPRO_EVENT_ROOM_LOCKED' => '[BUSTOURPRO] Заблокирован номер',
            'BUSTOURPRO_EVENT_PLACES_UNLOCKED' => '[BUSTOURPRO] Разблокированы места',
            'BUSTOURPRO_EVENT_ROOM_UNLOCKED' => '[BUSTOURPRO] Разблокирован номер',
            'BUSTOURPRO_EVENT_ORDER_CANCELLED' => '[BUSTOURPRO] Заказ отменен',
            'BUSTOURPRO_EVENT_POST_DATA' => '[BUSTOURPRO] Логирование POST данных'
        );
    }

    //перед отправкой письма проверяем домен
    AddEventHandler('main', 'OnBeforeEventSend', Array("MyForm", "my_OnBeforeEventSend"));
    class MyForm
    {
        function my_OnBeforeEventSend($arFields, $arTemplate)
        {            
            $arFields["SITE_NAME"] = $_SERVER["HTTP_HOST"];
            $arFields["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
        }
    }

    //логирование событий
    function eventLogAdd($event,$ID,$description) {
        //логирование
        if (!$ID) {
            $ID = "UNKNOWN";
        }

        CEventLog::Add(array(
            "SEVERITY" => "SECURITY",
            "AUDIT_TYPE_ID" => $event,
            "MODULE_ID" => "iblock",
            "ITEM_ID" => $ID,
            "DESCRIPTION" => $description,
        ));
    }
    ////////////


    //получаем название месяца по номеру
    function get_month_name($n){
        $n = intval($n);
        switch($n){
            case 1: $month_name = "января";break;
            case 2: $month_name = "февраля";break;
            case 3: $month_name = "марта";break;
            case 4: $month_name = "апреля";break;
            case 5: $month_name = "мая";break;
            case 6: $month_name = "июня";break;
            case 7: $month_name = "июля";break;
            case 8: $month_name = "августа";break;
            case 9: $month_name = "сентября";break;
            case 10: $month_name = "октября";break;
            case 11: $month_name = "ноября";break;
            case 12: $month_name = "декабря";break;   
        }
        return $month_name;
    }        




    //освободить место с идентификатором $placeID в схеме $schemeID
    function freeBusPlace($placeID,$schemeID){
        //получаем схему автобуса 
        if ($schemeID > 0 && $placeID) {
            $bus = CIBlockElement::GetList(array(), array("ID"=>$schemeID), false , false, array("ID","NAME","PROPERTY_P_SCHEME","PROPERTY_BUS_DIRECTION","PROPERTY_COMPANY", "PROPERTY_DEPARTURE", "PROPERTY_ARRIVAL"));
            $arBus = $bus->Fetch();
            $scheme_decode = json_decode($arBus["PROPERTY_P_SCHEME_VALUE"], true);
            //перебираем схему, как только нашли места, которые нужно освободить - меняем их состояние на "свободно"
            foreach($scheme_decode as $n=>$val) {
                foreach ($val as $i=>$place){
                    if ($i == $placeID) {                    
                        $scheme_decode[$n][$i] = "FP"; 
                    }
                }
            }

            //кодируем схему обратно
            $scheme_new = json_encode($scheme_decode); 
            //обновляем схему в инфоблоке
            //после этого обновляем схему рассадки в базе
            $el = new CIBlockElement;

            $PROP = array();
            $PROP["P_SCHEME"] = $scheme_new;
            $PROP["BUS_DIRECTION"] = Array("VALUE" => $arBus["PROPERTY_BUS_DIRECTION_ENUM_ID"]);
            $PROP["COMPANY"] = $arBus["PROPERTY_COMPANY_VALUE"];  
            $PROP["DEPARTURE"] = $arBus["PROPERTY_DEPARTURE_VALUE"];
            $PROP["ARRIVAL"] = $arBus["PROPERTY_ARRIVAL_VALUE"];

            $arLoadProductArray = Array(
                "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                "PROPERTY_VALUES"=> $PROP,
                "NAME"           => $arBus["NAME"],
                "ACTIVE"         => "Y",            // активен          
            );
            //обновляем схему рассадки в БД  
            $res = $el->Update($arBus["ID"], $arLoadProductArray);   

            $id = $schemeID;
            $description = "Место ".$placeID." на схеме ".$schemeID." освобождено";
            eventLogAdd("BUSTOURPRO_EVENT_PLACES_UNLOCKED",$id,$description);
        }  

    }




    //освободить номер для тура $tourID     
    function freeRoom($tourID){

        //получаем тур, к которому относится номер
        $tour = CIBlockElement::GetById($tourID);
        $deleteTour = $tour->Fetch();


        $el = new CIBlockElement;
        $tourPropsNew = array();
        //получаем свойства тура. нам нужно увеличить количество номеров данного тура на 1. 
        //это для случая, когда оформление заказа не было выплнено до конца, а номер списался
        $tourProps = CIBlockElement::GetProperty($deleteTour["IBLOCK_ID"],$deleteTour["ID"],Array(),Array());
        while($arTourProps = $tourProps->Fetch()){
            //возвращаем заблокированное место туру
            if ($arTourProps["CODE"] == 'NUMBER_ROOM') {
                $arTourProps["VALUE"] = $arTourProps["VALUE"]+1;  
            }
            $tourPropsNew[$arTourProps["ID"]] = $arTourProps["VALUE"];
        }

        $arLoadProductArray = Array(
            "PROPERTY_VALUES"=> $tourPropsNew,
            "ACTIVE"         => "Y",            // активен   
            "NAME"           => $deleteTour["NAME"]
        );

        $res = $el->Update($deleteTour["ID"], $arLoadProductArray); 

        //пишем лог
        $id = $tourID;
        $description = "Номер в туре ".$tourID." освобожден"; 
        eventLogAdd("BUSTOURPRO_EVENT_ROOM_UNLOCKED",$id,$description);

    }           




    //освождение мест в автобусе и гостинице при аннуляции тура
    function itemsDelete($ID) {

        //получаем инфо об элементе
        $element = CIBlockElement::GetList(array(), array("ID"=>$ID,"PROPERTY_COMPANY"=>getCurrentCompanyID()), false, false, array());
        $arElement = $element->Fetch();

        //проверяем инфоблок, из которого происходит удаление
        $iblock = CIBlock::GetById($arElement["IBLOCK_ID"]);
        $arIblock = $iblock->Fetch();


        switch($arIblock["CODE"]) {
            //удаление заказов
            case "ORDERS": 
                //при удалении заказа, нужно освободить занимаемые ими места и освободить номер
                $order = CIBlockElement::GetList(array(), array("ID"=>$arElement["ID"]), false,false, array("ID","PROPERTY_TYPE_BOOKING","PROPERTY_TOUR","PROPERTY_BUS_ID","PROPERTY_SECOND_BUS_ID"));
                $arOrder = $order->Fetch();
                //если стандартное бронирование

                if ($arOrder["PROPERTY_TOUR_VALUE"]) {   
                    //получаем информацию об автобусах в туре
                    $arTour = CIBlockElement::GetList(array(),array("ID"=>$arOrder["PROPERTY_TOUR_VALUE"]),false,false,array("PROPERTY_BUS_TO","PROPERTY_BUS_BACK"))->Fetch();    
                    //освобождаем номер                     
                    freeRoom($arOrder["PROPERTY_TOUR_VALUE"]);    
                }        

                //если в заказе не указаны автобусы, берем их из тура
                if (!$arOrder["PROPERTY_BUS_ID_VALUE"] || !$arOrder["PROPERTY_SECOND_BUS_ID_VALUE"]) {
                    if ($arTour["PROPERTY_BUS_TO_VALUE"] && !$arOrder["PROPERTY_BUS_ID_VALUE"]) {
                        $arOrder["PROPERTY_BUS_ID_VALUE"] = $arTour["PROPERTY_BUS_TO_VALUE"];  
                    }
                    if ($arTour["PROPERTY_BUS_BACK_VALUE"] && !$arOrder["PROPERTY_SECOND_BUS_ID_VALUE"]) {
                        $arOrder["PROPERTY_SECOND_BUS_ID_VALUE"] = $arTour["PROPERTY_BUS_BACK_VALUE"];  
                    }
                }    

                //получаем пассажиров и освобождаем их места                   
                $passangers = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"TOURIST","PROPERTY_ORDER"=>$arElement["ID"]), false, false, array("PROPERTY_PLACE", "PROPERTY_SECOND_PLACE", "ID")); 
                while ($arPassanger = $passangers->Fetch()) {
                    //если есть места
                    if ($arPassanger["PROPERTY_PLACE_VALUE"] || $arPassanger["PROPERTY_SECOND_PLACE_VALUE"]) {                        

                        if ($arOrder["PROPERTY_BUS_ID_VALUE"] && $arPassanger["PROPERTY_PLACE_VALUE"]) {
                            //освобождаем место в автобусе туда   
                            freeBusPlace($arPassanger["PROPERTY_PLACE_VALUE"],$arOrder["PROPERTY_BUS_ID_VALUE"]);
                        }
                        if ($arOrder["PROPERTY_SECOND_BUS_ID_VALUE"] && $arPassanger["PROPERTY_SECOND_PLACE_VALUE"]) {
                            //освобождаем место в автобусе обратно
                            freeBusPlace($arPassanger["PROPERTY_SECOND_PLACE_VALUE"],$arOrder["PROPERTY_SECOND_BUS_ID_VALUE"]);  
                        } 

                    }
                } 

                break;


                //при удалении тура
            case "TOUR": ;

                break;


        }
    }



    //формирование схем для автобуса

    function get_bus_scheme($scheme){
        //получаем массив схему автобуса в виде массива мест
        $buses_scheme = json_decode($scheme, true);
        //получаем html-схему расположения пассажиров в конкретном автобусе
        $table = busSchemeTable($buses_scheme);
        $busTable .= '
        <div class="tourViewBusTable">
        <table class="viewBusTable" summary="">
        <tr>
        <th>Схема расположения мест</th>         
        <th>Условные обозначения мест</th>
        </tr>
        <tr>
        <td>
        <div class="schemeDescr">
        '.$table['busTable'].'
        </div>        
        <td>
        <div class="legendTableDiv">
        <table summary="" class="legendTable">

        <tr>
        <td>
        <div class="legendPlaceFP">&nbsp;</div>
        </td>
        <td>
        <b>Доступные для<br/>бронирования места</b>.
        </td>
        </tr>  

        <tr>
        <td>
        <div class="legendPlacePP">&nbsp;</div>
        </td>
        <td>
        <b>Занятые места</b>.
        </td>
        </tr> 

        <tr>
        <td>
        <div class="legendPlaceBP">&nbsp;</div>
        </td>
        <td>
        <b>Недоступные для<br>бронирования</b>.
        </td>
        </tr>          

        </table>
        </div>
        </td>
        </tr>
        </table>
        </div>
        ';

        echo $busTable;
    }


    function busSchemeTable($scheme){
        //получаем количество рядов, отведенных под двери во всех схемах автобусов тура
        $doorsBus = busDoorGk($scheme);
        //ряд
        $i = 1;
        //вводим счетчик свободных мест для определения заполнения автобуса
        $s = 0;
        //считаем количество строк автобусе
        $countRow = count($scheme);
        //считаем места, отведенные под двери (для корректного отображения дверей на схеме)
        $door = 0;
        //считаем места, отведенные под сопровождающих (для корректного отображения сопровождения на схеме)
        $escort = 0;

        foreach($scheme as $row => $column){
            //место в ряде
            $j = 1;
            foreach($column as $place => $type){
                //считаем количество свободных мест в автобусе
                if($type == 'FP'){
                    $s++;
                }
                //в зависимости от типа места и его расположения отображаем его или нет (касаемо прохода и последнего центрального места в проходе)
                if($place == 'r_'.$i.'_c_3'){
                    //у последнего центрального места не ставим класс buspass
                    if($i != $countRow){
                        $class = 'busPass';
                    }
                    //если последнее место по центральному ряду и тип PP или FP, то ставим класс placeBooking
                    if($i == $countRow){
                        if($type != 'PP' && $type != 'FP'){
                            $class = 'busPass';
                        }
                        else{
                            $class = 'placeBooking';
                        }
                    }
                }
                else{
                    if($type == 'DP'){
                        //при первом упоминании двери присваиваем переменной $door значение 1, а дальше просто его увеличиваем и сравниваем с 1;
                        $door++;
                        //не присваиваем дверям класс
                        $class = '';
                    }
                    elseif($type == 'WP'){
                        //при первом упоминании сопровождения присваиваем переменной $escort значение 1, а дальше просто его увеличиваем и сравниваем с 1;
                        $escort++;
                        //не присваиваем сопровождающим класс
                        $class = '';
                    }
                    elseif ($type == 'TP') {
                        $table++;
                    }

                    elseif ($type == 'BP') {
                        $class = "";
                    }

                    else{
                        $class = 'placeBooking';
                    }
                }
                //собираем все ячейки одной строки
                //если место не отмечено как "дверь" и "сопровождение", то суммируем ячейки
                if($type != 'DP' && $type != 'WP' && $type != "TP"){
                    if($j == 1){
                        $rowSign = '<div class="rowSign">Ряд '.$i.'</div>';
                    }
                    else{
                        $rowSign = '';
                    }

                    if($j == 1 || $j == 5){
                        $placeLabel = '<span class="placeLabel">О</span>';
                    }
                    elseif($j == 2 || $j == 4 || ($j == 3 && $i == $countRow)){
                        $placeLabel = '<span class="placeLabel">П</span>';
                    }
                    else{
                        $placeLabel = '';
                    }
                    //если есть массив пассажиров и является массивом, то выводим дополнительные сведения о пассажирах (касается пересадки пассажиров в автобусах)
                    if(is_array($items)){
                        $td .='<td>
                        <div class="place'.$type.' '.$class.'">
                        '.$rowSign.$placeLabel.'
                        <input type="hidden" name="'.$place.'" value="'.$type.'">
                        <input type="hidden" name="item" value="'.$items['{"r_'.$i.'_c_'.$j.'":"'.$type.'"}']['id'].'">
                        <input type="hidden" name="order" value="'.$items['{"r_'.$i.'_c_'.$j.'":"'.$type.'"}']['idOrder'].'">
                        <input type="hidden" name="itemName" value="'.$items['{"r_'.$i.'_c_'.$j.'":"'.$type.'"}']['name'].'">
                        <span class="passengersName">'.$items['{"r_'.$i.'_c_'.$j.'":"'.$type.'"}']['name'].'</span>
                        </div>
                        </td>';
                    }
                    else{
                        $td .='<td>
                        <div class="place'.$type.' '.$class.'">
                        '.$rowSign.$placeLabel.'
                        <input type="hidden" name="'.$place.'" value="'.$type.'">
                        </div>
                        </td>';
                    }
                }
                //если место отмечено как дверь, то определяем размер двери
                if($door == 1 && $type == 'DP'){
                    $rowspan = $doorsBus/2;
                    $td .='<td colspan="2" rowspan="'.$rowspan.'">
                    <div class="doors_'.$rowspan.'">
                    Вторая дверь
                    </div>
                    </td>';
                }
                elseif(($door > 1 && $type == 'DP')){}


                //если место отмечено как "сопрвовождение", то определяем размер сопровождения
                if($escort %2 != 0 && $type == 'WP'){

                    $td .='<td colspan="2">
                    <div class="escort">
                    Сопровождение
                    </div>
                    </td>';
                }
                //в случае если уже есть отметка о наличии двери, ничего не добавляем
                elseif(($escort %2 == 0 && $type == 'WP')){}   


                //столик
                if ($table >= 1 && $type == 'TP') {
                    $td .='<td>
                    <div class="bus_table_place">
                    столик
                    </div>
                    </td>';  
                }

                $j++; 
            }
            //собираем все строки
            $tr .= '<tr>'.$td.'</tr>';
            //считаем ряды
            $i++;
            unset ($td);
        }
        //схема расположения мест с указанием занятых
        $result['busTable'] = '<table id="'.$idBus.'" class="busTable" summary="">
        <tr>
        <td><div class="busDriver">Водитель</div></td>
        <td colspan = "3"></td>
        <td><div class="firstBusDoor">Выход</div></td>
        </tr>
        '.$tr.'
        </table>';
        return $result;
    }


    function busDoorGk($busesScheme){

        $countDP = 0;
        foreach ($busesScheme as $row => $columns){
            foreach ($columns as $place_bus => $type_bus){
                if($type_bus == 'DP'){
                    //считаем места, отведенные под двери
                    $countDP++;
                }
            }
        }
        $doorsBus = $countDP;

        return $doorsBus;          
    }



    //схема автобуса для сопровождающих
    #$idBus - номер автобуса
    #$sType - тип. F - рассадка + инфа по гостиницам, B - только автобус, H - только гостиница, P - список пассажиров для границы
    function getEscortScheme($idBus,$sType) {

        //получаем автобус
        $bus = CIBlockElement::GetList(array(), array("ID"=>$idBus),false ,false, array("PROPERTY_P_SCHEME","PROPERTY_BUS_DIRECTION"));
        $arBus = $bus->Fetch();         
        $scheme = json_decode($arBus["PROPERTY_P_SCHEME_VALUE"]);

        //собираем туры, относящиеся к данному автобусу
        $tourFilter = array("IBLOCK_CODE"=>"TOUR","PROPERTY_COMPANY"=>getCurrentCompanyID());
        switch($arBus["PROPERTY_BUS_DIRECTION_VALUE"]) {
            case "Туда": $tourFilter["PROPERTY_BUS_TO"] = $idBus; break;
            case "Обратно": $tourFilter["PROPERTY_BUS_BACK"] = $idBus; break;
        }

        $arTours = array();  //массив ID туров, относящихся к данному автобусу  
        $arSecondTours = array(); // массив вторых туров, для двойных туров     
        $tours = CIBLockElement::GetList(array(), $tourFilter, false, false, array("ID","PROPERTY_BUS_TO","PROPERTY_BUS_BACK"));
        while($arTour = $tours->Fetch()) {
            $arTours[] = $arTour["ID"];
            //   $arSecondTours[$arTour["ID"]] = checkDoubleTour($arTour["ID"]);
        }

        // arshow($arSecondTours);         

        //для автобуса обратно нужно проверить, не является ли он автобусов из второго тура
        if ($arBus["PROPERTY_BUS_DIRECTION_VALUE"] == "Обратно")  {

            $prevTours = array();  //предыдущие туры
            //для каждого тура нужно проверить возможность двойного тура (предыдущего тура через checkPrevTour)
            foreach ($arTours as $tourID) {                 
                $prev = 0;
                $prev = checkPrevTour($tourID);
                if ($prev > 0) {
                    $prevTours[] = $prev; 
                }
            }    
            //соединяем массив текущих туров для данного автобуса и предыдущих
            $arTours  = array_merge($arTours,$prevTours); 

        }         

        //arshow($arTours);


        $arHotels = array(); //список гостиниц 
        $arOrders = array(); //массив ID заказов на данный тур
        $tourists = array(); //массив туристов

        $tousirsSelect = array(
            "ID",
            "PROPERTY_PLACE",
            "PROPERTY_SECOND_PLACE",
            "NAME",
            "PROPERTY_ORDER",
            "PROPERTY_PASSPORT",
            "PROPERTY_BIRTHDAY",
            "PROPERTY_PHONE");   

        //собираем заказы на выбранные туры
        $orderSelect = array(
            "ID",
            "PROPERTY_COMPANY_NAME",
            "DATE_CREATE",
            "PROPERTY_TOUR",
            "PROPERTY_TYPE_BOOKING",
            "PROPERTY_STATUS" , 
            "CREATED_BY",
            "PROPERTY_CITY",
            "PROPERTY_HOTEL"
        );

        $orders = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"ORDERS", "PROPERTY_TOUR"=>$arTours, "PROPERTY_COMPANY"=>getCurrentCompanyID()),false, false, $orderSelect);
        while($arOrder = $orders->Fetch()) {
            if ($arOrder["PROPERTY_STATUS_VALUE"] != "Запрос на аннулирование" && $arOrder["PROPERTY_STATUS_VALUE"] != "Заказ аннулирован") {
                //получаем туристов заказа   
                $tourist = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"TOURIST", "PROPERTY_ORDER"=>$arOrder["ID"], "PROPERTY_COMPANY"=>getCurrentCompanyID()),false, false, $tousirsSelect);
                while ($arTourist = $tourist->Fetch()) { 
                    //если в заказе не указано название компании, получаем его из свойств пользователя
                    if (!$arOrder["PROPERTY_COMPANY_NAME_VALUE"])  {
                        $user = CUser::GetById($arOrder["CREATED_BY"]);
                        $arUser = $user->Fetch();
                        $arOrder["PROPERTY_COMPANY_NAME_VALUE"] = $arUser["NAME"];
                    } 
                    $arTourist["COMPANY_NAME"] = $arOrder["PROPERTY_COMPANY_NAME_VALUE"];  

                    //место туриста
                    $id = $arTourist["PROPERTY_PLACE_VALUE"];

                    if (!$id) {
                        $id = $arTourist["PROPERTY_SECOND_PLACE_VALUE"];    
                    }    
                    //если у туриста есть место
                    if ($id) {    
                        //для двойного тура автобус обратно вычисляется иначе 
                        //если тур является двойным и его ID находится в массиве предыдущих туров, то добавляем его в автобус  
                        if ($arOrder["PROPERTY_TYPE_BOOKING_VALUE"] == "двойной тур" && in_array($arOrder["PROPERTY_TOUR_VALUE"],$prevTours) && $arBus["PROPERTY_BUS_DIRECTION_VALUE"] == "Обратно") {
                            $tourists[$id] = $arTourist; 
                            //собираем массив готиницы=>массив заказов
                            $arOrder["TOURISTS"][] = $arTourist;
                        } 
                        //если тур двойной и автобус обратно - пропускаем
                        else if ($arOrder["PROPERTY_TYPE_BOOKING_VALUE"] == "двойной тур" && $arBus["PROPERTY_BUS_DIRECTION_VALUE"] == "Обратно") {

                        }
                        //если тур не двойной и ID тура находится в массиве предыдущих - тоже пропускаем
                        else if ($arOrder["PROPERTY_TYPE_BOOKING_VALUE"] != "двойной тур" && in_array($arOrder["PROPERTY_TOUR_VALUE"],$prevTours)) {

                        }  

                        else {  
                            $tourists[$id] = $arTourist; 
                            //собираем массив готиницы=>массив заказов
                            $arOrder["TOURISTS"][] = $arTourist; 
                        }

                    }
                }                   


                if (count($arOrder["TOURISTS"]) > 0) {     
                    $arHotels[$arOrder["PROPERTY_HOTEL_VALUE"]][] = $arOrder;
                }

            }

        }      


        //собираем заказы "только проезд"
        $onlyRoad = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"ORDERS", "PROPERTY_BUS_ID"=>$idBus, "PROPERTY_COMPANY"=>getCurrentCompanyID()),false, false, $orderSelect); 
        while ($arOnlyRoad = $onlyRoad->Fetch()) {

            if ($arOnlyRoad["PROPERTY_STATUS_VALUE"] != "Запрос на аннулирование" && $arOnlyRoad["PROPERTY_STATUS_VALUE"] != "Заказ аннулирован") {
                //получаем туристов заказа   
                $tourist = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"TOURIST", "PROPERTY_ORDER"=>$arOnlyRoad["ID"], "PROPERTY_COMPANY"=>getCurrentCompanyID()),false, false, $tousirsSelect);
                while ($arTourist = $tourist->Fetch()) { 
                    //если в заказе не указано название компании, получаем его из свойств пользователя
                    if (!$arOnlyRoad["PROPERTY_COMPANY_NAME_VALUE"])  {
                        $user = CUser::GetById($arOnlyRoad["CREATED_BY"]);
                        $arUser = $user->Fetch();
                        $arOnlyRoad["PROPERTY_COMPANY_NAME_VALUE"] = $arUser["NAME"];
                    } 
                    $arTourist["COMPANY_NAME"] = $arOnlyRoad["PROPERTY_COMPANY_NAME_VALUE"];  

                    //место туриста
                    $id = $arTourist["PROPERTY_PLACE_VALUE"];

                    if (!$id) {
                        $id = $arTourist["PROPERTY_SECOND_PLACE_VALUE"];    
                    }    
                    //если у туриста есть место
                    if ($id) {    

                        $tourists[$id] = $arTourist; 
                        //собираем массив готиницы=>массив заказов
                        $arOnlyRoad["TOURISTS"][] = $arTourist;  
                    }
                }                   

                if (!$arOnlyRoad["PROPERTY_HOTEL_VALUE"]) {
                    $arOnlyRoad["PROPERTY_HOTEL_VALUE"] = 0; //только проезд
                }

                if (count($arOnlyRoad["TOURISTS"]) > 0) {     
                    $arHotels[$arOnlyRoad["PROPERTY_HOTEL_VALUE"]][] = $arOnlyRoad;
                }

            }  
        }
        //arshow($arHotels);

        //строим схему      

        //получаем количество рядов, отведенных под двери во всех схемах автобусов тура
        $doorsBus = busDoorGk($scheme);
        //ряд
        $i = 1;
        //вводим счетчик свободных мест для определения заполнения автобуса
        $s = 0;
        //считаем количество строк автобусе
        $countRow = count($scheme);
        //считаем места, отведенные под двери (для корректного отображения дверей на схеме)
        $door = 0;
        //считаем места, отведенные под сопровождающих (для корректного отображения сопровождения на схеме)
        $escort = 0;

        foreach($scheme as $row => $column){
            //место в ряде
            $j = 1;
            foreach($column as $place => $type){
                //считаем количество свободных мест в автобусе
                if($type == 'FP'){
                    $s++;
                }
                //в зависимости от типа места и его расположения отображаем его или нет (касаемо прохода и последнего центрального места в проходе)
                if($place == 'r_'.$i.'_c_3'){
                    //у последнего центрального места не ставим класс buspass
                    if($i != $countRow){
                        $class = 'busPass';
                    }
                    //если последнее место по центральному ряду и тип PP или FP, то ставим класс placeBooking
                    if($i == $countRow){
                        if($type != 'PP' && $type != 'FP'){
                            $class = 'busPass';
                        }
                        else{
                            $class = 'placeBooking';
                        }
                    }
                }
                else{
                    if($type == 'DP'){
                        //при первом упоминании двери присваиваем переменной $door значение 1, а дальше просто его увеличиваем и сравниваем с 1;
                        $door++;
                        //не присваиваем дверям класс
                        $class = '';
                    }
                    elseif($type == 'WP'){
                        //при первом упоминании сопровождения присваиваем переменной $escort значение 1, а дальше просто его увеличиваем и сравниваем с 1;
                        $escort++;
                        //не присваиваем сопровождающим класс
                        $class = '';
                    }
                    elseif ($type == 'TP') {
                        $table++;
                    }

                    else{
                        $class = 'placeBooking';
                    }
                }
                //собираем все ячейки одной строки
                //если место не отмечено как "дверь" и "сопровождение", то суммируем ячейки
                if($type != 'DP' && $type != 'WP' && $type != "TP"){

                    if ($type != 'NP') {
                        $td .='<td>
                        <div class="printEscortDiv">
                        <div class="printEscortRow"> '.$i.' </div>
                        <div class="itemsUsersName">
                        <span class="tourist">'.$tourists[$place]["NAME"].'</span>
                        <br>
                        <span class="agency">'.$tourists[$place]["COMPANY_NAME"].'</span>
                        </div>     
                        </div>
                        </td>';

                    } else {

                        $class = "";
                        if ($type == "NP") {
                            $class = "np_cell";
                        }        

                        $td .='<td class="'.$class.'"></td>';                         


                    }    
                }


                //если место отмечено как дверь, то определяем размер двери
                if($door == 1 && $type == 'DP'){
                    $rowspan = $doorsBus/2;
                    $td .='<td colspan="2" rowspan="'.$rowspan.'">
                    <div class="doors_'.$rowspan.'">
                    Вторая дверь
                    </div>
                    </td>';
                }
                elseif(($door > 1 && $type == 'DP')){}


                //если место отмечено как "сопрвовождение", то определяем размер сопровождения
                if($escort %2 != 0 && $type == 'WP'){

                    $td .='<td colspan="2">
                    <div class="escort">
                    Сопровождение
                    </div>
                    </td>';
                }
                //в случае если уже есть отметка о наличии двери, ничего не добавляем
                elseif(($escort %2 == 0 && $type == 'WP')){}   


                //столик
                if ($table >= 1 && $type == 'TP') {
                    $td .='<td>
                    <div class="bus_table_place">
                    столик
                    </div>
                    </td>';  
                }

                $j++; 
            }
            //собираем все строки
            $tr .= '<tr>'.$td.'</tr>';
            //считаем ряды
            $i++;
            unset ($td);
        }

        //схема расположения мест с указанием занятых
        $result = '<table id="'.$idBus.'" class="busTable" summary="">
        <tr>
        <td><div class="busDriver">Водитель</div></td>
        <td colspan = "3"></td>
        <td><div class="firstBusDoor">Выход</div></td>
        </tr>
        '.$tr.'
        </table>';


        // arshow($arHotels);



        $hotelData = "";

        //формируем таблицы по отелям 

        //arshow($arHotels);

        if (count($arHotels) > 0) {

            $tourists = "<table class='data-table' width='500'>
            <tr>
            <th>ФИО</th><th>Паспорт</th><th>Дата рождения</th>
            </tr>
            ";

            foreach ($arHotels as $id=>$order) {

                // arshow($order);

                $hotel = CIBlockElement::GetList(array(), array("ID"=>$id), false, false, array("NAME","PROPERTY_CITY"));
                $arHotel = $hotel->Fetch();

                if ($id == 0) {
                    $NAME = "Только проезд"; 
                }

                else {
                    $NAME = $arHotel["NAME"].", ".get_iblock_element_name($arHotel["PROPERTY_CITY_VALUE"]);
                }

                $hotelData .= "<br><br>
                <table class='data-table' width='1000'>
                <tr>
                <th colspan='7'>".$NAME."</th> 
                </tr>
                <tr>
                <th width='200'>Номер (тип бронирования)</th>
                <th width='200'>ФИО</th>
                <th width='110'>Дата рождения</th>
                <th width='100'>Паспорт</th>
                <th width='120'>Телефон</th>
                <th>Компания</th>
                <th width='60'>заказ №</th>
                </tr>
                ";

                //перебираем заказы гостиницы
                foreach ($order as $orderInfo) {

                    //получаем номер
                    $tour = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"TOUR","ID"=>$orderInfo["PROPERTY_TOUR_VALUE"]), false, false, array("PROPERTY_ROOM"));
                    $arTour = $tour->Fetch();
                    //получаем цену
                    // $room = CIBlockElement::GetList(array(), array("ID"=>$arTour["PROPERTY_ROOM_VALUE"]), false, false());

                    //только проезд
                    if ($id == 0) {
                        $hotelData .= "<tr>
                        <td rowspan='".count($orderInfo["TOURISTS"])."'>(".$orderInfo["PROPERTY_TYPE_BOOKING_VALUE"].")</td>";
                    }
                    else {
                        $hotelData .= "<tr>
                        <td rowspan='".count($orderInfo["TOURISTS"])."'>".get_iblock_element_name($arTour["PROPERTY_ROOM_VALUE"]).",
                        (".$orderInfo["PROPERTY_TYPE_BOOKING_VALUE"].")
                        </td>";

                    }


                    $i=0;
                    foreach ($orderInfo["TOURISTS"] as $torist) {
                        if ($i > 0) {
                            $hotelData .= "<tr>";  
                        }
                        $hotelData .= "
                        <td>".$torist["NAME"]."</td>
                        <td>".$torist["PROPERTY_BIRTHDAY_VALUE"]."</td>
                        <td>".$torist["PROPERTY_PASSPORT_VALUE"]."</td>
                        <td>".$torist["PROPERTY_PHONE_VALUE"]."</td>
                        <td>".$orderInfo["PROPERTY_COMPANY_NAME_VALUE"]."</td>
                        <td>".$orderInfo["ID"]."</td>
                        </tr>";  
                        $i++;

                        $tourists .= "<tr><td>".$torist["NAME"]."</td><td>".$torist["PROPERTY_PASSPORT_VALUE"]."</td><td>".$torist["PROPERTY_BIRTHDAY_VALUE"]."</td></tr>";
                    }    

                }       

                $hotelData .= "</table>";    

            }

            $tourists .= "</table>";

        }

        //проверяем, что нужно вывести
        switch ($sType) {
            case "F": $result .= "<br><br>".$hotelData; break; //рассадка + гостиницы
            case "B": $result = $result; break; //только рассадка
            case "H": $result = "<br><br>".$hotelData; break; //только гостиницы
            case "P": $result = $tourists; break; //только туристы
        }       


        return $result;   

    }




    //получить название элемента по его ID
    function get_iblock_element_name($id) {
        $element = CIBlockElement::GetById($id);
        $arElement = $element->Fetch();
        return $arElement["NAME"];
    }



    //проверяем активность родительского элемента  (ID - элемент, для которого проверяем родительский элемент)
    function checkParentActivity($ID) {
        //получаем элемент
        $element = CIBlockElement::GetById($ID);
        $arElement = $element->Fetch();

        //получаем инфоблок
        $iblock = CIBlock::GetById($arElement["IBLOCK_ID"]);
        $arIblock = $iblock->Fetch();


        $arSelect = array("ID");
        switch($arIblock["CODE"]) {
            case "CITY": $property = "PROPERTY_DIRECTION"; break;
            case "HOTEL": $property = "PROPERTY_CITY"; break;
            case "ROOM": $property = "PROPERTY_HOTEL"; break;
        }

        $arSelect[] = $property;


        $active = "N";
        //получаем элемент заново, включив в выборку свойство, в котором хранится привязка к родителю
        $fullElement = CIBlockElement::GetList(array(),array("ID"=>$ID,"PROPERTY_COMPANY"=>getCurrentCompanyId()), false, false, $arSelect);
        while ($arFullElement = $fullElement->Fetch()) {
            //получаем родителя элемента
            $parent = CIBlockElement::GetList(array(), array("ID"=>$arFullElement[$property."_VALUE"]), false, false, array("PROPERTY_ACTIVE"));
            while($arParent = $parent->Fetch()){
                //проверяем активность родителя
                if ($arParent["PROPERTY_ACTIVE_VALUE"] == "Да") {
                    $active = "Y"; 
                }
            } 
        }

        return $active;

    }

    //функция получения возраста человека с датой рождения $BIRTHDAY в выбранную дату $DATE  (в вормате дд.мм.гггг)
    function getFullAgeByDate($BIRTHDAY,$DATE){  
        //парсим дату рождения
        $arBirth = explode(".",$BIRTHDAY);
        //парсим нужную дату
        $arDate = explode(".",$DATE);        

        //(если месяц текущей даты больше месяцу рождения) или (месяц текущей даты = месяцу рождения и день даты > даты рождения)
        //другими словами - если у человека в нужном году уже был ДР, то просто считаем разницу лет
        if (($arDate[1] > $arBirth[1]) || ($arDate[1] == $arBirth[1] && $arDate[0] > $arBirth[0])) {
            $AGE = $arDate[2] - $arBirth[2]; 
        }
        //иначе считаем разницу лет и еще -1
        else {
            $AGE = $arDate[2] - $arBirth[2] - 1; 
        }

        return $AGE;

    }


    //получить величину скидки для цены: $PRICE - исходная величина, $DISCOUNT - размер скидки, $TYPE - единицы изменерия - рубли/проценты (R/P) 
    function getDiscountValue($PRICE,$DISCOUNT,$TYPE) {
        //проверяем единицы измерения
        switch ($TYPE) {
            //скидка в рублях - просто возвращаем число = $DISCOUNT 
            case "R": $totalDiscount = $DISCOUNT; break; 
                //проценты
            case "P": $totalDiscount = $PRICE * $DISCOUNT / 100;
        }

        return $totalDiscount;
    }


    //получение списка методов расчета, 
    #$age - возраст пассажира
    #$direction - направление
    #$method - id метода.
    #$hotel - id гостиницы 
    #$typeBooking - тип бронирования (например STANDART)   
    function getMathMethods($age,$direction,$method, $hotel, $typeBooking) {
        //получаем механизмы для текущего направления и возраста

        //получаем ID типов бронирования
        //получаем ID инфоблока с методами расчета
        $mathIblock = CIBlock::GetList(array(), array("CODE"=>"MATH"));
        $arMathIblock = $mathIblock->Fetch();

        $bookingTypes = CIBlockPropertyEnum::GetList(array(),Array("XML_ID"=>$typeBooking,"PROPERTY_ID"=>"TYPE_BOOKING", "IBLOCK_ID"=>$arMathIblock["ID"]));
        $arType = $bookingTypes->Fetch();


        $mathArFilter = array(
            "IBLOCK_CODE"=>"MATH",
            "PROPERTY_COMPANY"=>getCurrentCompanyId(), 
            "PROPERTY_DIRECTION"=> $direction,
            "PROPERTY_TYPE_BOOKING" => $arType["ID"]
        );

        if ($method != "N") {
            $mathArFilter["ID"] = $method;  
        }

        $mathArSelect = array(
            "PROPERTY_MATH_AGE_TO",
            "PROPERTY_MATH_AGE_FROM",
            "PROPERTY_MATH_TOUR",
            "PROPERTY_MATH_TOUR_DISCOUNT",
            "PROPERTY_MATH_ROAD",
            "PROPERTY_MATH_ROAD_DISCOUNT",
            "PROPERTY_DIRECTION",
            "NAME",
            "ID"
        );
        //подходящие под возраст схемы расчета
        $mathMethods = array(); 

        $math = CIBLockElement::GetList(array(), $mathArFilter, false, false, $mathArSelect);    
        while($arMath = $math->Fetch()) {
            //в конечный массив с методами расчета добавляем только те, которые подходят по возрасту текущего пассажира
            //arshow($arMath);
            //есть 2 границы возрастов
            if ($arMath["PROPERTY_MATH_AGE_FROM_VALUE"] != "" && 
                $arMath["PROPERTY_MATH_AGE_TO_VALUE"] != "" && 
                $arMath["PROPERTY_MATH_AGE_TO_VALUE"] > $age && 
                $arMath["PROPERTY_MATH_AGE_FROM_VALUE"] <= $age) {
                $mathMethods[] = $arMath;
                // break;    
            }
            //есть только нижняя граница возрастов
            else if ($arMath["PROPERTY_MATH_AGE_FROM_VALUE"] != "" && 
                !$arMath["PROPERTY_MATH_AGE_TO_VALUE"] != "" && 
                $arMath["PROPERTY_MATH_AGE_FROM_VALUE"] <= $age 
                ) {
                    $mathMethods[] = $arMath;  
                    //  break;
                }
                //есть только верхняя граница
                else if (!$arMath["PROPERTY_MATH_AGE_FROM_VALUE"] != "" && 
                    $arMath["PROPERTY_MATH_AGE_TO_VALUE"] != "" && 
                    $arMath["PROPERTY_MATH_AGE_TO_VALUE"] > $age){
                        $mathMethods[] = $arMath; 
                        //  break;
                    }

                    else {continue;}
        }   



        //проверяем гостиницу
        if ($hotel != "N") { 
            $hotel = CIBlockElement::GetList(array(),array("ID"=>$hotel), false, false, array("PROPERTY_ONE_MAN_FOR_BED"));
            $arHotel = $hotel->Fetch();  
            //$arHotel["PROPERTY_ONE_MAN_FOR_BED_VALUE"] == 'Да"   
            //проверяем методы, если выяснилось, что у отеля стоит флаг "один человек на 1 кровать"
            if ($arHotel["PROPERTY_ONE_MAN_FOR_BED_VALUE"] == "Да"){
                foreach ($mathMethods as $methodID=>$method){  
                    $mathTour = CIBlockPropertyEnum::GetList(array(),Array("ID"=>$method["PROPERTY_MATH_TOUR_ENUM_ID"]));
                    $arMathTour = $mathTour->Fetch();
                    //если у текущего метода стоимость проезда = коммунальным платежам, удаляем этот метод
                    if ($arMathTour["XML_ID"] == "PAYMENTS") {
                        unset($mathMethods[$methodID]);
                    }
                }
            } 
        }       

        return $mathMethods;

    }



    //расчет стоимости тура 
    #$tourID - ID тура, 
    #$bookingTYPE - тип бронирования (стандарт или только проживание) 
    #$extraPLACE - флаг "доп. место" (Y/N), 
    #$BIRTHDAY - дата рождения 
    #$mathMethod - ID метода расчета   
    function getTourPrice($tourID,$bookingTYPE,$extraPLACE,$BIRTHDAY,$mathMethod){       

        //получаем инфо о туре
        $arTourSelect = array(
            "PROPERTY_DIRECTION",
            "PROPERTY_CITY",
            "PROPERTY_HOTEL",
            "PROPERTY_ROOM",
            "PROPERTY_DATE_FROM",
            "PROPERTY_DATE_TO",
            "PROPERTY_PRICE",
            "PROPERTY_PRICE_ADDITIONAL_SEATS",
            "PROPERTY_DISCONT"
        );

        $tour = CIBLockElement::GetList(array(), array("ID"=>$tourID), false, false, $arTourSelect); 
        $arTour = $tour->Fetch();
        //arshow($arTour); 


        //получаем инфо об автобусе (для "ТОЛЬКО ПРОЕЗД")
        if ($bookingTYPE == "ONLY_ROAD" || $bookingTYPE == "DOUBLE_ROAD") {
            $bus = CIBlockElement::GetList(array(), array("ID"=>$tourID), false, false, array("PROPERTY_BUS_DIRECTION","ID"));
            $arBus = $bus->Fetch();
            $tourFilter = array();
            switch ($arBus["PROPERTY_BUS_DIRECTION_VALUE"]) {
                case "Туда": $tourFilter["PROPERTY_BUS_TO"] = $arBus["ID"]; break; 
                case "Обратно": $tourFilter["PROPERTY_BUS_BACK"] = $arBus["ID"]; break;
            }
            //получаем любой тур, к которому привязан данный автобус, чтобы получить направление
            $tour = CIBlockElement::GetList(array(), $tourFilter, false, false, $arTourSelect);
            $arTour = $tour->Fetch();
        }




        //для двойного тура собираем инфо по второму туру 
        if ($bookingTYPE == "DOUBLE_TOUR") {    
            $secondTourID = checkDoubleTour($tourID); 

            $secondTour = CIBLockElement::GetList(array(), array("ID"=>$secondTourID), false, false, $arTourSelect); 
            $arSecondTour = $secondTour->Fetch();
        }  


        //получаем направление
        $directionArSelect = array(
            "PROPERTY_ROAD_PRICE",
            "PROPERTY_ONLY_ROOM_ROAD_PRICE",
            "PROPERTY_DOUBLE_TOUR_ROAD_PRICE",
            "PROPERTY_ROAD_PRICE_IN_TOUR",
            "PROPERTY_ROAD_PRICE_BY_MONTH",
            "PROPERTY_MONTH_PRICE_1",
            "PROPERTY_MONTH_PRICE_2",
            "PROPERTY_MONTH_PRICE_3",
            "PROPERTY_MONTH_PRICE_4",
            "PROPERTY_MONTH_PRICE_5",
            "PROPERTY_MONTH_PRICE_6",
            "PROPERTY_MONTH_PRICE_7",
            "PROPERTY_MONTH_PRICE_8",
            "PROPERTY_MONTH_PRICE_9",
            "PROPERTY_MONTH_PRICE_10",
            "PROPERTY_MONTH_PRICE_11",
            "PROPERTY_MONTH_PRICE_12",                 
        );
        $direction = CIBlockElement::GetList(array(), array("ID"=>$arTour["PROPERTY_DIRECTION_VALUE"]), false, false, $directionArSelect);
        $arDirection = $direction->Fetch();
        //arshow($arDirection);    


        //полный возраст пассажира на момент начала тура
        $FULL_AGE = getFullAgeByDate($BIRTHDAY,$arTour["PROPERTY_DATE_FROM_VALUE"]);          

        if ($FULL_AGE >= 0) {          
            //получаем методы расчета для данного пассажира           
            $mathMethods =  getMathMethods($FULL_AGE,$arTour["PROPERTY_DIRECTION_VALUE"],$mathMethod,$arTour["PROPERTY_HOTEL_VALUE"], $bookingTYPE);
            //arshow($mathMethods);    
        }                                      

        //если методы расчеты не найдены, то стоимость тура = либо полной стоимости тура, либо стоимости доп места
        if (count($mathMethods) == 0) {
            if ($extraPLACE == "Y") {
                $PRICE = $arTour["PROPERTY_PRICE_ADDITIONAL_SEATS_VALUE"] /*- getDiscountValue($arTour["PROPERTY_PRICE_ADDITIONAL_SEATS_VALUE"],$arTour["PROPERTY_DISCONT_VALUE"],"P")*/; 
                $PRICE_SECOND = $arSecondTour["PROPERTY_PRICE_ADDITIONAL_SEATS_VALUE"] /*- getDiscountValue($arSecondTour["PROPERTY_PRICE_ADDITIONAL_SEATS_VALUE"],$arSecondTour["PROPERTY_DISCONT_VALUE"],"P")*/; 
            }
            else {                     
                $PRICE = $arTour["PROPERTY_PRICE_VALUE"] /*- getDiscountValue($arTour["PROPERTY_PRICE_VALUE"],$arTour["PROPERTY_DISCONT_VALUE"],"P")*/;  
                $PRICE_SECOND = $arSecondTour["PROPERTY_PRICE_VALUE"] /*- getDiscountValue($arSecondTour["PROPERTY_PRICE_VALUE"],$arSecondTour["PROPERTY_DISCONT_VALUE"],"P")*/; 
            } 
            //двойной тур. складывается из стоимости двух туров за вычетом "стоимости проезда для двойного тура"
            if ($bookingTYPE == "DOUBLE_TOUR") {
                $PRICE = $PRICE + $PRICE_SECOND - $arDirection["PROPERTY_DOUBLE_TOUR_ROAD_PRICE_VALUE"]; 
            }
            //только проживание
            if ($bookingTYPE == "ONLY_ROOM") {                  
                $PRICE = $PRICE - $arDirection["PROPERTY_ONLY_ROOM_ROAD_PRICE_VALUE"];
            }             

            //только проезд
            if ($bookingTYPE == "ONLY_ROAD" || $bookingTYPE == "DOUBLE_ROAD") {
                if ($arDirection["PROPERTY_ROAD_PRICE_BY_MONTH_VALUE"] == "Да") {
                    //парсим дату тура, чтобы получить месяц
                    $tour_date = explode(".",$arTour["PROPERTY_DATE_FROM_VALUE"]);
                    $month = intval($tour_date[1]);
                    $PRICE = $arDirection["PROPERTY_MONTH_PRICE_".$month."_VALUE"]; 
                }

                else {
                    $PRICE = $arDirection["PROPERTY_ROAD_PRICE_VALUE"]; //стоимость берется из свойства "только проезд"    
                } 

                if ($bookingTYPE == "DOUBLE_ROAD") {
                    $PRICE = $PRICE * 2; 
                }  
            }   

        }


        //если найдены механизмы расчета, то используем их для вычислния стоимости. по умолчанию берем первый попавшийся
        else {
            //в общем случае стоимость тура вычисляется по формуле:  
            //цена = (стоимость тура/доп места (или комунальных платежей) - скидка на тур) + (стоимость проезда - скидка на проезд)
            //price = (PROPERTY_MATH_TOUR_ENUM_ID - PROPERTY_MATH_TOUR_DISCOUNT_VALUE) + (PROPERTY_MATH_ROAD_ENUM_ID - PROPERTY_MATH_ROAD_DISCOUNT_VALUE)    

            //получаем текущие значения всех переменных

            ///////////////////1. стоимость тура///////////////////
            //получаем первое слагаемое
            $tourPrice = CIBlockPropertyEnum::GetList(array(),Array("ID"=>$mathMethods[0]["PROPERTY_MATH_TOUR_ENUM_ID"]));

            if ($arTourPrice = $tourPrice->Fetch()) {
                //проверяем, что выбрано в первом поле - стоимость тура, или комунальные платежи           
                switch($arTourPrice["XML_ID"]) {
                    //стоимость тура    $SECOND_TOUR_PRICE - для двойного тура
                    case "TOUR_PRICE" : 
                        if ($extraPLACE == "Y") {
                            $TOUR_PRICE = $arTour["PROPERTY_PRICE_ADDITIONAL_SEATS_VALUE"];   
                            //для двойного тура
                            if ($bookingTYPE == "DOUBLE_TOUR") {
                                $SECOND_TOUR_PRICE = $arSecondTour["PROPERTY_PRICE_ADDITIONAL_SEATS_VALUE"];
                                $TOUR_PRICE = $TOUR_PRICE + $SECOND_TOUR_PRICE;
                            }                              

                        }
                        else {                     
                            $TOUR_PRICE = $arTour["PROPERTY_PRICE_VALUE"];   
                            //для двойного тура                           
                            if ($bookingTYPE == "DOUBLE_TOUR") {
                                $SECOND_TOUR_PRICE = $arSecondTour["PROPERTY_PRICE_VALUE"];
                                $TOUR_PRICE = $TOUR_PRICE + $SECOND_TOUR_PRICE;
                            } 
                        }       

                        ; break;      
                        //комунальные платежи
                    case "PAYMENTS" : 
                        //получаем инфо о коммунальных платежах
                        $hotel = CIBlockElement::GetList(array(), array("ID"=>$arTour["PROPERTY_HOTEL_VALUE"]), false, false, array("PROPERTY_COST_UTILITIES_SERVICE"));
                        $arHotel = $hotel->Fetch();     
                        $TOUR_PRICE = $arHotel["PROPERTY_COST_UTILITIES_SERVICE_VALUE"];   
                        //для двойного тура
                        if ($bookingTYPE == "DOUBLE_TOUR") {
                            $SECOND_TOUR_PRICE = $arHotel["PROPERTY_COST_UTILITIES_SERVICE_VALUE"]; 
                            $TOUR_PRICE = $TOUR_PRICE + $SECOND_TOUR_PRICE;
                        }   
                        ; break;
                }

                //для двойного тура
                if ($bookingTYPE == "DOUBLE_TOUR") { 
                    $TOUR_PRICE = $TOUR_PRICE - $arDirection["PROPERTY_DOUBLE_TOUR_ROAD_PRICE_VALUE"]; 
                }

                //для только проживания 
                if ($bookingTYPE == "ONLY_ROOM") {                  
                    $TOUR_PRICE = $TOUR_PRICE - $arDirection["PROPERTY_ONLY_ROOM_ROAD_PRICE_VALUE"];
                }       

            } 
            else {
                $TOUR_PRICE = 0;  //первое слагаемое - стоимость тура
            }
            //////////////////////////////////////////////////////


            ///////////////////2. скидка на тур///////////////////
            //получаем скидку на тур
            $tourDiscount = CIBlockElement::GetList(array(), array("ID"=>$mathMethods[0]["PROPERTY_MATH_TOUR_DISCOUNT_VALUE"]), false, false, array("PROPERTY_DISCOUNT","PROPERTY_ED_IZM"));
            $arDiscount = $tourDiscount->Fetch();
            //arshow($arDiscount);
            //получем единицы измерения
            $discountValue = CIBlockPropertyEnum::GetList(array(), array("ID"=>$arDiscount["PROPERTY_ED_IZM_ENUM_ID"]));
            $arDiscountValue = $discountValue->Fetch();
            //arshow($arDiscountValue);

            $TOUR_DISCOUNT = getDiscountValue($TOUR_PRICE,$arDiscount["PROPERTY_DISCOUNT_VALUE"],$arDiscountValue["XML_ID"]);
            /////////////////////////////////////////////////////////                


            ///////////////////3. стоимость проезда///////////////////
            $roadPrice = CIBlockPropertyEnum::GetList(array(),Array("ID"=>$mathMethods[0]["PROPERTY_MATH_ROAD_ENUM_ID"]));

            if ($arTourPrice = $roadPrice->Fetch()) {
                //проверяем, что выбрано в первом поле - только проезд, только проживание или стоимость в туре 
                //получаем направление и его параметры   


                switch($arTourPrice["XML_ID"]) {
                    //только проезд
                    case "ROAD_PRICE": 

                        //если стоимость проезда задана помесячно, то берем ее за нужный месяц
                        if ($arDirection["PROPERTY_ROAD_PRICE_BY_MONTH_VALUE"] == "Да") {
                            //парсим дату тура, чтобы получить месяц
                            $tour_date = explode(".",$arTour["PROPERTY_DATE_FROM_VALUE"]);
                            $month = intval($tour_date[1]);
                            $ROAD_PRICE = $arDirection["PROPERTY_MONTH_PRICE_".$month."_VALUE"]; 

                            if ($bookingTYPE == "DOUBLE_ROAD") {
                                $ROAD_PRICE = $ROAD_PRICE * 2; 
                            }  
                        }

                        else {
                            $ROAD_PRICE = $arDirection["PROPERTY_ROAD_PRICE_VALUE"]; //стоимость берется из основной тсоимости тура    
                            if ($bookingTYPE == "DOUBLE_ROAD") {
                                $ROAD_PRICE = $ROAD_PRICE * 2; 
                            }  
                        }   

                        ; break;      
                        //только проживание    
                    case "ONLY_ROOM_ROAD_PRICE":
                        $ROAD_PRICE = $arDirection["PROPERTY_ONLY_ROOM_ROAD_PRICE_VALUE"];
                        break;
                        //стоимость проезда в туре
                    case "ROAD_PRICE_IN_TOUR":
                        $ROAD_PRICE = $arDirection["PROPERTY_ROAD_PRICE_IN_TOUR_VALUE"];
                        break; 
                        //стоимость для двойного тура

                }

            } 
            else {
                $ROAD_PRICE = 0;  //третье слагаемое - стоимость проезда
            }
            /////////////////////////////////////////////////////////       

            ///////////////////4. скидка на проезд///////////////////
            //получаем скидку на проезд
            $roadDiscount = CIBlockElement::GetList(array(), array("ID"=>$mathMethods[0]["PROPERTY_MATH_ROAD_DISCOUNT_VALUE"]), false, false, array("PROPERTY_DISCOUNT","PROPERTY_ED_IZM"));
            $arDiscount = $roadDiscount->Fetch();
            //arshow($arDiscount);
            //получем единицы измерения
            $discountValue = CIBlockPropertyEnum::GetList(array(), array("ID"=>$arDiscount["PROPERTY_ED_IZM_ENUM_ID"]));
            $arDiscountValue = $discountValue->Fetch();
            //arshow($arDiscountValue);

            $ROAD_DISCOUNT = getDiscountValue($ROAD_PRICE,$arDiscount["PROPERTY_DISCOUNT_VALUE"],$arDiscountValue["XML_ID"]);
            /////////////////////////////////////////////////////////         


            $PRICE = ($TOUR_PRICE - $TOUR_DISCOUNT) + ($ROAD_PRICE - $ROAD_DISCOUNT);        

        }   

        //echo "ЦЕНА: ".$PRICE;
        $PRICE = ceil($PRICE);

        return $PRICE;  

    }                            


    //проверяем возможность двойного тура для тура $ID
    // $check - проверять ли количество доступных мест - по умолчанию - нет
    function checkDoubleTour($ID, $check) {
        //выходное значение
        $secondTour = false;      

        $arSelect = array(
            "PROPERTY_DIRECTION",
            "PROPERTY_CITY",
            "PROPERTY_HOTEL",
            "PROPERTY_ROOM",
            "PROPERTY_DATE_FROM",
            "PROPERTY_DATE_TO",
            "IBLOCK_ID"
        );

        $tour = CIBlockElement::GetList(array(), array("ID"=>$ID), false, false, $arSelect);
        $arTour = $tour->Fetch(); 


        //проверяем направление, а именно время в пути.
        $direction = CIBlockElement::GetList(array(), array("ID"=>$arTour["PROPERTY_DIRECTION_VALUE"]), false, false, array("PROPERTY_ROAD_TIME"));
        $arDirection = $direction->Fetch();   

        //следующий тур должен начинаться в день, который равен дате окончания предыдущего тура - 2*время в пути 

        //параметры тура для выборки
        $arFilter = array(
            "PROPERTY_COMPANY" => getCurrentCompanyID(),
            "PROPERTY_DIRECTION"=>$arTour["PROPERTY_DIRECTION_VALUE"],  
            "PROPERTY_CITY"=>$arTour["PROPERTY_CITY_VALUE"],       
            "PROPERTY_HOTEL"=>$arTour["PROPERTY_HOTEL_VALUE"],  
            "PROPERTY_ROOM"=>$arTour["PROPERTY_ROOM_VALUE"], 
            "IBLOCK_ID"=>$arTour["IBLOCK_ID"],
            //  ">PROPERTY_NUMBER_ROOM" => 0 //количество номеров для второго тура должно быть больше 0        
        );

        if ($check == "Y") {
            $arFilter[">PROPERTY_NUMBER_ROOM"] = 0; 
        }


        //получаем нужную дату
        $firstTourDate = explode(".",$arTour["PROPERTY_DATE_TO_VALUE"]);
        //следующий день после окончания исходного тура
        $newDateLabel =  mktime(0,0,0,$firstTourDate[1],$firstTourDate[0],$firstTourDate[2]);
        global $DB;
        $arFilter["<PROPERTY_DATE_FROM"] = date("Y-m-d 00:00:00", $newDateLabel);
        $arFilter[">=PROPERTY_DATE_FROM"] = date("Y-m-d 00:00:00", $newDateLabel - 86400*2*$arDirection["PROPERTY_ROAD_TIME_VALUE"]);


        //проверяем следующий тур с такими же параметрами, как у первого
        $check = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter, false, false, array("ID"));
        if ($arCheck = $check->Fetch()) {
            $secondTour = $arCheck["ID"];  
        }

        //на выходе передаем ID следующего тура, а если он не найден то false
        return $secondTour;
    }     




    //проверяем предыдущий тур. аналог проверки двойного тура, тоьлко в обратную сторону - получаем предыдущий тур
    function checkPrevTour($ID) {
        //выходное значение
        $secondTour = false;       

        $arSelect = array(
            "PROPERTY_DIRECTION",
            "PROPERTY_CITY",
            "PROPERTY_HOTEL",
            "PROPERTY_ROOM",
            "PROPERTY_DATE_FROM",
            "PROPERTY_DATE_TO",
            "IBLOCK_ID"
        );

        $tour = CIBlockElement::GetList(array(), array("ID"=>$ID), false, false, $arSelect);
        $arTour = $tour->Fetch(); 


        //проверяем направление, а именно время в пути.
        $direction = CIBlockElement::GetList(array(), array("ID"=>$arTour["PROPERTY_DIRECTION_VALUE"]), false, false, array("PROPERTY_ROAD_TIME"));
        $arDirection = $direction->Fetch();   

        //следующий тур должен начинаться в день, который равен дате окончания предыдущего тура - 2*время в пути 

        //параметры тура для выборки
        $arFilter = array(
            "PROPERTY_COMPANY" => getCurrentCompanyID(),
            "PROPERTY_DIRECTION"=>$arTour["PROPERTY_DIRECTION_VALUE"],  
            "PROPERTY_CITY"=>$arTour["PROPERTY_CITY_VALUE"],       
            "PROPERTY_HOTEL"=>$arTour["PROPERTY_HOTEL_VALUE"],  
            "PROPERTY_ROOM"=>$arTour["PROPERTY_ROOM_VALUE"], 
            "IBLOCK_ID"=>$arTour["IBLOCK_ID"],
        );



        //получаем нужную дату
        $firstTourDate = explode(".",$arTour["PROPERTY_DATE_FROM_VALUE"]);
        //следующий день после окончания исходного тура
        $newDateLabel =  mktime(0,0,0,$firstTourDate[1],$firstTourDate[0],$firstTourDate[2]);
        $arFilter[">PROPERTY_DATE_TO"] = date("Y-m-d 00:00:00", $newDateLabel);
        $arFilter["<=PROPERTY_DATE_TO"] = date("Y-m-d 23:59:00", $newDateLabel + 86400*2*$arDirection["PROPERTY_ROAD_TIME_VALUE"]);
        //arshow($arFilter);

        //проверяем следующий тур с такими же параметрами, как у первого
        $check = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter, false, false, array("ID"));
        if ($arCheck = $check->Fetch()) {
            $secondTour = $arCheck["ID"];  
        }

        //на выходе передаем ID следующего тура, а если он не найден то false
        return $secondTour;
    }                    




    //получаем список групп пользователя ID=>СИМВОЛЬНЫЙ КОД
    function getUserGroup($userID){
        $groups = array();

        //массив ID групп
        $groupsIDS = CUser::GetUserGroup($userID);

        //собираем инфо о группах пользователя, а именно символьный код
        foreach ($groupsIDS as $groupID) {
            $group = CGroup::GetById($groupID);
            $arGroup = $group->Fetch();
            $groups[$arGroup["ID"]] = $arGroup["STRING_ID"];
        }

        return $groups;        

    }



    //функция получения свойства компании из настроек
    function getCompanyProperties(){
        //получаем количество дней для помещения заказа в архив и другие свойства          
        $ID = getCurrentCompanyID();         
        $property = CIBlockElement::GetList(array(),array("ID"=>$ID), false, false,  array("ID", "IBLOCK_ID", "PROPERTY_*"));  
        while($ob = $property->GetNextElement()){                           
            $prop = $ob->GetProperties();    
            $arProps = $prop ;         
        }
        return $arProps;

    }     


    //функция, вычисляющая скидку агентству, в %
    function getAgencyDiscount() {
        if (!is_object($USER)) {$USER = new CUser;} 
        $props = getCompanyProperties();
        $company = CUser::GetById($USER->GetId());
        $arCompany = $company->Fetch();
        //скидка агентствам из настроек + доп скидка для агентства
        $discount = $props["DISCOUNT"]["VALUE"] + $arCompany["UF_COMPANY_DISCOUNT"];

        return $discount;            
    } 

    //функция, вычисляющая ДОПОЛНИТЕЛЬНУЮ скидку агентству, в %
    function getAgencyAdditionalDiscount() {
        if (!is_object($USER)) {$USER = new CUser;} 
        $company = CUser::GetById($USER->GetId());
        $arCompany = $company->Fetch();
        //скидка агентствам из настроек + доп скидка для агентства
        $discount = $arCompany["UF_COMPANY_DISCOUNT"];    
        return $discount;    
    }


    //функция вычисляющая доп скидку на тур
    #$tourID - ID тура
    function getAddTourDiscount($tourID) {
        $tour = CIBlockElement::GetList(array(), array("ID"=>$tourID), false, false, array("PROPERTY_DISCONT"));
        $arTour = $tour->Fetch(); 

        return  $arTour["PROPERTY_DISCONT_VALUE"];
    }      



    //функция получения скидки для направления/типа бронирования
    function getServiceDiscount($direction,$service) {
        //инфоблок с комиссией
        $iblock = CIblock::GetList(array(),array("CODE"=>"PERCENTS"));
        $arIblock = $iblock->Fetch();    
        //получим ID типа бронирования по его коду
        $type = CIBlockPropertyEnum::GetList(array(),array("CODE"=>"TYPE_BOOKING","XML_ID"=>$service,"IBLOCK_ID"=>$arIblock["ID"]));
        $arType = $type->Fetch();
        //получаем значение скидки
        $discount = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"PERCENTS","PROPERTY_DIRECTION"=>$direction,"PROPERTY_TYPE_BOOKING"=>$arType["ID"]), false, false, array("PROPERTY_DISCOUNT"));
        $arDiscount = $discount->Fetch();

        //если значение не найдено - выводим дефолтную скидку для агентства
        if ($arDiscount["PROPERTY_DISCOUNT_VALUE"] == "" ) {
            $discount = getAgencyDiscount();
        }

        else {
            $discount = $arDiscount["PROPERTY_DISCOUNT_VALUE"];
        }

        return $discount;

    }



    //собираем данне о заказе $ID
    function getOrderInfo($ID) {

        $arOrderInfo = array();

        $companyId = getCurrentCompanyID();

        $arFilter = array("ID"=>$ID,
            "PROPERTY_COMPANY"=>$companyId
        );

        $arSelect = array(
            "NAME",
            "DATE_CREATE",        
            "CREATED_BY",
            "PROPERTY_TOUR",
            "PROPERTY_STATUS",
            "PROPERTY_OPERATOR_PRICE",
            "PROPERTY_PRICE",
            "PROPERTY_TYPE_BOOKING",
            "PROPERTY_NOTES",
            "PROPERTY_BUS_ID",
            "PROPERTY_SECOND_BUS_ID",
            "PROPERTY_DEPARTURE_CITY",
            "PROPERTY_DATE_FROM",
            "PROPERTY_CITY",
            "PROPERTY_HOTEL",
            "PROPERTY_COMPANY_NAME",
        );


        $order = CIBLockElement::GetList(array(), $arFilter, false, false, $arSelect);
        if ($arOrder = $order->Fetch()) {
            $arOrderInfo = array(
                "ID"               => $ID,
                "NAME"             => $arOrder["NAME"],
                "DATE_CREATE"      => $arOrder["DATE_CREATE"],
                "COMPANY"          => array("ID"=>$arOrder["CREATED_BY"], "NAME"=>$arOrder["PROPERTY_COMPANY_NAME_VALUE"]),
                "TYPE_BOOKING"     => array("ID"=>$arOrder["PROPERTY_TYPE_BOOKING_ENUM_ID"],"NAME"=>$arOrder["PROPERTY_TYPE_BOOKING_VALUE"]),
                "CITY"             => array("ID"=>$arOrder["PROPERTY_CITY_VALUE"],"NAME"=>get_iblock_element_name($arOrder["PROPERTY_CITY_VALUE"])),
                "HOTEL"            => array("ID"=>$arOrder["PROPERTY_HOTEL_VALUE"],"NAME"=>get_iblock_element_name($arOrder["PROPERTY_HOTEL_VALUE"])),
                "PRICE"            => $arOrder["PROPERTY_PRICE_VALUE"],
                "PRICE_AGENCY"     => $arOrder["PROPERTY_OPERATOR_PRICE_VALUE"],
                "BUS_ID"           => $arOrder["PROPERTY_BUS_ID_VALUE"],
                "SECOND_BUS_ID"    => $arOrder["PROPERTY_SECOND_BUS_ID_VALUE"],
                "DEPARTURE_CITY"   => $arOrder["PROPERTY_DEPARTURE_CITY_VALUE"],
                "TRANSFER_PRICE"   => getTransferPrice($arOrder["PROPERTY_DEPARTURE_CITY_VALUE"])
            );




            //если есть тур 
            if ($arOrder["PROPERTY_TOUR_VALUE"] > 0) {
                $arTourSelect = array(
                    "NAME",
                    "ID",
                    "PROPERTY_ROOM",
                    "PROPERTY_DATE_FROM",
                    "PROPERTY_DATE_TO",
                    "PROPERTY_DIRECTION"
                );

                $tour = CIBLockElement::GetList(array(),array("ID"=>$arOrder["PROPERTY_TOUR_VALUE"]), false, false, $arTourSelect);
                $arTour =$tour->Fetch();

                $arOrderInfo["TOUR"] = array(
                    "ID" => $arTour["ID"],
                    "NAME" => $arTour["NAME"],
                    "DATE_FROM" => $arTour["PROPERTY_DATE_FROM_VALUE"],
                    "DATE_TO" => $arTour["PROPERTY_DATE_TO_VALUE"],
                    "ROOM" => array("ID"=>$arTour["PROPERTY_ROOM_VALUE"], "NAME"=>get_iblock_element_name($arTour["PROPERTY_ROOM_VALUE"]))
                );  

                //для двойного тура
                if ($arOrderInfo["TYPE_BOOKING"]["NAME"] == "двойной тур") {

                    $secondTour = CIBLockElement::GetList(array(),array("ID"=>checkDoubleTour($arOrder["PROPERTY_TOUR_VALUE"])), false, false, $arTourSelect);
                    $arSecondTour = $secondTour->Fetch();
                    $arOrderInfo["TOUR"]["DATE_TO"] = $arSecondTour["PROPERTY_DATE_TO_VALUE"];
                }                 

                //собираем инфо о направлении
                if ($arTour["PROPERTY_DIRECTION_VALUE"] > 0) {
                    $direction = CIBlockElement::GetList(array(), array("ID"=>$arTour["PROPERTY_DIRECTION_VALUE"]), false, false, array("PROPERTY_ROAD_TIME"));
                    $arDirection = $direction->Fetch();      
                } 

                //даты отдыха
                $arOrderInfo["DIRECTION"]["ROAD_TIME"] = $arDirection["PROPERTY_ROAD_TIME_VALUE"];

                //вычисляем дни отдыха
                $dateFrom = explode(".",$arTour["PROPERTY_DATE_FROM_VALUE"]);
                $labelDateFrom = mktime(0,0,0,$dateFrom[1],$dateFrom[0],$dateFrom[2]) + 86400 * $arOrderInfo["DIRECTION"]["ROAD_TIME"];
                $arOrderInfo["TOUR"]["REST_DATE"]["FROM"] = date("d.m.Y",$labelDateFrom);

                $dateTo = explode(".",$arOrderInfo["TOUR"]["DATE_TO"]);
                $labelDateTo = mktime(0,0,0,$dateTo[1],$dateTo[0],$dateTo[2]) - 86400 * $arOrderInfo["DIRECTION"]["ROAD_TIME"];
                $arOrderInfo["TOUR"]["REST_DATE"]["TO"] = date("d.m.Y",$labelDateTo);

                $arOrderInfo["TOUR"]["REST_LENGTH"] = ceil(($labelDateTo - $labelDateFrom) / 86400); //длина отдыха, дней

            }

            //только проезд
            else {    

                $busSelect = array(
                    "PROPERTY_BUS_DIRECTION"
                );

                $bus = CIBlockElement::GetList(array(), array("ID"=>$arOrder["PROPERTY_BUS_ID_VALUE"]), false, false, $busSelect);
                $arBus = $bus->Fetch();

                $arOrderInfo["BUS_DIRECTION"] = $arBus["PROPERTY_BUS_DIRECTION_VALUE"];     

                $arTourSelect = array(
                    "NAME",
                    "ID",
                    "PROPERTY_ROOM",
                    "PROPERTY_DATE_FROM",
                    "PROPERTY_DATE_TO",
                    "PROPERTY_DIRECTION"
                );

                $tourFilter = array();
                switch ($arOrderInfo["BUS_DIRECTION"]) {
                    case "Туда": $tourFilter["PROPERTY_BUS_TO"] = $arOrder["PROPERTY_BUS_ID_VALUE"]; break;
                    case "Обратно": $tourFilter["PROPERTY_BUS_BACK"] = $arOrder["PROPERTY_BUS_ID_VALUE"]; break;
                }


                $tour = CIBLockElement::GetList(array(),$tourFilter, false, false, $arTourSelect);
                $arTour =$tour->Fetch();

                $arOrderInfo["TOUR"] = $arTour;

                $arOrderInfo["DIRECTION"]["NAME"] = get_iblock_element_name($arTour["PROPERTY_DIRECTION_VALUE"]);


            }    

            //собираем туристов
            $tourist = CIBlockElement::GetList(array(), array("PROPERTY_ORDER"=>$ID), false, false, array("ID","NAME","PROPERTY_PLACE","PROPERTY_PHONE","PROPERTY_PASSPORT","PROPERTY_BIRTHDAY" ,"PROPERTY_SECOND_PLACE" ,"PROPERTY_SERVICES"));
            $i = 0; //счетчик
            $prev = 0;  //id предыдущего туриста (используется для сбора доп услуг)
            while($arTourist = $tourist->Fetch()) { 
                if ($arTourist["ID"] != $prev && $prev != 0) {$i++;} 
                $services[$i][] = $arTourist["PROPERTY_SERVICES_VALUE"];
                $arOrderInfo["TOURISTS"][$i] = array(
                    "ID"=>$arTourist["ID"],
                    "NAME"=>$arTourist["NAME"],
                    "PHONE"=>$arTourist["PROPERTY_PHONE_VALUE"],
                    "BIRTHDAY"=>$arTourist["PROPERTY_BIRTHDAY_VALUE"],
                    "PASSPORT"=>$arTourist["PROPERTY_PASSPORT_VALUE"],
                    "PLACE"=>$arTourist["PROPERTY_PLACE_VALUE"],
                    "SECOND_PLACE"=>$arTourist["PROPERTY_SECOND_PLACE_VALUE"],
                    "SERVICES"=> $services[$i]
                );
                if ($arTourist["PROPERTY_PLACE_VALUE"]) {
                    $arOrderInfo["PLACES"][] = $arTourist["PROPERTY_PLACE_VALUE"];
                }
                if ($arTourist["PROPERTY_SECOND_PLACE_VALUE"]) {
                    $arOrderInfo["SECOND_PLACES"][] = $arTourist["PROPERTY_SECOND_PLACE_VALUE"];     
                }
                $prev = $arTourist["ID"];
            }



            $userID = $arOrder["CREATED_BY"];

            //проверяем является пользователь оператором или менеджером оператора
            $USER = new CUser;
            //if (checkUserStatus($USER->GetId()) == "Y") {                
            $userID = getUserOperator($USER->GetId());
            //}          

            //дополнительные параметры компании
            $companyProps = CIBlockElement::GetList(array(),array("ID"=>$companyId),false,false,array("ID","NAME","PROPERTY_STAMP"))->Fetch();

            //данные компании
            $user = CUser::GetById($userID);
            $arUser = $user->Fetch(); 
            //arshow($arUser);
            $arOrderInfo["COMPANY"] = array(
                "ID" => $arUser["ID"],
                "NAME" => $arUser["NAME"],
                "MANAGER" => $arUser["SECOND_NAME"],
                "EMAIL" => $arUser["EMAIL"],
                "PHONE" => $arUser["PERSONAL_PHONE"],
                "CITY"  => $arUser["UF_COMPANY_CITY"],
                "COMPANY_PHONE" => $arUser["UF_COMPANY_PHONE"],
                "COMPANY_EMAIL" => $arUser["UF_COMPANY_EMAIL"],
                "COMPANY_FULL_NAME" => $arUser["UF_FULL_NAME"],
                "STAMP" => $companyProps["PROPERTY_STAMP_VALUE"],
                "ALL_PROPS" => $arUser
            );




            //создатель заказа
            $createdBy = CUser::GetById($arOrder["CREATED_BY"]);
            $arCreatedBy = $createdBy->Fetch();

            $arOrderInfo["CREATED_BY"] = $arCreatedBy;      


            return  $arOrderInfo;
        }

        else {
            return "заказ не найден!";
        }

    }


    //функция которая возвращает название места по его коду
    function getPlaceName($place) {

        $places = "";
        if($place != ''){     

            $placeArr = explode('_', $place);

            if($placeArr[3] == 1){
                $type = 'ЛС-О';
            }
            elseif($placeArr[3] == 2){
                $type = 'ЛС-П';
            }
            elseif($placeArr[3] == 4){
                $type = 'ПС-П';
            }
            if($placeArr[3] == 5){
                $type = 'ПС-О';
            }

            $places .= $placeArr[1].' ряд ('.$type.')';
        }

        return $places;
    }


    //проверяем не заблокировал ли туроперетор доступ к системе
    function checkLock() {
        $props = getCompanyProperties();
        $lock = "N";

        //проверяем, к какой группе принадлежит пользователь
        if (!is_object($USER)) {$USER = new CUser;}
        $groups = getUserGroup($USER->GetId());
        //если система заблокирована и пользователь не является туроперетором
        if ($props["LOCK"]["VALUE"] == "Да" && !in_array("TOUR_OPERATOR",$groups) && $USER->IsAuthorized() ) {
            $lock = "Y";
        }

        return $lock;
    }


    //получаем стоимость трансфера по ID города забора туристов
    function getTransferPrice($cityID) {
        if ($cityID > 0) {
            $city = CIBlockElement::GetList(array(), array("ID"=>$cityID), false, false, array("PROPERTY_PRICE"));
            $arCity = $city->Fetch();
        }

        if ($arCity["PROPERTY_PRICE_VALUE"] > 0) {
            $price = $arCity["PROPERTY_PRICE_VALUE"];
        }  
        else {
            $price = 0;
        }

        return $price;
    }



    //функция проверяет, является ли пользователь менеджером туроператора или оператором
    function checkUserStatus($ID) {
        $status = "N";
        if ($ID){
            $user = CUser::GetById($ID);
            $arUser = $user->Fetch();
            if ($arUser["UF_IS_OPERATOR"] == 1) {
                $status = "Y";   
            }  

            //также проверяем группы пользователя
            $groups = getUserGroup($ID);           
            if (in_array("TOUR_OPERATOR",$groups)) {
                $status = "Y"; 
            }             
        }


        return $status;  
    }


    //проверяем, включены ли у туроператора уведомления
    function checkNotice() {
        $notice = "N";
        $props = getCompanyProperties();          
        if ($props["SEND_MESSAGES"]["VALUE"] && $props["EMAIL"]["VALUE"]) {
            $notice = "Y";   
        }

        return $notice;
    }  



    /**
    * Возвращает сумму прописью
    * @author runcore
    * @uses morph(...)
    */
    function num2str($num) {
        $nul='ноль';
        $ten=array(
            array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
            array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
        );
        $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
        $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
        $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
        $unit=array( // Units
            array('копейка' ,'копейки' ,'копеек',     1),
            array('рубль'   ,'рубля'   ,'рублей'    ,0),
            array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
            array('миллион' ,'миллиона','миллионов' ,0),
            array('миллиард','милиарда','миллиардов',0),
        );
        //
        list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub)>0) {
            foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit)-$uk-1; // unit key
                $gender = $unit[$uk][3];
                list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
                else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
            } //foreach
        }
        else $out[] = $nul;
        $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
        $out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
    }

    /**
    * Склоняем словоформу
    * @ author runcore
    */
    function morph($n, $f1, $f2, $f5) {
        $n = abs(intval($n)) % 100;
        if ($n>10 && $n<20) return $f5;
        $n = $n % 10;
        if ($n>1 && $n<5) return $f2;
        if ($n==1) return $f1;
        return $f5;
    }



    //получить ID пользователя-туроператора для текущего пользователя
    function getUserOperator($ID) {
        //получаем список гркпп, чтобы получить ID группы туроператоров
        $groups = CGroup::GetList(($by="id"), ($order="asc"), array(),"N");
        $groupID = 0;
        while($arGroup = $groups->Fetch()) {
            if ($arGroup["STRING_ID"] == "TOUR_OPERATOR") {
                $groupID = $arGroup["ID"];
                break;  
            }
        }

        $operator = CUser::GetList(($by="id"), ($order="asc"), array("UF_COMPANY_ID"=>getCurrentCompanyID(), "GROUPS_ID"=>$groupID),array());
        $arOperator = $operator->Fetch();              
        return $arOperator["ID"];               
    }



    //проверяем, есть ли свободные номера для данного тура
    function checkTourRoom($tourID){
        $res = "N"; //тур недоступен
        $tour = CIBlockElement::GetList(array(),array("ID"=>$tourID), false, false, array("PROPERTY_NUMBER_ROOM"));
        $arTour = $tour->Fetch();

        if ($arTour["PROPERTY_NUMBER_ROOM_VALUE"] > 0) {
            $res = "Y"; //тур доступен, если еще остались свободные номера  
        }

        return $res;
    }


    //проверяем доступность места в автобусе
    function checkBusPlace($place,$bus) {
        $res = "N"; //место недоступно
        $bus = CIBLockElement::GetList(array(), array("ID"=>$bus), false, false, array("PROPERTY_P_SCHEME"));
        $arBus = $bus->Fetch();

        $scheme_decode = json_decode($arBus["PROPERTY_P_SCHEME_VALUE"], true);
        //перебираем схему, как только нашли места, проверяем место
        foreach($scheme_decode as $n=>$val) {
            foreach ($val as $i=>$busPlaceStatus){
                if ($i == $place && $busPlaceStatus == "FP") {                    
                    $res = "Y";  //место свободно 
                    break;
                }
            }
        }

        return $res;
    }

?>