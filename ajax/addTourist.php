<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<?$i = date("U");?>
<tr class="tourist">
    <td><input type="text" value="" name="Tourist[<?=$i?>][name]"></td>
    <td><input type="text" value="" name="Tourist[<?=$i?>][passport]"></td>
    <td><input type="text" value="" name="Tourist[<?=$i?>][phone]"></td>
    <td class="birthday">
        <?$APPLICATION->IncludeComponent(
                "bitrix:main.calendar",
                "order",
                Array(
                    "SHOW_INPUT" => "Y",
                    "FORM_NAME" => "ORDER_MAKE",
                    "INPUT_NAME" => "Tourist[".$i."][birthday]",
                    "INPUT_NAME_FINISH" => "",
                    "INPUT_VALUE" => "",
                    "INPUT_VALUE_FINISH" => "",
                    "SHOW_TIME" => "N",
                    "HIDE_TIMEBAR" => "Y"
                ),
                false
            );?>

        <input type="hidden" value="<?=$arResult["CUR_PLASES"][$i-1]?>" name="Tourist[<?=$i?>][place]">
    </td>



    <td align="center">
        <input type="checkbox" value="Y" class="add_place_checker" name="Tourist[<?=$i?>][add]" <?if ($arResult["ROOM"]["PLACES"] >= $arResult["PLACES_COUNT"]){?>disabled="disabled"<?}?> onchange="getTourPrice(this);">
    </td>


    <?//собираем доп услуги
        $services = CIBLockELement::GetList(array(), array("IBLOCK_CODE"=>"SERVICES","PROPERTY_COMPANY"=>getCurrentCompanyID()), false, false, array("ID","NAME","PROPERTY_PRICE"));
        while($arService = $services->Fetch()){
        ?>   
        <td align="center">
            <input class="service_checker" type="checkbox" value="<?=$arService["ID"]?>" name="Tourist[<?=$i?>][services][]" onchange="getTourPrice(this);">
        </td>
        <?}?>

    <td align="center">
        <?/*<input type="checkbox" name="use_full_price" class="use_full_price" value="Y" onchange="getTourPrice(this);">  */?>
        <select name="Tourist[<?=$i?>][math]" class="mathMethod" onchange="getTourPrice(this);">
            <option value="N">Без скидки</option>
        </select> 
    </td>

    <td>
        <input type="text" value="<?=$arResult["PRICE"]?>" name="Tourist[<?=$i?>][price]" class="tourist_price" >
        <input type="hidden" class="tour_price" value="" name="Tourist[<?=$i?>][tour_price]">
    </td>

    <td><a href="javascript:void(0)" onclick="removeTourist(this)">Удалить</a></td>

</tr>