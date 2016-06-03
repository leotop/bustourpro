<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    //echo "<pre>"; print_r($arParams); echo "</pre>";
    //echo "<pre>"; print_r($arResult); echo "</pre>";
    $colspan = 2;
    if ($arResult["CAN_EDIT"] == "Y") $colspan++;
    if ($arResult["CAN_DELETE"] == "Y") $colspan++;
?>
<?//arshow($arResult["ELEMENTS"])?>

<script>

    //функция изменения статуса заказа. на вход ID значения в БД и значение которое было при загрузке
    function changeOrderStatus(valueID,defaultID,orderID) {
        //проверяем будущий статус
        var newStatusName;
        var newStatusValue = $("#orderStatus_" + valueID).val();
        $("#orderStatus_" + valueID).find("option").each(function(){  
            if ($(this).attr("selected") == "selected") {   
                newStatusName = $(this).attr("rel");  
            }
        })

        //если новый статус "аннулирован" или "запрос на аннуляцию", выдаем предупреждение
        if (newStatusName == "STATUS_FOR_CANCEL" || newStatusName == "STATUS_CANCELLED") {
            if (confirm("Внимание, данное действие нельзя будет отменить. Продолжить?")) {
                //меняем статус

            }
            else {
                //иначе возвращаем значение которое было
                $("#orderStatus_" + valueID).val(defaultID);
                return false; 
            } 
        }

        $.post("/ajax/updateOrderStatus.php",{
            status_id : valueID,  
            new_status : newStatusValue,
            new_status_name: newStatusName,
            order_id: orderID
            }, function(data) {

                if (newStatusName == "STATUS_FOR_CANCEL" || newStatusName == "STATUS_CANCELLED") {
                    window.top.location.reload(); 
                }   
        });
    }

</script>

<?if (strlen($arResult["MESSAGE"]) > 0):?>
    <?=ShowNote($arResult["MESSAGE"])?>
    <?endif?>   
<?$APPLICATION->IncludeComponent(
        "bitrix:catalog.filter",
        "orders",
        Array(
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "FILTER_NAME" => "arrFilter",
            "FIELD_CODE" => array(),
            "CACHE_TYPE" => "N",
            "CACHE_TIME" => 3600,
            "CACHE_GROUPS" => "N",
        ),
        $component
    );
?>    

<?
    //получаем доступные статусы заказа
    $statuses = array();                         
    $order_statuses = CIBlockProperty::GetPropertyEnum("STATUS",array("SORT"=>"ASC"), Array()); 
    while($arStatus = $order_statuses->Fetch()) {
        $statuses[$arStatus["ID"]] = array("VALUE"=>$arStatus["VALUE"],"XML_ID"=>$arStatus["XML_ID"]);
    }


    //получаем список групп пользователя  
    $userGroups = getUserGroup($USER->GetID());     

?>

