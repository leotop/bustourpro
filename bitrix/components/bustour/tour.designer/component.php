<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    function isAccess(&$COMPANY_ID) {
        global $USER;

        if (!CModule::IncludeModule("iblock")) {
            return false;
        }

        $arGroups = $USER->GetUserGroupArray();
        if (!(count(array_intersect($arGroups, array(6))) > 0)) {
            return false;
        }

        $COMPANY_ID = getCurrentCompanyID();
        if (!$COMPANY_ID) {
            return false;
        }

        return true;
    }

    $arResult["ERRORS"] = array();

    $COMPANY_ID = null;
    if (!isAccess($COMPANY_ID)) {
        $APPLICATION->AuthForm("");
    }

    $arResult["STEP"] = (int) CBRequest::gi()->getPost("STEP");
    if (!($arResult["STEP"] >= 1 && $arResult["STEP"] <= 7)) {
        $arResult["STEP"] = 1;
    }
    if (!(isset($_POST["TOUR_DESIGNER"]) && check_bitrix_sessid())) {
        $arResult["STEP"] = 1;
    }

    $f = sfForm::getSFK('designer-tours__status');
    if ($f && is_array($f)) {
        $arResult["MESSAGE"] = $f['message'];
        $APPLICATION->SetTitle($APPLICATION->GetTitle() .' - Шаг 7');
        $this->IncludeComponentTemplate("result");
        return;
    }


    if ($arResult["STEP"] >= 1) {
        //Список автобусов
        $arResult['LIST_BUS'] = array();

        $arSelect = Array("ID", "NAME");
        $arFilter = Array(
            "IBLOCK_CODE"=>"BUS",
            "PROPERTY_COMPANY"=>$COMPANY_ID,

            "ACTIVE_DATE"=>"Y",
            "ACTIVE"=>"Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            $arResult['LIST_BUS'][$arFields["ID"]] = $arFields;
        }

        $BUS = CBRequest::gi()->getPost('BUS');
        $arResult["BUS"] = (!empty($arResult["LIST_BUS"][$BUS]))? $BUS: null;

        if ($arResult["STEP"] > 1) {
            if (empty($arResult["BUS"])) {
                $arResult["ERRORS"][] = "Выберите автобус.";
                $arResult["STEP"] = 1;
            }
        }
    }

    if ($arResult["STEP"] >= 2) {
        //Список направлений
        $arResult['LIST_DIRECTION'] = array();
        $arResult['LIST_DIRECTION_DATA'] = array();

        $arSelect = Array("ID", "NAME");
        $arFilter = Array(
            "IBLOCK_CODE"=>"DIRECTION",
            "PROPERTY_COMPANY"=>$COMPANY_ID,

            "ACTIVE_DATE"=>"Y",
            "ACTIVE"=>"Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            $arResult['LIST_DIRECTION'][$arFields["ID"]] = $arFields;
        }
        $DIRECTION = CBRequest::gi()->getPost('DIRECTION');
        $arResult["DIRECTION"] = (!empty($arResult["LIST_DIRECTION"][$DIRECTION]))? $DIRECTION: null;


        if ($arResult["STEP"] > 2) {
            if (empty($arResult["DIRECTION"])) {
                $arResult["ERRORS"][] = "Выберите направление.";
                $arResult["STEP"] = 2;
            }
        }

        //Формирование данных по Городу->Гостинице->Номеру
        $arResult["DIRECTION_DATA__LIST_CITY"] = array();
        $arResult["DIRECTION_DATA__LIST_HOTEL"] = array();
        $arResult["DIRECTION_DATA__LIST_ROOM"] = array();

        if ($arResult["DIRECTION"]) {
            //DIRECTION_DATA__LIST_CITY
            $arSelect = Array("ID", "NAME");
            $arFilter = Array(
                "IBLOCK_CODE"=>"CITY",
                "PROPERTY_COMPANY"=>$COMPANY_ID,
                "PROPERTY_DIRECTION"=>$arResult["LIST_DIRECTION"][$DIRECTION]["ID"],

                "ACTIVE_DATE"=>"Y",
                "ACTIVE"=>"Y");
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

            while($ob = $res->GetNextElement())
            {
                $arFields = $ob->GetFields();
                $arResult["DIRECTION_DATA__LIST_CITY"][$arFields["ID"]] = $arFields;
            }

            //DIRECTION_DATA__LIST_HOTEL
            if (!empty($arResult["DIRECTION_DATA__LIST_CITY"])) {
                $arSelect = Array("ID", "NAME", "PROPERTY_CITY");
                $arFilter = Array(
                    "IBLOCK_CODE"=>"HOTEL",
                    "PROPERTY_COMPANY"=>$COMPANY_ID,
                    "PROPERTY_CITY"=>array_keys($arResult["DIRECTION_DATA__LIST_CITY"]),

                    "ACTIVE_DATE"=>"Y",
                    "ACTIVE"=>"Y"
                );

                $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

                while($ob = $res->GetNextElement())
                {
                    $arFields = $ob->GetFields();
                    $arResult["DIRECTION_DATA__LIST_HOTEL"][$arFields["ID"]] = $arFields;
                }
            }

            //DIRECTION_DATA__LIST_ROOM
            if (!empty($arResult["DIRECTION_DATA__LIST_HOTEL"])) {
                $arSelect = Array("ID", "NAME", "PROPERTY_HOTEL", "PROPERTY_NUMBER_ROOM", "PROPERTY_IS_ADD_ADDITIONAL_SEATS");
                $arFilter = Array(
                    "IBLOCK_CODE"=>"ROOM",
                    "PROPERTY_COMPANY"=>$COMPANY_ID,
                    "PROPERTY_HOTEL"=>array_keys($arResult["DIRECTION_DATA__LIST_HOTEL"]),

                    "ACTIVE_DATE"=>"Y",
                    "ACTIVE"=>"Y");
                $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

                while($ob = $res->GetNextElement())
                {
                    $arFields = $ob->GetFields();
                    $arResult["DIRECTION_DATA__LIST_ROOM"][$arFields["ID"]] = $arFields;
                }
            }
        }

        //__dump($arResult["DIRECTION_DATA__LIST_CITY"]);
        //__dump($arResult["DIRECTION_DATA__LIST_HOTEL"]);
        // __dump($arResult["DIRECTION_DATA__LIST_ROOM"]);
    }

    if ($arResult["STEP"] >= 3) {

        /**
        * $arResult["DIRECTION_DATA__LIST"][idCity][idHotel][idRoom]: bool - Наличие элемента
        * $_POST["LIST_DIRECTION_DATA__ROOM"]: array() - Данные с формы
        */
        //Обработка формы
        $LIST_DIRECTION_DATA__ROOM = CBRequest::gi()->getPost('LIST_DIRECTION_DATA__ROOM');
        if (!is_array($LIST_DIRECTION_DATA__ROOM)) {
            $LIST_DIRECTION_DATA__ROOM = array();
        }

        foreach ($LIST_DIRECTION_DATA__ROOM as $idData_Room => &$vData_Room) {
            if (empty($vData_Room["S"]) || empty($arResult["DIRECTION_DATA__LIST_ROOM"][$idData_Room])) {
                $vData_Room["S"] = 0;
            }

            if ($vData_Room["NR"] > $arResult["DIRECTION_DATA__LIST_ROOM"][$idData_Room]["PROPERTY_NUMBER_ROOM_VALUE"]) {
                $vData_Room["NR"] = $arResult["DIRECTION_DATA__LIST_ROOM"][$idData_Room]["PROPERTY_NUMBER_ROOM_VALUE"];
            }
        }
        unset($vData_Room);

        //Значения по дефолту
        if ($arResult["STEP"] == 3) {
            $LIST_DIRECTION_DATA__ROOM = array();
            foreach ($arResult["DIRECTION_DATA__LIST_ROOM"] as $idRoom => $vRoom) {
                $LIST_DIRECTION_DATA__ROOM[$idRoom] = array(
                    "S"=>1,
                    "NR"=>$vRoom["PROPERTY_NUMBER_ROOM_VALUE"]
                );
            }
        }
        $arResult["LIST_DIRECTION_DATA__ROOM"] = $LIST_DIRECTION_DATA__ROOM;

        //Формирование видимости элементов
        $arResult["DIRECTION_DATA__LIST"] = array();
        foreach ($arResult["DIRECTION_DATA__LIST_ROOM"] as $vRoom) {
            $idRoom = $vRoom["ID"];
            $idHotel = $vRoom["PROPERTY_HOTEL_VALUE"];
            $idCity = $arResult["DIRECTION_DATA__LIST_HOTEL"][$idHotel]["PROPERTY_CITY_VALUE"];

            if ($LIST_DIRECTION_DATA__ROOM[$idRoom]["S"]) {
                $arResult["DIRECTION_DATA__LIST"][$idCity][$idHotel][$idRoom] = true;
            }
        }

        if ($arResult["STEP"] > 3) {

            if (empty($arResult["DIRECTION_DATA__LIST"])) {
                $arResult["ERRORS"][] = "Выберите номера в гостиницах.";
                $arResult["STEP"] = 3;
            }

            foreach ($arResult["LIST_DIRECTION_DATA__ROOM"] as $vRoom) {
                if ($vRoom["S"]) {
                    if (!is_numeric($vRoom["NR"]) || $vRoom["NR"] < 1) {
                        $arResult["ERRORS"][] = "Заполните все поля корректно.";
                        $arResult["STEP"] = 3;
                    }
                }
            }
        }
    }

    if ($arResult["STEP"] >= 3) { 
        if (empty($arResult["LIST_DIRECTION_DATA__ROOM"])) {
            $arResult["ERRORS"][] = "В гостиницах данного направления не найдено ни одного номера! Добавьте номера в гостиницы, либо выберите другое направление.";
            $arResult["STEP"] = 3;
        }
    }


    //arshow($arResult["LIST_DIRECTION_DATA__ROOM"]);
    if ($arResult["STEP"] >= 4) {

        //Время проживания
        $arResult["LIST_DURATION"] = array();
        for ($i = 1; $i < 30; $i++) {
            $arResult["LIST_DURATION"][$i] = array(
                "ID"=>$i,
                "VALUE"=>$i
            );
        }

        $DURATION = (int) CBRequest::gi()->getPost('DURATION');
        $arResult["DURATION"] = (!empty($arResult["LIST_DURATION"][$DURATION]))? $DURATION: null;

        if ($arResult["STEP"] > 4) {
            if (empty($arResult["DURATION"])) {
                $arResult["ERRORS"][] = "Выберите продолжительность тура.";
                $arResult["STEP"] = 4;
            }
        }
    }

    if ($arResult["STEP"] >= 4) {
        //Даты отъезда
        $arResult["LIST_DATE_DEPARTURE"] = array();

        $LIST_DATE_DEPARTURE = CBRequest::gi()->getPost('LIST_DATE_DEPARTURE');
        if (!is_array($LIST_DATE_DEPARTURE)) {
            $LIST_DATE_DEPARTURE = array();
        }

        $arResult["LIST_DATE_DEPARTURE"] = $LIST_DATE_DEPARTURE;

        if ($arResult["STEP"] > 4) {
            if (empty($arResult["LIST_DATE_DEPARTURE"])) {
                $arResult["ERRORS"][] = "Выберите даты отъезда";
                $arResult["STEP"] = 4;
            }
        }
    }

    //Список дат для последнего этапа.
    $arResult["LIST_FORMATION_DATE"] = array();
    foreach ($LIST_DATE_DEPARTURE as $vDate) {
        $DATE_FROM = strtotime($vDate);
        $DATE_TO = $DATE_FROM + $arResult["DURATION"]*24*60*60;

        $ITEM_DATE = null;
        $ITEM_DATE["DATE_FROM_UNIX"] = $DATE_FROM;
        $ITEM_DATE["DATE_FROM"] = date('Y-m-d', $DATE_FROM);

        $ITEM_DATE["DATE_TO_UNIX"] = $DATE_TO;
        $ITEM_DATE["DATE_TO"] = date('Y-m-d', $DATE_TO);

        $arResult["LIST_FORMATION_DATE"][$ITEM_DATE["DATE_FROM"]] = $ITEM_DATE;
    }

    $LIST_FORMATION_DATA = array();
    //Данные по умолчанию
    if ($arResult["STEP"] >= 5) {
        foreach ($arResult["DIRECTION_DATA__LIST_ROOM"] as $idRoom => $vRoom) {
            $idHotel = $vRoom["PROPERTY_HOTEL_VALUE"];
            $idCity = $arResult["DIRECTION_DATA__LIST_HOTEL"][$idHotel]["PROPERTY_CITY_VALUE"];
            if (empty($arResult["DIRECTION_DATA__LIST"][$idCity][$idHotel][$idRoom]) || !$arResult["DIRECTION_DATA__LIST"][$idCity][$idHotel][$idRoom]) {
                continue;
            }

            $ITEM_ROOM = null;

            $ITEM_ROOM["PRICE1"] = '';

            if ($arResult["DIRECTION_DATA__LIST_ROOM"][$idRoom]["PROPERTY_IS_ADD_ADDITIONAL_SEATS_ENUM_ID"] == 3) {
                $ITEM_ROOM["PRICE2"] = '';
            }

            $ITEM_ROOM["BUS"] = $arResult["BUS"];
            $ITEM_ROOM["NUMBER_ROOM"] = "";

            foreach (array_keys($arResult["LIST_FORMATION_DATE"]) as $vDate) {
                $LIST_FORMATION_DATA[$idRoom][$vDate] = $ITEM_ROOM;
            }
        }
    }

    if ($arResult["STEP"] > 5) {
        //Заполнение данных с формы
        $LIST_FORMATION_DATA__FORM = CBRequest::gi()->getPost('LIST_FORMATION_DATA__FORM');

        //__dump($LIST_FORMATION_DATA__FORM);
        foreach ($LIST_FORMATION_DATA__FORM as $kIdRoom => $vListData) {
            foreach ($vListData as $kDate => $vData) {
                if (!empty($vData)) {
                    if (isset($vData["PRICE1"])) {
                        $LIST_FORMATION_DATA[$kIdRoom][$kDate]["PRICE1"] = $vData["PRICE1"];
                    }

                    if ($vData["PRICE2"]) {
                        if (array_key_exists("PRICE2", $vData)) {
                            $LIST_FORMATION_DATA[$kIdRoom][$kDate]["PRICE2"] = $vData["PRICE2"];
                        }
                    }

                    if (isset($vData["BUS"])) {
                        $LIST_FORMATION_DATA[$kIdRoom][$kDate]["BUS"] = $vData["BUS"];
                    }
                }
            }
        }
    }
    $arResult["LIST_FORMATION_DATA"] = $LIST_FORMATION_DATA;

    if ($arResult["STEP"] > 5) {
        //Проверка веденных данных
        foreach ($LIST_FORMATION_DATA__FORM as $kIdRoom => $vListData) {
            foreach ($vListData as $kDate => $vData) {
                if (!is_numeric($vData["PRICE1"]) || (array_key_exists("PRICE2", $vData) && !is_numeric($vData["PRICE2"])) || !isset($arResult["LIST_BUS"][$vData["BUS"]])) {
                    $arResult["ERRORS"][] = "Заполните все поля корректно";
                    $arResult["STEP"] = 5;
                    break;
                }
            }

            if (!empty($arResult["ERRORS"])) {
                break;
            }
        }
    }

    if ($arResult["STEP"] >= 6) {
        $arResult["TOUR_NAME"] = trim(CBRequest::gi()->getPost("TOUR_NAME"));

        if ($arResult["STEP"] > 6) {
            if (!$arResult["TOUR_NAME"]) {
                $arResult["ERRORS"][] = "Введите незвание тура";
                $arResult["STEP"] = 6;
            }
        }
    }

    //Добавление туров
    //todo: Проверить наличие проверки на привязку парметров к COMAPNY_ID
    if ($arResult["STEP"] == 7) {
        
       

        
         //перед добавление туров, создаем схему рассадки пассажиров в автобусе для данного тура
        $bus_schemes = array();
        //формируем массив вида дата отправления=>id автобуса
        foreach ($arResult["LIST_FORMATION_DATA"] as $vListData) {
            foreach ($vListData as $kDate => $vData) {
                $bus_schemes[$kDate] =  $vData["BUS"];
            }

        }
        
        //arshow($bus_schemes);
        
        //получаем время в пути
        $direction = CIBlockElement::GetList(array(),array("ID"=>$arResult["DIRECTION"]),false,false, array("ID","NAME","PROPERTY_ROAD_TIME"));
        $arDirection = $direction->Fetch();
        $roadTime = $arDirection["PROPERTY_ROAD_TIME_VALUE"]; //время в пути в одну сторону
        

        $sootv = array(); //в этот массив (вида дата=>id схемы) будут заносится id новых схем.
        //далее перебираем полученный выше массив, чтобы создать схемы рассадки для каждой даты
        foreach ($bus_schemes as $date=>$bus_id) {
            //1. получаем схему выбранного автобуса
            $bus = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"BUS","ID"=>$bus_id), false, false, array("PROPERTY_SCHEME"));
            $arBus = $bus->Fetch();
            //arshow($arBus);
            //2. создаем запись в инфоблоке "автобусы для туров"     

            //получаем свойство "направление" для схемы автобуса (туда/обратно)
            $bus_directions = array();
            $busDirection = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"),Array("CODE"=>"BUS_DIRECTION"));
            while($arBusDirection = $busDirection->Fetch()) {
                $bus_directions[$arBusDirection["XML_ID"]] = $arBusDirection; 
            }
            
             //получаем ID инфоблока со схемами автобусов 
            $busSchemeIblock = CIBLock::GEtLIst(array(), array("CODE"=>"BUS_ON_TOUR"));
            $arBusSchemeIblock = $busSchemeIblock->Fetch();

            //автобус ТУДА
            $bus_el = new CIBlockElement;    
            $PROP["P_SCHEME"] = $arBus["PROPERTY_SCHEME_VALUE"];
            $PROP["BUS_DIRECTION"] = Array("VALUE" => $bus_directions["TO"]["ID"]);
            $PROP["COMPANY"] = $COMPANY_ID;
            $PROP["DEPARTURE"] = date('d.m.Y', $arResult["LIST_FORMATION_DATE"][$date]["DATE_FROM_UNIX"]);
            $PROP["ARRIVAL"] = date('d.m.Y', $arResult["LIST_FORMATION_DATE"][$date]["DATE_FROM_UNIX"] + $roadTime*86400);;            

            $arBusLoadProductArray = Array(
                "MODIFIED_BY"=>$GLOBALS["USER"]->GetID(), // элемент изменен текущим пользователем
                "IBLOCK_ID"=>$arBusSchemeIblock["ID"], 
                "PROPERTY_VALUES" => $PROP,
                "NAME" => $arResult["TOUR_NAME"]." (".$arResult["LIST_FORMATION_DATE"][$date]["DATE_FROM"]." - ".$arResult["LIST_FORMATION_DATE"][$date]["DATE_TO"]."), ".$bus_directions["TO"]["VALUE"],
                "ACTIVE" => "Y",
            );
            //arshow($arBusLoadProductArray); 
            $bus_on_tout_scheme_to = $bus_el->Add($arBusLoadProductArray);  
            $sootv[$date]["TO"] = $bus_on_tout_scheme_to;
            
            

            //автобус ОБРАТНО
            $bus_el = new CIBlockElement;
            $PROP["BUS_DIRECTION"] = Array("VALUE" => $bus_directions["BACK"]["ID"]);
            $PROP["DEPARTURE"] = date('d.m.Y', $arResult["LIST_FORMATION_DATE"][$date]["DATE_TO_UNIX"] - $roadTime*86400);;
            $PROP["ARRIVAL"] = date('d.m.Y', $arResult["LIST_FORMATION_DATE"][$date]["DATE_TO_UNIX"]);

            $arBusLoadProductArray = Array(
                "MODIFIED_BY"=>$GLOBALS["USER"]->GetID(), // элемент изменен текущим пользователем
                "IBLOCK_ID"=>$arBusSchemeIblock["ID"], 
                "PROPERTY_VALUES" => $PROP,
                "NAME" => $arResult["TOUR_NAME"]." (".$arResult["LIST_FORMATION_DATE"][$date]["DATE_FROM"]." - ".$arResult["LIST_FORMATION_DATE"][$date]["DATE_TO"]."), ".$bus_directions["BACK"]["VALUE"],
                "ACTIVE" => "Y",
            );
            //arshow($arBusLoadProductArray);
            $bus_on_tout_scheme_back = $bus_el->Add($arBusLoadProductArray);  
            $sootv[$date]["BACK"] = $bus_on_tout_scheme_back;
        }

        
         //arshow($arResult);
       

        //формируем туры

        // arshow($arResult["LIST_FORMATION_DATA"]);


        $el = new CIBlockElement;
        foreach ($arResult["LIST_FORMATION_DATA"] as $kIdRoom => $vListData) {   
            foreach ($vListData as $kDate => $vData) {
                $_room = $arResult["DIRECTION_DATA__LIST_ROOM"][$kIdRoom];

                $PROP = array();
                $PROP["COMPANY"] = $COMPANY_ID;
                $PROP["PRICE"] = intval($vData["PRICE1"]);
                $PROP["PRICE_ADDITIONAL_SEATS"] = (array_key_exists("PRICE2", $vData))? intval($vData["PRICE2"]): 0;
                $PROP["BUS_TO"] = $sootv[$kDate]["TO"];
                $PROP["BUS_BACK"] = $sootv[$kDate]["BACK"];
                $PROP["DIRECTION"] = $arResult["DIRECTION"];
                $PROP["HOTEL"] = $_room["PROPERTY_HOTEL_VALUE"];
                $PROP["CITY"] = $arResult["DIRECTION_DATA__LIST_HOTEL"][$_room["PROPERTY_HOTEL_VALUE"]]["PROPERTY_CITY_VALUE"];
                $PROP["ROOM"] = $kIdRoom;

                $PROP["DATE_FROM"] = date('d.m.Y H:i:s', $arResult["LIST_FORMATION_DATE"][$kDate]["DATE_FROM_UNIX"]);
                $PROP["DATE_TO"] = date('d.m.Y H:i:s', $arResult["LIST_FORMATION_DATE"][$kDate]["DATE_TO_UNIX"]);

                $PROP["DISCONT"] = 0;
                $PROP["DISCONT_ON_ROOM_AND_DATE_TOUR"] = 0;

                $PROP["NUMBER_ROOM"] = $arResult["LIST_DIRECTION_DATA__ROOM"][$kIdRoom]["NR"]; //количество номеров, изменяется при бронировании
                $PROP["MAX_ROOMS"] = $arResult["LIST_DIRECTION_DATA__ROOM"][$kIdRoom]["NR"];  //максимальное количество номеров - не изменяется


                //получаем ID инфоблока "ТУРЫ"
                $tourIblock = CIBLock::GEtLIst(array(), array("CODE"=>"TOUR"));
                $arTourIblock = $tourIblock->Fetch(); 

                $arLoadProductArray = Array(
                    "MODIFIED_BY"=>$GLOBALS["USER"]->GetID(), // элемент изменен текущим пользователем
                    "IBLOCK_ID"=>$arTourIblock["ID"], 
                    "PROPERTY_VALUES" => $PROP,
                    "NAME" => $arResult["TOUR_NAME"],
                    "ACTIVE" => "Y",
                );

                // arshow($arLoadProductArray);

                $el->Add($arLoadProductArray);
            }
        }

        sfForm::setSFK('designer-tours__status',
            array(
                'status' => 1,
                'message' => 'Формирование завершено!'
            )
        );
        LocalRedirect($APPLICATION->GetCurPageParam());
        exit();
    }

    $APPLICATION->SetTitle($APPLICATION->GetTitle() .' - Шаг '. $arResult["STEP"]);
    $this->IncludeComponentTemplate("template");

?>