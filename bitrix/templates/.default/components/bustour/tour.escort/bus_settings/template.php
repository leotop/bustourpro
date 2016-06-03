<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
    //arshow($arResult);       
?>
<script>
    //функция получения схемы автобуса с данными туристов
    function getBusScheme(id) {
        if (id > 0) {
            $("#bus_scheme").html("загрузка данных...");
            $.post("/ajax/getBus.php",
                {id:id},
                function(data){
                    $("#bus_scheme").html(data);
                    scheme_write();  
            })        
        }
        else {
            $("#bus_scheme").html(""); 
        }         
    }   


    function scheme_set(){
        //пишем новую схему
        var rowNum = $(".busTable tr").length;
        var columnNum = Math.round(($(".busTable tr td").length)/rowNum);
        var schema = new Object();
        //alert(columnNum);
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


    $(function(){


        $("body").on("click", ".placeFP", function(){
            $(this).removeClass("placeFP");
            $(this).addClass("placeBP");
            $(this).find("input").val("BP");
            scheme_set();

        })

        $("body").on("click", ".placeBP", function(){
            $(this).removeClass("placeBP");
            $(this).addClass("placeFP");
            $(this).find("input").val("FP");
            scheme_set();
        }) 
    })
</script>


<input type="hidden" id="Bus_scheme" value="">
<select id="bus_list" onchange="getBusScheme($(this).val())">
    <option value="0">Выберите автобус</option>
    <?foreach ($arResult["ITEMS"] as $busID=>$bus) {?>
        <option value="<?=$busID?>"><?="#".$busID;?> - <?=$bus["TOUR"]["NAME"]." (".$bus["TOUR"]["PROPERTY_DATE_FROM_VALUE"]." - ".$bus["TOUR"]["PROPERTY_DATE_TO_VALUE"]."), ".$bus["PROPERTY_BUS_DIRECTION_VALUE"]?></option>
        <?}?>
</select>

 <br>
 <br>
<div id="bus_scheme"></div>