<table class="data-table orders_table">
    <?if($arResult["NO_USER"] == "N"):?>
        <tr>
            <td><b>#</b></td>
            <td><b>Дата создания</b></td>
            <td><b>Дата отправления</b></td>
            <td><b>Туристы</b></td>
            <td><b>Город</b></td>
            <td><b>Гостиница</b></td>
            <td><b>Номер</b></td>
            <td><b>Тип</b></td>
            <td><b>Статус</b></td>
            <td><b>Город забора туристов</b></td>  
            <td><b>Агентство</b></td>
            <td><b>Стоимость</b></td>
            <td><b>К оплате</b></td>
            <?/*//для туроператоров добавляем столбец "удалить"
                if (in_array("TOUR_OPERATOR",$userGroups)) { ?>
                <td></td>   
                <?}*/?>

        </tr>
        <?if (count($arResult["ELEMENTS"]) > 0):?>
            <?foreach ($arResult["ELEMENTS"] as $arElement):?>
                <?//arshow($arElement)
                    if ($arElement["PROPERTIES"]["TOUR"]["VALUE"][0]) {$tour_id = $arElement["PROPERTIES"]["TOUR"]["VALUE"][0];} else {$tour_id = $arElement["PROPERTIES"]["BUS_ID"]["VALUE"][0];}
                    $tour = CIBlockElement::GetList(array(), array("ID"=>$tour_id), false, false, array("ID","NAME","PROPERTY_DIRECTION","PROPERTY_CITY","PROPERTY_ROOM","PROPERTY_DATE_FROM","PROPERTY_DATE_TO","PROPERTY_PRICE","PROPERTY_HOTEL"));
                    $arTour = $tour->Fetch();
                ?>
                <tr>
                    <td>
                        <a class="fancybox" href="<?=$arParams["EDIT_URL"]?>?edit=Y&amp;CODE=<?=$arElement["ID"]?>">
                            <?=$arElement["ID"]?>
                        </a>
                    </td>

                    <td>                       
                        <?=substr($arElement["DATE_CREATE"],0,10);?>
                    </td>

                    <td>
                        <?=$arElement["PROPERTIES"]["DATE_FROM"]["VALUE"]?>
                        <?if (!$arElement["PROPERTIES"]["DATE_FROM"]["VALUE"])
                            {
                                echo $arTour["PROPERTY_DATE_FROM_VALUE"];
                        }?>
                    </td>     
                    <td>
                        <?
                            $tourist = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"TOURIST","PROPERTY_ORDER"=>$arElement["ID"]), false, false, array("NAME","ID"));
                            while($arTourist = $tourist->Fetch()) {?>
                            <a class="fancybox" href="/order-management/passenger/?edit=Y&CODE=<?=$arTourist["ID"]?>"><?=$arTourist["NAME"]?></a><br>
                            <?}
                        ?>
                    </td>

                    <td>
                        <?
                            $city = CIBLockElement::GetById($arTour["PROPERTY_CITY_VALUE"]);
                            $arCity = $city->Fetch();
                            echo $arCity["NAME"]; 
                        ?>
                    </td>

                    <td>
                        <?
                            $hotel = CIBLockElement::GetById($arTour["PROPERTY_HOTEL_VALUE"]);
                            $arHotel = $hotel->Fetch();
                            echo $arHotel["NAME"]; 
                        ?>
                    </td>

                    <td>
                        <?
                            $room = CIBLockElement::GetById($arTour["PROPERTY_ROOM_VALUE"]);
                            $arRoom = $room->Fetch();
                            echo $arRoom["NAME"]; 
                        ?>
                    </td>

                    <td>
                        <?=$arElement["PROPERTIES"]["TYPE_BOOKING"]["VALUE_ENUM"]?>
                    </td>


                    <td>
                        <?//arshow($arElement["PROPERTIES"]["STATUS"])?>
                        <?
                            //ID значения статуса для данного заказа
                            $orderStatusValueID = $arElement["PROPERTIES"]["STATUS"]["PROPERTY_VALUE_ID"];
                        ?>
                        <select class="order_status" onchange="changeOrderStatus(<?=$orderStatusValueID?>,<?=$arElement["PROPERTIES"]["STATUS"]["VALUE"]?>,<?=$arElement["ID"]?>)" id="orderStatus_<?=$orderStatusValueID?>">
                            <?      foreach ($statuses as $sID=>$sVAL) {  
                                    //если пользователь - туроперетор, или если это текущий статус или статус "запрос на аннулирование" или заказ не отменен, то помещаем текущий статус в список
                                    if (
                                        ((in_array("TOUR_OPERATOR",$userGroups) or 
                                            $sVAL["XML_ID"] == "STATUS_FOR_CANCEL" or 
                                            $sID == $arElement["PROPERTIES"]["STATUS"]["VALUE"]) and 
                                            $statuses[$arElement["PROPERTIES"]["STATUS"]["VALUE"]]["XML_ID"] != "STATUS_CANCELLED") or
                                        $sVAL["XML_ID"] == "STATUS_CANCELLED"                                    
                                    ) 
                                    {

                                        if ($sVAL["XML_ID"] == "STATUS_CANCELLED" && 
                                            $statuses[$arElement["PROPERTIES"]["STATUS"]["VALUE"]]["XML_ID"] != "STATUS_CANCELLED" && 
                                            !in_array("TOUR_OPERATOR",$userGroups)
                                        )   {
                                            continue;
                                        }


                                    ?>
                                    <option value="<?=$sID?>" rel="<?=$sVAL["XML_ID"]?>" <?if ($sID == $arElement["PROPERTIES"]["STATUS"]["VALUE"]){?> selected="selected"<?}?>><?=$sVAL["VALUE"]?></option>
                                    <?}?>    
                                <?}?>
                        </select>            
                    </td>



                    <td>
                        <?//arshow($arElement["PROPERTIES"])?>
                        <?=get_iblock_element_name($arElement["PROPERTIES"]["DEPARTURE_CITY"]["VALUE"][0])?>
                    </td>


                    <td>
                        <?=$arElement["PROPERTIES"]["COMPANY_NAME"]["VALUE"]?>
                    </td>

                    <td>
                        <?=$arElement["PROPERTIES"]["PRICE"]["VALUE"]?>
                    </td>

                    <td>
                    <?
                    //проверяем статус пользователя (является ли он менеджером туроператора)
                    $userID = $USER->GetId();
                    $meStatus = checkUserStatus($userID); //статус текущего пользователя
                    $status = checkUserStatus($arElement["CREATED_BY"]); //статус создателя заказа
                    $groups = getUserGroup($userID);                     
                    ?>                     
                    <?
                    //если пользователь является создателем заказа и не является менеджером туроператора, то выводим ему цену без скидки
                    if ((!in_array("TOUR_OPERATOR",$groups) && $meStatus != "Y") || (in_array("TOUR_OPERATOR",$groups) && $status != "Y")){?>
                        <?=$arElement["PROPERTIES"]["OPERATOR_PRICE"]["VALUE"]?>
                        <?} else {echo $arElement["PROPERTIES"]["PRICE"]["VALUE"];}?>
                    </td>

                    <?/*//столбец "удаление", только для туроператоров
                        if (in_array("TOUR_OPERATOR",$userGroups)) { ?>
                        <td><?if ($arElement["CAN_DELETE"] == "Y"):?><a href="?delete=Y&amp;CODE=<?=$arElement["ID"]?>&amp;<?=bitrix_sessid_get()?>" onClick="return confirm('<?echo CUtil::JSEscape(str_replace("#ELEMENT_NAME#", $arElement["NAME"], GetMessage("IBLOCK_ADD_LIST_DELETE_CONFIRM")))?>')"><?=GetMessage("IBLOCK_ADD_LIST_DELETE")?></a><?else:?>&nbsp;<?endif?></td>
                        <?}*/?>
                </tr>
                <?endforeach?>
            <?else:?>
            <tr>
                <td<?=$colspan > 1 ? " colspan=\"".$colspan."\"" : ""?>><?=GetMessage("IBLOCK_ADD_LIST_EMPTY")?></td>
            </tr>
            <?endif?>

        <?endif?>
</table>
<?if (strlen($arResult["NAV_STRING"]) > 0):?><?=$arResult["NAV_STRING"]?><?endif?>