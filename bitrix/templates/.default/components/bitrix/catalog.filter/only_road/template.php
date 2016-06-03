<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<form name="arrFilter_form" action="<?=$APPLICATION->GetCurPage()?>" method="get">
    <table class="data-table" cellspacing="0" cellpadding="2" style="float: left;">
        <thead>
            <tr>
                <td colspan="2" align="center"><?=GetMessage("IBLOCK_FILTER_TITLE")?></td>
            </tr>
        </thead>
        <tbody>


            <tr>
                <td valign="top">Город:</td>
                <td valign="top">
                    <select name="city" id="city">  
                        <option value="0">Все</option>
                        <?
                            //выбираем города
                            $cities = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"CITY","PROPERTY_COMPANY"=>getCurrentCompanyID()));
                            while($arCity = $cities->Fetch()) {?>
                            <option value="<?=$arCity["ID"]?>" <?if ($arCity["ID"] == $_GET["city"] && $_GET["set_filter"] == "Y"){?>selected="selected"<?}?>><?=$arCity["NAME"]?></option>
                            <?}
                        ?>
                    </select>
                </td>
            </tr>   

            <tr>
                <td valign="top">Дата отправления:</td>
                <td valign="top">
                    <?$APPLICATION->IncludeComponent(
                            "bitrix:main.calendar",
                            "",
                            Array(
                                "SHOW_INPUT" => "Y",
                                "FORM_NAME" => "arrFilter_departure",
                                "INPUT_NAME" => "departure_date_begin",
                                "INPUT_NAME_FINISH" => "departure_date_end",
                                "INPUT_VALUE" => $_GET["departure_date_begin"],
                                "INPUT_VALUE_FINISH" => $_GET["departure_date_end"],
                                "SHOW_TIME" => "N",
                                "HIDE_TIMEBAR" => "Y"
                            ),
                            false
                        );?>  
                </td>
            </tr>


            <tr>
                <td valign="top">Дата прибытия:</td>
                <td valign="top">  <?$APPLICATION->IncludeComponent(
                            "bitrix:main.calendar",
                            "",
                            Array(
                                "SHOW_INPUT" => "Y",
                                "FORM_NAME" => "arrFilter_arrival",
                                "INPUT_NAME" => "arrival_date_begin",
                                "INPUT_NAME_FINISH" => "arrival_date_end",
                                "INPUT_VALUE" => $_GET["arrival_date_begin"],
                                "INPUT_VALUE_FINISH" => $_GET["arrival_date_end"],
                                "SHOW_TIME" => "N",
                                "HIDE_TIMEBAR" => "Y"
                            ),
                            false
                        );?> 

                </td>
            </tr>


            <tr>
                <td valign="top">Туда/обратно</td>
                <td valign="top">
                    <select name="bus_direction">
                        <option value="">-</option>
                        <?$busDirections = CIBlockPropertyEnum::GetList(Array(), Array("CODE"=>"BUS_DIRECTION"));
                            while($arBusDirection = $busDirections->Fetch()){?>
                            <option value="<?=$arBusDirection["ID"]?>" <?if ($_GET["bus_direction"] == $arBusDirection["ID"] && $_GET["set_filter"] == "Y"){?>selected="selected"<?}?>><?=$arBusDirection["VALUE"]?></option> 
                            <?}?>

                    </select>
                </td>            
            </tr>

        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    <input type="submit" value="Подобрать" /><input type="hidden" name="set_filter" value="Y" />&nbsp;&nbsp;
                    <input type="button" value="Сбросить" onclick="document.location.href='<?=$APPLICATION->GetCurPage()?>'"/></td>
            </tr>
        </tfoot>
    </table>

    <a class="add_button only_road_button" href="/tour_selection/tour/">Стандартный тур</a>
</form>
<div class="separator"></div>
