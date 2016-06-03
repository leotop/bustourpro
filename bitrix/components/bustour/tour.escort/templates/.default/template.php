<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
    //arshow($arResult);       
?>
<script>
    //функция получения схемы автобуса с данными туристов
    function getEscort(id) {
        if (id > 0) {
            $("#escort_bus").html("загрузка данных...");
            $.post("/ajax/getEscort.php",
                {id:id},
                function(data){
                    $("#escort_bus").html(data);  
            })  

        }
        else {
            $("#escort_bus").html(""); 
        }


    }


    function goPrint(type) {
        if ($("#bus_list").val() > 0) {
            window.open("/escort/print.php?print=Y&id=" + $("#bus_list").val() + "&type=" + type, "_blank"); 
        }
    } 

</script>



<select id="bus_list" onchange="getEscort($(this).val())">
    <option value="0">Выберите автобус</option>
    <?foreach ($arResult["ITEMS"] as $busID=>$bus) {?>
        <option value="<?=$busID?>"><?="#".$busID;?> - <?=$bus["TOUR"]["NAME"]." (".$bus["TOUR"]["PROPERTY_DATE_FROM_VALUE"]." - ".$bus["TOUR"]["PROPERTY_DATE_TO_VALUE"]."), ".$bus["PROPERTY_BUS_DIRECTION_VALUE"]?></option>
        <?}?>
</select>
<div class="print_buttons">
    <a class="add_button" href="javascript:void(0)" onclick="goPrint('B')" id="print_button">Печатать схему</a>
    <a class="add_button" href="javascript:void(0)" onclick="goPrint('H')" id="print_button">Печатать гостиницы</a>
    <a class="add_button" href="javascript:void(0)" onclick="goPrint('P')" id="print_button">Список пассажиров</a>
 </div>
 <br>
<div id="escort_bus"></div>


