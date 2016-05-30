$(function(){
    
    //���������� ��� ������������
    var typeBooking = "standart"; //$('.sendBookingInfo input[name="Tour[booking][typeBooking]"]').val();
    //���������� ����� (���� ������ 1, �� ��� ������� ���)
    var countTours = 1; //$('.sendBookingInfo input[name="Tour[booking][tour]"]').length;
    //���������� ���������
    var countTables = $(".bookingBusScheme .busTable").length;
    if(countTours > 1){
        var countDoubleTourTables = [];    
        countDoubleTourTables['departure'] = $(".departureTables .busTable").length;
        countDoubleTourTables['arrival'] = $(".arrivalTables .busTable").length;
    }
    //��� ������������, ��������������� �����
    
    var userGroup =  9; //groupUser;
    
    //��� ���� (��� ����(��� �������): ����������� ��� ��������)
    var tourType = '';
    
    //�������� ������ �� ������� ���� ���������
    var tables = [];
    $(".bookingBusScheme .busTable").each(function(i){
        tables[i] = $(this);
    });
    
    //������� ���
    if(countTours > 1){
        //������������� ������������ ���������� ���� ��� �������
        var maxPlaces = [];
        //������������� ���������� ���������� ����.
        var countPlaces = [];
        countPlaces['departure'] = 0;
        countPlaces['arrival'] = 0;
        //���������� ��������� ���� (��� ���� � ����� ����������)
        var freePlaces = [];
        freePlaces['departure'] = 0;
        freePlaces['arrival'] = 0;
    }
    //������� ���
    else{
        //������������� ������������ ���������� ���� ��� �������
        var maxPlaces = 0;
        //������������� ���������� ���������� ����.
        var countPlaces = 0;
        //���������� ��������� ���� (��� ���� � ����� ����������)
        var freePlaces = $($(".bookingBusScheme .busTable")[0]).find('div.placeFP').length;
    }
    
    var table_0 = '', table_1 = '';
    
    //������� ���������� ��������� ���� � ��������
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
    
    //������������� ��������� ������ �� ��������� ��� ���� ���� ������ ����� ��������
    $(table_1).find('div.placeFP.placeBooking').css('cursor', 'default');
    
    //���������� �������� �� ������ �������. ���� ��, �� ����������  � ���������� �������.
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
        
        //���� ������� ����, �� ������ �� ������
        if(countTables == 1){}
        else if(freePlacesBus < 1){
            //��������� �� ����� ������ ����� �������� ����������� �������
            (tables.length>1) && $(table_1).find('div').unbind('click');
            (tables.length>1) && selectPlaces($(table_1).find('div'));
            //������������� ��� ���� div ���� cursor pointer
            $(table_1).find('div.placeFP.placeBooking').css('cursor', 'pointer');
        }
        //���� ������� ������� � ������ �� ���� � ������������ ��������, �� ���������� ���� �� ������ �������� ���������� ���������� � ��������� ��� ���������� �����
        else{
            //������� ���������� ���������� ���� � ������ ��������
            var countWhenDelete = 0;
            //����� input, ���������� �� ������������� ��������� ���� �� �������
            var input = '';
            //������� ������ ����������� �������
            (tables.length>1) && $(table_1).find('div').unbind('click');
            //��������������� �������� ��������
            $(table_1).find('div').each(function(){
                if($(this).attr('class') == 'placePP specialPlace'){
                    $(this).attr('class', 'placeFP');
                    $(this).find('input').val('FP');
                }
            });
            
            //������� ���������� ���������� ���� � ������ ��������
            $(table_0).find('div.placePP.specialPlace').each(function(){
                countWhenDelete++;
            });
                        
            //���� ���������� ���������� ���� � ������ �������� ������, ��� � countPlacesBus, �� ����������� input, ���������� �� ������������� ��������� ���� �� �������
            if(countWhenDelete < countPlacesBus){
                
                if(countTours > 1){
                    table_0 = $('.departureTables .busTable_0, .departureTables .busTable_1, .arrivalTables .busTable_0, .arrivalTables .busTable_1');
                }
                
                $(table_0).find('div.placePP.specialPlace').each(function(){
                    input += '<input name="Tour[booking][bus_'+$(this).parents('.busTable').attr('id')+'][places]['+$(this).find('input').attr('name')+']" type="hidden" value="PP">';
                });
                $('.passangersNumber').html(input);
            }
            //������������� ��� ���� div ���� cursor default
            $(table_1).find('div').css('cursor', 'default');
            //����������� ����������, ���������� �� ���������� ����� ����� ����, ��������� � ������ ����� ��������
            
            if(countTours > 1){
                countPlaces[tourType] = countWhenDelete;
            }
            else{
                countPlaces = countWhenDelete;
            }
            
        }
    };
    
    var selectPlaces = function(obj){
        //�������� �� ����� �������� ����� ��� ������������
        obj.click(function(){
            var currClass = $(this).attr("class");
            var currInput = $(this).find("input");
            var currName = $(currInput).attr("value");
            var currInputName = $(currInput).attr("name");
            
            //���������� ����������� ���������� ���������� ���������� ����
            if(countTours > 1){
                //���������� � ����� ����� �� �������� (��� ������� �����)
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
                
                //������ ��� ���� ������������ "������ ������"
                //������������, ��� � �������� �� ����� ���� ������ 20 �����!
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
                //��������� �������: ��� ������������ "������ ������" ����� ����������� �����, ������� � 11-�� ����
                
                if(match == 0 && typeBooking == 'onlyRoad' && userGroup != 9){
                    $.colorbox({
                        html: '<div class="colorboxError"><p>������ ��� ������������ "������ ������". ������������ ���� ��������, ������� � 11-�� ����!</p></div>',
                        close: '<b>�������</b>',
                        maxWidth: '70%'
                    });
                }
                else{
                    if(countPlacesBus < maxPlacesBus){
                        $(this).removeClass().addClass("placePP specialPlace");
                        currInput.attr("value", "PP");        
                        var input = '<input name="Tour[booking][bus_'+$(this).parents('table.busTable').attr('id')+'][places]['+currInputName+']" type="hidden" value="PP"/>';            
                        $('.passangersNumber').append(input);
                        //��������� ���������� ��������� ��� ������� ����
                        
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
                    //������ ��� ������ ������������ "�����������", ����� ����� �������� ��� ������ �������� � �����, ��� ���� ������, ��� ����������
                    else if(countPlacesBus >= maxPlacesBus && typeBooking == 'standard'){
                        $.colorbox({
                            html: '<div class="colorboxError"><p>������� ������������ ����� ����, ��������� ��� ������� ���� ������!</p></div><div class="colorboxNotify"><p>��� ����������� ������ ���� ������� "��". ������ ����� �������� ������ "������".</p></div>',
                            close: '<b>��</b>',
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
                            html: '<div class="colorboxError"><p>������� ������������ ����� ����, ��������� ��� ������� ���� ������!</p></div>',
                            close: '<b>�������</b>',
                            maxWidth: '70%'
                        });
                    }
                }
            }
            else if(currName == "PP" && currClass == "placePP specialPlace"){
                var currentDiv = $(this);
                $(this).removeClass().addClass("placeFP");
                $(currInput).attr("value", "FP");
                
                //���� ���� ������ ��� �� ���������� �����, �� ������� ������� � ������������ � ������� input ���������� �������� � �����            
                $('.passangersNumber input').each(function(i){                
                    var name = currentDiv.find('input').attr('name');                            
                    if($(this).attr('name') == 'Tour[booking][bus_'+currentDiv.parents('table.busTable').attr('id')+'][places]['+name+']'){                    
                        $(this).remove();
                    }
                });
                //������������� ��������� ����� � ��������� "pointer"
                currentDiv.parents('table.busTable').find('div.placeFP').css('cursor','pointer');
                //��������� �������� ��� ���� �����
                if(countTours > 1){
                    countPlaces[tourType]--;
                }
                else{
                    countPlaces--;
                }
            }
            //�������� �������, ���������� �� ����������� ������ "�����������"
            booking();
            //�������� ������� �������� ��������� ���� � ��������
            free(obj);
        });
    };
    
    //�������� �������, ���������� �� ������� ���� ��� ������ ���������
    
    if(typeBooking != 'onlyRoad'){
        selectPlaces($(tables[0]).find('div'));
    }
    else{
        selectPlaces($(tables[0]).find('div'));
        selectPlaces($(tables[1]).find('div'));
    }
    
    //��������� �� ������� ��� (����� 2)
    if(countTours > 1){        
        //���� � ������-���� �� ����� ��� � ����� 2 ��������
        if(countDoubleTourTables['departure'] > 1){
            selectPlaces($(tables[2]).find('div'));
        }
        else{
            selectPlaces($(tables[1]).find('div'));
        }
    }
    
    //� ����������� �� ���������� ������ ���� � ������ �������� ��������� ��� ��� ������ � ���������� ������� (��� "������ ������" ��������� ������)
    if(typeBooking != 'onlyRoad'){
        $('.bookingBusScheme .busTable div').click(function(){
            freeBus($(this));
        });
    }
    
    //���������� ��� �������� ������ "�����������" � ����������� �� ���������� ����
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
