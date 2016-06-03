<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();



    // $COMPANY_ID = null;
    if (!getCurrentCompanyID()) {
        $APPLICATION->AuthForm("");
    }   

    global $USER;
    $userID = $USER->GetID();  

    //проверяем тип бронирования. если через GET тип не передали, то тип=СТАНДАРТ

    switch ($_GET["TYPE"]) {
        case "ONLY_ROAD": $arResult["TYPE_BOOKING"] = $_GET["TYPE"]; break;

        case "ONLY_ROOM": $arResult["TYPE_BOOKING"] = $_GET["TYPE"]; break;

        case "DOUBLE_TOUR": $arResult["TYPE_BOOKING"] = $_GET["TYPE"]; break;

        case "DOUBLE_ROAD": $arResult["TYPE_BOOKING"] = $_GET["TYPE"]; break;  

        default: $arResult["TYPE_BOOKING"] = "STANDART";
    }


    //соберем возможный статусы заказа
    $statuses = array(); //XML_ID => ID статусов
    $statusesNAMES = array(); //XML_ID => название статусов
    $order_statuses = CIBlockProperty::GetPropertyEnum("STATUS",array("SORT"=>"ASC"), Array());
    while($arStatus = $order_statuses->Fetch()) { 
        $statuses[$arStatus["XML_ID"]] =  $arStatus["ID"]; 
        $statusesNAMES[$arStatus["ID"]] = $arStatus["VALUE"];
    } 

    //получаем инфо о пользователе
    $user = CUser::GetById($userID);
    $arUser = $user->Fetch();


    //получаем настройки компании
    $companySettings = getCompanyProperties();  


    //получаем доступные типы бронирования
    //получаем ID инфоблока заказов
    $ordersIblock = CIBlock::GetList(array(), array("CODE"=>"ORDERS"));
    $arOrdresIblock = $ordersIblock->Fetch();

    $avaible_booking_types = array(); //массив доступных типов бронирования вида ТИП=>ID, например STANDART=>10
    $avaible_booking_types_NAMES = array(); //названия типов бронирования
    $booking_types = CIBlockPropertyEnum::GetList(array(), Array("CODE"=>"TYPE_BOOKING","IBLOCK_ID"=>$arOrdresIblock["ID"]));
    while($booking_type = $booking_types->Fetch()) {
        $avaible_booking_types[$booking_type["XML_ID"]] = $booking_type["ID"];
        $avaible_booking_types_NAMES[$booking_type["XML_ID"]] = $booking_type["VALUE"];
    }    

    //скидка для агентства (с учетом дополнительной)
    $arResult["AGENCY_DISCOUNT"] = getAgencyDiscount();    

    //ДОПОЛОНИТЕЛЬНАЯ скидка для агентства (только дополнительная)
    $arResult["AGENCY_ADDITIONAL_DISCOUNT"] = getAgencyAdditionalDiscount(); 




    /////////////////////////////////////////////////////////////////////////
    //////////////СТАНДАРТНОЕ БРОНИРОВАНИЕ ИЛИ ТОЛЬКО ПРОЖИВАНИЕ/////////////
    /////////////////////////////////////////////////////////////////////////

    if ($arResult["TYPE_BOOKING"] == "STANDART" || $arResult["TYPE_BOOKING"] == "ONLY_ROOM" ) {   


        //текущий заказ
        $arResult["TOUR_ID"] = ($arParams["TOUR_ID"]);
        $arResult["ID"] = $arResult["TOUR_ID"];
        $arResult["DIRECTION"] = array();
        $arResult["CITY"] = array();
        $arResult["HOTEL"] = array();
        $arResult["ROOM"] = array();
        $arResult["BUS_TO"] = array();
        $arResult["BUS_BACK"] = array();


        //собираем параметры тура 

        $arSelect = array(
            "ID",
            "NAME",
            "PROPERTY_COMPANY",
            "PROPERTY_DIRECTION",
            "PROPERTY_CITY",
            "PROPERTY_ROOM",
            "PROPERTY_DATE_FROM",
            "PROPERTY_DATE_TO",
            "PROPERTY_PRICE",
            "PROPERTY_DISCONT",  
            "PROPERTY_HOTEL",
            "PROPERTY_PRICE_ADDITIONAL_SEATS",
            "PROPERTY_BUS_TO",
            "PROPERTY_BUS_BACK"
        );

        $arFilter = array("IBLOCK_CODE"=>"TOUR","ID"=>$arParams["TOUR_ID"],"PROPERTY_COMPANY"=>getCurrentCompanyID());

        $tour = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $arTour = $tour->Fetch();

        $arResult["PRICE"] = $arTour["PROPERTY_PRICE_VALUE"];
        $arResult["DATE_FROM"] = $arTour["PROPERTY_DATE_FROM_VALUE"];
        $arResult["DATE_TO"] = $arTour["PROPERTY_DATE_TO_VALUE"];
        $arResult["DISCOUNT"] = $arTour["PROPERTY_DISCONT_VALUE"];
        $arResult["PRICE_ADDITIONAL_SEATS"] = $arTour["PROPERTY_PRICE_ADDITIONAL_SEATS_VALUE"];
        $arResult["COMPANY"] = $arTour["PROPERTY_COMPANY_VALUE"];

        //получаем информацию о направлении
        $direction = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"DIRECTION","ID"=>$arTour["PROPERTY_DIRECTION_VALUE"]));
        $arDirection = $direction->Fetch();
        $arResult["DIRECTION"]["ID"] = $arDirection["ID"];
        $arResult["DIRECTION"]["NAME"] = "Направление";
        $arResult["DIRECTION"]["VALUE"] = $arDirection["NAME"];

        //получаем информацию о городе
        $city = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"CITY","ID"=>$arTour["PROPERTY_CITY_VALUE"]));
        $arCity = $city->Fetch();
        $arResult["CITY"]["ID"] = $arCity["ID"];
        $arResult["CITY"]["NAME"] = "Город";
        $arResult["CITY"]["VALUE"] = $arCity["NAME"]; 

        //получаем информацию об отеле
        $hotel = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"HOTEL","ID"=>$arTour["PROPERTY_HOTEL_VALUE"]));
        $arHotel = $hotel->Fetch();
        $arResult["HOTEL"]["ID"] = $arHotel["ID"];
        $arResult["HOTEL"]["NAME"] = "Гостиница"; 
        $arResult["HOTEL"]["VALUE"] = $arHotel["NAME"];     

        //получаем информацию о номере
        $room = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"ROOM","ID"=>$arTour["PROPERTY_ROOM_VALUE"]),false, false, array("ID","NAME","PROPERTY_NUMBER_SEATS", "PROPERTY_IS_ADD_ADDITIONAL_SEATS"));
        $arRoom = $room->Fetch();
        $arResult["ROOM"]["ID"] = $arRoom["ID"];
        $arResult["ROOM"]["NAME"] = "Номер"; 
        $arResult["ROOM"]["VALUE"] = $arRoom["NAME"];
        $arResult["ROOM"]["PLACES"] = $arRoom["PROPERTY_NUMBER_SEATS_VALUE"];
        $arResult["ROOM"]["ADDITIONAL_PLACES"] = $arRoom["PROPERTY_IS_ADD_ADDITIONAL_SEATS_VALUE"];
        $additional_places_count = 0; //количествл доп мест
        if ($arResult["ROOM"]["ADDITIONAL_PLACES"] == "Да") {$additional_places_count = 1;}
        $arResult["ROOM"]["TOTAL_PLACES_COUNT"] = $arResult["ROOM"]["PLACES"] + $additional_places_count;

        //получаем информацию об автобусе ТУДА для данного тура 
        $bus_to = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"BUS_ON_TOUR","ID"=>$arTour["PROPERTY_BUS_TO_VALUE"]), false, false, array("NAME","ID","PROPERTY_P_SCHEME","PROPERTY_BUS_DIRECTION","PROPERTY_DEPARTURE", "PROPERTY_ARRIVAL"));
        $arBusTo = $bus_to->Fetch();
        $arResult["BUS_TO"]["ID"] = $arBusTo["ID"];
        $arResult["BUS_TO"]["NAME"] = "Автобус (Туда)"; 
        $arResult["BUS_TO"]["SCHEME"] = $arBusTo["PROPERTY_P_SCHEME_VALUE"]; 
        $arResult["BUS_TO"]["DEPARTURE"] = $arBusTo["PROPERTY_DEPARTURE_VALUE"];
        $arResult["BUS_TO"]["ARRIVAL"] = $arBusTo["PROPERTY_ARRIVAL_VALUE"];

        //получаем информацию об автобусе ОБРАТНО для данного тура 
        $bus_back = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"BUS_ON_TOUR","ID"=>$arTour["PROPERTY_BUS_BACK_VALUE"]), false, false, array("NAME","ID","PROPERTY_P_SCHEME","PROPERTY_BUS_DIRECTION","PROPERTY_DEPARTURE", "PROPERTY_ARRIVAL"));
        $arBusBack = $bus_back->Fetch();
        $arResult["BUS_BACK"]["ID"] = $arBusBack["ID"];
        $arResult["BUS_BACK"]["NAME"] = "Автобус (Обратно)"; 
        $arResult["BUS_BACK"]["SCHEME"] = $arBusBack["PROPERTY_P_SCHEME_VALUE"];
        $arResult["BUS_BACK"]["DEPARTURE"] = $arBusTo["PROPERTY_DEPARTURE_VALUE"];
        $arResult["BUS_BACK"]["ARRIVAL"] = $arBusTo["PROPERTY_ARRIVAL_VALUE"]; 


        //для туристов, заказывающих стандартное бронирование нужно учитывать занятые места в обоих схемах,
        //поэтому видимая схема будет объединением схем автобусов туда и обратно

        $scheme_to = json_decode($arResult["BUS_TO"]["SCHEME"], true);
        $scheme_back = json_decode($arResult["BUS_BACK"]["SCHEME"], true);

        $view_scheme = array(); //объединенная схема
        foreach ($scheme_to as $row=>$place) {
            foreach ($place as $number=>$status) {
                if ($status == "PP" or $scheme_back[$row][$number] == "PP") { //если место занято в автобусе "туда" или "обратно", то оно помечается как занятое
                    $view_scheme[$row][$number] = "PP";  
                } 
                else {
                    $view_scheme[$row][$number] = $status;  
                }
            }
        }

        //схема рассадки с учетом занятых мест в обоих автобусах (туда и обратно)
        $arResult["BUS_SCHEME_VIEW"] =  json_encode($view_scheme);


        //arshow($arResult);

        if ($arResult["TYPE_BOOKING"] == "STANDART")  {

            $arResult["STEP"] = (int) CBRequest::gi()->getPost("STEP");
            if (!($arResult["STEP"] >= 1 && $arResult["STEP"] <= 3)) {
                $arResult["STEP"] = 1;
            }
            if (!(isset($_POST["ORDER_MAKE"]) && check_bitrix_sessid())) {
                $arResult["STEP"] = 1;
            }

        }

        //для только проживания пропускаем первый шаг с выбором мест

        if ($arResult["TYPE_BOOKING"] == "ONLY_ROOM")  {

            $arResult["STEP"] = (int) CBRequest::gi()->getPost("STEP");

            if (!($arResult["STEP"] >= 1 && $arResult["STEP"] <= 3)) {
                $arResult["STEP"] = 2;
            }
            if (!(isset($_POST["ORDER_MAKE"]) && check_bitrix_sessid())) {
                $arResult["STEP"] = 2;
            }

        }    


        //при переходе на второй шаг, нам нужно в схеме рассадки для данного тура отметить места туристов как забронированные
        if ($arResult["STEP"] == 2) {

            //логируем данные
            $postData = serialize($_POST);
            eventLogAdd("BUSTOURPRO_EVENT_PLACES_BOOKING",$arResult["ID"],$postData);


            //проверяем не занял ли кто-то данный номер 
            $roomCheck = checkTourRoom($arResult["ID"]);
            if ($roomCheck == "N") {
                echo "К сожалению свободных номеров данного типа не осталось. Выберите <a href='/'>другой номер</a>";
                die();
            }  


            $placesCheck = "Y";
            if (is_array($_POST["Places"]) && count($_POST["Places"]) > 0) {
                //проверяем, не занял ли кто-то данные места в момент оформления заказа 
                foreach ($_POST["Places"] as $place=>$status){
                    if (checkBusPlace($place,$arResult["BUS_TO"]["ID"]) == "N" || checkBusPlace($place,$arResult["BUS_BACK"]["ID"]) == "N") {
                        $placesCheck = "N";
                    }
                }
                if ($placesCheck == "N") {
                    echo "К сожалению одно или несколько выбранных вами мест уже занято. Выберите <a href=''>другие места</a>";
                    die();
                }
            }    



            //блокировака мест 


            if ($arResult["TYPE_BOOKING"] == "STANDART") {      


                //автобус ТУДА   

                //преобразуем схему в ассоциативный массив
                $scheme = json_decode($arResult["BUS_TO"]["SCHEME"], true);
                //перебираем схему, как только нашли места, которые выбрали ранее - меняем их состояние на "занято"
                foreach($scheme as $n=>$val) {
                    foreach ($val as $i=>$place){
                        if (array_key_exists($i,$_POST["Places"])) {
                            $scheme[$n][$i] = $_POST["Places"][$i];
                            eventLogAdd("BUSTOURPRO_EVENT_PLACES_BOOKING",$arResult["BUS_TO"]["ID"],"место ".$i." в автобусе ".$arResult["BUS_TO"]["ID"]." забронировано"); 
                        }
                    }
                }

                //кодируем схему обратно
                $scheme_new = json_encode($scheme);  
                //после этого обновляем схему рассадки в базе
                $el = new CIBlockElement;

                $PROP = array();
                $PROP["P_SCHEME"] = $scheme_new; 
                $PROP["COMPANY"] = getCurrentCompanyID();
                $PROP["BUS_DIRECTION"] = Array("VALUE" => $arBusTo["PROPERTY_BUS_DIRECTION_ENUM_ID"]);
                $PROP["DEPARTURE"] = $arBusTo["PROPERTY_DEPARTURE_VALUE"];  
                $PROP["ARRIVAL"] = $arBusTo["PROPERTY_ARRIVAL_VALUE"];

                $arLoadProductArray = Array(
                    "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                    "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                    "PROPERTY_VALUES"=> $PROP,
                    "NAME"           => $arBusTo["NAME"],
                    "ACTIVE"         => "Y",            // активен          
                );
                //обновляем схему рассадки туда и обратно в БД            
                $res = $el->Update($arResult["BUS_TO"]["ID"], $arLoadProductArray);

                //пишем лог




                /////////////////////////////////////////////////////////////////
                //////////////////////////////////////////////////////////////////
                //////////////////////////////////////////////////////////////// 

                //автобус ОБРАТНО                   

                //преобразуем схему в ассоциативный массив
                $scheme = json_decode($arResult["BUS_BACK"]["SCHEME"], true);
                //перебираем схему, как только нашли места, которые выбрали ранее - меняем их состояние на "занято"
                foreach($scheme as $n=>$val) {
                    foreach ($val as $i=>$place){
                        if (array_key_exists($i,$_POST["Places"])) {
                            $scheme[$n][$i] = $_POST["Places"][$i]; 
                            //логирование
                            eventLogAdd("BUSTOURPRO_EVENT_PLACES_BOOKING",$arResult["BUS_BACK"]["ID"],"место ".$i." в автобусе ".$arResult["BUS_BACK"]["ID"]." забронировано"); 
                        }
                    }
                }

                //кодируем схему обратно
                $scheme_new = json_encode($scheme);  
                //после этого обновляем схему рассадки в базе
                $el = new CIBlockElement;

                $PROP = array();
                $PROP["P_SCHEME"] = $scheme_new; 
                $PROP["COMPANY"] = getCurrentCompanyID();
                $PROP["BUS_DIRECTION"] = Array("VALUE" => $arBusBack["PROPERTY_BUS_DIRECTION_ENUM_ID"]);
                $PROP["DEPARTURE"] = $arBusBack["PROPERTY_DEPARTURE_VALUE"];  
                $PROP["ARRIVAL"] = $arBusBack["PROPERTY_ARRIVAL_VALUE"]; 

                $arLoadProductArray = Array(
                    "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                    "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                    "PROPERTY_VALUES"=> $PROP,
                    "NAME"           => $arBusBack["NAME"],
                    "ACTIVE"         => "Y",            // активен          
                );
                //обновляем схему рассадки туда и обратно в БД            
                $res = $el->Update($arResult["BUS_BACK"]["ID"], $arLoadProductArray); 


                //преобразуем места в массив удобного вида [i]=>place
                $arResult["CUR_PLASES"] = (array_keys($_POST["Places"]));


                //после добавления забронированных мест на схему, добавляем запись в инфоблок "блокировка"
                //это делается для того, чтобы если вдруг оформление заказа оборвется на втором шаге, можно было освободить забронированные места
                //это будет делать скрипт, весящий на кроне/хитах

                //получаем ID инфоблока с блокировкой мест
                $place_locker = CIBlock::GetList(array(), array("CODE"=>"PLACES_LOCKER"));
                $arPlaceLocker = $place_locker->Fetch();

                foreach ($arResult["CUR_PLASES"] as $place) {

                    //автобус туда
                    $lock = new CIBlockElement;  

                    $lock_PROP = array();
                    $lock_PROP["SCHEME_ID"] = $arResult["BUS_TO"]["ID"];
                    $lock_PROP["USER_ID"] = $userID;  

                    $lockArray = Array(
                        "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                        "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                        "PROPERTY_VALUES"=> $lock_PROP,
                        "NAME"           => $place,
                        "ACTIVE"         => "Y",            // активен 
                        "IBLOCK_ID"=>$arPlaceLocker["ID"]         
                    );     
                    $lock_res = $lock->Add($lockArray);

                    //логирование
                    eventLogAdd("BUSTOURPRO_EVENT_PLACES_LOCKED",$lock_res,"Начато оформление заказа. место ".$place." в автобусе ".$arResult["BUS_TO"]["ID"]." заблокировано");
                    // echo $lock->LAST_ERROR;


                    //автобус обратно  
                    $lock = new CIBlockElement;  

                    $lock_PROP = array();
                    $lock_PROP["SCHEME_ID"] = $arResult["BUS_BACK"]["ID"];
                    $lock_PROP["USER_ID"] = $userID;  

                    $lockArray = Array(
                        "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                        "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                        "PROPERTY_VALUES"=> $lock_PROP,
                        "NAME"           => $place,
                        "ACTIVE"         => "Y",            // активен 
                        "IBLOCK_ID"=>$arPlaceLocker["ID"]         
                    );     
                    $lock_res = $lock->Add($lockArray);  

                    //логирование
                    eventLogAdd("BUSTOURPRO_EVENT_PLACES_LOCKED",$lock_res,"Начато оформление заказа. место ".$place." в автобусе ".$arResult["BUS_BACK"]["ID"]." заблокировано");
                    //   echo $lock->LAST_ERROR;  

                }


            }


            //также нужно уменьшить количество доступных номеров в текущем туре на 1 и добавить запись о блокировке

            //получаем ID инфоблока с блокировкой номеров
            $room_locker = CIBlock::GetList(array(), array("CODE"=>"ROOM_LOCKER"));
            $arRoomLocker = $room_locker->Fetch();

            $room_lock = new CIBlockElement;  

            $room_lock_PROP = array();
            $room_lock_PROP["USER_ID"] = $userID;  

            $roomlockArray = Array(
                "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                "PROPERTY_VALUES"=> $room_lock_PROP,
                "NAME"           => $arResult["ID"],
                "ACTIVE"         => "Y",            // активен 
                "IBLOCK_ID"=>$arRoomLocker["ID"]         
            );     
            $room_lock_res = $room_lock->Add($roomlockArray);

            eventLogAdd("BUSTOURPRO_EVENT_PLACES_LOCKED",$room_lock_res,"Начато оформление заказа. номер в туре ".$arResult["ID"]." заблокирован");


            /////////////////////////////////////////
            ////тут уменьшаем количество номеров/////
            /////////////////////////////////////////


            $tourNew = CIBlockElement::GetById($arResult["ID"]);
            $arTourNew = $tourNew->Fetch();

            // уменьшаем у нужного тура количество мест на 1
            $el = new CIBlockElement;
            $tourPropsNew = array();

            $tourProps = CIBlockElement::GetProperty($arTourNew["IBLOCK_ID"],$arTourNew["ID"],Array(),Array());
            while($arTourProps = $tourProps->Fetch()){
                //забираем у тура один номер
                if ($arTourProps["CODE"] == 'NUMBER_ROOM') {
                    $arTourProps["VALUE"] = $arTourProps["VALUE"]-1;  
                }
                $tourPropsNew[$arTourProps["ID"]] = $arTourProps["VALUE"];
            }

            $arLoadProductArray = Array(
                "PROPERTY_VALUES"=> $tourPropsNew,
                "ACTIVE"         => "Y",            // активен   
                "NAME"           => $arTourNew["NAME"]
            );

            $res = $el->Update($arTourNew["ID"], $arLoadProductArray);    

            eventLogAdd("BUSTOURPRO_EVENT_PLACES_BOOKING",$arResult["ID"],"Начато оформление заказа. номер в туре ".$arResult["ID"]." забронирован");


            //общее количество выбранных мест
            $arResult["PLACES_COUNT"] = $_POST["selectedPlacesCount"];


            if ($arResult["TYPE_BOOKING"] == "ONLY_ROOM")  {
                $arResult["PLACES_COUNT"] = 1; 
            }

        }

        //при переходе на третий шаг добавлям в базу новый заказ и записи о туристах
        if ($arResult["STEP"] == 3) { 

            //логируем данные
            $postData = serialize($_POST);
            eventLogAdd("BUSTOURPRO_EVENT_PLACES_BOOKING",$arResult["ID"],$postData);

            //создаем массив с местами
            $arResult["PLACES_RESERV"] = array();
            $p = 1;
            foreach ($_POST["Places"] as $pKey=>$pStatus) {
                $arResult["PLACES_RESERV"][$p] = $pKey;   
                $p++;
            }     


            //вычисляем стоимость тура для агентства
            //полная скидка в рублях
            $agencyDISCOUNT = 0;

            //полная скидка в процентах на тур
            $full_discount_tour = getServiceDiscount($arResult["DIRECTION"]["ID"],$arResult["TYPE_BOOKING"]) + $arResult["DISCOUNT"];

            //условие для случая, когда тур считается по коммунальным             
            $payments_discount = getServiceDiscount($arResult["DIRECTION"]["ID"],"PAYMENTS") + $arResult["DISCOUNT"];

            //отдельно считаем процент с услуг                                             
            $services_discount = getServiceDiscount($arResult["DIRECTION"]["ID"],"SERVICES");   

            //перебираем туристов для расчета скидки для агентства
            foreach ($_POST["Tourist"] as $tourist) {  

                //проверяем метод расчета туриста. 
                //если использовался метод, в котором стоимость тура == коммунальным платежам, используес другой процент
                if ($tourist["math"] > 0) {
                    $mathMethod = CIBlockElement::GetList(array(),array("ID"=>$tourist["math"]), false, false, array("PROPERTY_MATH_TOUR"));
                    $arMathMethod = $mathMethod->Fetch();
                    if ($arMathMethod["PROPERTY_MATH_TOUR_VALUE"] == "Коммунальные платежи") {
                        $full_discount_tour = $payments_discount;  
                    }  
                }      
                //скидка с тура
                $price = $tourist["tour_price"]; //цена тура для туриста без учета доп услуг
                $agencyDISCOUNT = $agencyDISCOUNT + $price * $full_discount_tour / 100; 

                //скидка с услуг    
                if (is_array($tourist["services"]) && count($tourist["services"]) > 0) {   
                    $services = CIBlockElement::GetList(array(),array("ID"=>$tourist["services"]), false, false, array("PROPERTY_PRICE"));
                    while($arService = $services->Fetch()) {
                        $agencyDISCOUNT = $agencyDISCOUNT + $arService["PROPERTY_PRICE_VALUE"] * $services_discount / 100; 
                    }
                }

            } 


            //вычисляем цену и скидку на трансфер
            $transferPrice = getTransferPrice($_POST["departureCity"]);
            $agencyDISCOUNT = $agencyDISCOUNT + $transferPrice * $services_discount / 100;           



            //добавляем заказ
            $new_order =  new CIBlockElement;

            $new_order_props = array();
            $new_order_props["COMPANY"] = getCurrentCompanyID();
            $new_order_props["TOUR"] = $arResult["TOUR_ID"];
            $new_order_props["OPERATOR_PRICE"] = ceil($_POST["all_summ"] - $agencyDISCOUNT); //вычитаем скидку, вычеслкнную выше
            $new_order_props["PRICE"] = ceil($_POST["all_summ"]);
            $new_order_props["DEPARTURE_CITY"] = $_POST["departureCity"];
            $new_order_props["DATE_FROM"] = $arTour["PROPERTY_DATE_FROM_VALUE"];
            $new_order_props["HOTEL"] = $arResult["HOTEL"]["ID"];
            $new_order_props["CITY"] = $arResult["CITY"]["ID"];
            $new_order_props["COMPANY_NAME"] = $arUser["NAME"];

            //если количество человек в заказе == количеству основных мест в номере 
            //и установлен флаг "автоматически подтверждать заказ", то статус "заказ одобрен", иначе "под запрос"
            if ($arResult["ROOM"]["PLACES"] == count($_POST["Tourist"]) && $companySettings["AUTO_ORDER_CONFIRM"]["VALUE"] == "Да") {
                $new_order_props["STATUS"] = $statuses["STATUS_ACCEPTED"];  
            }
            else {
                $new_order_props["STATUS"] = $statuses["STATUS_NEW"]; 
            }

            $new_order_props["TYPE_BOOKING"] = $avaible_booking_types[$arResult["TYPE_BOOKING"]];   
            $new_order_props["NOTES"] = $_POST["notes"];


            //получаем ID инфоблока заказов
            $orders_iblock = CIBlock::GetList(array(), array("CODE"=>"ORDERS"));
            $arOrders = $orders_iblock->Fetch();

            $arNewOrder = Array(
                "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                "PROPERTY_VALUES"=> $new_order_props,
                "IBLOCK_ID" => $arOrders["ID"],
                "NAME"           => $arTour["NAME"]." (".$arTour["PROPERTY_DATE_FROM_VALUE"]." - ".$arTour["PROPERTY_DATE_TO_VALUE"].")",
                "ACTIVE"         => "Y",            // активен          
            );
            //добавляем заказ в БД    
            $order_id = $new_order->Add($arNewOrder);

            //логирование
            $description = "Новый заказ №".$order_id."; тип бронирования: ".$arResult["TYPE_BOOKING"];                  
            eventLogAdd("BUSTOURPRO_EVENT_NEW_ORDER",$order_id,$description);

            //отправка письма
            if (checkNotice() == "Y") {
                //формируем данные для письма
                $props = getCompanyProperties();

                $userData = CUser::GetById($userID);
                $arUserData = $userData->Fetch();

                //письмо ОПЕРАТОРУ
                $THEME = "Новый заказ в системе онлайн бронирования BUSTOURPRO"; 
                $TEXT = "<h3>Данные о заказе</h3>
                <p>
                № заказа: <b>".$order_id."</b><br>
                Тип бронирования: <b>".$avaible_booking_types_NAMES[$arResult["TYPE_BOOKING"]]."</b><br>
                Статус заказа: <b>".$statusesNAMES[$new_order_props["STATUS"]]."</b><br>
                Тур: <b>".$arTour["NAME"]." (".$arTour["PROPERTY_DATE_FROM_VALUE"]." - ".$arTour["PROPERTY_DATE_TO_VALUE"].")"."</b><br>
                Компания: <b>".$arUserData["NAME"]."</b>                   
                </p>
                "; 
                $emailData = array(
                    "EMAIL_FROM" => $props["EMAIL"]["VALUE"],
                    "EMAIL" => $props["EMAIL"]["VALUE"],
                    "THEME" => $THEME,
                    "TEXT" => $TEXT
                );                                  
                CEvent::Send("BUSTOUR_NEW_AGENCY",LANG,$emailData,"N");
            }
            //////////////////////////          


            $arResult["ORDER_ID"] = $order_id;


            //получаем ID инфоблока с туристами
            $tourist_iblock = CIBlock::GetList(array(),array("CODE"=>"TOURIST"));
            $arTouristIblock = $tourist_iblock->Fetch();

            //получаем свойство "доп место"
            $extra_place = CIBlockPropertyEnum::GetList(array(),Array("CODE"=>"ADD_PLACE","IBLOCK_ID"=>$arTouristIblock["ID"]));
            $arExtraPlace = $extra_place->Fetch();



            //перебираем массив туристов и пишем в базу
            foreach ($_POST["Tourist"] as $tNum=>$tourist) {

                $el = new CIBlockElement;


                if (!$tourist["place"] || $tourist["place"] == "") {
                    $tourist["place"] = $arResult["PLACES_RESERV"][$tNum];
                }

                $PROP = array();
                $PROP["COMPANY"] = getCurrentCompanyID();
                $PROP["TOUR"] = $arResult["TOUR_ID"];
                $PROP["ORDER"] = $order_id;
                $PROP["PASSPORT"] = $tourist["passport"];
                $PROP["PHONE"] = $tourist["phone"];
                $PROP["PLACE"] = $tourist["place"];
                $PROP["SECOND_PLACE"] = $tourist["place"];  
                $PROP["BIRTHDAY"] = $tourist["birthday"];
                $PROP["PRICE"] = $tourist["price"];
                $PROP["MATH_METHOD"] = $tourist["math"];
                //собираем услуги
                foreach($tourist["services"] as $service) {
                    $PROP["SERVICES"][] = $service;  
                }

                if ($tourist["add"] == "Y") {
                    $PROP["ADD_PLACE"] = $arExtraPlace["ID"]; //турист на доп месте    
                }



                $arLoadProductArray = Array(
                    "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                    "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                    "PROPERTY_VALUES"=> $PROP,
                    "IBLOCK_ID" => $arTouristIblock["ID"],
                    "NAME"           => $tourist["name"],
                    "ACTIVE"         => "Y",            // активен          
                );
                //добавляем туристов в БД    
                //arshow($arLoadProductArray);
                $tourist_new_id = $el->Add($arLoadProductArray);

                //логирование
                if ($tourist_new_id){
                    $description = "Новый пассажир ".$tourist_new_id."; ID заказа: ".$order_id."; места (туда/обратно): ".$PROP["PLACE"]."/".$PROP["SECOND_PLACE"];                  
                    eventLogAdd("BUSTOURPRO_EVENT_NEW_TOURIST",$tourist_new_id,$description);  

                    //после этого нужно удалить из инфоблока с блокировкой мест запись о блокировке текущих мест
                    //получаем элемент с местом текущего пассажира             
                    $lock_place = CIBLockElement::GetList(array(), array("IBLOCK_CODE"=>"PLACES_LOCKER","NAME"=>$tourist["place"], "PROPERTY_USER_ID"=>$userID), false, false, array("ID","NAME" ,"PROPERTY_SCHEME_ID"));
                    while($arLockPlace = $lock_place->Fetch()){
                        if (CIBlockElement::Delete($arLockPlace["ID"])) {
                            //логирование  
                            $description = "Оформление заказа № ".$order_id." завершено, место ".$tourist["place"]." в автобусе ".$arLockPlace["PROPERTY_SCHEME_ID_VALUE"]." разблокировано" ;                          
                        } else {
                            $description = "Оформление заказа № ".$order_id." завершено, ОШИБКА! место ".$tourist["place"]." в автобусе ".$arLockPlace["PROPERTY_SCHEME_ID_VALUE"]." не разблокировано" ;    
                        }
                        eventLogAdd("BUSTOURPRO_EVENT_PLACES_UNLOCKED",$arLockPlace["ID"],$description);
                    }  
                }
                else {
                    $data = serialize($arLoadProductArray);
                    $description = "Ошибка добавления туриста ".$el->LAST_ERROR."; ID заказа: ".$order_id."; места (туда/обратно): ".$PROP["PLACE"]."/".$PROP["SECOND_PLACE"]."данные туриста: ".$data;                  
                    eventLogAdd("BUSTOURPRO_EVENT_NEW_TOURIST",$tourist_new_id,$description); 
                }       


            }



            //и из инфоблока с блокировкой номеров тоже нужно удалить запись
            $lock_rooms = CIBLockElement::GetList(array(), array("IBLOCK_CODE"=>"ROOM_LOCKER","NAME"=>$arResult["ID"],"PROPERTY_USER_ID"=>$userID), false, false, array("ID","NAME"));
            $arLockRoom = $lock_rooms->Fetch();
            if (CIBlockElement::Delete($arLockRoom["ID"])) {
                //логирование
                $description = "Оформление заказа № ".$order_id." завершено, номер в туре ".$arLockRoom["NAME"]." разблокирован" ; 
            }
            else {
                $description = "Оформление заказа № ".$order_id." завершено, ОШИБКА! номер в туре ".$arLockRoom["NAME"]." не разблокирован" ;   
            }
            eventLogAdd("BUSTOURPRO_EVENT_ROOM_UNLOCKED",$arLockRoom["ID"],$description);


        }

        if ($arResult["STEP"] == 4){
            header("location: /order-management/tour_selection/");
        }



    }


    ///////////////////////////////////////////////////////////
    ////////////////////////ДВОЙНОЙ ТУР////////////////////////
    ///////////////////////////////////////////////////////////



    if ($arResult["TYPE_BOOKING"] == "DOUBLE_TOUR") {

        //текущий заказ
        $arResult["TOUR_ID"] = ($arParams["TOUR_ID"]);
        $arResult["ID"] = $arResult["TOUR_ID"];
        $arResult["DIRECTION"] = array();
        $arResult["CITY"] = array();
        $arResult["HOTEL"] = array();
        $arResult["ROOM"] = array();
        $arResult["BUS_TO"] = array();
        $arResult["BUS_BACK"] = array();


        //собираем параметры тура 

        $arSelect = array(
            "ID",
            "NAME",
            "PROPERTY_COMPANY",
            "PROPERTY_DIRECTION",
            "PROPERTY_CITY",
            "PROPERTY_ROOM",
            "PROPERTY_DATE_FROM",
            "PROPERTY_DATE_TO",
            "PROPERTY_PRICE",
            "PROPERTY_DISCONT",  
            "PROPERTY_HOTEL",
            "PROPERTY_PRICE_ADDITIONAL_SEATS",
            "PROPERTY_BUS_TO",
            "PROPERTY_BUS_BACK"
        );

        $arFilter = array("IBLOCK_CODE"=>"TOUR","ID"=>$arParams["TOUR_ID"],"PROPERTY_COMPANY"=>getCurrentCompanyID());

        //получаем инфо о первом туре
        $tour = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $arTour = $tour->Fetch();

        $arResult["PRICE"] = $arTour["PROPERTY_PRICE_VALUE"];
        $arResult["DATE_FROM"] = $arTour["PROPERTY_DATE_FROM_VALUE"];
        $arResult["DATE_TO"] = $arTour["PROPERTY_DATE_TO_VALUE"];
        $arResult["DISCOUNT"] = $arTour["PROPERTY_DISCONT_VALUE"];
        $arResult["PRICE_ADDITIONAL_SEATS"] = $arTour["PROPERTY_PRICE_ADDITIONAL_SEATS_VALUE"];
        $arResult["COMPANY"] = $arTour["PROPERTY_COMPANY_VALUE"];

        //получаем информацию о направлении
        $direction = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"DIRECTION","ID"=>$arTour["PROPERTY_DIRECTION_VALUE"]));
        $arDirection = $direction->Fetch();
        $arResult["DIRECTION"]["ID"] = $arDirection["ID"];
        $arResult["DIRECTION"]["NAME"] = "Направление";
        $arResult["DIRECTION"]["VALUE"] = $arDirection["NAME"];

        //получаем информацию о городе
        $city = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"CITY","ID"=>$arTour["PROPERTY_CITY_VALUE"]));
        $arCity = $city->Fetch();
        $arResult["CITY"]["ID"] = $arCity["ID"];
        $arResult["CITY"]["NAME"] = "Город";
        $arResult["CITY"]["VALUE"] = $arCity["NAME"]; 

        //получаем информацию об отеле
        $hotel = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"HOTEL","ID"=>$arTour["PROPERTY_HOTEL_VALUE"]));
        $arHotel = $hotel->Fetch();
        $arResult["HOTEL"]["ID"] = $arHotel["ID"];
        $arResult["HOTEL"]["NAME"] = "Гостиница"; 
        $arResult["HOTEL"]["VALUE"] = $arHotel["NAME"];     

        //получаем информацию о номере
        $room = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"ROOM","ID"=>$arTour["PROPERTY_ROOM_VALUE"]),false, false, array("ID","NAME","PROPERTY_NUMBER_SEATS", "PROPERTY_IS_ADD_ADDITIONAL_SEATS"));
        $arRoom = $room->Fetch();
        $arResult["ROOM"]["ID"] = $arRoom["ID"];
        $arResult["ROOM"]["NAME"] = "Номер"; 
        $arResult["ROOM"]["VALUE"] = $arRoom["NAME"];
        $arResult["ROOM"]["PLACES"] = $arRoom["PROPERTY_NUMBER_SEATS_VALUE"];
        $arResult["ROOM"]["ADDITIONAL_PLACES"] = $arRoom["PROPERTY_IS_ADD_ADDITIONAL_SEATS_VALUE"];
        $additional_places_count = 0; //количествл доп мест
        if ($arResult["ROOM"]["ADDITIONAL_PLACES"] == "Да") {$additional_places_count = 1;}
        $arResult["ROOM"]["TOTAL_PLACES_COUNT"] = $arResult["ROOM"]["PLACES"] + $additional_places_count; 

        //получаем информацию об автобусе ТУДА для первого тура 
        $bus_to = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"BUS_ON_TOUR","ID"=>$arTour["PROPERTY_BUS_TO_VALUE"]), false, false, array("NAME","ID","PROPERTY_P_SCHEME","PROPERTY_BUS_DIRECTION", "PROPERTY_DEPARTURE", "PROPERTY_ARRIVAL"));
        $arBusTo = $bus_to->Fetch();
        $arResult["BUS_TO"]["ID"] = $arBusTo["ID"];
        $arResult["BUS_TO"]["NAME"] = "Автобус (Туда)"; 
        $arResult["BUS_TO"]["SCHEME"] = $arBusTo["PROPERTY_P_SCHEME_VALUE"];
        $arResult["BUS_TO"]["DEPARTURE"] = $arBusTo["PROPERTY_DEPARTURE_VALUE"];
        $arResult["BUS_TO"]["ARRIVAL"] = $arBusTo["PROPERTY_ARRIVAL_VALUE"]; 


        $arResult["BUS_SCHEME_VIEW"] = $arResult["BUS_TO"]["SCHEME"];


        //id второго тура
        $arResult["SECOND_TOUR"]["ID"] = checkDoubleTour($arResult["ID"]);
        //собираем второй тур


        $arFilter = array("IBLOCK_CODE"=>"TOUR","ID"=>$arResult["SECOND_TOUR"]["ID"],"PROPERTY_COMPANY"=>getCurrentCompanyID());

        //получаем инфо о первом туре
        $secondTour = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $arSecondTour = $secondTour->Fetch();

        $arResult["SECOND_TOUR"]["PRICE"] = $arSecondTour["PROPERTY_PRICE_VALUE"];
        $arResult["SECOND_TOUR"]["DATE_FROM"] = $arSecondTour["PROPERTY_DATE_FROM_VALUE"];
        $arResult["SECOND_TOUR"]["DATE_TO"] = $arSecondTour["PROPERTY_DATE_TO_VALUE"];
        $arResult["SECOND_TOUR"]["DISCOUNT"] = $arSecondTour["PROPERTY_DISCONT_VALUE"];
        $arResult["SECOND_TOUR"]["PRICE_ADDITIONAL_SEATS"] = $arSecondTour["PROPERTY_PRICE_ADDITIONAL_SEATS_VALUE"];
        $arResult["SECOND_TOUR"]["COMPANY"] = $arSecondTour["PROPERTY_COMPANY_VALUE"];

        //получаем информацию о направлении
        $directionSecond = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"DIRECTION","ID"=>$arSecondTour["PROPERTY_DIRECTION_VALUE"]));
        $arDirectionSecond = $directionSecond->Fetch();
        $arResult["SECOND_TOUR"]["DIRECTION"]["ID"] = $arDirectionSecond["ID"];
        $arResult["SECOND_TOUR"]["DIRECTION"]["NAME"] = "Направление";
        $arResult["SECOND_TOUR"]["DIRECTION"]["VALUE"] = $arDirectionSecond["NAME"];

        //получаем информацию о городе
        $citySecond = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"CITY","ID"=>$arSecondTour["PROPERTY_CITY_VALUE"]));
        $arCitySecond = $citySecond->Fetch();
        $arResult["SECOND_TOUR"]["CITY"]["ID"] = $arCitySecond["ID"];
        $arResult["SECOND_TOUR"]["CITY"]["NAME"] = "Город";
        $arResult["SECOND_TOUR"]["CITY"]["VALUE"] = $arCitySecond["NAME"]; 

        //получаем информацию об отеле
        $hotelSecond = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"HOTEL","ID"=>$arSecondTour["PROPERTY_HOTEL_VALUE"]));
        $arHotelSecond = $hotelSecond->Fetch();
        $arResult["SECOND_TOUR"]["HOTEL"]["ID"] = $arHotelSecond["ID"];
        $arResult["SECOND_TOUR"]["HOTEL"]["NAME"] = "Гостиница"; 
        $arResult["SECOND_TOUR"]["HOTEL"]["VALUE"] = $arHotelSecond["NAME"];     

        //получаем информацию о номере
        $roomSecond = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"ROOM","ID"=>$arSecondTour["PROPERTY_ROOM_VALUE"]),false, false, array("ID","NAME","PROPERTY_NUMBER_SEATS", "PROPERTY_IS_ADD_ADDITIONAL_SEATS"));
        $arRoomSecond = $roomSecond->Fetch();
        $arResult["SECOND_TOUR"]["ROOM"]["ID"] = $arRoomSecond["ID"];
        $arResult["SECOND_TOUR"]["ROOM"]["NAME"] = "Номер"; 
        $arResult["SECOND_TOUR"]["ROOM"]["VALUE"] = $arRoomSecond["NAME"];
        $arResult["SECOND_TOUR"]["ROOM"]["PLACES"] = $arRoomSecond["PROPERTY_NUMBER_SEATS_VALUE"];
        $arResult["SECOND_TOUR"]["ROOM"]["ADDITIONAL_PLACES"] = $arRoomSecond["PROPERTY_IS_ADD_ADDITIONAL_SEATS_VALUE"];
        $additional_places_count = 0; //количествл доп мест
        if ($arResult["SECOND_TOUR"]["ROOM"]["ADDITIONAL_PLACES"] == "Да") {$additional_places_count = 1;}
        $arResult["SECOND_TOUR"]["ROOM"]["TOTAL_PLACES_COUNT"] = $arResult["SECOND_TOUR"]["ROOM"]["PLACES"] + $additional_places_count; 




        //получаем информацию об автобусе ОБРАТНО для второго тура 
        $bus_back = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"BUS_ON_TOUR","ID"=>$arSecondTour["PROPERTY_BUS_BACK_VALUE"]), false, false, array("NAME","ID","PROPERTY_P_SCHEME","PROPERTY_BUS_DIRECTION", "PROPERTY_DEPARTURE", "PROPERTY_ARRIVAL"));
        $arBusBack = $bus_back->Fetch();
        $arResult["BUS_BACK"]["ID"] = $arBusBack["ID"];
        $arResult["BUS_BACK"]["NAME"] = "Автобус (Обратно)"; 
        $arResult["BUS_BACK"]["SCHEME"] = $arBusBack["PROPERTY_P_SCHEME_VALUE"];
        $arResult["BUS_BACK"]["DEPARTURE"] = $arBusBack["PROPERTY_DEPARTURE_VALUE"];
        $arResult["BUS_BACK"]["ARRIVAL"] = $arBusBack["PROPERTY_ARRIVAL_VALUE"]; 





        $arResult["STEP"] = (int) CBRequest::gi()->getPost("STEP");
        if (!($arResult["STEP"] >= 1 && $arResult["STEP"] <= 3)) {
            $arResult["STEP"] = 1;
        }
        if (!(isset($_POST["ORDER_MAKE"]) && check_bitrix_sessid())) {
            $arResult["STEP"] = 1;
        }




        if ($arResult["STEP"] == 2) {

            //логируем данные
            $postData = serialize($_POST);
            eventLogAdd("BUSTOURPRO_EVENT_PLACES_BOOKING",$arResult["ID"],$postData);


            //проверяем не занял ли кто-то данный номер 
            if (checkTourRoom($arResult["ID"]) == "N" || checkTourRoom($arResult["SECOND_TOUR"]["ID"]) == "N") {
                echo "К сожалению свободных номеров данного типа не осталось. Выберите <a href='/'>другой номер</a>";
                die();
            }

            $placesCheck = "Y";
            if (is_array($_POST["Places"]) && count($_POST["Places"]) > 0) {
                //проверяем, не занял ли кто-то данные места в момент оформления заказа 
                foreach ($_POST["Places"] as $place=>$status){
                    if (checkBusPlace($place,$arResult["BUS_TO"]["ID"]) == "N" || checkBusPlace($place,$arResult["BUS_BACK"]["ID"]) == "N") {
                        $placesCheck = "N";
                    }
                }                   
                foreach ($_POST["SecondPlaces"] as $place=>$status){
                    if (checkBusPlace($place,$arResult["BUS_TO"]["ID"]) == "N" || checkBusPlace($place,$arResult["BUS_BACK"]["ID"]) == "N") {
                        $placesCheck = "N";
                    }
                }     

                if ($placesCheck == "N") {
                    echo "К сожалению одно или несколько выбранных вами мест уже занято. Выберите <a href=''>другие места</a>";
                    die();
                }
            }


            //занимаем места

            //автобус ТУДА

            //преобразуем схему в ассоциативный массив
            $scheme = json_decode($arResult["BUS_TO"]["SCHEME"], true);
            //перебираем схему, как только нашли места, которые выбрали ранее - меняем их состояние на "занято"
            foreach($scheme as $n=>$val) {
                foreach ($val as $i=>$place){
                    if (array_key_exists($i,$_POST["Places"])) {
                        $scheme[$n][$i] = $_POST["Places"][$i]; 
                        //логирование
                        eventLogAdd("BUSTOURPRO_EVENT_PLACES_BOOKING",$arResult["BUS_TO"]["ID"],"место ".$i." в автобусе ".$arResult["BUS_TO"]["ID"]." забронировано");
                    }
                }
            }

            //кодируем схему обратно
            $scheme_new = json_encode($scheme);  
            //после этого обновляем схему рассадки в базе
            $el = new CIBlockElement;

            $PROP = array();
            $PROP["P_SCHEME"] = $scheme_new; 
            $PROP["COMPANY"] = getCurrentCompanyID();
            $PROP["BUS_DIRECTION"] = Array("VALUE" => $arBusTo["PROPERTY_BUS_DIRECTION_ENUM_ID"]);
            $PROP["DEPARTURE"] = $arBusTo["PROPERTY_DEPARTURE_VALUE"];  
            $PROP["ARRIVAL"] = $arBusTo["PROPERTY_ARRIVAL_VALUE"];  

            $arLoadProductArray = Array(
                "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                "PROPERTY_VALUES"=> $PROP,
                "NAME"           => $arBusTo["NAME"],
                "ACTIVE"         => "Y",            // активен          
            );
            // arshow($arLoadProductArray);
            //обновляем схему рассадки туда и обратно в БД            
            $res = $el->Update($arResult["BUS_TO"]["ID"], $arLoadProductArray);

            /////////////////////////////////////////////////////////////////
            //////////////////////////////////////////////////////////////////


            //автобус ОБРАТНО                   

            //преобразуем схему в ассоциативный массив
            $scheme = json_decode($arResult["BUS_BACK"]["SCHEME"], true);
            //перебираем схему, как только нашли места, которые выбрали ранее - меняем их состояние на "занято"
            foreach($scheme as $n=>$val) {
                foreach ($val as $i=>$place){
                    if (array_key_exists($i,$_POST["SecondPlaces"])) {
                        $scheme[$n][$i] = $_POST["SecondPlaces"][$i];
                        //логирование
                        eventLogAdd("BUSTOURPRO_EVENT_PLACES_BOOKING",$arResult["BUS_BACK"]["ID"],"место ".$i." в автобусе ".$arResult["BUS_BACK"]["ID"]." забронировано"); 
                    }
                }
            }

            //кодируем схему обратно
            $scheme_new = json_encode($scheme);  
            //после этого обновляем схему рассадки в базе
            $el = new CIBlockElement;

            $PROP = array();
            $PROP["P_SCHEME"] = $scheme_new; 
            $PROP["COMPANY"] = getCurrentCompanyID();
            $PROP["BUS_DIRECTION"] = Array("VALUE" => $arBusBack["PROPERTY_BUS_DIRECTION_ENUM_ID"]); 
            $PROP["DEPARTURE"] = $arBusBack["PROPERTY_DEPARTURE_VALUE"];  
            $PROP["ARRIVAL"] = $arBusBack["PROPERTY_ARRIVAL_VALUE"];

            $arLoadProductArray = Array(
                "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                "PROPERTY_VALUES"=> $PROP,
                "NAME"           => $arBusBack["NAME"],
                "ACTIVE"         => "Y",            // активен          
            );
            //arshow($arLoadProductArray);
            //обновляем схему рассадки туда и обратно в БД            
            $res = $el->Update($arResult["BUS_BACK"]["ID"], $arLoadProductArray);




            //преобразуем места в массив удобного вида [i]=>place
            $arResult["CUR_PLASES"] = (array_keys($_POST["Places"]));
            $arResult["CUR_SECOND_PLASES"] = (array_keys($_POST["SecondPlaces"]));



            //после добавления забронированных мест на схему, добавляем запись в инфоблок "блокировка"
            //это делается для того, чтобы если вдруг оформление заказа оборвется на втором шаге, можно было освободить забронированные места
            //это будет делать скрипт, весящий на кроне/хитах

            //получаем ID инфоблока с блокировкой мест
            $place_locker = CIBlock::GetList(array(), array("CODE"=>"PLACES_LOCKER"));
            $arPlaceLocker = $place_locker->Fetch();

            //автобус туда 
            foreach ($arResult["CUR_PLASES"] as $place) {


                $lock = new CIBlockElement;  

                $lock_PROP = array();
                $lock_PROP["SCHEME_ID"] = $arResult["BUS_TO"]["ID"];
                $lock_PROP["USER_ID"] = $userID;  

                $lockArray = Array(
                    "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                    "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                    "PROPERTY_VALUES"=> $lock_PROP,
                    "NAME"           => $place,
                    "ACTIVE"         => "Y",            // активен 
                    "IBLOCK_ID"=>$arPlaceLocker["ID"]         
                );     
                $lock_res = $lock->Add($lockArray);

                //логирование
                eventLogAdd("BUSTOURPRO_EVENT_PLACES_LOCKED",$lock_res,"Начато оформление заказа. место ".$place." в автобусе ".$arResult["BUS_TO"]["ID"]." заблокировано");     

            }


            //автобус обратно  
            foreach ($arResult["CUR_SECOND_PLASES"] as $place) {     

                $lock = new CIBlockElement;  

                $lock_PROP = array();
                $lock_PROP["SCHEME_ID"] = $arResult["BUS_BACK"]["ID"];
                $lock_PROP["USER_ID"] = $userID;  

                $lockArray = Array(
                    "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                    "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                    "PROPERTY_VALUES"=> $lock_PROP,
                    "NAME"           => $place,
                    "ACTIVE"         => "Y",            // активен 
                    "IBLOCK_ID"=>$arPlaceLocker["ID"]         
                );     
                $lock_res = $lock->Add($lockArray); 

                //логирование
                eventLogAdd("BUSTOURPRO_EVENT_PLACES_LOCKED",$lock_res,"Начато оформление заказа. место ".$place." в автобусе ".$arResult["BUS_BACK"]["ID"]." заблокировано");   

            }



            //также нужно уменьшить количество доступных номеров в текущем и втором туре на 1 и добавить запись о блокировке

            //получаем ID инфоблока с блокировкой номеров
            $room_locker = CIBlock::GetList(array(), array("CODE"=>"ROOM_LOCKER"));
            $arRoomLocker = $room_locker->Fetch();

            //выполняем действия для обоих туров
            $rooms = array($arResult["ID"],$arResult["SECOND_TOUR"]["ID"]);

            foreach($rooms as $room) {

                $room_lock = new CIBlockElement;  

                $room_lock_PROP = array();
                $room_lock_PROP["USER_ID"] = $userID;  

                $roomlockArray = Array(
                    "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                    "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                    "PROPERTY_VALUES"=> $room_lock_PROP,
                    "NAME"           => $room,
                    "ACTIVE"         => "Y",            // активен 
                    "IBLOCK_ID"=>$arRoomLocker["ID"]         
                );     
                $room_lock_res = $room_lock->Add($roomlockArray);

                //логирование
                eventLogAdd("BUSTOURPRO_EVENT_PLACES_LOCKED",$room_lock_res,"Начато оформление заказа. номер в туре ".$room." заблокирован");                 



                /////////////////////////////////////////
                ////тут уменьшаем количество номеров/////
                /////////////////////////////////////////


                $tourNew = CIBlockElement::GetById($room);
                $arTourNew = $tourNew->Fetch();

                // уменьшаем у нужного тура количество мест на 1
                $el = new CIBlockElement;
                $tourPropsNew = array();

                $tourProps = CIBlockElement::GetProperty($arTourNew["IBLOCK_ID"],$arTourNew["ID"],Array(),Array());
                while($arTourProps = $tourProps->Fetch()){
                    //забираем у тура один номер
                    if ($arTourProps["CODE"] == 'NUMBER_ROOM') {
                        $arTourProps["VALUE"] = $arTourProps["VALUE"]-1;  
                    }
                    $tourPropsNew[$arTourProps["ID"]] = $arTourProps["VALUE"];
                }

                $arLoadProductArray = Array(
                    "PROPERTY_VALUES"=> $tourPropsNew,
                    "ACTIVE"         => "Y",            // активен   
                    "NAME"           => $arTourNew["NAME"]
                );

                $res = $el->Update($arTourNew["ID"], $arLoadProductArray); 

                //логирование
                eventLogAdd("BUSTOURPRO_EVENT_PLACES_BOOKING",$arTourNew["ID"],"Начато оформление заказа. номер в туре ".$arTourNew["ID"]." забронирован");      

            }       

            //общее количество выбранных мест
            $arResult["PLACES_COUNT"] = $_POST["selectedPlacesCount"];  
            // die();   
        }


        //при переходе на третий шаг добавлям в базу новый заказ и записи о туристах
        if ($arResult["STEP"] == 3) {  

            //логируем данные
            $postData = serialize($_POST);
            eventLogAdd("BUSTOURPRO_EVENT_PLACES_BOOKING",$arResult["ID"],$postData);

            //создаем массив с местами
            $arResult["PLACES_RESERV"] = array();
            $p = 1;
            foreach ($_POST["Places"] as $pKey=>$pStatus) {
                $arResult["PLACES_RESERV"][$p] = $pKey;   
                $p++;
            }  

            //создаем массив с местами
            $arResult["PLACES_SECOND_RESERV"] = array();
            $p = 1;
            foreach ($_POST["SecondPlaces"] as $pKey=>$pStatus) {
                $arResult["PLACES_SECOND_RESERV"][$p] = $pKey;   
                $p++;
            }  



            //вычисляем стоимость тура для агентства
            //полная скидка в рублях
            $agencyDISCOUNT = 0;

            //полная скидка в процентах на тур
            $full_discount_tour = getServiceDiscount($arResult["DIRECTION"]["ID"],$arResult["TYPE_BOOKING"]) + $arResult["DISCOUNT"];
            //полная скидка в процентах на второй тур
            $full_discount_tour_second = getServiceDiscount($arResult["DIRECTION"]["ID"],$arResult["TYPE_BOOKING"]) + $arResult["SECOND_TOUR"]["DISCOUNT"];

            //условие для случая, когда тур считается по коммунальным             
            $payments_discount = getServiceDiscount($arResult["DIRECTION"]["ID"],"PAYMENTS") + $arResult["DISCOUNT"];
            $payments_discount_second = getServiceDiscount($arResult["DIRECTION"]["ID"],"PAYMENTS") + $arResult["SECOND_TOUR"]["DISCOUNT"];

            //отдельно считаем процент с услуг                                             
            $services_discount = getServiceDiscount($arResult["DIRECTION"]["ID"],"SERVICES");   


            //перебираем туристов
            foreach ($_POST["Tourist"] as $tourist) {

                //проверяем метод расчета туриста. 
                //если использовался метод, в котором стоимость тура == коммунальным платежам, используес другой процент
                if ($tourist["math"] > 0) {
                    $mathMethod = CIBlockElement::GetList(array(),array("ID"=>$tourist["math"]), false, false, array("PROPERTY_MATH_TOUR"));
                    $arMathMethod = $mathMethod->Fetch();
                    if ($arMathMethod["PROPERTY_MATH_TOUR_VALUE"] == "Коммунальные платежи") {
                        $full_discount_tour = $payments_discount; 
                        $full_discount_tour_second = $payments_discount_second; 
                    }  
                }   

                if ($tourist["add"] == "Y") {
                    $add = "Y";
                }
                else {
                    $add = "N";
                }    
                //скидка с туров
                // чтобы получить полную стоимость номера для туриста и вычислить скидку (для первого и второго тура)
                $price = getTourPrice($arResult["ID"],"STANDART",$add,$tourist["birthday"],$tourist["math"]); //цена тура для туриста без учета доп услуг
                $price_second = getTourPrice($arResult["SECOND_TOUR"]["ID"],"STANDART",$add,$tourist["birthday"],$tourist["math"]); //цена второго тура без учеда доп услуг
                //скидка на тур = цена тура * скидка / 100 (рублей)
                $agencyDISCOUNT = $agencyDISCOUNT + $price * $full_discount_tour / 100 + $price_second * $full_discount_tour_second / 100; 

                //скидка с услуг
                if (is_array($tourist["services"]) && count($tourist["services"]) > 0) {                            
                    $services = CIBlockElement::GetList(array(),array("ID"=>$tourist["services"]), false, false, array("PROPERTY_PRICE"));
                    while($arService = $services->Fetch()) {
                        $agencyDISCOUNT = $agencyDISCOUNT + $arService["PROPERTY_PRICE_VALUE"] * $services_discount / 100; 
                    } 
                }    


            }                 

            //вычисляем цену и скидку на трансфер
            $transferPrice = getTransferPrice($_POST["departureCity"]);
            $agencyDISCOUNT = $agencyDISCOUNT + $transferPrice * $services_discount / 100;        

            //добавляем заказ
            $new_order =  new CIBlockElement;

            $new_order_props = array();
            $new_order_props["COMPANY"] = getCurrentCompanyID();
            $new_order_props["TOUR"] = $arResult["TOUR_ID"];
            $new_order_props["OPERATOR_PRICE"] = ceil($_POST["all_summ"] - $agencyDISCOUNT); //вычитаем скидку, посчитанную выше
            $new_order_props["PRICE"] = ceil($_POST["all_summ"]); 
            $new_order_props["DEPARTURE_CITY"] = $_POST["departureCity"];
            $new_order_props["DATE_FROM"] = $arTour["PROPERTY_DATE_FROM_VALUE"];
            $new_order_props["HOTEL"] = $arResult["HOTEL"]["ID"];
            $new_order_props["CITY"] = $arResult["CITY"]["ID"];
            $new_order_props["COMPANY_NAME"] = $arUser["NAME"];
            $new_order_props["BUS_ID"] = $arResult["BUS_TO"]["ID"]; 
            $new_order_props["SECOND_BUS_ID"] = $arResult["BUS_BACK"]["ID"];
            //если количество человек в заказе == количеству основных мест в номере, то статус "заказ одобрен", иначе "под запрос"
            if ($arResult["ROOM"]["PLACES"] == count($_POST["Tourist"]) && $companySettings["AUTO_ORDER_CONFIRM"]["VALUE"] == "Да") {
                $new_order_props["STATUS"] = $statuses["STATUS_ACCEPTED"];  
            }
            else {
                $new_order_props["STATUS"] = $statuses["STATUS_NEW"]; 
            }

            $new_order_props["TYPE_BOOKING"] = $avaible_booking_types[$arResult["TYPE_BOOKING"]];   
            $new_order_props["NOTES"] = $_POST["notes"];


            //получаем ID инфоблока заказов
            $orders_iblock = CIBlock::GetList(array(), array("CODE"=>"ORDERS"));
            $arOrders = $orders_iblock->Fetch();

            $arNewOrder = Array(
                "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                "PROPERTY_VALUES"=> $new_order_props,
                "IBLOCK_ID" => $arOrders["ID"],
                "NAME"           => $arTour["NAME"]." (".$arTour["PROPERTY_DATE_FROM_VALUE"]." - ".$arTour["PROPERTY_DATE_TO_VALUE"].")",
                "ACTIVE"         => "Y",            // активен          
            );
            //добавляем заказ в БД    
            $order_id = $new_order->Add($arNewOrder); 

            //логирование
            $description = "Новый заказ №".$order_id."; тип бронирования: ".$arResult["TYPE_BOOKING"];                  
            eventLogAdd("BUSTOURPRO_EVENT_NEW_ORDER",$order_id,$description);



            //отправка письма
            if (checkNotice() == "Y") {
                //формируем данные для письма
                $props = getCompanyProperties();

                $userData = CUser::GetById($userID);
                $arUserData = $userData->Fetch();

                //письмо ОПЕРАТОРУ
                $THEME = "Новый заказ в системе онлайн бронирования BUSTOURPRO"; 
                $TEXT = "<h3>Данные о заказе</h3>
                <p>
                № заказа: <b>".$order_id."</b><br>
                Тип бронирования: <b>".$avaible_booking_types_NAMES[$arResult["TYPE_BOOKING"]]."</b><br>
                Статус заказа: <b>".$statusesNAMES[$new_order_props["STATUS"]]."</b><br>
                Тур: <b>".$arTour["NAME"]." (".$arTour["PROPERTY_DATE_FROM_VALUE"]." - ".$arTour["PROPERTY_DATE_TO_VALUE"].")"."</b><br>
                Компания: <b>".$arUserData["NAME"]."</b>                   
                </p>
                "; 
                $emailData = array(
                    "EMAIL_FROM" => $props["EMAIL"]["VALUE"],
                    "EMAIL" => $props["EMAIL"]["VALUE"],
                    "THEME" => $THEME,
                    "TEXT" => $TEXT
                );                                  
                CEvent::Send("BUSTOUR_NEW_AGENCY",LANG,$emailData,"N");
            }
            /////////////////////////////


            $arResult["ORDER_ID"] = $order_id;


            //получаем ID инфоблока с туристами
            $tourist_iblock = CIBlock::GetList(array(),array("CODE"=>"TOURIST"));
            $arTouristIblock = $tourist_iblock->Fetch();

            //получаем свойство "доп место"
            $extra_place = CIBlockPropertyEnum::GetList(array(),Array("CODE"=>"ADD_PLACE","IBLOCK_ID"=>$arTouristIblock["ID"]));
            $arExtraPlace = $extra_place->Fetch();



            //перебираем массив туристов и пишем в базу
            foreach ($_POST["Tourist"] as $tNum=>$tourist) {

                $el = new CIBlockElement;



                if (!$tourist["place"] || $tourist["place"] == "") {
                    $tourist["place"] = $arResult["PLACES_RESERV"][$tNum];
                }

                if (!$tourist["secondPlace"] || $tourist["secondPlace"] == "") {
                    $tourist["secondPlace"] = $arResult["PLACES_SECOND_RESERV"][$tNum];
                }


                $PROP = array();
                $PROP["COMPANY"] = getCurrentCompanyID();
                $PROP["TOUR"] = $arResult["TOUR_ID"];
                $PROP["ORDER"] = $order_id;
                $PROP["PASSPORT"] = $tourist["passport"];
                $PROP["PHONE"] = $tourist["phone"];
                $PROP["PLACE"] = $tourist["place"];
                $PROP["SECOND_PLACE"] = $tourist["secondPlace"];  
                $PROP["BIRTHDAY"] = $tourist["birthday"];
                $PROP["PRICE"] = $tourist["price"];
                $PROP["MATH_METHOD"] = $tourist["math"];
                //собираем услуги
                foreach($tourist["services"] as $service) {
                    $PROP["SERVICES"][] = $service;  
                }

                if ($tourist["add"] == "Y") {
                    $PROP["ADD_PLACE"] = $arExtraPlace["ID"]; //турист на доп месте    
                }       

                $arLoadProductArray = Array(
                    "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                    "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                    "PROPERTY_VALUES"=> $PROP,
                    "IBLOCK_ID" => $arTouristIblock["ID"],
                    "NAME"           => $tourist["name"],
                    "ACTIVE"         => "Y",            // активен          
                );
                //добавляем туристов в БД    
                //arshow($arLoadProductArray);
                $tourist_new_id = $el->Add($arLoadProductArray); 

                //логирование
                if ($tourist_new_id){
                    $description = "Новый пассажир ".$tourist_new_id."; ID заказа: ".$order_id."; места (туда/обратно): ".$PROP["PLACE"]."/".$PROP["SECOND_PLACE"];                  
                    eventLogAdd("BUSTOURPRO_EVENT_NEW_TOURIST",$tourist_new_id,$description);

                    //после этого нужно удалить из инфоблока с блокировкой мест запись о блокировке текущих мест
                    //получаем элемент с местом текущего пассажира             
                    $lock_place = CIBLockElement::GetList(array(), array("IBLOCK_CODE"=>"PLACES_LOCKER","NAME"=>array($tourist["place"],$tourist["secondPlace"]), "PROPERTY_USER_ID"=>$userID), false, false, array("ID","NAME", "PROPERTY_SCHEME_ID"));
                    while($arLockPlace = $lock_place->Fetch()){
                        if (CIBlockElement::Delete($arLockPlace["ID"])) {
                            //логирование  
                            $description = "Оформление заказа № ".$order_id." завершено, место ".$tourist["place"]." в автобусе ".$arLockPlace["PROPERTY_SCHEME_ID_VALUE"]." разблокировано" ;                          
                        } else {
                            $description = "Оформление заказа № ".$order_id." завершено, ОШИБКА! место ".$tourist["place"]." в автобусе ".$arLockPlace["PROPERTY_SCHEME_ID_VALUE"]." не разблокировано" ;    
                        }
                        eventLogAdd("BUSTOURPRO_EVENT_PLACES_UNLOCKED",$arLockPlace["ID"],$description);
                    }         
                }
                else {
                    $data = serialize($arLoadProductArray);
                    $description = "Ошибка добавления туриста ".$el->LAST_ERROR."; ID заказа: ".$order_id."; места (туда/обратно): ".$PROP["PLACE"]."/".$PROP["SECOND_PLACE"]."данные туриста: ".$data;                  
                    eventLogAdd("BUSTOURPRO_EVENT_NEW_TOURIST",$tourist_new_id,$description); 
                }





            }

            //и из инфоблока с блокировкой номеров тоже нужно удалить запись
            $lock_rooms = CIBLockElement::GetList(array(), array("IBLOCK_CODE"=>"ROOM_LOCKER","NAME"=>array($arResult["ID"],$arResult["SECOND_TOUR"]["ID"]),"PROPERTY_USER_ID"=>$userID), false, false, array("ID","NAME"));
            while($arLockRoom = $lock_rooms->Fetch()){
                if (CIBlockElement::Delete($arLockRoom["ID"])) {
                    //логирование
                    $description = "Оформление заказа № ".$order_id." завершено, номер в туре ".$arLockRoom["NAME"]." разблокирован" ; 
                }
                else {
                    $description = "Оформление заказа № ".$order_id." завершено, ОШИБКА! номер в туре ".$arLockRoom["NAME"]." не разблокирован" ;   
                }
                eventLogAdd("BUSTOURPRO_EVENT_ROOM_UNLOCKED",$arLockRoom["ID"],$description);
            }


        }

        if ($arResult["STEP"] == 4){
            header("location: /order-management/tour_selection/");
        }     


    }        




    ///////////////////////////////////////////////////////////
    ////////////////////////ТОЛЬКО ПРОЕЗД//////////////////////
    ///////////////////////////////////////////////////////////


    if ($arResult["TYPE_BOOKING"] == "ONLY_ROAD" || $arResult["TYPE_BOOKING"] == "DOUBLE_ROAD") {   


        //текущий заказ
        $arResult["TOUR_ID"] = $arParams["TOUR_ID"];
        $arResult["ID"] = $arResult["TOUR_ID"];
        $arResult["DIRECTION"] = array();
        $arResult["CITY"] = array();       
        $arResult["BUS_TO"] = array();
        $arResult["BUS_BACK"] = array();    



        //получаем параметры текущего автобуса

        $bus = CIBlockElement::GetList(array(), array("ID"=>$arResult["ID"],"PROPERTY_COMPANY"=>getCurrentCompanyID()), false, false, array("ID","NAME","PROPERTY_P_SCHEME","PROPERTY_BUS_DIRECTION", "PROPERTY_DEPARTURE", "PROPERTY_ARRIVAL"));
        $arBus = $bus->Fetch(); 
        //arshow($arBus);
        //собираем параметры тура (нам нужно получить города, в которые поедет автобус, стоимость только проезда) 

        $arResult["BUS"] = $arBus;

        $arSelect = array(
            "ID",
            "NAME",
            "PROPERTY_COMPANY",
            "PROPERTY_DIRECTION",
            "PROPERTY_CITY",    
            "PROPERTY_DATE_FROM",
            "PROPERTY_DATE_TO",
            "PROPERTY_DISCOUNT",  
            "PROPERTY_DISCONT_ON_ROOM_AND_DATE_TOUR",
        );

        $arFilter = array("IBLOCK_CODE"=>"TOUR");

        switch ($arBus["PROPERTY_BUS_DIRECTION_VALUE"]){
            case "Туда": $arFilter["PROPERTY_BUS_TO"] = $arResult["ID"]; break;
            case "Обратно": $arFilter["PROPERTY_BUS_BACK"] = $arResult["ID"]; break; 
        }          


        $tour = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $arTour = $tour->Fetch();

        switch ($arBus["PROPERTY_BUS_DIRECTION_VALUE"]){
            case "Туда": $arResult["DATE_FROM"] = $arTour["PROPERTY_DATE_FROM_VALUE"] ; break;
            case "Обратно": $arResult["DATE_FROM"] = $arTour["PROPERTY_DATE_TO_VALUE"] ; break; 
        }        




        //получаем информацию о направлении
        $direction = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"DIRECTION","ID"=>$arTour["PROPERTY_DIRECTION_VALUE"]),false, false, array("ID","NAME","PROPERTY_ROAD_PRICE"));
        $arDirection = $direction->Fetch();
        $arResult["DIRECTION"]["ID"] = $arDirection["ID"];
        $arResult["DIRECTION"]["NAME"] = "Направление";
        $arResult["DIRECTION"]["VALUE"] = $arDirection["NAME"];
        $arResult["DIRECTION"]["ROAD_PRICE"] = $arDirection["PROPERTY_ROAD_PRICE_VALUE"];

        $arResult["PRICE"] = $arResult["DIRECTION"]["ROAD_PRICE"];


        //схема рассадки с учетом занятых мест
        $arResult["BUS_SCHEME_VIEW"] = $arBus["PROPERTY_P_SCHEME_VALUE"];



        //если проезд туда и обратно, нужно собрать все возможные автобусы
        if ($arResult["TYPE_BOOKING"] == "DOUBLE_ROAD") {


            //получаем любой тур к которому относится текущий автобус, чтобы вычислить даты отправления/прибытия
            $tourFilter = array();
            switch($arResult["BUS"]["PROPERTY_BUS_DIRECTION_VALUE"]) {
                case "Туда": $tourFilter["PROPERTY_BUS_TO"] = $arResult["ID"]; break;
                case "Обратно": $tourFilter["PROPERTY_BUS_BACK"] = $arResult["ID"]; break;
            }

            $tour = CIBlockElement::GetList(array(),$tourFilter, false, array("nTopCount"=>1), array("PROPERTY_DATE_FROM","PROPERTY_DATE_TO","ID","NAME"));
            $arTour = $tour->Fetch(); 

            $dateTo = explode(".",$arTour["PROPERTY_DATE_TO_VALUE"]);
            $dateFrom = explode(".",$arTour["PROPERTY_DATE_FROM_VALUE"]);  

            $filterDateFrom = $dateFrom[2]."-".$dateFrom[1]."-".$dateFrom[0];
            $filterDateTo = $dateTo[2]."-".$dateTo[1]."-".$dateTo[0];  

            $arBusFilter = array(
                "IBLOCK_CODE"=>"BUS_ON_TOUR",
                "!PROPERTY_BUS_DIRECTION"=>$arResult["BUS"]["PROPERTY_BUS_DIRECTION_ENUM_ID"],  
                "PROPERTY_COMPANY" => getCurrentCompanyID()      
            );

            switch($arResult["BUS"]["PROPERTY_BUS_DIRECTION_VALUE"]) {
                case "Туда": $arBusFilter["ID"] = CIBlockElement::SubQuery("PROPERTY_BUS_BACK", array("IBLOCK_CODE" => "TOUR",">=PROPERTY_DATE_TO" => date($filterDateFrom." 00:00:00"))) ; break;
                case "Обратно": $arBusFilter["ID"] = CIBlockElement::SubQuery("PROPERTY_BUS_TO", array("IBLOCK_CODE" => "TOUR",">=PROPERTY_DATE_FROM" => date($filterDateTo." 00:00:00"))) ; break;  
            }


            //собираем все автобусы которые идут в противоположном направлении относительно первого и позже по дате отправления
            $buses = CIBLockElement::GetList(array(),$arBusFilter, false, false, array("ID","NAME","PROPERTY_P_SCHEME"));
            while($arBuses = $buses->Fetch()) {
                $arResult["BUSES"][$arBuses["ID"]] = array("ID"=>$arBuses["ID"],"NAME"=>$arBuses["NAME"],"SCHEME"=>$arBuses["PROPERTY_P_SCHEME_VALUE"]);
            }


        }


        //arshow($arResult);

        $arResult["STEP"] = (int) CBRequest::gi()->getPost("STEP");
        if (!($arResult["STEP"] >= 1 && $arResult["STEP"] <= 3)) {
            $arResult["STEP"] = 1;
        }
        if (!(isset($_POST["ORDER_MAKE"]) && check_bitrix_sessid())) {
            $arResult["STEP"] = 1;
        }

        //при переходе на второй шаг, нам нужно в схеме рассадки для данного тура отметить места туристов как забронированные
        if ($arResult["STEP"] == 2) {

            //логируем данные
            $postData = serialize($_POST);
            eventLogAdd("BUSTOURPRO_EVENT_PLACES_BOOKING",$arResult["ID"],$postData);

            $placesCheck = "Y";
            if (is_array($_POST["Places"]) && count($_POST["Places"]) > 0) {
                //проверяем, не занял ли кто-то данные места в момент оформления заказа 

                //первый автобус
                foreach ($_POST["Places"] as $place=>$status){
                    if (checkBusPlace($place,$arBus["ID"]) == "N") {
                        $placesCheck = "N";
                    }
                }
                if ($placesCheck == "N") {
                    echo "К сожалению одно или несколько выбранных вами мест уже занято. Выберите <a href=''>другие места</a>";
                    die();
                }
                //второй автобус
                foreach ($_POST["SecondPlaces"] as $place=>$status){
                    if (checkBusPlace($place,$_POST["secondBus"]) == "N") {
                        $placesCheck = "N";
                    }
                }
                if ($placesCheck == "N") {
                    echo "К сожалению одно или несколько выбранных вами мест уже занято. Выберите <a href=''>другие места</a>";
                    die();
                }
            }         


            //автобус ПЕРВЫЙ 
            //преобразуем схему в ассоциативный массив
            $scheme = json_decode($arResult["BUS_SCHEME_VIEW"], true);
            //перебираем схему, как только нашли места, которые выбрали ранее - меняем их состояние на "занято"
            foreach($scheme as $n=>$val) {
                foreach ($val as $i=>$place){
                    if (array_key_exists($i,$_POST["Places"])) {
                        $scheme[$n][$i] = $_POST["Places"][$i]; 
                        eventLogAdd("BUSTOURPRO_EVENT_PLACES_BOOKING",$arBus["ID"],"место ".$i." в автобусе ".$arBus["ID"]." забронировано");                          
                    }
                }
            }

            //кодируем схему обратно
            $scheme_new = json_encode($scheme);  
            //после этого обновляем схему рассадки в базе
            $el = new CIBlockElement;

            $PROP = array();
            $PROP["P_SCHEME"] = $scheme_new; 
            $PROP["COMPANY"] = getCurrentCompanyID();
            $PROP["BUS_DIRECTION"] = Array("VALUE" => $arBus["PROPERTY_BUS_DIRECTION_ENUM_ID"]); 
            $PROP["DEPARTURE"] = $arBus["PROPERTY_DEPARTURE_VALUE"];  
            $PROP["ARRIVAL"] = $arBus["PROPERTY_ARRIVAL_VALUE"];

            $arLoadProductArray = Array(
                "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                "PROPERTY_VALUES"=> $PROP,
                "NAME"           => $arBus["NAME"],
                "ACTIVE"         => "Y",            // активен          
            );
            //обновляем схему рассадки туда и обратно в БД            
            $res = $el->Update($arResult["ID"], $arLoadProductArray);


            if ($_POST["secondBus"] > 0) {

                //получаем схему второго автобуса
                $busSecond = CIBlockElement::GetList(array(), array("ID"=>$_POST["secondBus"]), false, false, array("ID","NAME","PROPERTY_P_SCHEME","PROPERTY_BUS_DIRECTION", "PROPERTY_DEPARTURE", "PROPERTY_ARRIVAL"));
                $arBusSecond = $busSecond->Fetch();   

                $arResult["BUS_SECOND"] = $arBusSecond;


                //автобус ВТОРОЙ 
                //преобразуем схему в ассоциативный массив
                $scheme = json_decode($arBusSecond["PROPERTY_P_SCHEME_VALUE"], true);
                //перебираем схему, как только нашли места, которые выбрали ранее - меняем их состояние на "занято"
                foreach($scheme as $n=>$val) {
                    foreach ($val as $i=>$place){
                        if (array_key_exists($i,$_POST["SecondPlaces"])) {
                            $scheme[$n][$i] = $_POST["SecondPlaces"][$i]; 
                            eventLogAdd("BUSTOURPRO_EVENT_PLACES_BOOKING",$arBusSecond["ID"],"место ".$i." в автобусе ".$arBusSecond["ID"]." забронировано");                          
                        }
                    }
                }

                //кодируем схему обратно
                $scheme_new = json_encode($scheme);  
                //после этого обновляем схему рассадки в базе
                $el = new CIBlockElement;

                $PROP = array();
                $PROP["P_SCHEME"] = $scheme_new; 
                $PROP["COMPANY"] = getCurrentCompanyID();
                $PROP["BUS_DIRECTION"] = Array("VALUE" => $arBusSecond["PROPERTY_BUS_DIRECTION_ENUM_ID"]);
                $PROP["DEPARTURE"] = $arBusSecond["PROPERTY_DEPARTURE_VALUE"];  
                $PROP["ARRIVAL"] = $arBusSecond["PROPERTY_ARRIVAL_VALUE"]; 

                $arLoadProductArray = Array(
                    "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                    "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                    "PROPERTY_VALUES"=> $PROP,
                    "NAME"           => $arBusSecond["NAME"],
                    "ACTIVE"         => "Y",            // активен          
                );
                //обновляем схему рассадки туда и обратно в БД            
                $res = $el->Update($arBusSecond["ID"], $arLoadProductArray);

            }


            //преобразуем места в массив удобного вида [i]=>place
            $arResult["CUR_PLASES"] = (array_keys ($_POST["Places"]));            
            $arResult["CUR_SECOND_PLASES"] = (array_keys ($_POST["SecondPlaces"]));


            //после добавления забронированных мест на схему, добавляем запись в инфоблок "блокировка"
            //это делается для того, чтобы если вдруг оформление заказа оборвется на втором шаге, можно было освободить забронированные места
            //это будет делать скрипт, весящий на кроне/агентах

            //получаем ID инфоблока с блокировкой мест
            $place_locker = CIBlock::GetList(array(), array("CODE"=>"PLACES_LOCKER"));
            $arPlaceLocker = $place_locker->Fetch();

            foreach ($arResult["CUR_PLASES"] as $place) {
                $lock = new CIBlockElement;  

                $lock_PROP = array();
                $lock_PROP["SCHEME_ID"] = $arResult["ID"];  
                $lock_PROP["USER_ID"] = $userID;

                $lockArray = Array(
                    "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                    "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                    "PROPERTY_VALUES"=> $lock_PROP,
                    "NAME"           => $place,
                    "ACTIVE"         => "Y",            // активен 
                    "IBLOCK_ID"=>$arPlaceLocker["ID"]         
                );     
                $lock_res = $lock->Add($lockArray);

                //логирование
                eventLogAdd("BUSTOURPRO_EVENT_PLACES_LOCKED",$lock_res,"Начато оформление заказа. место ".$place." в автобусе ".$arBus["ID"]." заблокировано");
                // echo $lock->LAST_ERROR;
            }

            //места во втором автобусе
            if (is_array($arResult["CUR_SECOND_PLASES"]) && count($arResult["CUR_SECOND_PLASES"]) > 0) {
                foreach ($arResult["CUR_SECOND_PLASES"] as $place) {
                    $lock = new CIBlockElement;  

                    $lock_PROP = array();
                    $lock_PROP["SCHEME_ID"] = $arBusSecond["ID"];  
                    $lock_PROP["USER_ID"] = $userID;

                    $lockArray = Array(
                        "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                        "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                        "PROPERTY_VALUES"=> $lock_PROP,
                        "NAME"           => $place,
                        "ACTIVE"         => "Y",            // активен 
                        "IBLOCK_ID"=>$arPlaceLocker["ID"]         
                    );     
                    $lock_res = $lock->Add($lockArray);

                    //логирование
                    eventLogAdd("BUSTOURPRO_EVENT_PLACES_LOCKED",$lock_res,"Начато оформление заказа. место ".$place." в автобусе ".$arBusSecond["ID"]." заблокировано");
                    // echo $lock->LAST_ERROR;
                }
            }



            //общее количество выбранных мест
            $arResult["PLACES_COUNT"] = $_POST["selectedPlacesCount"];
        }

        //при переходе на третий шаг добавлям в базу новый заказ и записи о туристах
        if ($arResult["STEP"] == 3) {    

            //логируем данные
            $postData = serialize($_POST);
            eventLogAdd("BUSTOURPRO_EVENT_PLACES_BOOKING",$arResult["ID"],$postData);


            //создаем массив с местами
            $arResult["PLACES_RESERV"] = array();
            $p = 1;
            foreach ($_POST["Places"] as $pKey=>$pStatus) {
                $arResult["PLACES_RESERV"][$p] = $pKey;   
                $p++;
            }  

            //создаем массив с местами
            $arResult["PLACES_SECOND_RESERV"] = array();
            $p = 1;
            foreach ($_POST["SecondPlaces"] as $pKey=>$pStatus) {
                $arResult["PLACES_SECOND_RESERV"][$p] = $pKey;   
                $p++;
            }   



            //вычисляем стоимость тура для агентства
            //полная скидка в рублях
            $agencyDISCOUNT = 0;

            //полная скидка в процентах на тур
            $full_discount_tour = getServiceDiscount($arResult["DIRECTION"]["ID"],$arResult["TYPE_BOOKING"]) + $arResult["DISCOUNT"];

            //условие для случая, когда тур считается по коммунальным             
            $payments_discount = getServiceDiscount($arResult["DIRECTION"]["ID"],"PAYMENTS") + $arResult["DISCOUNT"];

            //отдельно считаем процент с услуг                                             
            $services_discount = getServiceDiscount($arResult["DIRECTION"]["ID"],"SERVICES");   

            //перебираем туристов для расчета скидки для агентства
            foreach ($_POST["Tourist"] as $tourist) {  

                //проверяем метод расчета туриста. 
                //если использовался метод, в котором стоимость тура == коммунальным платежам, используес другой процент
                $mathMethod = CIBlockElement::GetList(array(),array("ID"=>$tourist["math"]), false, false, array("PROPERTY_MATH_TOUR"));
                $arMathMethod = $mathMethod->Fetch();
                if ($arMathMethod["PROPERTY_MATH_TOUR_VALUE"] == "Коммунальные платежи") {
                    $full_discount_tour = $payments_discount;  
                }       
                //скидка с тура (проезда)
                $price = $tourist["tour_price"]; //цена тура для туриста без учета доп услуг
                $agencyDISCOUNT = $agencyDISCOUNT + $price * $full_discount_tour / 100; 

                //скидка с услуг 
                if (is_array($tourist["services"]) && count($tourist["services"]) > 0) {      
                    $services = CIBlockElement::GetList(array(),array("ID"=>$tourist["services"]), false, false, array("PROPERTY_PRICE"));
                    while($arService = $services->Fetch()) {
                        $agencyDISCOUNT = $agencyDISCOUNT + $arService["PROPERTY_PRICE_VALUE"] * $services_discount / 100; 
                    }
                }

            } 

            //вычисляем цену и скидку на трансфер
            $transferPrice = getTransferPrice($_POST["departureCity"]);
            $agencyDISCOUNT = $agencyDISCOUNT + $transferPrice * $services_discount / 100;


            //добавляем заказ
            $new_order =  new CIBlockElement;

            $new_order_props = array();
            $new_order_props["COMPANY"] = getCurrentCompanyID();
            $new_order_props["OPERATOR_PRICE"] = ceil($_POST["all_summ"] - $agencyDISCOUNT);
            $new_order_props["PRICE"] = ceil($_POST["all_summ"]); //вычитаем скидку, посчитанную выше
            $new_order_props["BUS_ID"] = $arResult["ID"]; //для только проезда
            $new_order_props["SECOND_BUS_ID"] = $_POST["busSecond"]; //для только проезда в обе стороны
            $new_order_props["DEPARTURE_CITY"] = $_POST["departureCity"];
            $new_order_props["DATE_FROM"] = $arTour["PROPERTY_DATE_FROM_VALUE"];
            $new_order_props["HOTEL"] = $arResult["HOTEL"]["ID"];
            $new_order_props["CITY"] = $arResult["CITY"]["ID"];
            $new_order_props["COMPANY_NAME"] = $arUser["NAME"];

            //если установлен флаг автоматического подтверждения заказа
            if ($companySettings["AUTO_ORDER_CONFIRM"]["VALUE"] == "Да") {
                $new_order_props["STATUS"] = $statuses["STATUS_ACCEPTED"];  
            }
            else {
                $new_order_props["STATUS"] = $statuses["STATUS_NEW"]; 
            }


            $new_order_props["TYPE_BOOKING"] = $avaible_booking_types[$arResult["TYPE_BOOKING"]];   
            $new_order_props["NOTES"] = $_POST["notes"];

            //получаем ID инфоблока заказов
            $orders_iblock = CIBlock::GetList(array(), array("CODE"=>"ORDERS"));
            $arOrders = $orders_iblock->Fetch();

            $arNewOrder = Array(
                "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                "PROPERTY_VALUES"=> $new_order_props,
                "IBLOCK_ID" => $arOrders["ID"],
                "NAME"           => $arTour["NAME"]." (".$arTour["PROPERTY_DATE_FROM_VALUE"]." - ".$arTour["PROPERTY_DATE_TO_VALUE"].")",
                "ACTIVE"         => "Y",            // активен          
            );
            //добавляем заказ в БД    
            $order_id = $new_order->Add($arNewOrder);

            //логирование
            $description = "Новый заказ №".$order_id."; тип бронирования: ".$arResult["TYPE_BOOKING"];                  
            eventLogAdd("BUSTOURPRO_EVENT_NEW_ORDER",$order_id,$description);              


            //отправка письма
            if (checkNotice() == "Y") {
                //формируем данные для письма
                $props = getCompanyProperties();

                $userData = CUser::GetById($userID);
                $arUserData = $userData->Fetch();

                //письмо ОПЕРАТОРУ
                $THEME = "Новый заказ в системе онлайн бронирования BUSTOURPRO"; 
                $TEXT = "<h3>Данные о заказе</h3>
                <p>
                № заказа: <b>".$order_id."</b><br>
                Тип бронирования: <b>".$avaible_booking_types_NAMES[$arResult["TYPE_BOOKING"]]."</b><br>
                Статус заказа: <b>".$statusesNAMES[$new_order_props["STATUS"]]."</b><br>
                Тур: <b>".$arTour["NAME"]." (".$arTour["PROPERTY_DATE_FROM_VALUE"]." - ".$arTour["PROPERTY_DATE_TO_VALUE"].")"."</b><br>
                Компания: <b>".$arUserData["NAME"]."</b>                   
                </p>
                "; 
                $emailData = array(
                    "EMAIL_FROM" => $props["EMAIL"]["VALUE"],
                    "EMAIL" => $props["EMAIL"]["VALUE"],
                    "THEME" => $THEME,
                    "TEXT" => $TEXT
                );                                  
                CEvent::Send("BUSTOUR_NEW_AGENCY",LANG,$emailData,"N");
            }
            ///////////////////////

            $arResult["ORDER_ID"] = $order_id;


            //получаем ID инфоблока с туристами
            $tourist_iblock = CIBlock::GetList(array(),array("CODE"=>"TOURIST"));
            $arTouristIblock = $tourist_iblock->Fetch();

            //получаем свойство "доп место"
            $extra_place = CIBlockPropertyEnum::GetList(array(),Array("CODE"=>"ADD_PLACE","IBLOCK_ID"=>$arTouristIblock["ID"]));
            $arExtraPlace = $extra_place->Fetch();



            //перебираем массив туристов и пишем в базу
            foreach ($_POST["Tourist"] as $tNum=>$tourist) {

                $el = new CIBlockElement;                   

                if (!$tourist["place"] || $tourist["place"] == "") {
                    $tourist["place"] = $arResult["PLACES_RESERV"][$tNum];
                }

                if (!$tourist["secondPlace"] || $tourist["secondPlace"] == "") {
                    $tourist["secondPlace"] = $arResult["PLACES_SECOND_RESERV"][$tNum];
                }

                $PROP = array();
                $PROP["COMPANY"] = getCurrentCompanyID();
                $PROP["TOUR"] = $arResult["TOUR_ID"];
                $PROP["ORDER"] = $order_id;
                $PROP["PASSPORT"] = $tourist["passport"];
                $PROP["PHONE"] = $tourist["phone"];
                switch ($arBus["PROPERTY_BUS_DIRECTION_VALUE"]){
                    case "Туда": 
                        $PROP["PLACE"] = $tourist["place"]; 
                        $PROP["SECOND_PLACE"] = $tourist["secondPlace"];
                        break;
                    case "Обратно": 
                        $PROP["PLACE"] = $tourist["secondPlace"]; 
                        $PROP["SECOND_PLACE"] = $tourist["place"];
                        break; 
                }       
                $PROP["BIRTHDAY"] = $tourist["birthday"];
                $PROP["PRICE"] = $tourist["price"];
                $PROP["MATH_METHOD"] = $tourist["math"];
                if ($tourist["add"] == "Y") {
                    $PROP["ADD_PLACE"] = $arExtraPlace["ID"]; //турист на доп месте    
                }     

                $arLoadProductArray = Array(
                    "MODIFIED_BY"    => $userID, // элемент изменен текущим пользователем
                    "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
                    "PROPERTY_VALUES"=> $PROP,
                    "IBLOCK_ID" => $arTouristIblock["ID"],
                    "NAME"           => $tourist["name"],
                    "ACTIVE"         => "Y",            // активен          
                );
                //добавляем туристов в БД    
                $tourist_new_id = $el->Add($arLoadProductArray);

                //логирование
                if ($tourist_new_id){
                    $description = "Новый пассажир ".$tourist_new_id."; ID заказа: ".$order_id."; места (туда/обратно): ".$PROP["PLACE"]."/".$PROP["SECOND_PLACE"];                  
                    eventLogAdd("BUSTOURPRO_EVENT_NEW_TOURIST",$tourist_new_id,$description);


                    //после этого нужно удалить из инфоблока с блокировкой мест запись о блокировке текущих мест
                    //получаем элемент с местом текущего пассажира             
                    $lock_place = CIBLockElement::GetList(array(), array("IBLOCK_CODE"=>"PLACES_LOCKER","NAME"=>$tourist["place"]), false, false, array("ID","NAME","PROPERTY_SCHEME_ID"));
                    $arLockPlace = $lock_place->Fetch(); 
                    if (CIBlockElement::Delete($arLockPlace["ID"])) {
                        //логирование  
                        $description = "Оформление заказа № ".$order_id." завершено, место ".$tourist["place"]." в автобусе ".$arLockPlace["PROPERTY_SCHEME_ID_VALUE"]." разблокировано" ;                          
                    } else {
                        $description = "Оформление заказа № ".$order_id." завершено, ОШИБКА! место ".$tourist["place"]." в автобусе ".$arLockPlace["PROPERTY_SCHEME_ID_VALUE"]." не разблокировано" ;    
                    }
                    eventLogAdd("BUSTOURPRO_EVENT_PLACES_UNLOCKED",$arLockPlace["ID"],$description);
                }
                else {
                    $data = serialize($arLoadProductArray);
                    $description = "Ошибка добавления туриста ".$el->LAST_ERROR."; ID заказа: ".$order_id."; места (туда/обратно): ".$PROP["PLACE"]."/".$PROP["SECOND_PLACE"]."данные туриста: ".$data;                  
                    eventLogAdd("BUSTOURPRO_EVENT_NEW_TOURIST",$tourist_new_id,$description); 
                }



            }      


        }

        if ($arResult["STEP"] == 4){
            header("location: /order-management/tour_selection/");
        }



    }

    $arResult["DEPARTURE_CITY"] = array();
    //собираем города забора туристов
    $departureCity = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"TOURIST_DEPARTURE_CITY","PROPERTY_DIRECTION"=>$arResult["DIRECTION"]["ID"],"PROPERTY_COMPANY"=>getCurrentCompanyID()),false, false, array("ID","NAME"));
    while($arDepartureCity = $departureCity->Fetch()) {
        $arResult["DEPARTURE_CITY"][$arDepartureCity["ID"]] = $arDepartureCity["NAME"];  
    }

    $arResult["SERVICES"] = array();
    //собираем дополнительный услуги для текущего направления
    $service = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"SERVICES_GROOPS","PROPERTY_DIRECTION"=>$arResult["DIRECTION"]["ID"],"PROPERTY_COMPANY"=>getCurrentCompanyID()), false, false, array("PROPERTY_SERVICE")); 
    while($arService = $service->Fetch()) {   
        $serviceDetail = CIBLockELement::GetList(array(), array("ID"=>$arService["PROPERTY_SERVICE_VALUE"]), false, false, array("ID","NAME","PROPERTY_PRICE"));
        $arCerviceDetail = $serviceDetail->Fetch();

        $service_params = array(
            "ID" => $arCerviceDetail["ID"],
            "NAME" => $arCerviceDetail["NAME"],
            "PRICE"=> $arCerviceDetail["PROPERTY_PRICE_VALUE"]
        );

        $arResult["SERVICES"][] = $service_params;  
    }


    //arshow($arResult);

    $this->IncludeComponentTemplate();
?>