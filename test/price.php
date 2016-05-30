<?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
    $APPLICATION->SetTitle("калькулятор цен");
?>


<form action="" method="get" name="calc">
    <table class="data-table">
        <tr>
            <td>Тур</td>
            <td>
                <?
                    $tour = CIBlockElement::GetList(array("ID"=>"ASC"/* "PROPERTY_DATE_FROM"=>"ASC"*/), array("IBLOCK_CODE"=>"TOUR","PROPERTY_COMPANY"=>getCurrentCompanyId()), false, false, array("ID","NAME","PROPERTY_DATE_FROM","PROPERTY_DATE_TO","PROPERTY_ROOM")); 
                ?>
                <select name="tour">
                    <?while($arTour = $tour->Fetch()){
                        $room_name = get_iblock_element_name($arTour["PROPERTY_ROOM_VALUE"]);
                        ?>
                        <option value="<?=$arTour["ID"]?>" <?if ($arTour["ID"] == $_GET["tour"]){?>selected="selected" <?}?>>
                        #<?=$arTour["ID"]?> - [<?=$arTour["PROPERTY_DATE_FROM_VALUE"]." - ".$arTour["PROPERTY_DATE_TO_VALUE"]?>] <?=$arTour["NAME"]?>, <?=$room_name?>
                        </option>
                        <?}?>
                </select>
            </td>
        </tr>

        <tr>
            <td>Доп. место</td>
            <td>
                <select name="extra_place">
                    <option value="N" <?if ($_GET["extra_place"] == "N"){?> selected="selected"<?}?>>Нет</option>
                    <option value="Y" <?if ($_GET["extra_place"] == "Y"){?> selected="selected"<?}?>>Да</option>
                </select>
            </td>
        </tr>

        <tr>
            <td>Тип бронирования</td>
            <td>
                <select name="type">
                    <option value="STANDART" <?if ($_GET["type"] == "STANDART"){?> selected="selected"<?}?>>Стандарт</option>
                    <option value="ONLY_ROOM" <?if ($_GET["type"] == "ONLY_ROOM"){?> selected="selected"<?}?>>Только проживание</option>
                    <option value="DOUBLE_TOUR" <?if ($_GET["type"] == "DOUBLE_TOUR"){?> selected="selected"<?}?>>Двойной тур</option>
                </select>
            </td>
        </tr>

        <tr>
            <td>Дата рождения</td>
            <td>
                <?$APPLICATION->IncludeComponent(
                        'bitrix:main.calendar',
                        '',
                        array(
                            'FORM_NAME' => 'calc',
                            'INPUT_NAME' => "birthday",
                            'INPUT_VALUE' => $_GET["birthday"],
                            "SHOW_INPUT" => "Y"
                        ),
                        null,
                        array('HIDE_ICONS' => 'N')
                    );?>
            </td>
        </tr>


    </table>

    <input type="submit" value="Расчитать">
</form>
<br><br>

