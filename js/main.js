$(function(){
    //синхронная смена селектов
    //смена автобусов для туров, выпадающих на одну дату
    $("select").change(function(){
        //дата тура выбранного автобуса
        var tour_date = $(this).attr("data-date");
        //id текущего выбранного автобуса
        var cur_bus = $(this).val();

        if (tour_date)   {
            //перебираем остальные списки автобусов.
            //у туров, у которых совпадает дата отправления, автобусы должны совпадать!!
            $("select").each(function(){
                //если у текущего автобуса дата совпадает с той, что выше, нужно у этого селекта выбрать тот же автобус, как и вверху
                if ($(this).attr("data-date") == tour_date) {
                    $(this).val(cur_bus);
                }
            })
        }

    })
})

//----------------------------------Модальные окна
$(document).ready(function() {

    $(".fancybox").fancybox({
        //--------Общие настройки Fancybox
        type : 'iframe',
        scrolling : 'no',
        maxWidth : '1000px',
        width : '1000px',
        height : 'auto',
        autoSize : false,
        closeClick : false,
        openEffect : 'none',
        closeEffect : 'none',
        afterShow : function() {
        },
        //------------Настройки отдельно для объекта Frame
        iframe : {
            scrolling : 'yes'
        },
        //------------------Самое интересное,функция,задающая высоту

    });

});


//изменить свойство "активность" элемента  (не дефолтную битриксовую активность!!! а именно свойство "ACTIVE")
function setActive(ID) {
    $.post('/ajax/setActive.php', {ID : ID}, 
        function(data) {
            //alert(data);
    })
}



//переключение чекбоксов для городов и гостиниц
function show_items(e){           
    if (($(e).attr("id") == "city[0]"  || $(e).attr("id") == "hotel[0]") && $(e).attr("checked") == "checked") {
        $(e).parent("label").siblings("label").find("input").removeAttr("checked");
        $("#hotels > label").css("display","block"); 
    }
    else if ($(e).val() != 0) {
        $(e).parent("label").siblings("label").find(".default_value").removeAttr("checked"); 
    }   

    showHotels()

}




function showHotels(){  
    //считаем количество выбранных городов
    var checked_city = 0;
    $("#cities input").each(function(){
        if ($(this).attr("checked") == "checked") {
            checked_city++; 
        }
    })

    if (checked_city == 0) {
        $("#cities .default_value").attr("checked","checked");
        // $("#hotels input").removeAttr("checked");
    } 

    //считаем количество выбранных отелей
    var checked_hotel = 0;
    $("#hotels input").each(function(){
        if ($(this).attr("checked") == "checked") {
            checked_hotel++; 
        }
    })  


    if (checked_hotel == 0) {            
        $("#hotels .default_value").attr("checked","checked");
    }    


    ////////////////////////////////////   


    if ($("#cities .default_value").attr("checked") == "checked") {
        $("#hotels > label").css("display","block");   
        // $("#hotels input").removeAttr("checked"); 
        // $("#hotels .default_value").attr("checked","checked");

    }

    else {

        $(".city").each(function(){
            var id = $(this).attr("id");
            if ($(this).attr("checked") == "checked") {
                $("." + id).css("display","block");                
            }
            else {
                $("." + id).css("display","none");
                $("." + id).find("input").removeAttr("checked"); 
            }
        })
    }
}     


$(function(){  
    showHotels();
})   



//форматирование даты рождения в заказе
$(function(){
    //форматирование даты  
    $(".birthday input").inputmask("d.m.y");  
    $(".filter_calendar").inputmask("d.m.y");   
})

$(document).ajaxSuccess(function() {
   $(".birthday input").inputmask("d.m.y");
   $(".filter_calendar").inputmask("d.m.y");  
});

