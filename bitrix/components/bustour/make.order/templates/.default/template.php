<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
    //arshow($_POST);
?>
<?if (!empty($arResult["ERRORS"])):?>
    <div class="alert alert-danger">
        <?= implode("<br />", $arResult["ERRORS"]);?>
    </div>
    <?endif?>
<form name="ORDER_MAKE" action="<?=POST_FORM_ACTION_URI?>" method="post" id="ORDER_MAKE" onsubmit="">
    <input type="hidden" name="ORDER_MAKE" value="1">
    <?=bitrix_sessid_post()?>      

    <script> 
        //защита от долгого простоя при оформлении заказа
        function mainRedirect() {                  
            setTimeout(function(){alert("Время оформления заказа истекло. Вы будете перенаправлены на главную страницу");document.location.href="/"},900000);
        }

        $(function(){
            //для защиты от дурака. устанавливает блокировку на двойной сабмит формы
            submit = "N";

        })


        function getSecondBus() {
            if ($("#secondBus").val() != 0) { 
                var busScheme = $("#secondBus > option:selected").attr("rel");
                //alert(busScheme);
                $.post("/ajax/getScheme.php",{scheme:busScheme}, function(data){
                    $(".busTable_1").html(data);  
                }); 
            }

            else {
                $(".busTable_1").html(""); 
            }

        }


        //пересчет всех стоимостей
        function allBlur() {
            //если есть поля ввода, делаем расчет цен     
            $(".data-table .birthday input").each(function(){
                getTourPrice(this);   
            })              
        }


        //проверка заполнения полей формы 
        function formCheck(){      

            $(".making_order").show();

            // allBlur();  

            var res = "Y";

            //если тип бронирования не "только проживание", то проверяем количество выбранных мест на первом шаге
            <?if ($arResult["TYPE_BOOKING"] != "ONLY_ROOM" && $arResult["STEP"] == 1){?>
                if (parseInt($("#selectedPlacesCount").val()) == 0) {
                    alert("Вы не выбрали ни одного места!");
                    $(".making_order").hide();
                    res = "N"; 
                    return false;                                                                            
                }
                <?}?>

            //проверяем дату
            $(".birthday input").each(function(){
                var patt = /^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i;
                if(!patt.test($(this).val())) {
                    res = "N";   
                }
            })

            if (res == "N") {
                alert("Неверный формат даты!");
                $(".making_order").hide();
                return false;
            }  

            $("#tourists").find("input[type=text]").each(function(){
                if ($(this).val() == "") {
                    res = "N";   
                } 
            });  
            if (res == "N") {
                alert("Заполните все поля!");
                $(".making_order").hide();
                return false;
            } 
            //при двойном туре количество отмеченных мест в обоих схемах должно быть одинаково
            else if ($(".bus_1").find(".passangersNumber > input").length != $(".bus_2").find(".passangersNumber > input").length) {
                alert("Выберите одинаковое количество мест на схемах!");
                $(".making_order").hide();
                return false;
            }

            else {                 

                //защита от двойного нажатия сабмита
                if (submit != "Y") {
                    submit = "Y";                       
                    $("#ORDER_MAKE").submit();      
                }
                else {
                    return false;
                }
            }



        }

        //получаем методы расчета
        function getMathMethods(e) {                                          

            var date_from = $("#tour_date_from").val();
            var direction = $("#tour_direction").val(); 
            var hotel = $("#tour_hotel").val(); 
            var type = $("#tour_type").val();
            var birthday = $(e).val();


            if (!hotel) {hotel = "N";}

            //  alert(direction + " "+  date_from + " " + hotel + " " + birthday + " " + type )
            if (direction && date_from && birthday && type) {
                $.post('/ajax/getMathMethods.php', {
                    date_from : date_from,
                    direction : direction,
                    birthday : birthday,
                    type: type,
                    hotel: hotel
                    }, function(data) {  
                        $(e).parents("tr.tourist").find(".mathMethod").html(data);
                        getTourPrice(e);
                });  
            }             
        }


        //функция расчета стоимости тура
        function getTourPrice(e) { 
            var tourist = $(e).parents("tr.tourist");
            $(tourist).find(".tourist_price").val("расчет...")
            var id = '<?=$arResult["ID"]?>';
            var type = '<?=$arResult["TYPE_BOOKING"]?>';
            var extraplace;
            var extraplaceChecker = $(tourist).find(".add_place_checker").attr("checked");
            if (extraplaceChecker == "checked") {
                extraplace = "Y"; 
            }
            else {
                extraplace = "N";
            }
            var birthday = $(tourist).find(".birthday input[type=text]").val();  

            var method = $(tourist).find(".mathMethod").val();    



            //проверяем, не отмечен ли флаг "без скидки"
            if (method == "N") {
                //если отмечен, то для расчета используем другую дату рождения
                birthday = "01.01.9999";  //в таком случае методы расчета не будут получены
            }



            var v = $(tourist).find(".tourist_price");
            var h = $(tourist).find(".tour_price");


            if (id && type && extraplace && birthday) {
                $.post('/ajax/getTourPrice.php', {
                    id : id,
                    type : type,
                    extraplace : extraplace,
                    birthday : birthday,
                    method: method
                    }, function(data) {                             

                        $(v).val(data); //это видимое значение
                        $(h).val(data); //это скрытое, используем для расчетов    

                        calc_tour_price(); 

                });  
            }                      

        }


        //выбор мест   
        function setPlaces(){ 

            var i = 1;

            $(".bookingBusSchemeTop").each(function() {

                var input = '';                  
                var second_bus = "";

                //для второго автобуса вводим дополнительный поддмассив для мест, чтобы отличить их на выходе от основных  
                if (i == 1) { 
                    places = "Places"  
                }
                else {
                    places = "SecondPlaces"
                }



                $(this).find('.specialPlace').each(function(){
                    input += '<input name="' + places +'['+$(this).find('input').attr('name')+']" type="hidden" value="PP">';
                });
                $(this).find('.passangersNumber').html(input);

                $("#selectedPlacesCount").val($(".bookingBusSchemeTop:eq(0)").find(".specialPlace").length);

                i++;
            })



        }


        //подсчет общей стоимости тура
        function calc_tour_price(){  

            var all_summ = 0;       

            $(".tourist").each(function(){

                var tourist_tour_price = 0;
                var tourist_services_price = 0;

                var tour_price = $(this).find(".tour_price").val(); //стоимость тура без учета доп услуг


                //перебираем доп услуги для текущего туриста  
                $(this).find(".service_checker").each(function() {
                    if ($(this).attr("checked") == "checked") {
                        tourist_services_price = tourist_services_price*1 + $("#service_" + $(this).val()+ "_price").val()*1;  
                    }
                })

                //общая стоимость тура для туриста с учетом доп услуг
                tourist_tour_price =  tour_price*1 + tourist_services_price*1;

                $(this).find(".tourist_price").val(tourist_tour_price);

                all_summ = all_summ*1 + tourist_tour_price*1;      

            });

            //последним пунктом плюсуем стоимость трансфера

            var transferPrice = $("#departureCity > option:selected").attr("rel");
            if (transferPrice > 0 ) {
                all_summ = all_summ*1 + transferPrice*$(".tourist").length;
            }

            $("#all_summ").val(all_summ); 
        }




        $(function(){ 

            //каждый раз при изменении ввода пересчитываем стоимость
            $("#ORDER_MAKE input[type=checkbox]").change(function() {
                var e = $(this);                
                getTourPrice(e);               
            })

            //пересчет цены при вводе даты рождения
            $('body').on('keyup', '.birthday input', function(){
                var e = $(this);
                var patt = /^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/i;
                if(patt.test(e.val())) {                     
                    getMathMethods(e);   
                }
            });       


            <?if ($arResult["TYPE_BOOKING"] == "STANDART" || $arResult["TYPE_BOOKING"] == "DOUBLE_TOUR" || $arResult["TYPE_BOOKING"] == "ONLY_ROOM"){?>

                //клики по чекбоксам "доп место". выбран может быть только 1
                $(".add_place_checker").live("click",function(){
                    if ($(this).attr("checked") == "checked") {
                        $(".add_place_checker:not(:checked)").attr("disabled","disabled");

                        // getTourPrice();

                    }
                    else if ($(this).attr("disabled") != "disabled" && $(this).attr("checked") != "checked") {
                        $(".add_place_checker").removeAttr("disabled");

                        //  getTourPrice();
                    }      

                    //   $(this).parent().parent("tr").find(".service_checker").removeAttr("checked");

                })

                <?}?>


            //выбрать место
            $(".placeFP").live("click",function(){

                <?if ($arResult["TYPE_BOOKING"] == "STANDART" || $arResult["TYPE_BOOKING"] == "DOUBLE_TOUR"){?>



                    //проверяем количество выбранных мест
                    if ($(this).parents(".bookingBusSchemeTop").find(".specialPlace").length >= $("#maxPlaces").val()) {

                        //    alert("Выбранное количество мест превышает вместимость номера!");                
                        //    return false;



                        ////////убрать позже///////
                        $(this).removeClass("placeFP");
                        $(this).addClass("placePP");
                        $(this).addClass("specialPlace");                
                        $(this).find("input").val("PP");                  

                        //записываем количество выбранных мест  
                        setPlaces();
                        ////////убрать позже///////
                    }                 

                    else {  
                        if ($(this).parents(".bookingBusSchemeTop").find(".specialPlace").length >= $("#roomPlaces").val()) {
                            //    alert("Выбранное количество мест превышает вместимость номера! В номере будет использовано ДОПОЛНИТЕЛЬНОЕ МЕСТО");
                        }

                        $(this).removeClass("placeFP");
                        $(this).addClass("placePP");
                        $(this).addClass("specialPlace");                
                        $(this).find("input").val("PP");                  

                        //записываем количество выбранных мест  
                        setPlaces();

                    }
                    <?} else {?>

                    //проверяем количество выбранных мест 
                    $(this).removeClass("placeFP");
                    $(this).addClass("placePP");
                    $(this).addClass("specialPlace");                
                    $(this).find("input").val("PP");                  

                    //записываем количество выбранных мест 
                    setPlaces();


                    <?}?>

            })  

            //отменить выбор
            $(".placePP").live("click",function(){  
                if ($(this).hasClass("specialPlace")) {
                    $(this).removeClass("placePP");
                    $(this).removeClass("specialPlace"); 
                    $(this).addClass("placeFP");             
                    $(this).find("input").val("FP");

                    setPlaces();   
                }      

            })   



            <?if ($arResult["STEP"] == 2){?>
                //освобождение мест и номеров при переходе по любой ссылке в момент бронирования
                $("a").click(function(e){
                    var link = $(this);   
                    if (!$(this).hasClass("dropdown-toggle")) {
                        e.preventDefault();
                        if (confirm("Все данные будут удалены. Продолжить?")) {
                            //удаляем записи о блокировке
                            $.post("/ajax/removeLock.php", {places: '<?=implode(";",$arResult["CUR_PLASES"])?>', room: '<?=$arResult["ID"]?>'},
                                function(data){
                                    document.location.href=$(link).attr("href");

                                }
                            )   
                        }
                        else {}
                    }

                })
                <?}?>

        })


        //добавляет строку в таблицу с туристами
        function addTourist() {
            //вычисляем текущее количество строк с туристами
            var tourust_count = $(".tourist").length;

            var room_places = $("#roomPlaces").val();  //количество основных мест в номере
            var total_places =  $("#maxPlaces").val();

            if (tourust_count == room_places) {
                //   alert("Внимание! Количество мест превышает вместительность номера. В номере будет использовано ДОПОЛНИТЕЛЬНОЕ МЕСТО!");
            }

            else if (tourust_count > room_places) {
                //   alert("Выбрано максимальное количество мест!");
                //   return false; 
            }

            //подгружаем доп ряд
            $.post('/ajax/addTourist.php', {count:tourust_count}, 
                function(data) {                             
                    $("#tourists").append(data);
            });   


        }

        //удалить туриста
        function removeTourist(t){ 
            //если у удаляемого туриста стоит флаг "доп место", то нужно разлочить чекбоксы остальных
            if ($(t).parents("tr.tourist").find(".add_place_checker").attr("checked") == "checked") {
                $(".add_place_checker").removeAttr("disabled");  
            }
            $(t).parents("tr.tourist").remove();
        }


    </script>

    <?if ($arResult["STEP"] == 1){?>
        <?//arshow($arResult);?>

        <h2>        
            Шаг 1. Выберите места в 
            <?if ($arResult["TYPE_BOOKING"] != "DOUBLE_TOUR"){?>
                автобусе
                <?} else{?>
                автобусах
                <?}?>
        </h2>     


        <div class="booking">
            <input name="Bus_scheme" id="Bus_scheme" value="" type="hidden">
            <input type="hidden" value="<?=$arResult["ROOM"]["PLACES"]?>" name="roomPlaces" id="roomPlaces">  
            <input type="hidden" value="<?=$arResult["ROOM"]["TOTAL_PLACES_COUNT"]?>" name="maxPlaces" id="maxPlaces">


            <input name="selectedPlacesCount" type="hidden" value="" id="selectedPlacesCount">


            <?if ($arResult["TYPE_BOOKING"] == "DOUBLE_TOUR"){?>
                <h3>Автобус "ТУДА" (<?=$arResult["DATE_FROM"]?> из города отправления)</h3>
                <div class="bookingBusSchemeTop bus_1">
                    <div class="bookingBusScheme">
                        <div class="twoBus">                        
                            <div class="busTable_0">
                                <?get_bus_scheme($arResult["BUS_SCHEME_VIEW"]); ?>                          
                            </div>   
                        </div>       
                        <div class="sendBookingInfo">  
                            <div class="passangersNumber">&nbsp;</div>
                        </div>
                    </div>
                    <div class="notifyBooking">
                        <b>Уважаемые коллеги</b>, просим корректно рассаживать туристов, не оставляя по одному свободному месту перед и после своих туристов.
                        ТК <b>"<?=getCurrentCompanyName();?>"</b> оставляет за собой право пересаживать туристов в случае объективной необходимости.
                    </div> 
                </div>

                <h3>Автобус "ОБРАТНО" (прибывает <?=$arResult["SECOND_TOUR"]["DATE_TO"]?> в город отправления)</h3>
                <div class="bookingBusSchemeTop bus_2">
                    <div class="bookingBusScheme">
                        <div class="twoBus">                        
                            <div class="busTable_1">
                                <?get_bus_scheme($arResult["BUS_BACK"]["SCHEME"]); ?>                          
                            </div>                

                        </div>

                        <div class="sendBookingInfo">  
                            <div class="passangersNumber">&nbsp;</div>
                        </div>
                    </div>

                </div>


                <?} else { //для всех остальных типов бронирования?>
                <div class="bookingBusSchemeTop <?if ($arResult["TYPE_BOOKING"] == "DOUBLE_ROAD"){?>bus_1 <?}?>">
                    <div class="bookingBusScheme">
                        <div class="twoBus">                        
                            <div class="busTable_0">
                                <?get_bus_scheme($arResult["BUS_SCHEME_VIEW"]); ?>                          
                            </div>  
                        </div>      

                        <div class="sendBookingInfo">  
                            <div class="passangersNumber">&nbsp;</div>
                        </div>
                    </div>
                    <div class="notifyBooking">
                        <b>Уважаемые коллеги</b>, просим корректно рассаживать туристов, не оставляя по одному свободному месту перед и после своих туристов.
                        Туроператор оставляет за собой право пересаживать туристов в случае объективной необходимости.
                    </div> 
                </div>


                <?
                    //только проезд туда и обратно
                    if ($arResult["TYPE_BOOKING"] == "DOUBLE_ROAD"){?>
                    <h3>Автобус возвращения в начальный пункт</h3>
                    <select onchange="getSecondBus()" id="secondBus" name="secondBus">
                        <option value="0">Выберите автобус</option>
                        <?foreach ($arResult["BUSES"] as $bus) {?>
                            <option value="<?=$bus["ID"]?>" rel='<?=$bus["SCHEME"]?>'><?=$bus["NAME"]?></option>   
                            <?}?>  
                    </select>
                    <br>
                    <div class="bookingBusSchemeTop bus_2">
                        <div class="bookingBusScheme">
                            <div class="twoBus">                        
                                <div class="busTable_1">

                                </div>                

                            </div>

                            <div class="sendBookingInfo">  
                                <div class="passangersNumber">&nbsp;</div>
                            </div>
                        </div>

                    </div>
                    <?}?>


                <?}?>

        </div>



        <?}?>

    <?if ($arResult["STEP"] == 2){?>
        <?//arshow($arResult);?>

        <h2>Шаг 2. Заполните данные о туристах</h2> 
        <input type="hidden" id="default_price" value="<?=$arResult["PRICE"]?>">
        <input type="hidden" value="<?=$arResult["ROOM"]["PLACES"]?>" name="roomPlaces" id="roomPlaces">  
        <input type="hidden" value="<?=$arResult["ROOM"]["TOTAL_PLACES_COUNT"]?>" name="maxPlaces" id="maxPlaces">
        <input type="hidden" value="<?=$arResult["DATE_FROM"];?>" id="tour_date_from">
        <input type="hidden" value="<?=$arResult["DIRECTION"]["ID"]?>" id="tour_direction">
        <input type="hidden" value="<?=$arResult["HOTEL"]["ID"]?>" id="tour_hotel">
        <input type="hidden" value="<?=$arResult["TYPE_BOOKING"]?>" id="tour_type">
        <input type="hidden" value="<?=$arResult["BUS_SECOND"]["ID"]?>" name="busSecond"> 

        <?//выведем текущие места
            foreach ($_POST["Places"] as $id=>$place){?>
            <input type="hidden" name="Places[<?=$id?>]" value="<?=$place?>">    
            <?}?>

        <table class="data-table" id="tourists">
            <tr>
                <th>ФИО</th>
                <th>Паспорт</th>
                <th>Телефон</th>
                <th>Дата рождения</th>
                <?if ($arResult["TYPE_BOOKING"] == "STANDART" || $arResult["TYPE_BOOKING"] == "ONLY_ROOM" || $arResult["TYPE_BOOKING"] == "DOUBLE_TOUR"){?>
                    <th>
                        Доп. место
                        <input type="hidden" id="default_price_additional" value="<?=$arResult["PRICE_ADDITIONAL_SEATS"]?>">
                    </th>
                    <?}?>

                <?//собираем доп услуги
                    if (is_array($arResult["SERVICES"]) && count($arResult["SERVICES"]) > 0) {  ?>
                    <?  foreach($arResult["SERVICES"] as $service) { ?>   
                        <th>
                            <?=$service["NAME"]?> (<?=$service["PRICE"]?> р.)
                            <input type="hidden" id="service_<?=$service["ID"]?>_price" value="<?=$service["PRICE"]?>">
                        </th>
                        <?}?>
                    <?}?>


                <th>Метод расчета</th>

                <th>Цена*</th>
            </tr>
            <?
                //выводим поля для заполнения данных о туристах
                for ($i = 1; $i <= $arResult["PLACES_COUNT"];$i++) {?>
                <tr class="tourist">
                    <td><input type="text" class="data" value="" name="Tourist[<?=$i?>][name]">
                        <input type="hidden" value="<?=$arResult["CUR_PLASES"][$i-1]?>" name="Tourist[<?=$i?>][place]">
                        <?if (is_array($arResult["CUR_SECOND_PLASES"]) && count($arResult["CUR_SECOND_PLASES"]) > 0){?>
                            <input type="hidden" value="<?=$arResult["CUR_SECOND_PLASES"][$i-1]?>" name="Tourist[<?=$i?>][secondPlace]">
                            <?}?>
                    </td>
                    <td><input type="text" class="data" value="" name="Tourist[<?=$i?>][passport]"></td>
                    <td><input type="text" class="data" value="" name="Tourist[<?=$i?>][phone]"></td>
                    <td class="birthday">
                        <?$APPLICATION->IncludeComponent(
                                "bitrix:main.calendar",
                                "order",
                                Array(
                                    "SHOW_INPUT" => "Y",
                                    "FORM_NAME" => "ORDER_MAKE",
                                    "INPUT_NAME" => "Tourist[".$i."][birthday]",
                                    "INPUT_NAME_FINISH" => "",
                                    "INPUT_VALUE" => "",
                                    "INPUT_VALUE_FINISH" => "",
                                    "SHOW_TIME" => "N",
                                    "HIDE_TIMEBAR" => "Y"
                                ),
                                false
                            );?>


                    </td>

                    <?if ($arResult["TYPE_BOOKING"] == "STANDART" || $arResult["TYPE_BOOKING"] == "ONLY_ROOM" || $arResult["TYPE_BOOKING"] == "DOUBLE_TOUR"){?>

                        <td align="center">
                            <input type="checkbox" value="Y" class="add_place_checker" name="Tourist[<?=$i?>][add]" <?if (/*$arResult["ROOM"]["PLACES"] >= $arResult["PLACES_COUNT"]*/ !$arResult["PRICE_ADDITIONAL_SEATS"]){?>disabled="disabled"<?}?> >
                        </td>
                        <?}?>

                    <?//собираем доп услуги
                        if (is_array($arResult["SERVICES"]) && count($arResult["SERVICES"]) > 0) {?>
                        <?foreach ($arResult["SERVICES"] as $service) {?>   
                            <td align="center">
                                <input class="service_checker" type="checkbox" value="<?=$service["ID"]?>" name="Tourist[<?=$i?>][services][]" >
                            </td>
                            <?}?>
                        <?}?>

                    <td align="center"> 
                        <select name="Tourist[<?=$i?>][math]" class="mathMethod" onchange="getTourPrice(this);">
                            <option value="0">Без скидки</option>
                        </select>    
                        <?/* <input type="checkbox" name="use_full_price" class="use_full_price" value="Y" > */?>
                    </td>

                    <td>
                        <input type="text" value="<?=$arResult["PRICE"]?>" name="Tourist[<?=$i?>][price]" class="tourist_price" readonly="readonly">
                        <input type="hidden" class="tour_price" value="" name="Tourist[<?=$i?>][tour_price]">
                    </td>

                </tr>
                <?}?>            

        </table>
        <?if ( $arResult["TYPE_BOOKING"] == "ONLY_ROOM"){?>
            <span href="javascript:void(0)" onclick="addTourist()" class="tourist_add">Добавить пассажира (+)</span><br>
            <?}?>

        <br>
        *Для того, чтобы увидеть конечную стоимость тура для туриста, заполните дату рождения (формат даты рождения: дд.мм.гггг)
        <br>
        <br>
        <table class="data-table">

            <?if ($arResult["TYPE_BOOKING"] != "ONLY_ROOM"){?>
                <tr>
                    <td>Город забора туристов:</td>
                    <td>
                        <select name="departureCity" id="departureCity" onchange="calc_tour_price()">
                            <option value="" rel="0">-</option>
                            <?foreach ($arResult["DEPARTURE_CITY"] as $cityID=>$cityName){
                                    //получаем стоимость трансфера для города забора
                                    $transferPrice = getTransferPrice($cityID);?>
                                <option value="<?=$cityID?>" rel="<?=$transferPrice?>">
                                    <?=$cityName?>
                                    <?if ($transferPrice > 0){?>
                                        (<?=$transferPrice?> руб.)
                                        <?}?>
                                </option>
                                <?}?>
                        </select>
                    </td>
                </tr>
                <?}?>

            <tr>
                <td>Итого:</td>
                <?
                    //проверяем является ли пользователь туроператором
                    $userGroups = CUser::GetUserGroup($USER->GetId());                   
                ?>
                <td><input type="text" value="" id="all_summ" name="all_summ" <?if (!in_array(6,$userGroups)){?>readonly="readonly"<?}?>></td>
            </tr>

            <tr>
                <td>Примечание:</td>
                <td><textarea cols="" rows="" name="notes" class="order_notes_area"></textarea></td>
            </tr>
        </table>
        <br>
        <script>
            $(function(){
                mainRedirect();
            })
        </script>
        <?}?>

    <?if ($arResult["STEP"] == 3){?> 
        Ваш заказ оформлен. номер заказа: <?=$arResult["ORDER_ID"]?>
        <br>
        <?}?> 




    <input type="hidden" name="STEP" value="<?=$arResult["STEP"] + 1;?>">
    <?//if ($arResult["TYPE_BOOKING"] != "DOUBLE_TOUR"){?>
    <?if ($arResult["STEP"] < 3 ){?>
        <input type="button" class="btn btn-primary" name="BNEXT" value="Далее" onclick="formCheck();">
        <span class="making_order" style="display: none;">Подождите...</span>
        <?if ($arResult["STEP"] == 2){?> 
            <br><br><br>
            <a class="save_button" href="/">ОТМЕНА</a>
            <?}?>   
        <?} else{?>
        <a href="/order-management/order/">перейти к заказам</a>
        <?header("location:  /order-management/order/ ");?>
        <?}?>
    <?//}?>
</form>

