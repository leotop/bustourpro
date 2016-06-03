<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//arshow($_GET["filter"])?>

<?//получаем список групп пользователя  
    $userGroups = getUserGroup($USER->GetID()); 
?>

<form name="arrFilter_form" action="<?=$APPLICATION->GetCurPage()?>" method="get">
    <table class="data-table" cellspacing="0" cellpadding="2" style="float: left;">
        <thead>
            <tr>
                <td colspan="4" align="center"><?=GetMessage("IBLOCK_FILTER_TITLE")?></td>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td>Номер заказа: </td>
                <td>
                    <input type="text" value="<?=$_GET["filter"]["ID"]?>" name="filter[ID]">
                </td>
            </tr>

            <tr>
                <td>Тип бронирования: </td>
                <td>
                    <select name="filter[TYPE_BOOKING]">
                        <option value="">-</option>
                        <?
                            //получаем доступные варианты бронирования
                            //получаем ID инфоблока заказов
                            $iblock = CIBlock::GetList(array(),array("CODE"=>"ORDERS"));
                            $arIblock = $iblock->Fetch(); 
                            $booking_types = CIBlockProperty::GetPropertyEnum("TYPE_BOOKING",array("SORT"=>"ASC"), Array("IBLOCK_ID"=>$arIblock["ID"])); 
                            while($arType = $booking_types->Fetch()) {?>
                            <option value="<?=$arType["ID"]?>" <?if ($arType["ID"] == $_GET["filter"]["TYPE_BOOKING"]){?> selected="selected"<?}?>><?=$arType["VALUE"]?></option>   
                            <?  }
                        ?>
                    </select>
                </td>
            </tr>


            <tr>
                <td>Статус заказа: </td>
                <td>
                    <?
                        //получаем доступные статусы
                        $statuses = array();                         
                        $order_statuses = CIBlockProperty::GetPropertyEnum("STATUS",array("SORT"=>"ASC"), Array()); 
                        while($arStatus = $order_statuses->Fetch()) {
                            $statuses[$arStatus["ID"]] = $arStatus["VALUE"];
                        }
                    ?>
                    <select name="filter[STATUS]">
                        <option value="">-</option>
                        <?                           
                            //перебираем статусы
                            foreach($statuses as $arStatus["ID"]=>$arStatus["VALUE"]) {?>
                            <option value="<?=$arStatus["ID"]?>" <?if ($arStatus["ID"] == $_GET["filter"]["STATUS"]){?> selected="selected"<?}?>><?=$arStatus["VALUE"]?></option>   
                            <?  }
                        ?>
                    </select>
                </td>
            </tr>


            <tr>
                <td>Показывать аннулированные</td>
                <td>
                    <input type="checkbox" name="filter[SHOW_CANCELLED]" value="Y" <?if ($_GET["filter"]["SHOW_CANCELLED"] =="Y"){?>  checked="checked"<?}?>>
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
                <td valign="top">Дата создания заказа:</td>
                <td valign="top">
                    <?$APPLICATION->IncludeComponent(
                            "bitrix:main.calendar",
                            "",
                            Array(
                                "SHOW_INPUT" => "Y",
                                "FORM_NAME" => "arrFilter_form",
                                "INPUT_NAME" => "filter[DATE_CREATE][FROM]",
                                "INPUT_NAME_FINISH" => "filter[DATE_CREATE][TO]",
                                "INPUT_VALUE" => $_GET["filter"]["DATE_CREATE"]["FROM"],
                                "INPUT_VALUE_FINISH" => $_GET["filter"]["DATE_CREATE"]["TO"],
                                "SHOW_TIME" => "N",
                                "HIDE_TIMEBAR" => "Y"
                            ),
                            false
                        );?> 
                </td>
            </tr>

            <tr>
                <td>Фамилия туриста</td>
                <td>
                    <input type="text" value="<?=$_GET["filter"]["TOURIST"]?>" name="filter[TOURIST]">
                </td>
            </tr>


            <?if (in_array("TOUR_OPERATOR",$userGroups)){?>
                <tr>
                    <td>Агентство</td>
                    <td>
                        <input type="text" value="<?=$_GET["filter"]["COMPANY_NAME"]?>" name="filter[COMPANY_NAME]">
                    </td>
                </tr>
                <?}?>

            <tr>
                <td valign="top">Курорт:<br>
                    <div class="filter_block" id="cities">
                        <label for="city[0]" >
                            <input type="checkbox" value="0" name="filter[CITY][0]" id="city[0]" onchange="show_items(this);" <?if (in_array(0,$_GET["city"])){?>checked="checked" <?}?> class="default_value city"> 
                            Все</label>
                        <?
                            //выбираем города
                            $cities = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"CITY","PROPERTY_COMPANY"=>getCurrentCompanyID(),"PROPERTY_ACTIVE_VALUE"=>"Да"));
                            while($arCity = $cities->Fetch()) {?>
                            <label  for="city<?=$arCity["ID"]?>">
                                <input class="city" type="checkbox" value="Y" id="city<?=$arCity["ID"]?>" name="filter[CITY][<?=$arCity["ID"]?>]" <?if ($_GET["filter"]["CITY"][$arCity["ID"]] == "Y" && $_GET["setFilter"] == "Y"){?>checked="checked"<?}?> onchange="show_items(this);"> 
                            <?=$arCity["NAME"]?></label>
                            <?}
                        ?>
                    </div> 


                </td>


                <td valign="top">Гостиница:<br>
                    <div class="filter_block" id="hotels">                            
                        <label for="hotel[0]" rel="0" class="city0">
                            <input type="checkbox" value="0" name="filter[HOTEL][0]" id="hotel[0]" rel="0" onchange="show_items(this)"  class="default_value"> 
                            Все</label>
                        <?
                            //выбираем гостиницу
                            $h_filter = array("IBLOCK_CODE"=>"HOTEL","PROPERTY_COMPANY"=>getCurrentCompanyID(),"PROPERTY_ACTIVE_VALUE"=>"Да");                         
                            $hotels = CIBlockElement::GetList(array(), $h_filter, false, false, array("ID","NAME","PROPERTY_CITY"));
                            while($arHotel = $hotels->Fetch()) {?>
                            <label for="hotel[<?=$arHotel["ID"]?>]" rel="<?=$arHotel["PROPERTY_CITY_VALUE"]?>" class="city<?=$arHotel["PROPERTY_CITY_VALUE"]?>">
                                <input type="checkbox" id="hotel[<?=$arHotel["ID"]?>]"  name="filter[HOTEL][<?=$arHotel["ID"]?>]" value="Y" <?if ($_GET["filter"]["HOTEL"][$arHotel["ID"]] == "Y" && $_GET["setFilter"] == "Y"){?>checked="checked"<?}?> onchange="show_items(this)"> 
                                <?=$arHotel["NAME"]?>
                            </label>
                            <?}
                        ?>
                    </div>
                </td>    
            </tr>  


            <tr>
                <td>Тур:</td>
                <td>
                    <?
                        $tour = CIBlockElement::GetList(array("ID"=>"ASC"/* "PROPERTY_DATE_FROM"=>"ASC"*/), array("IBLOCK_CODE"=>"TOUR","PROPERTY_COMPANY"=>getCurrentCompanyId()), false, false, array("ID","NAME","PROPERTY_DATE_FROM","PROPERTY_DATE_TO","PROPERTY_ROOM")); 
                    ?>
                    <select name="filter[TOUR]">
                        <option value="">-</option>
                        <?while($arTour = $tour->Fetch()){
                                $room_name = get_iblock_element_name($arTour["PROPERTY_ROOM_VALUE"]);
                            ?>
                            <option value="<?=$arTour["ID"]?>" <?if ($arTour["ID"] == $_GET["filter"]["TOUR"]){?>selected="selected" <?}?>>
                                #<?=$arTour["ID"]?> - [<?=$arTour["PROPERTY_DATE_FROM_VALUE"]." - ".$arTour["PROPERTY_DATE_TO_VALUE"]?>] <?=$arTour["NAME"]?>, <?=$room_name?>
                            </option>
                            <?}?>
                    </select>
                </td>
            </tr>

        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">
                    <input type="submit" value="Подобрать" /><input type="hidden" name="setFilter" value="Y" />&nbsp;&nbsp;
                    <input type="button" value="Сбросить" onclick="document.location.href='<?=$APPLICATION->GetCurPage()?>'"/></td>
            </tr>
        </tfoot>
    </table>


</form>
<div class="separator"></div>
<br>
