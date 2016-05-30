<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<?
    //получаем инфо об автобусе
    $busID = intval($_POST["id"]);
  
?>

<div class="booking">
    <div class="bookingBusSchemeTop">
        <div class="bookingBusScheme">
            <div class="twoBus">                        
                <div class="busTable_0">
                    <?//get_bus_scheme($arBus["PROPERTY_P_SCHEME_VALUE"]); 
                     echo getEscortScheme($busID, "F");
                    ?>                         
                </div>   
            </div> 
        </div>  
    </div>      
</div>