<table class="data-table">
    <tr class="item_unactive">
        <td colspan="2"><b>ТУР</b></td>
    </tr>
    <?
        //получаем тур
        $arSelect = array(
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
        $tour = CIBlockElement::GetList(array(), array("ID"=>$_GET["tour"]), false, false, $arSelect);
        $arTour = $tour->Fetch();    
    ?>
    <tr>
        <td>Дата начала</td><td><?=$arTour["PROPERTY_DATE_FROM_VALUE"]?></td>
    </tr>

    <tr>
        <td>Дата окончания</td><td><?=$arTour["PROPERTY_DATE_TO_VALUE"]?></td>
    </tr>

    <tr>
        <td>Стоимость тура</td><td><?=$arTour["PROPERTY_PRICE_VALUE"]?></td>
    </tr> 

    <tr>
        <td>Стоимость доп места</td><td><?=$arTour["PROPERTY_PRICE_ADDITIONAL_SEATS_VALUE"]?></td>
    </tr>

    <tr>
        <td>Скидка на тур</td><td><?=$arTour["PROPERTY_DISCONT_VALUE"]?>% (<?=getDiscountValue($arTour["PROPERTY_PRICE_VALUE"],$arTour["PROPERTY_DISCONT_VALUE"],"P");?> руб)</td>
    </tr>

    <tr class="item_unactive">
        <td colspan="2" ><b>НАПРАВЛЕНИЕ</b></td>
    </tr>

    <? $directionArSelect = array(
            "PROPERTY_ROAD_PRICE",
            "PROPERTY_ONLY_ROOM_ROAD_PRICE",
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

        $direction = CIBlockElement::GetLIst(array(), array("ID"=>$arTour["PROPERTY_DIRECTION_VALUE"]), false, false, $directionArSelect);
        $arDirection = $direction->Fetch();?>
    <tr>
        <td>Только проезд</td><td><?=$arDirection["PROPERTY_ROAD_PRICE_VALUE"]?></td>
    </tr>

    <tr>
        <td>Проезд для "Только проживание"</td><td><?=$arDirection["PROPERTY_ONLY_ROOM_ROAD_PRICE_VALUE"]?></td>
    </tr>

    <tr>
        <td>Стоимость проезда в туре</td><td><?=$arDirection["PROPERTY_ROAD_PRICE_IN_TOUR_VALUE"]?></td>
    </tr>

    <?if ($arDirection["PROPERTY_ROAD_PRICE_BY_MONTH_VALUE"]){?>

        <tr>
            <td>Стоимость проезда помесячно</td><td><?=$arDirection["PROPERTY_ROAD_PRICE_BY_MONTH_VALUE"]?></td>
        </tr>               

        <tr>
            <td>Стоимость проезда январь</td><td><?=$arDirection["PROPERTY_MONTH_PRICE_1_VALUE"]?></td>
        </tr>

        <tr>
            <td>Стоимость проезда февраль</td><td><?=$arDirection["PROPERTY_MONTH_PRICE_2_VALUE"]?></td>
        </tr>

        <tr>
            <td>Стоимость проезда март</td><td><?=$arDirection["PROPERTY_MONTH_PRICE_3_VALUE"]?></td>
        </tr>

        <tr>
            <td>Стоимость проезда апрель</td><td><?=$arDirection["PROPERTY_MONTH_PRICE_4_VALUE"]?></td>
        </tr>

        <tr>
            <td>Стоимость проезда март</td><td><?=$arDirection["PROPERTY_MONTH_PRICE_5_VALUE"]?></td>
        </tr>

        <tr>
            <td>Стоимость проезда июнь</td><td><?=$arDirection["PROPERTY_MONTH_PRICE_6_VALUE"]?></td>
        </tr>

        <tr>
            <td>Стоимость проезда июль</td><td><?=$arDirection["PROPERTY_MONTH_PRICE_7_VALUE"]?></td>
        </tr>

        <tr>
            <td>Стоимость проезда август</td><td><?=$arDirection["PROPERTY_MONTH_PRICE_8_VALUE"]?></td>
        </tr>

        <tr>
            <td>Стоимость проезда сентябрь</td><td><?=$arDirection["PROPERTY_MONTH_PRICE_9_VALUE"]?></td>
        </tr>

        <tr>
            <td>Стоимость проезда октябрь</td><td><?=$arDirection["PROPERTY_MONTH_PRICE_10_VALUE"]?></td>
        </tr>

        <tr>
            <td>Стоимость проезда ноябрь</td><td><?=$arDirection["PROPERTY_MONTH_PRICE_11_VALUE"]?></td>
        </tr>

        <tr>
            <td>Стоимость проезда декабрь</td><td><?=$arDirection["PROPERTY_MONTH_PRICE_12_VALUE"]?></td>
        </tr>

        <?}?>






    <tr class="item_unactive">
        <td colspan="2" ><b>ГОСТИНИЦА</b></td>
    </tr>

    <?
        //получаем гостиницу
        $hotel = CIBlockElement::GetList(array(), array("ID"=>$arTour["PROPERTY_HOTEL_VALUE"]), false, false, array("PROPERTY_COST_UTILITIES_SERVICE"));
        $arHotel = $hotel->Fetch();
    ?>
    <tr>
        <td>Стоимость комунальных услуг</td><td><?=$arHotel["PROPERTY_COST_UTILITIES_SERVICE_VALUE"]?></td>
    </tr> 

    <tr class="item_unactive">
        <td colspan="2" ><b>ТУРИСТ</b></td> 
    </tr> 

    <tr>
        <td>Возраст (на момент начала тура)</td><td><?=getFullAgeByDate($_GET["birthday"],$arTour["PROPERTY_DATE_FROM_VALUE"])?></td>
    </tr>

    <tr class="item_unactive">
        <td colspan="2" ><b>СКИДКИ</b></td> 
    </tr> 

    <?


        //получаем механизмы для текущего направления и возраста
        $mathArFilter = array(
            "IBLOCK_CODE"=>"MATH",
            "PROPERTY_COMPANY"=>getCurrentCompanyId(), 
            // ">=PROPERTY_MATH_AGE_TO_VALUE"=>$FULL_AGE, 
            // "<=PROPERTY_MATH_AGE_FROM_VALUE"=>$FULL_AGE,
            "PROPERTY_DIRECTION"=> $arTour["PROPERTY_DIRECTION_VALUE"]
        );

        $mathArSelect = array(
            "PROPERTY_MATH_AGE_TO",
            "PROPERTY_MATH_AGE_FROM",
            "PROPERTY_MATH_TOUR",
            "PROPERTY_MATH_TOUR_DISCOUNT",
            "PROPERTY_MATH_ROAD",
            "PROPERTY_MATH_ROAD_DISCOUNT",
            "PROPERTY_DIRECTION",
            "NAME"
        );
        //подходящие под возраст схемы расчета
        $mathMethods = array(); 

        $math = CIBLockElement::GetList(array(), $mathArFilter, false, false, $mathArSelect);  
        while($arMath = $math->Fetch()) {
            //в конечный массив с методами расчета добавляем только те, которые подходят по возрасту текущего пассажира
            if ($arMath["PROPERTY_MATH_AGE_TO_VALUE"] >= getFullAgeByDate($_GET["birthday"],$arTour["PROPERTY_DATE_FROM_VALUE"]) && $arMath["PROPERTY_MATH_AGE_FROM_VALUE"] <= getFullAgeByDate($_GET["birthday"],$arTour["PROPERTY_DATE_FROM_VALUE"])) {
                $mathMethods[] = $arMath; 
            }
        }
        //arshow($mathMethods);
    ?>

    <?
        $tourPrice = CIBlockPropertyEnum::GetList(array(),Array("ID"=>$mathMethods[0]["PROPERTY_MATH_TOUR_ENUM_ID"]));

            if ($arTourPrice = $tourPrice->Fetch()) {
                //проверяем, что выбрано в первом поле - стоимость тура, или комунальные платежи
                switch($arTourPrice["XML_ID"]) {
                    //стоимость тура
                    case "TOUR_PRICE" : 
                        if ($extraPLACE == "Y") {
                            $TOUR_PRICE = $arTour["PROPERTY_PRICE_ADDITIONAL_SEATS_VALUE"] - getDiscountValue($arTour["PROPERTY_PRICE_ADDITIONAL_SEATS_VALUE"],$arTour["PROPERTY_DISCONT_VALUE"],"P"); 
                        }
                        else {                     
                            $TOUR_PRICE = $arTour["PROPERTY_PRICE_VALUE"] - getDiscountValue($arTour["PROPERTY_PRICE_VALUE"],$arTour["PROPERTY_DISCONT_VALUE"],"P");  
                        }    

                        ; break;      
                        //комунальные платежи
                    case "PAYMENTS" : 
                        //получаем инфо о коммунальных платежах
                        $hotel = CIBlockElement::GetList(array(), array("ID"=>$arTour["PROPERTY_HOTEL_VALUE"]), false, false, array("PROPERTY_COST_UTILITIES_SERVICE"));
                        $arHotel = $hotel->Fetch();
                        $TOUR_PRICE = $arHotel["PROPERTY_COST_UTILITIES_SERVICE_VALUE"];
                        ; break;
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



            ///////////////////2. стоимость проезда///////////////////
            $roadPrice = CIBlockPropertyEnum::GetList(array(),Array("ID"=>$mathMethods[0]["PROPERTY_MATH_ROAD_ENUM_ID"]));

            if ($arTourPrice = $roadPrice->Fetch()) {
                //проверяем, что выбрано в первом поле - только проезд, только проживание или стоимость в туре 
                //получаем направление и его параметры
                $directionArSelect = array(
                    "PROPERTY_ROAD_PRICE",
                    "PROPERTY_ONLY_ROOM_ROAD_PRICE",
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

                $direction = CIBlockElement::GetLIst(array(), array("ID"=>$arTour["PROPERTY_DIRECTION_VALUE"]), false, false, $directionArSelect);
                $arDirection = $direction->Fetch();
                //arshow($arDirection);

                switch($arTourPrice["XML_ID"]) {
                    //только проезд
                    case "ROAD_PRICE": 

                        //если стоимость проезда задана помесячно, то берем ее за нужный месяц
                        if ($arDirection["PROPERTY_ROAD_PRICE_BY_MONTH_VALUE"] == "Да") {
                            //парсим дату тура, чтобы получить месяц
                            $tour_date = explode(".",$arTour["PROPERTY_DATE_FROM_VALUE"]);
                            $month = intval($tour_date[1]);
                            $ROAD_PRICE = $arDirection["PROPERTY_MONTH_PRICE_".$month."_VALUE"]; 
                        }

                        else {
                            $ROAD_PRICE = $arDirection["PROPERTY_ROAD_PRICE_VALUE"]; //стоимость берется из основной тсоимости тура    
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
                }

            } 
            else {
                $ROAD_PRICE = 0;  //первое слагаемое - стоимость тура
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

            //для только проживания стоимость проезда не учитывается
            if ($bookingTYPE == "ONLY_ROOM") {
                $ROAD_PRICE = 0; 
                $ROAD_DISCOUNT = 0; 
            }
    ?>

    <tr>
        <td>Скидка на тур</td><td><?=$TOUR_DISCOUNT?> <?if ($TOUR_DISCOUNT){?>руб<?}?></td>
    </tr>       

    <tr>
        <td>Скидка на проезд</td><td><?=$ROAD_DISCOUNT?> <?if ($ROAD_DISCOUNT){?>руб<?}?></td>
    </tr>









    <tr>
        <td><b>ИТОГО</b></td><td><?echo getTourPrice($_GET["tour"],$_GET["type"],$_GET["extra_place"],$_GET["birthday"],"N");?></td>
    </tr>

</table>



 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>