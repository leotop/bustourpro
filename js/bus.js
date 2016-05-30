function scheme_write(){
    //пишем новую схему
    var rowNum = $(".busTable tr").length;
    var columnNum = ($(".busTable tr td").length)/rowNum;
    var schema = new Object();

    for(j = 1; j <= rowNum; j++){
        var schemaBeetween = new Object();                                                    
        for(i = 1; i <= columnNum; i++){                        
            var arr = $(".busTable input[name=\'r_"+j+"_c_"+i+"\']");                    
            schemaBeetween[arr.attr("name")] = arr.attr("value");                    
        }    
        schema[j] = schemaBeetween;
        delete schemaBeetween;                
    };
    $("#Bus_scheme").attr("value", $.toJSON(schema)); 
}

controllerAction = "tour/update";

$(function(){   


    //добавление новых мест в схему автобуса
    var addDelete = function(typeOper, obj){
        var row ='', column = '';
        if(obj != ''){
            row = $(obj).find("tr").length;
            column = ($(obj).find("tr td").length)/row;
        }
        else{
            row = $(".busTable tr").length;
            column = ($(".busTable tr td").length)/row;
        }

        if(typeOper == "addColumn" || typeOper == "deleteColumn"){
            for(var i = 1; i <= row; i++){
                if(obj != ''){
                    var columnOper = $(obj).find("input[name='r_"+i+"_c_"+column+"']").parent().parent();
                }
                else{
                    var columnOper = $(".busTable input[name='r_"+i+"_c_"+column+"']").parent().parent();
                }

                if(typeOper == "addColumn"){                    
                    columnOper.after("<td><div class=\"createBusItemsDiv\"><input type='hidden' name='r_"+i+"_c_"+(column+1)+"' value='FP'/></div></td>");                    
                }
                if(typeOper == "deleteColumn" && column > 1){
                    columnOper.remove();
                }            
            }
        }
        else{
            var columnOper ='', addTrTd ='';

            if(obj != ''){
                columnOper = $(obj).find("input[name='r_"+row+"_c_1']").parent().parent().parent();
            }
            else{
                columnOper = $(".busTable input[name='r_"+row+"_c_1']").parent().parent().parent();
            }

            for(var i = 1; i <= column; i++){
                if(i == 1){
                    var rowSign ='';
                    rowSign = '<div class="rowSign">Ряд '+(row+1)+'</div>';
                }
                addTrTd += "<td><div class=\"createBusItemsDiv\">"+rowSign+"<input type='hidden' name='r_"+(row+1)+"_c_"+i+"' value='FP'/></div></td>";
                rowSign = '';
            }

            if(typeOper == "addRow"){                    
                columnOper.after("<tr>"+addTrTd+"</tr>");
            }

            if(typeOper == "deleteRow" && row > 1){
                columnOper.remove();
            }
        } 

        scheme_write();

    };

    //выводит схему автобуса из бд в раздделе автобусы        
    //@param - передаем название модели, для которой выводим схему    

    var busSchemeGK = function(param){
        if(param == "bus"){
            var busSchemeVal = $("#Bus_scheme").attr("value");
            if(busSchemeVal != "" && busSchemeVal != undefined){
                var schemeObjects = window.xxx = $.evalJSON(busSchemeVal);
                $(".busTable tr").remove();
                for(i in schemeObjects){
                    var thisTr = $("<tr></tr>");
                    $(".busTable").append(thisTr);
                    for(j in schemeObjects[i]){
                        if(j == "r_"+i+"_c_1"){                    
                            var rowNum = "<div class='rowSign'>Ряд "+i+"</div>";
                        }
                        else {
                            rowNum = "";
                        }
                        thisTr.append("<td><div class='place"+schemeObjects[i][j]+"'>"+rowNum+"<input type='hidden' value='"+schemeObjects[i][j]+"' name='"+j+"' /></div></td>");
                    }
                }
            }
        }
        //если пришли на редактирование тура
        else if(param == "tour"){
            $('.tourUpdateBusScheme').each(function(){

                var busSchemeVal = $(this).attr("value");

                if(busSchemeVal != ""){
                    var schemeObjects = window.xxx = $.evalJSON(busSchemeVal);
                    $(this).parent().find(".busTable tr").remove();
                    for(i in schemeObjects){
                        var thisTr = $("<tr></tr>");
                        $(this).parent().find(".busTable").append(thisTr);
                        for(j in schemeObjects[i]){
                            if(j == "r_"+i+"_c_1"){                    
                                var rowNum = "<div class='rowSign'>Ряд "+i+"</div>";
                            }
                            else {
                                rowNum = "";
                            }
                            thisTr.append("<td><div class='place"+schemeObjects[i][j]+"'>"+rowNum+"<input type='hidden' value='"+schemeObjects[i][j]+"' name='"+j+"' /></div></td>");
                        }
                    }
                }

                var table = $(this).parent().find('.busTableWrap');
                //вешаем события "добавления/удаления" ячеек/строк на таблицы
                $(table).find(".addDeletePlace a").click(function(){

                    //!!!!чтобы разрешить добавление/удаление мест в автобусе, раскомментируйте строчку
                    //addDelete($(this).attr("id"), $(table).find('.busTable'));
                });

            });
        }
    };


    //action и controller где должен исполниться скрипт
    if (!controllerAction){
        //var controllerAction
    }

    //если запускаем скрипт не на странице создания/редактирования автобуса, функция не вызывается 
    if(controllerAction == 'bus/create' || controllerAction == 'bus/update'){
        busSchemeGK("bus");
    }
    //если запускаем скрипт на странице редактирования тура
    else if(controllerAction == 'tour/update'){
        busSchemeGK("tour");

        //убираем помеченные как "недоступные места"
        $('.busTable div').live("click", function(){
            var currClass = $(this).attr("class");
            var currInput = $(this).find("input");
            var currName = currInput.attr("value");

            if(currClass == "rowSign"){
                return false;
            }
            else if(currName == "FP"){
                $(this).removeClass().addClass("placeNA");
                currInput.attr("value", "NA");
            }
            else if(currName == "NA"){
                $(this).removeClass().addClass("placeFP");
                currInput.attr("value", "FP");
            }
        });

        scheme_write();
    }

    //переключение цветов мест в режиме построения схемы автобуса, при указании неактивных мест в схеме автобуса при создании тура   
    $(".busTable div, .busTable.busTableColorbox div").live("click", function(){
        var currClass = $(this).attr("class");
        var currInput = $(this).find("input");
        var currName = currInput.attr("value");

        if(controllerAction != 'tour/create' && controllerAction != 'tour/update'){
            if(currClass == "rowSign"){
                return false;
            }
            else if(currName == "FP"){
                $(this).removeClass().addClass("placeNP");
                currInput.attr("value", "NP");
            }
            else if(currName == "NP"){
                $(this).removeClass().addClass("placeWP");
                currInput.attr("value", "WP");
            }
            else if(currName == "WP"){
                $(this).removeClass().addClass("placeDP");
                currInput.attr("value", "DP");
            }             
            else if(currName == "DP"){
                $(this).removeClass().addClass("placeTP");
                currInput.attr("value", "TP");
            }     
            else {
                $(this).removeClass().addClass("placeFP");
                currInput.attr("value", "FP");
            }
        }

        else if(controllerAction == 'tour/create' || controllerAction == 'tour/update'){
            if(currClass == "rowSign"){
                return false;
            }
            else if(currName == "FP"){
                $(this).removeClass().addClass("placeNA");
                currInput.attr("value", "NA");
            }
            else if(currName == "NA"){
                $(this).removeClass().addClass("placeFP");
                currInput.attr("value", "FP");
            }
        }          

        scheme_write();

    });            


    //события добавления/удаления мест в автобусе
    if(controllerAction != 'tour/update'){
        $(".busTableWrap .addDeletePlace a").live("click", function(){
            addDelete($(this).attr("id"), '');
        });    
    }

    //преобразовываем измененную схему автобуса перед сохранением (при создании/редактировании автобуса)
    window.busCreateUpdate = function (){
        var rowNum = $(".busTable tr").length;
        var columnNum = ($(".busTable tr td").length)/rowNum;
        var schema = new Object();

        for(j = 1; j <= rowNum; j++){
            var schemaBeetween = new Object();                                                    
            for(i = 1; i <= columnNum; i++){                        
                var arr = $(".busTable input[name=\'r_"+j+"_c_"+i+"\']");                    
                schemaBeetween[arr.attr("name")] = arr.attr("value");                    
            }    
            schema[j] = schemaBeetween;
            delete schemaBeetween;                
        };
        $("#Bus_scheme").attr("value", $.toJSON(schema));
    };

    //преобразовываем измененную схему автобуса перед сохранением (при редактировании тура)
    window.tourUpdateBus = function(){
        $('.tourUpdateBusScheme').each(function(){
            var rowNum = $(this).parent().find(".busTable tr").length;
            var columnNum = ($(this).parent().find(".busTable tr td").length)/rowNum;
            var schema = new Object();

            for(j = 1; j <= rowNum; j++){
                var schemaBeetween = new Object();                                                    
                for(i = 1; i <= columnNum; i++){                        
                    var arr = $(this).parent().find(".busTable input[name=\'r_"+j+"_c_"+i+"\']");
                    schemaBeetween[arr.attr("name")] = arr.attr("value");                    
                }    
                schema[j] = schemaBeetween;
                console.log(schema);
                delete schemaBeetween;
            };
            $(this).attr("value", $.toJSON(schema));
        });
    };

});
