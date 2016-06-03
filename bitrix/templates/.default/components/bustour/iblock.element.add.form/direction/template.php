<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<?//arshow($_SESSION["filter"])?>
<script>
    function select_from_filter(prop_name,value) {
        $("#property_" + prop_name ).val(value);
    }

    //проверка на помесячную стоимость проезда
    function month_road_check(){
        if ($("#property_ROAD_PRICE_BY_MONTH").attr("checked") == "checked") { 

            $(".form-control").each(function(){
                var check = $(this).attr("id").indexOf("MONTH_PRICE");
                if (check > 0) {
                    $(this).removeAttr("disabled");
                    $(this).siblings("label").removeAttr("style");
                    //$(this).val($("#property_ROAD_PRICE").val());
                }  
            })
        $("#property_ROAD_PRICE").val("");    
        $("#property_ROAD_PRICE").attr("disabled","disabled");
        } 
        else {
            $(".form-control").each(function(){
                var check = $(this).attr("id").indexOf("MONTH_PRICE");
                if (check > 0) {
                    $(this).attr("disabled","disabled");   
                    $(this).siblings("label").css("color","#ddd");
                    $(this).val("");
                }  
            })
            $("#property_ROAD_PRICE").removeAttr("disabled");
        }
        
        
    }


    $(function(){
        <?  //если в сессии есть фильтр для текущего раздела, то по умолчанию у селектов выбираем значения из фильтра
            if ($_SESSION["filter"]["URL"] == $APPLICATION->GetCurPage()){?>
            <?foreach ($_SESSION["filter"] as $code=>$val) {
                if (is_array($val)){
                    foreach ($val as $id=>$prop){ 
                    ?>
                    select_from_filter('<?=$code?>','<?=$id?>');        
                    <?}}
                else {?>
                select_from_filter('<?=$code?>','<?=$val?>');    
                <?}
            }?>
            <?}?>


        month_road_check();

        $("#property_ROAD_PRICE_BY_MONTH").change(function(){
            month_road_check();  
        })


        //проверяем город отправления - если не указан, то подставляем город по умолчанию
        <?
            $d_city_default = CIBLockElement::GetList(array(), array("IBLOCK_CODE"=>"DEPARTURE_CITY","PROPERTY_DEFAULT_VALUE"=>"Да","PROPERTY_COMPANY"=>getCurrentCompanyID()), false, false, array("ID"));
            $arD_city_default = $d_city_default->Fetch();
        ?>
        var cur_city = $("#property_DEPARTURE_CITY").val();
        if (!parseInt(cur_city)) {
            $("#property_DEPARTURE_CITY").val(<?=$arD_city_default["ID"]?>)  
        }



        //проверка на заполнение поля "только проезд"
        $("#direction_submit").submit(function(){
            if ($("#property_ROAD_PRICE").val() == "" && $("#property_ROAD_PRICE_BY_MONTH").attr("checked") != "checked"){
                alert('Заполните поле "Только проезд" или укажите стоимость проезда помесячно');
                return false;
            }
        })
        
        $("input[type=text]").keyup(function(){
            var check = $(this).attr("id").indexOf("PRICE");
                if (check > 0) {
                     $(this).val($(this).val().replace(/\D+/,''));
                }  
          
        })
        
        


    })



</script>
<?
    //echo "<pre>Template arParams: "; print_r($arParams); echo "</pre>";
    //echo "<pre>Template arResult: "; print_r($arResult); echo "</pre>";
    //exit();
?>

<?if (count($arResult["ERRORS"])):?>
    <?=ShowError(implode("<br />", $arResult["ERRORS"]))?>
    <?endif?>
<?if (strlen($arResult["MESSAGE"]) > 0):?>
    <?=ShowNote($arResult["MESSAGE"])?>
    <script>
        $(function(){
            window.top.location.reload();
        })
    </script>
    <?endif?>

