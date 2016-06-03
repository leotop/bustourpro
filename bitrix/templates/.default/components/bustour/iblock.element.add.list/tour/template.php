<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    //echo "<pre>"; print_r($arParams); echo "</pre>";
    //echo "<pre>"; print_r($arResult); echo "</pre>";
    $colspan = 2;
    if ($arResult["CAN_EDIT"] == "Y") $colspan++;
    if ($arResult["CAN_DELETE"] == "Y") $colspan++;
?>
<script>
    //функция, которая проверяет наличие заказов на данный тур. возвращает список ID заказов через запятую
    function beforeTourDelete(tourID) {
        $.post("/ajax/getTourOrders.php",{tour_id:tourID},
            function(data){                     
                if (data) {
                    if (confirm("Внимание! На данный тур есть не аннулированные заказы. Перейти к заказам?")) {
                        document.location.href='/order-management/order/?filter[TOUR]=' + tourID + '&setFilter=Y' 
                    }                                            

                    else {
                        return false;
                    }
                } 

                else {
                    if (confirm("Внимание! Данное действие нельзя будет отменить. Продолжить?")) {
                        var link = $("#delete_link_" + tourID).attr("rel");
                        document.location.href=link; 
                    }                                            

                    else {
                        return false;
                    }
                }
        })     

    }



    $(function(){
        //функция активации/деактивации тура
        $(".tour_active").click(function(){
            var tourID = $(this).attr("rel");
            var active;
            if ($(this).attr("checked") == "checked") {
                active = "Y"; 
                $(this).parents(".tour_row").removeClass("item_unactive");
            }
            else {
                active = "N";
                $(this).parents(".tour_row").addClass("item_unactive");
            }
            
            $.post("/ajax/setTourActive.php",{active:active, tour:tourID}, function(data){});
        })    
    })

</script>

<h4>Данный раздел создан для просмотра и редактирования туров. Для создания новых туров, воспользуйтесь разделом <a href="/tours/tour_designer/">формирование туров</a></h4>

<?if (strlen($arResult["MESSAGE"]) > 0):?>
    <?=ShowNote($arResult["MESSAGE"])?>
    <?endif?>  

<form name="arrFilter_form" action="<?=$APPLICATION->GetCurPage()?>" method="get">
    <table class="data-table" cellspacing="0" cellpadding="2" style="float: left;">
        <thead>
            <tr>
                <td colspan="2" align="center"><?=GetMessage("IBLOCK_FILTER_TITLE")?></td>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td>Название</td>
                <td>
                    <input type="text" name="filter[NAME]" value="<?=htmlspecialcharsbx($_GET["filter"]["NAME"])?>">
                </td>
            </tr>

            <tr>
                <td valign="top">Дата начала тура:</td>
                <td valign="top">
                    <?$APPLICATION->IncludeComponent(
                            "bitrix:main.calendar",
                            "",
                            Array(
                                "SHOW_INPUT" => "Y",
                                "FORM_NAME" => "arrFilter_form",
                                "INPUT_NAME" => "filter[DATE_FROM][FROM]",
                                "INPUT_NAME_FINISH" => "filter[DATE_FROM][TO]",
                                "INPUT_VALUE" => $_GET["filter"]["DATE_FROM"]["FROM"],
                                "INPUT_VALUE_FINISH" => $_GET["filter"]["DATE_FROM"]["TO"],
                                "SHOW_TIME" => "N",
                                "HIDE_TIMEBAR" => "Y"
                            ),
                            false
                        );?> 
                </td>
            </tr>

            <tr>
                <td valign="top">Курорт:<br>
                    <div class="filter_block" id="cities">
                        <label for="city[0]" >
                            <input type="checkbox" value="Y" name="filter[CITY][0]" id="city[0]" onchange="show_items(this);" <?if (array_key_exists(0,$_GET["filter"]["CITY"])){?>checked="checked" <?}?> class="default_value city"> 
                            Все</label>
                        <?
                            //выбираем города
                            $cities = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"CITY","PROPERTY_COMPANY"=>getCurrentCompanyID(),"PROPERTY_ACTIVE_VALUE"=>"Да"));
                            while($arCity = $cities->Fetch()) {?>
                            <label  for="city<?=$arCity["ID"]?>">
                                <input class="city" type="checkbox" value="Y" id="city<?=$arCity["ID"]?>" name="filter[CITY][<?=$arCity["ID"]?>]" <?if (array_key_exists($arCity["ID"],$_GET["filter"]["CITY"]) && $_GET["setFilter"] == "Y"){?>checked="checked"<?}?> onchange="show_items(this);"> 
                            <?=$arCity["NAME"]?></label>
                            <?}
                        ?>
                    </div> 


                </td>


                <td valign="top">Гостиница:<br>
                    <div class="filter_block" id="hotels">                            
                        <label for="hotel[0]" rel="0" class="city0">
                            <input type="checkbox" value="Y" name="filter[HOTEL][0]" id="hotel[0]" rel="0" onchange="show_items(this)"  class="default_value"> 
                            Все</label>
                        <?
                            //выбираем гостиницу
                            $h_filter = array("IBLOCK_CODE"=>"HOTEL","PROPERTY_COMPANY"=>getCurrentCompanyID(),"PROPERTY_ACTIVE_VALUE"=>"Да");                         
                            $hotels = CIBlockElement::GetList(array(), $h_filter, false, false, array("ID","NAME","PROPERTY_CITY"));
                            while($arHotel = $hotels->Fetch()) {?>
                            <label for="hotel[<?=$arHotel["ID"]?>]" rel="<?=$arHotel["PROPERTY_CITY_VALUE"]?>" class="city<?=$arHotel["PROPERTY_CITY_VALUE"]?>">
                                <input type="checkbox" id="hotel[<?=$arHotel["ID"]?>]"  name="filter[HOTEL][<?=$arHotel["ID"]?>]" value="Y" <?if (array_key_exists($arHotel["ID"],$_GET["filter"]["HOTEL"]) && $_GET["setFilter"] == "Y"){?>checked="checked"<?}?> onchange="show_items(this)"> 
                                <?=$arHotel["NAME"]?>
                            </label>
                            <?}
                        ?>
                    </div>
                </td>
            </tr>  

            <tr>
                <td>Спецпредложение</td>
                <td><input type="checkbox" value="Y" name="filter[SPECIAL_OFFER]" <?if ($_GET["filter"]["SPECIAL_OFFER"] == "Y"){?> checked="checked"<?}?>></td>
            </tr>

            <tr>
                <td>Есть дополнительная скидка</td>
                <td><input type="checkbox" value="Y" name="filter[IS_DISCOUNT]" <?if ($_GET["filter"]["IS_DISCOUNT"] == "Y"){?> checked="checked"<?}?>></td>
            </tr>



        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    <input type="submit" value="Подобрать" /><input type="hidden" name="setFilter" value="Y" />&nbsp;&nbsp;
                    <input type="button" value="Сбросить" onclick="document.location.href='<?=$APPLICATION->GetCurPage()?>'"/></td>
            </tr>
        </tfoot>
    </table>           
