$(function(){
    
    //определяем тип бронирования
    var typeBooking = "standart"; //$('.sendBookingInfo input[name="Tour[booking][typeBooking]"]').val();
    //количество туров (если больше 1, то это двойной тур)
    var countTours = 1; //$('.sendBookingInfo input[name="Tour[booking][tour]"]').length;
    //количество автобусов
    var countTables = $(".bookingBusScheme .busTable").length;
    if(countTours > 1){
        var countDoubleTourTables = [];    
        countDoubleTourTables['departure'] = $(".departureTables .busTable").length;
        countDoubleTourTables['arrival'] = $(".arrivalTables .busTable").length;
    }
    //тип пользователя, осуществляющего бронь
    
    var userGroup =  9; //groupUser;
    
    //тип тура (тип тура(для двойных): отправление или прибытие)
    var tourType = '';
    
    //получаем массив со схемами мест автобусов
    var tables = [];
    $(".bookingBusScheme .busTable").each(function(i){
        tables[i] = $(this);
    });
    
    //двойной тур
    if(countTours > 1){
        //устанавливаем максимальное количество мест для отметки
        var maxPlaces = [];
        //устанавливаем количество отмеченных мест.
        var countPlaces = [];
        countPlaces['departure'] = 0;
        countPlaces['arrival'] = 0;
        //количество свободных мест (для схем с двумя автобусами)
        var freePlaces = [];
        freePlaces['departure'] = 0;
        freePlaces['arrival'] = 0;
    }
    //обычный тур
    else{
        //устанавливаем максимальное количество мест для отметки
        var maxPlaces = 0;
        //устанавливаем количество отмеченных мест.
        var countPlaces = 0;
        //количество свободных мест (для схем с двумя автобусами)
        var freePlaces = $($(".bookingBusScheme .busTable")[0]).find('div.placeFP').length;
    }
    
    var table_0 = '', table_1 = '';
    
    //считаем количество свободных мест в автобусе
    var free = function(obj){
        
        if($(".bookingBusScheme .busTable").length > 1){
            
            if(countTours > 1){
                var busTable = $(".departureTables .busTable");
                
                if($(obj).parents('table').parent().parent().attr('class') == 'arrivalTables'){
                    busTable = $(obj).parents('table').parent().parent().find('table');
                }
                if($(busTable).find('div.placeFP').length>0){
                    freePlaces[tourType] = $($(busTable)[0]).find('div.placeFP').length;
                }
                else{
                    freePlaces[tourType] = 0;
                }
            }
            else{
                if($($(".bookingBusScheme .busTable")[0]).find('div.placeFP').length>0){
                    freePlaces = $($(".bookingBusScheme .busTable")[0]).find('div.placeFP').length;
                }
                else{
                    freePlaces = 0;
                }                
            }
        }
    };
    
    //устанавливаем дефолтный курсор по умолчанию для всех мест второй схемы автобуса
    $(table_1).find('div.placeFP.placeBooking').css('cursor', 'default');
    
    //Определяем заполнен ли первый автобус. Если да, то приступаем  к заполнению второго.
    var freeBus = function(obj){
        
        table_0 = tables[0];
        table_1 = tables[1];
        if(countTours > 1){
            
            countPlacesBus = countPlaces[tourType];
            freePlacesBus = freePlaces[tourType];
            
            if($(obj).parents('table').parent().parent().attr('class') == 'arrivalTables'){    
                if(countDoubleTourTables['departure'] > 1){
                    table_0 = tables[2];
                    table_1 = tables[3];
                }
                else if(countDoubleTourTables['arrival'] > 1){
                    table_0 = tables[1];
                    table_1 = tables[2];
                }
                else {
                    table_0 = tables[1];
                    table_1 = '';
                }
            }
            else{
                if(countDoubleTourTables['departure'] > 1){
                    table_0 = tables[0];
                    table_1 = tables[1];
                }
                else {
                    table_0 = tables[0];
                    table_1 = '';
                }
            }
            
        }
        else{
            countPlacesBus = countPlaces;
            freePlacesBus = freePlaces;
            table_1 = tables[1];
        }
        
        //если автобус один, то ничего не делаем
        if(countTables == 1){}
        else if(freePlacesBus < 1){
            //назначаем на места второй схемы автобуса обработчики событий
            (tables.length>1) && $(table_1).find('div').unbind('click');
            (tables.length>1) && selectPlaces($(table_1).find('div'));
            //устанавливаем для всех div мест cursor pointer
            $(table_1).find('div.placeFP.placeBooking').css('cursor', 'pointer');
        }
        //если снимаем отметку с одного из мест в незаполненно автобусе, то заполнение мест во втором автобусе становится недоступно и пропадают все отмеченные места
        else{
            //считаем количество отмеченных мест в первом автобусе
            var countWhenDelete = 0;
            //новые input, отвечающие за идентификацию выбранных мест на сервере
            var input = '';
            //удаляем старые обработчики событий
            (tables.length>1) && $(table_1).find('div').unbind('click');
            //восстанавливаем исходные значения
            $(table_1).find('div').each(function(){
                if($(this).attr('class') == 'placePP specialPlace'){
                    $(this).attr('class', 'placeFP');
                    $(this).find('input').val('FP');
                }
            });
            
            //считаем количество отмеченных мест в первом автобусе
            $(table_0).find('div.placePP.specialPlace').each(function(){
                countWhenDelete++;
            });
                        
            //если количество отмеченных мест в первом автобусе меньше, чем в countPlacesBus, то пересоздаем input, отвечающие за идентификацию выбранных мест на сервере
            if(countWhenDelete < countPlacesBus){
                
                if(countTours > 1){
                    table_0 = $('.departureTables .busTable_0, .departureTables .busTable_1, .arrivalTables .busTable_0, .arrivalTables .busTable_1');
                }
                
                $(table_0).find('div.placePP.specialPlace').each(function(){
                    input += '<input name="Tour[booking][bus_'+$(this).parents('.busTable').attr('id')+'][places]['+$(this).find('input').attr('name')+']" type="hidden" value="PP">';
                });
                $('.passangersNumber').html(input);
            }
            //устанавливаем для всех div мест cursor default
            $(table_1).find('div').css('cursor', 'default');
            //присваиваем переменной, отвечающей за отмеченные места число мест, указанных в первой схеме автобуса
            
            if(countTours > 1){
                countPlaces[tourType] = countWhenDelete;
            }
            else{
                countPlaces = countWhenDelete;
            }
            
        }
    };
    
    var selectPlaces = function(obj){
        //отмечаем на схеме автобуса места для бронирования
        obj.click(function(){
            var currClass = $(this).attr("class");
            var currInput = $(this).find("input");
            var currName = $(currInput).attr("value");
            var currInputName = $(currInput).attr("name");
            
            //определяем максимально допустимое количество отмеченных мест
            if(countTours > 1){
                //определяем с каким туром мы работаем (для двойных туров)
                if($(this).parents('table').parent().parent().attr('class') == 'arrivalTables'){
                    maxPlaces['arrival'] = $(this).parents('table.busTable').parent().find('input[name="maxPlaces"]').val();
                    tourType = 'arrival';
                }
                else{
                    maxPlaces['departure'] = $(this).parents('table.busTable').parent().find('input[name="maxPlaces"]').val();
                    tourType = 'departure';
                }
                maxPlacesBus = maxPlaces[tourType];
                countPlacesBus = countPlaces[tourType];
            }
            else{
                maxPlaces = $(this).parents('table.busTable').parent().find('input[name="maxPlaces"]').val();
                maxPlacesBus = maxPlaces;
                countPlacesBus = countPlaces;
            }
            
            if(currClass == "rowSign"){
                return false;
            }
            else if(currName == "FP"){
                
                //только для типа бронирования "Только проезд"
                //предполагаем, что в автобусе не может быть больше 20 рядов!
                var match = 0;
                if(typeBooking == 'onlyRoad'){
                    for(var i = 11; i<=20; i++){
                        for(var j = 1; j<=5; j++){
                            if(currInputName == 'r_'+i+'_c_'+j){
                                match++;
                            }
                        }
                    }
                }
                //проверяем условие: для бронирования "только проезд" можно бронировать места, начиная с 11-го ряда
                
                if(match == 0 && typeBooking == 'onlyRoad' && userGroup != 9){
                    $.colorbox({
                        html: '<div class="colorboxError"><p>Выбран тип бронирования "Только проезд". Бронирование мест возможно, начиная с 11-го ряда!</p></div>',
                        close: '<b>закрыть</b>',
                        maxWidth: '70%'
                    });
                }
                else{
                    if(countPlacesBus < maxPlacesBus){
                        $(this).removeClass().addClass("placePP specialPlace");
                        currInput.attr("value", "PP");        
                        var input = '<input name="Tour[booking][bus_'+$(this).parents('table.busTable').attr('id')+'][places]['+currInputName+']" type="hidden" value="PP"/>';            
                        $('.passangersNumber').append(input);
                        //уменьшаем количество доступных для отметки мест
                        
                        if(countTours > 1){
                            countPlaces[tourType]++;
                            if(countPlaces[tourType] == maxPlacesBus){
                                $(this).parents('table.busTable').find('div.placeFP').css('cursor','default');
                            }
                        }
                        else{
                            countPlaces++;
                            if(countPlaces == maxPlacesBus){
                                $(this).parents('table.busTable').find('div.placeFP').css('cursor','default');
                            }
                        }
                    }
                    //только для режима бронирования "бронировать", когда нужно посадить еще одного человека в номер, где мест меньше, чем пассажиров
                    else if(countPlacesBus >= maxPlacesBus && typeBooking == 'standard'){
                        $.colorbox({
                            html: '<div class="colorboxError"><p>Выбрано максимальное число мест, доступное для данного типа номера!</p></div><div class="colorboxNotify"><p>Для продолжения выбора мест нажмите "ок". Заявке будет присвоен статус "Запрос".</p></div>',
                            close: '<b>ок</b>',
                            maxWidth: '70%'
                        });
                        
                        $(this).removeClass().addClass("placePP specialPlace");
                        currInput.attr("value", "PP");        
                        var input = '<input name="Tour[booking][bus_'+$(this).parents('table.busTable').attr('id')+'][places]['+currInputName+']" type="hidden" value="PP"/>';            
                        $('.passangersNumber').append(input);
                        countPlaces++;
                    }
                    else if(countPlacesBus == maxPlacesBus){
                        
                        $.colorbox({
                            html: '<div class="colorboxError"><p>Выбрано максимальное число мест, доступное для данного типа номера!</p></div>',
                            close: '<b>закрыть</b>',
                            maxWidth: '70%'
                        });
                    }
                }
            }
            else if(currName == "PP" && currClass == "placePP specialPlace"){
                var currentDiv = $(this);
                $(this).removeClass().addClass("placeFP");
                $(currInput).attr("value", "FP");
                
                //если жмем второй раз на отмеченное место, то снимаем отметку о бронировании и удаляем input содержащий сведения о месте            
                $('.passangersNumber input').each(function(i){                
                    var name = currentDiv.find('input').attr('name');                            
                    if($(this).attr('name') == 'Tour[booking][bus_'+currentDiv.parents('table.busTable').attr('id')+'][places]['+name+']'){                    
                        $(this).remove();
                    }
                });
                //устанавливаем указатель мышки в положение "pointer"
                currentDiv.parents('table.busTable').find('div.placeFP').css('cursor','pointer');
                //разрешаем отметить еще одно место
                if(countTours > 1){
                    countPlaces[tourType]--;
                }
                else{
                    countPlaces--;
                }
            }
            //вызываем функцию, отвечающую за отображение кнопки "бронировать"
            booking();
            //вызываем функцию подсчета свободных мест в автобусе
            free(obj);
        });
    };
    
    //вызываем функцию, отвечающую за пометку мест для первых автобусов
    
    if(typeBooking != 'onlyRoad'){
        selectPlaces($(tables[0]).find('div'));
    }
    else{
        selectPlaces($(tables[0]).find('div'));
        selectPlaces($(tables[1]).find('div'));
    }
    
    //проверяем на двойной тур (туров 2)
    if(countTours > 1){        
        //если у какого-либо из туров или у обоих 2 автобуса
        if(countDoubleTourTables['departure'] > 1){
            selectPlaces($(tables[2]).find('div'));
        }
        else{
            selectPlaces($(tables[1]).find('div'));
        }
    }
    
    //в зависимости от количества пустых мест в первом автобусе разрешаем или нет доступ к заполнению второго (для "только проезд" разрешаем всегда)
    if(typeBooking != 'onlyRoad'){
        $('.bookingBusScheme .busTable div').click(function(){
            freeBus($(this));
        });
    }
    
    //отображаем или скрываем кнопку "бронировать" в зависимости от отмеченных мест
    var booking = function(){
        if($('.passangersNumber').find('input').length > 0){
            if(countTours > 1){
                if((countPlaces['departure'] != 0) && (countPlaces['departure'] == countPlaces['arrival'])){
                    $('.forwardBooking a').show().css('display', 'block');
                }
                else{
                    $('.forwardBooking a').hide();
                }
            }
            else{
                $('.forwardBooking a').show().css('display', 'block');
            }
        }
        else{
            $('.forwardBooking a').hide();
        }
    };
    
    $('.forwardBooking a').click(function(){        
        $('#booking').submit();
    });
    
});