<div class="direction_edit_block">  


    <form name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data" id="direction_submit">

        <?=bitrix_sessid_post()?>


        <?//arshow($arResult["ELEMENT_PROPERTIES"])?>
        <?if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])):?>
            <?foreach ($arResult["PROPERTY_LIST"] as $propertyID):?>

                <?if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"] == "ROAD_PRICE"){?>
                    <h4>Стоимость проезда</h4>
                    <?}?>
                <?if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"] == "DOUBLE_TOUR"){?>
                    <h4>Доступные типы бронирования</h4>
                    <?}?>

                <?
                    //для помесячной стоимости добавляем отдельный класс, чтобы блоки выводились в 2 столбца через float
                    $class = "";
                    if (strpos($arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"],"NTH_PRICE_") > 0) {
                        $class = "table_view";
                    }
                ?>

                <div class="form-group <?=$class?>">
                    <?if (!in_array($arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"],array("ONLY_ROAD","ONLY_ROOM","DOUBLE_TOUR","ROAD_PRICE_BY_MONTH","ACTIVE"))){?>
                        <label><?if (intval($propertyID) > 0):?><?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?><?else:?><?=!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID)?><?endif?><?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?><span class="starrequired">*</span><?endif?></label>
                        <?}?>
                    <? //arshow($arResult["PROPERTY_LIST_FULL"][$propertyID]);?>                      
                    <?
                        //echo "<pre>"; print_r($arResult["PROPERTY_LIST_FULL"]); echo "</pre>";
                        if (intval($propertyID) > 0)
                        {
                            if (
                                $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "T"
                                &&
                                $arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] == "1"
                            )
                                $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "S";
                            elseif (
                                (
                                    $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "S"
                                    ||
                                    $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "N"
                                )
                                &&
                                $arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] > "1"
                            )
                                $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "T";
                        }
                        elseif (($propertyID == "TAGS") && CModule::IncludeModule('search'))
                            $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "TAGS";

                        if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y")
                        {
                            $inputNum = ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) ? count($arResult["ELEMENT_PROPERTIES"][$propertyID]) : 0;
                            $inputNum += $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE_CNT"];
                        }
                        else
                        {
                            $inputNum = 1;
                        }

                        if($arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"])
                            $INPUT_TYPE = "USER_TYPE";
                        else
                            $INPUT_TYPE = $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"];

                        switch ($INPUT_TYPE):   

                        case "S":
                        case "N":
                            for ($i = 0; $i<$inputNum; $i++)
                            {
                                if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
                                {
                                    $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
                                }
                                elseif ($i == 0)
                                {
                                    $value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];

                                }
                                else
                                {
                                    $value = "";
                                }
                            ?>
                            <?

                                switch ($arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"]) {
                                    case "S": $type_length = "input-xxlarge";
                                        break;
                                    case "N": $type_length = "input-medium";
                                        break;
                                    default: $type_length = '';
                                }
                                ($arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"]);?>
                            <input class="form-control <?= $type_length;?>" id="property_<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"]?>" type="text" name="PROPERTY[<?=$propertyID?>][<?=$i?>]" size="25" value="<?=$value?>" />

                            <?
                            }
                            break;

                        case "L":
                            if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["LIST_TYPE"] == "C")
                                $type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" || count($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"]) == 1 ? "checkbox" : "radio";
                            else
                                $type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "multiselect" : "dropdown";

                            switch ($type):
                            case "checkbox": 
                            case "radio":

                                //echo "<pre>"; print_r($arResult["PROPERTY_LIST_FULL"][$propertyID]); echo "</pre>";

                                foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
                                {
                                    $checked = false;
                                    if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
                                    {
                                        if (is_array($arResult["ELEMENT_PROPERTIES"][$propertyID]))
                                        {
                                            foreach ($arResult["ELEMENT_PROPERTIES"][$propertyID] as $arElEnum)
                                            {
                                                if ($arElEnum["VALUE"] == $key) {$checked = true; break;}
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if ($arEnum["DEF"] == "Y") $checked = true;
                                    }

                                ?>
                                <div class="<?=$type?>">
                                    <input type="<?=$type?>" id="property_<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"]?>" name="PROPERTY[<?=$propertyID?>]<?=$type == "checkbox" ? "[".$key."]" : ""?>" value="<?=$key?>" id="property_<?=$key?>"<?=$checked ? " checked=\"checked\"" : ""?> />
                                    <label for="property_<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"]?>"><?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?></label>
                                </div>      
                                <?
                                }
                                break; 



                                endswitch;
                            break;  


                        case "E":

                            if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["LIST_TYPE"] == "C")
                                $type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "checkbox" : "radio";
                            else
                                $type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "multiselect" : "dropdown";

                            switch ($type):
                            case "checkbox":
                            case "radio":

                                //echo "<pre>"; print_r($arResult["PROPERTY_LIST_FULL"][$propertyID]); echo "</pre>";

                                foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
                                {
                                    $checked = false;
                                    if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
                                    {
                                        if (is_array($arResult["ELEMENT_PROPERTIES"][$propertyID]))
                                        {
                                            foreach ($arResult["ELEMENT_PROPERTIES"][$propertyID] as $arElEnum)
                                            {
                                                if ($arElEnum["VALUE"] == $key) {$checked = true; break;}
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if ($arEnum["DEF"] == "Y") $checked = true;
                                    }

                                ?>
                                <div class="<?=$type?>">
                                    <input type="<?=$type?>" id="property_<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"]?>" name="PROPERTY[<?=$propertyID?>]<?=$type == "checkbox" ? "[".$key."]" : ""?>" value="<?=$key?>" id="property_<?=$key?>"<?=$checked ? " checked=\"checked\"" : ""?> />
                                    <label for="property_<?=$key?>">
                                        <?=$arEnum["VALUE"]?>
                                    </label>
                                </div>
                                <?
                                }
                                break;

                            case "dropdown":
                            case "multiselect":
                                //__dump($arResult["ELEMENT_PROPERTIES"][$propertyID]);die;
                            ?>
                            <select class="form-control input-xlarge" id="property_<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"]?>" name="PROPERTY[<?=$propertyID?>]<?=$type=="multiselect" ? "[]\" size=\"".$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"]."\" multiple=\"multiple" : ""?>">
                                <option value=""><?echo GetMessage("CT_BIEAF_PROPERTY_VALUE_NA")?></option>
                                <?
                                    if (intval($propertyID) > 0) $sKey = "ELEMENT_PROPERTIES";
                                    else $sKey = "ELEMENT";


                                    foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum)
                                    {
                                        $checked = false;
                                        if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
                                        {
                                            foreach ($arResult[$sKey][$propertyID] as $elKey => $arElEnum)
                                            {
                                                if ($key == $arElEnum["VALUE"]) {$checked = true; break;}
                                            }

                                            /*foreach ($arResult[$sKey][$propertyID] as $elKey => $arElEnum)
                                            {
                                            if ($arParams["ID"] == $arElEnum["ID"]) {$checked = true; break;}
                                            }*/
                                        }
                                        else
                                        {
                                            if ($arEnum["DEF"] == "Y") $checked = true;
                                        }
                                    ?>
                                    <option value="<?=$key?>" <?=$checked ? " selected=\"selected\"" : ""?>><?=$arEnum["VALUE"]?></option>
                                    <?
                                    }
                                ?>
                            </select>
                            <?
                                break;

                                endswitch;
                            break;

                            endswitch;?>
                    <?if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["HINT"]){?>
                        <div class="hint">
                            <?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["HINT"]?>
                        </div>
                        <?}?>
                </div>
                <?endforeach;?>  
            <?endif?>

        <input type="submit" name="iblock_apply" value="<?=GetMessage("IBLOCK_FORM_SUBMIT")?>" class="btn btn-success"/>

        <br />
        <br />
        <b><a href="javascript:void(0)" onclick="window.top.location.reload()">назад к списку</a></b>

    </form>

</div>  