</form>
<div class="separator"></div>
<br>   
 <a class="save_button" onclick="document.location.reload()" href="javascript:void(0)">Сохранить</a>
<table class="data-table">
    <?if($arResult["NO_USER"] == "N"):?>
        <thead>


            <tr>
                <td><b>Активность</b></td> 
                <td><b>#</b></td>
                <td><b>Тур</b></td>
                <td><b>Дата начала</b></td>
                <td><b>Дата окончания</b></td> 
                <td><b>Направление</b></td>  
                <td><b>Город</b></td>
                <td><b>Гостиница</b></td>                      
                <td><b>Номер</b></td>
                <td><b>Заселение с детьми</b></td>                   
                <td><b>Кол-во свободных номеров</b></td>
                <td><b>Цена</b></td>   
                <td><b>Доп. место</b></td>
                <td><b>Цена доп. места</b></td>   
                <td><b>Доп. скидка, %</b></td>  
                <td colspan="2"></td>
            </tr>

        </thead>
        <tbody>
            <?if (count($arResult["ELEMENTS"]) > 0):?>
                <?foreach ($arResult["ELEMENTS"] as $arElement):?>
                    <tr class="tour_row <?if ($arElement["ACTIVE"] == "N"){?> item_unactive<?} elseif ($arElement["PROPERTIES"]["SPECIAL_OFFER"]["VALUE_ENUM"] == "Да"){?> tour_spec_offer<?}?>">

                        <td align="center"><input type="checkbox" class="tour_active" rel="<?=$arElement["ID"]?>" value="" name="tour_active_<?=$arElement["ID"]?>" <?if ($arElement["ACTIVE"] == "Y"){?>checked="checked"<?}?> ></td>
                        <td><?=$arElement["ID"]?></td>
                        <td><?=$arElement["NAME"]?></td>

                        <td>
                            <?=$arElement["PROPERTIES"]["DATE_FROM"]["VALUE"]?>
                        </td>

                        <td>
                            <?=$arElement["PROPERTIES"]["DATE_TO"]["VALUE"]?>
                        </td>


                        <td>
                            <?foreach ($arElement["PROPERTIES"]["DIRECTION"]["VALUE"] as $i=>$id) {
                                $direction = CIBlockElement::GetById($id);
                                $arDirection = $direction->Fetch();
                                echo $arDirection["NAME"];
                                if ($i < count($arElement["PROPERTIES"]["DIRECTION"]["VALUE"]) - 1) {echo "<br>";};
                            }?>
                        </td>



                        <td>
                            <?foreach ($arElement["PROPERTIES"]["CITY"]["VALUE"] as $i=>$id) {
                                $city = CIBlockElement::GetById($id);
                                $arCity = $city->Fetch();
                                echo $arCity["NAME"];
                                if ($i < count($arElement["PROPERTIES"]["CITY"]["VALUE"]) - 1) {echo "<br>";};
                            }?>
                        </td>

                        <td>                          

                            <?$hotel = CIBLockElement::GetList(array(), array("ID"=>$arElement["PROPERTIES"]["HOTEL"]["VALUE"][0]), false, false, array("NAME", "PROPERTY_IS_CHILDREN"));
                                $arHotel = $hotel->Fetch();
                                echo $arHotel["NAME"];?>
                        </td>      


                        <td>   
                            <?$room = CIBLockElement::GetList(array(),array("ID"=>$arElement["PROPERTIES"]["ROOM"]["VALUE"][0]),false, false, array("ID","NAME","PROPERTY_IS_ADD_ADDITIONAL_SEATS"));
                                $arRoom = $room->Fetch();
                                echo $arRoom["NAME"];?>
                        </td>

                        <td>
                            <?if($arHotel["PROPERTY_IS_CHILDREN_VALUE"]) {echo $arHotel["PROPERTY_IS_CHILDREN_VALUE"];} else {echo "Нет";}?>
                        </td>    

                        <td>
                            <?=$arElement["PROPERTIES"]["NUMBER_ROOM"]["VALUE"]?>
                        </td>

                        <td>
                            <?=$arElement["PROPERTIES"]["PRICE"]["VALUE"]?>
                        </td>

                        <td>
                            <?=$arRoom["PROPERTY_IS_ADD_ADDITIONAL_SEATS_VALUE"]?>                        
                        </td>

                        <td><?if($arElement["PROPERTIES"]["PRICE_ADDITIONAL_SEATS"]["VALUE"]> 0){echo $arElement["PROPERTIES"]["PRICE_ADDITIONAL_SEATS"]["VALUE"];}?></td>

                        <td>
                            <?if ($arElement["PROPERTIES"]["DISCONT"]["VALUE"] > 0){?>
                                <b>
                                    <?=$arElement["PROPERTIES"]["DISCONT"]["VALUE"]?>
                                </b>
                                <?}?>
                        </td>

                        <!--<td><small><?/*=is_array($arResult["WF_STATUS"]) ? $arResult["WF_STATUS"][$arElement["WF_STATUS_ID"]] : $arResult["ACTIVE_STATUS"][$arElement["ACTIVE"]]*/?></small></td>-->
                        <?if ($arResult["CAN_EDIT"] == "Y"):?>
                            <td><?if ($arElement["CAN_EDIT"] == "Y"):?><a class="fancybox" href="<?=$arParams["EDIT_URL"]?>?edit=Y&amp;CODE=<?=$arElement["ID"]?>"><?=GetMessage("IBLOCK_ADD_LIST_EDIT")?><?else:?>&nbsp;<?endif?></a></td>
                            <?endif?>
                        <?if ($arResult["CAN_DELETE"] == "Y"):?>
                            <td>
                                <?if ($arElement["CAN_DELETE"] == "Y"):?>
                                    <?/*
                                        <a href="?delete=Y&amp;CODE=<?=$arElement["ID"]?>&amp;<?=bitrix_sessid_get()?>" onClick="return confirm('<?echo CUtil::JSEscape(str_replace("#ELEMENT_NAME#", $arElement["NAME"], GetMessage("IBLOCK_ADD_LIST_DELETE_CONFIRM")))?>')">
                                        <?=GetMessage("IBLOCK_ADD_LIST_DELETE")?>
                                    </a> */?>
                                    <a href="javascript:void(0)" id="delete_link_<?=$arElement["ID"]?>" rel="?delete=Y&amp;CODE=<?=$arElement["ID"]?>&amp;<?=bitrix_sessid_get()?>" onClick="beforeTourDelete(<?=$arElement["ID"]?>);">
                                        <?=GetMessage("IBLOCK_ADD_LIST_DELETE")?>
                                    </a>
                                    <?else:?>
                                    &nbsp;
                                    <?endif?>
                            </td>
                            <?endif?>  
                    </tr>
                    <?endforeach?>
                <?else:?>
                <tr>
                    <td<?=$colspan > 1 ? " colspan=\"".$colspan."\"" : ""?>><?=GetMessage("IBLOCK_ADD_LIST_EMPTY")?></td>
                </tr>
                <?endif?>
        </tbody>
        <?endif?>
</table>  

<a class="save_button" onclick="document.location.reload()" href="javascript:void(0)">Сохранить</a>
<?if (strlen($arResult["NAV_STRING"]) > 0):?><?=$arResult["NAV_STRING"]?><?endif?>