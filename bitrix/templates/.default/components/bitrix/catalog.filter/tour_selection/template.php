<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//arshow($_GET)?>

<form name="arrFilter_form" action="<?=$APPLICATION->GetCurPage()?>" method="get">
    <table class="data-table" cellspacing="0" cellpadding="2" style="float: left;">
        <thead>
            <tr>
                <td colspan="4" align="center"><?=GetMessage("IBLOCK_FILTER_TITLE")?></td>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td valign="top">Дата начала тура:</td>
                <td valign="top">
                    <?$APPLICATION->IncludeComponent(
                            "bitrix:main.calendar",
                            "",
                            Array(
                                "SHOW_INPUT" => "Y",
                                "FORM_NAME" => "arrFilter_form",
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

                <td valign="top">Количество взрослых:</td>
                <td valign="top">
                    <select name="people_quantity">
                        <option value="0">-</option>
                        <?for ($i = 1; $i <= 10; $i++){?>
                            <option value="<?=$i?>" <?if ($i == $_GET["people_quantity"] && $_GET["set_filter"] == "Y"){?>selected="selected"<?}?>><?=$i?></option>
                            <?}?>
                    </select>
                </td>
            </tr>

            <tr>
                <td valign="top">Курорт:<br>
                    <div class="filter_block" id="cities">
                        <label for="city[0]" >
                            <input type="checkbox" value="0" name="city[]" id="city[0]" onchange="show_items(this);" <?if (in_array(0,$_GET["city"])){?>checked="checked" <?}?> class="default_value city"> 
                            Все</label>
                        <?
                            //выбираем города
                            $cities = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"CITY","PROPERTY_COMPANY"=>getCurrentCompanyID(),"PROPERTY_ACTIVE_VALUE"=>"Да"));
                            while($arCity = $cities->Fetch()) {?>
                            <label  for="city<?=$arCity["ID"]?>">
                                <input class="city" type="checkbox" value="<?=$arCity["ID"]?>" id="city<?=$arCity["ID"]?>" name="city[]" <?if (in_array($arCity["ID"],$_GET["city"]) && $_GET["set_filter"] == "Y"){?>checked="checked"<?}?> onchange="show_items(this);"> 
                            <?=$arCity["NAME"]?></label>
                            <?}
                        ?>
                    </div> 


                </td>


                <td valign="top">Гостиница:<br>
                    <div class="filter_block" id="hotels">                            
                        <label for="hotel[0]" rel="0" class="city0">
                            <input type="checkbox" value="0" name="hotel[]" id="hotel[0]" rel="0" onchange="show_items(this)"  class="default_value"> 
                            Все</label>
                        <?
                            //выбираем гостиницу
                            $h_filter = array("IBLOCK_CODE"=>"HOTEL","PROPERTY_COMPANY"=>getCurrentCompanyID(),"PROPERTY_ACTIVE_VALUE"=>"Да");                         
                            $hotels = CIBlockElement::GetList(array(), $h_filter, false, false, array("ID","NAME","PROPERTY_CITY"));
                            while($arHotel = $hotels->Fetch()) {?>
                            <label for="hotel[<?=$arHotel["ID"]?>]" rel="<?=$arHotel["PROPERTY_CITY_VALUE"]?>" class="city<?=$arHotel["PROPERTY_CITY_VALUE"]?>">
                                <input type="checkbox" id="hotel[<?=$arHotel["ID"]?>]"  name="hotel[]" value="<?=$arHotel["ID"]?>" <?if (in_array($arHotel["ID"],$_GET["hotel"]) && $_GET["set_filter"] == "Y"){?>checked="checked"<?}?> onchange="show_items(this)"> 
                                <?=$arHotel["NAME"]?>
                            </label>
                            <?}
                        ?>
                    </div>
                </td>



                <td valign="top">Количество детей:</td>
                <td valign="top">
                    <select name="children_quantity">
                        <option value="0">-</option>
                        <?for ($i = 1; $i <= 10; $i++){?>
                            <option value="<?=$i?>" <?if ($i == $_GET["children_quantity"] && $_GET["set_filter"] == "Y"){?>selected="selected"<?}?>><?=$i?></option>
                            <?}?>
                    </select>
                </td>
            </tr>           



        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">
                    <input type="submit" value="Подобрать" /><input type="hidden" name="set_filter" value="Y" />&nbsp;&nbsp;
                    <input type="button" value="Сбросить" onclick="document.location.href='<?=$APPLICATION->GetCurPage()?>'"/></td>
            </tr>
        </tfoot>
    </table>
    <a class="add_button only_road_button" href="/tour_selection/road/">Только проезд</a>   

</form>
<div class="separator"></div>
