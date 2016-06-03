<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<?//arshow($_SESSION["filter"])?>

<?
    //echo "<pre>Template arParams: "; print_r($arParams); echo "</pre>";
    //echo "<pre>Template arResult: "; print_r($arResult); echo "</pre>";
    //exit();

    //arshow($arResult);
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


<form name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">

    <?=bitrix_sessid_post()?>

    <?if ($arParams["MAX_FILE_SIZE"] > 0):?><input type="hidden" name="MAX_FILE_SIZE" value="<?=$arParams["MAX_FILE_SIZE"]?>" /><?endif?>

    <?//arshow($arResult["ELEMENT_PROPERTIES"])?>
    <?if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])):?>
        <?foreach ($arResult["PROPERTY_LIST"] as $propertyID):?>
            <div class="form-group">
                <label><?if (intval($propertyID) > 0):?><?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?><?else:?><?=!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID)?><?endif?><?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?><span class="starrequired">*</span><?endif?></label>

                <? //arshow($arResult["PROPERTY_LIST_FULL"][$propertyID]);?>
                <?if ($propertyID == 75 /*схема автобуса*/) { ?>

                    <?} else {?> 
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
                        case "USER_TYPE":
                            for ($i = 0; $i<$inputNum; $i++)
                            {
                                if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
                                {
                                    $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["~VALUE"] : $arResult["ELEMENT"][$propertyID];
                                    $description = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["DESCRIPTION"] : "";
                                }
                                elseif ($i == 0)
                                {
                                    $value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
                                    $description = "";
                                }
                                else
                                {
                                    $value = "";
                                    $description = "";
                                }
                                echo "<br>".call_user_func_array($arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"],
                                    array(
                                        $arResult["PROPERTY_LIST_FULL"][$propertyID],
                                        array(
                                            "VALUE" => $value,
                                            "DESCRIPTION" => $description,
                                        ),
                                        array(
                                            "VALUE" => "PROPERTY[".$propertyID."][".$i."][VALUE]",
                                            "DESCRIPTION" => "PROPERTY[".$propertyID."][".$i."][DESCRIPTION]",
                                            "FORM_NAME"=>"iblock_add",
                                        ),
                                ));
                            ?>
                            <?
                            }
                            break;
                        case "TAGS":
                            $APPLICATION->IncludeComponent(
                                "bitrix:search.tags.input",
                                "",
                                array(
                                    "VALUE" => $arResult["ELEMENT"][$propertyID],
                                    "NAME" => "PROPERTY[".$propertyID."][0]",
                                    "TEXT" => 'size="'.$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"].'"',
                                ), null, array("HIDE_ICONS"=>"Y")
                            );
                            break;
                        case "HTML":
                            $LHE = new CLightHTMLEditor;
                            $LHE->Show(array(
                                'id' => preg_replace("/[^a-z0-9]/i", '', "PROPERTY[".$propertyID."][0]"),
                                'width' => '100%',
                                'height' => '200px',
                                'inputName' => "PROPERTY[".$propertyID."][0]",
                                'content' => $arResult["ELEMENT"][$propertyID],
                                'bUseFileDialogs' => false,
                                'bFloatingToolbar' => false,
                                'bArisingToolbar' => false,
                                'toolbarConfig' => array(
                                    'Bold', 'Italic', 'Underline', 'RemoveFormat',
                                    'CreateLink', 'DeleteLink', 'Image', 'Video',
                                    'BackColor', 'ForeColor',
                                    'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyFull',
                                    'InsertOrderedList', 'InsertUnorderedList', 'Outdent', 'Indent',
                                    'StyleList', 'HeaderList',
                                    'FontList', 'FontSizeList',
                                ),
                            ));
                            break;
                        case "T":
                            for ($i = 0; $i<$inputNum; $i++)
                            {

                                if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
                                {
                                    $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
                                }
                                elseif ($i == 0)
                                {
                                    $value = intval($propertyID) > 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
                                }
                                else
                                {
                                    $value = "";
                                }
                            ?>
                            <textarea class="form-control" id="property_<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"]?>" cols="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"]?>" rows="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"]?>" name="PROPERTY[<?=$propertyID?>][<?=$i?>]"><?=$value?></textarea>
                            <?
                            }
                            break;

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
                            <input class="form-control <?= $type_length;?>" id="property_<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"]?>" type="text" name="PROPERTY[<?=$propertyID?>][<?=$i?>]" size="25" value="<?=$value?>" /><br><?
                                if($arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"] == "DateTime"):?><?
                                    $APPLICATION->IncludeComponent(
                                        'bitrix:main.calendar',
                                        '',
                                        array(
                                            'FORM_NAME' => 'iblock_add',
                                            'INPUT_NAME' => "PROPERTY[".$propertyID."][".$i."]",
                                            'INPUT_VALUE' => $value,
                                        ),
                                        null,
                                        array('HIDE_ICONS' => 'Y')
                                    );
                                ?><br /><small><?=GetMessage("IBLOCK_FORM_DATE_FORMAT")?><?=FORMAT_DATETIME?></small><?
                                    endif
                            ?>
                            <?
                            }
                            break;

                        case "F":
                            for ($i = 0; $i<$inputNum; $i++)
                            {
                                $value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
                            ?>
                            <blockquote>
                                <input type="hidden" id="property_<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"]?>" name="PROPERTY[<?=$propertyID?>][<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>]" value="<?=$value?>" />
                                <input type="file" size="<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"]?>"  name="PROPERTY_FILE_<?=$propertyID?>_<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>" />
                                <?

                                    if (!empty($value) && is_array($arResult["ELEMENT_FILES"][$value]))
                                    {
                                    ?>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="property_<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"]?>" name="DELETE_FILE[<?=$propertyID?>][<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i?>]" id="file_delete_<?=$propertyID?>_<?=$i?>" value="Y">
                                            <?=GetMessage("IBLOCK_FORM_FILE_DELETE")?>
                                        </label>
                                    </div>
                                    <?

                                        if ($arResult["ELEMENT_FILES"][$value]["IS_IMAGE"])
                                        {
                                            /*$arNewFile = CIBlock::ResizeImageGet($arResult["ELEMENT_FILES"][$value], array(
                                            "WIDTH" => 700,
                                            "HEIGHT" => 700
                                            ));
                                            //__dump($arResult["ELEMENT_FILES"], true);
                                            __dump($arNewFile, true);*/
                                            //echo $arResult["ELEMENT_FILES"][$value]["PATH_IMAGE"];
                                            $imgFile = CFile::ResizeImageGet($arResult["ELEMENT_FILES"][$value], array('width'=>300, 'height'=>150), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                                            //var_dump($file);
                                            //__dump($file, true);
                                        ?>

                                        <!--<img src="<?/*=$arResult["ELEMENT_FILES"][$value]["SRC"]*/?>" height="<?/*=$arResult["ELEMENT_FILES"][$value]["HEIGHT"]*/?>" width="<?/*=$arResult["ELEMENT_FILES"][$value]["WIDTH"]*/?>" border="0" />-->
                                        <div>
                                            <a href="<?= $arResult["ELEMENT_FILES"][$value]["SRC"];?>" target="_blank"><img src="<?=$imgFile["src"]?>" width="<?=$imgFile["width"];?>px" height="<?=$imgFile["height"];?>px" class="img-thumbnail"/></a>
                                        </div>

                                        <?
                                        }
                                        else
                                        {
                                        ?>
                                        <div>
                                            <?=GetMessage("IBLOCK_FORM_FILE_NAME")?>: <?=$arResult["ELEMENT_FILES"][$value]["ORIGINAL_NAME"]?><br />
                                            <?=GetMessage("IBLOCK_FORM_FILE_SIZE")?>: <?=$arResult["ELEMENT_FILES"][$value]["FILE_SIZE"]?> b<br />
                                            [<a href="<?=$arResult["ELEMENT_FILES"][$value]["SRC"]?>"><?=GetMessage("IBLOCK_FORM_FILE_DOWNLOAD")?></a>]
                                        </div>
                                        <?
                                        }
                                    }
                                ?>
                            </blockquote>
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
                                    <label for="property_<?=$key?>">
                                        <?=$arEnum["VALUE"]?>
                                    </label>
                                </div>

                                <!-- <input type="<?/*=$type*/?>" name="PROPERTY[<?/*=$propertyID*/?>]<?/*=$type == "checkbox" ? "[".$key."]" : ""*/?>" value="<?/*=$key*/?>" id="property_<?/*=$key*/?>"<?/*=$checked ? " checked=\"checked\"" : ""*/?> /><label for="property_<?/*=$key*/?>"><?/*=$arEnum["VALUE"]*/?></label><br />-->
                                <?
                                }
                                break;

                            case "dropdown":
                            case "multiselect":
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
                    <?}?>
            </div>
            <?endforeach;?>

        <?if($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0):?>
            <!--<tr>
            <td><?/*=GetMessage("IBLOCK_FORM_CAPTCHA_TITLE")*/?></td>
            <td>
            <input type="hidden" name="captcha_sid" value="<?/*=$arResult["CAPTCHA_CODE"]*/?>" />
            <img src="/bitrix/tools/captcha.php?captcha_sid=<?/*=$arResult["CAPTCHA_CODE"]*/?>" width="180" height="40" alt="CAPTCHA" />
            </td>
            </tr>
            <tr>
            <td><?/*=GetMessage("IBLOCK_FORM_CAPTCHA_PROMPT")*/?><span class="starrequired">*</span>:</td>
            <td><input type="text" name="captcha_word" maxlength="50" value=""></td>
            </tr>-->
            <?endif?>
        <?endif?>

    <?/*<input type="submit" name="iblock_submit" value="<?=GetMessage("IBLOCK_FORM_SUBMIT")?>" class="btn btn-primary"/>*/?>
    <?/*if (strlen($arParams["LIST_URL"]) > 0 && $arParams["ID"] > 0){?>
        <input type="submit" name="iblock_apply" value="<?=GetMessage("IBLOCK_FORM_APPLY")?>" class="btn btn-success"/>
        <?} else {?>
        <input type="submit" name="iblock_submit" value="<?=GetMessage("IBLOCK_FORM_SUBMIT")?>" class="btn btn-primary"/>   
    <?}*/?>
    <?/*<input type="reset" value="<?=GetMessage("IBLOCK_FORM_RESET")?>" />*/?>
    <input type="submit" name="iblock_apply" value="<?=GetMessage("IBLOCK_FORM_SUBMIT")?>" class="btn btn-success"/>

    <br />
    <br />

    <?  
        //arshow($arResult);
        //получаем информацию о туристах
        $touristSelect = array("NAME","ID","PROPERTY_PASSPORT","PROPERTY_PHONE","PROPERTY_PLACE","PROPERTY_BIRTHDAY","PROPERTY_PRICE","PROPERTY_SECOND_PLACE");
        $tourist = CIBlockElement::GetList(array(), array("IBLOCK_CODE"=>"TOURIST","PROPERTY_ORDER"=>$arResult["ELEMENT"]["ID"]), false, false, $touristSelect);

        //собираем места туристов
        $places = array();
        $secondPlaces = array();
        $i = 1;
        while ($arTourist = $tourist->Fetch()){
            if ($arTourist["PROPERTY_PLACE_VALUE"]){
                $places[] = $arTourist["PROPERTY_PLACE_VALUE"]; 
            }
            if ($arTourist["PROPERTY_SECOND_PLACE_VALUE"]) {
                $secondPlaces[] = $arTourist["PROPERTY_SECOND_PLACE_VALUE"];  
            }
        }           

    ?>
    <?   //arshow($arResult);
        //получаем инфо о заказе
        $orderSelect = array(
            "NAME",
            "DATE_CREATE",
            "ID",
            "PROPERTY_TOUR",
            "PROPERTY_STATUS",
            "PROPERTY_OPERATOR_PRICE",
            "PROPERTY_PRICE",
            "PROPERTY_STATUS",
            "PROPERTY_TYPE_BOOKING",
            "PROPERTY_BUS_ID",
            "PROPERTY_SECOND_BUS_ID",
            "PROPERTY_DATE_FROM",
            "PROPERTY_CITY",
            "PROPERTY_HOTEL",
            "PROPERTY_COMPANY_NAME",
            "PROPERTY_DEPARTURE_CITY"
        );

        //собираем инфо о заказе
        $order = CIBlockElement::GetList(array(), array("ID"=>$arResult["ELEMENT"]["ID"],'PROPERTY_COMPANY'=>getCurrentCompanyID()), false, false, $orderSelect);
        $arOrder = $order->Fetch();

        //arshow($arOrder);

        //собираем инфо о туре
        $arSelect = array(
            "ID",
            "NAME",
            "PROPERTY_COMPANY",
            "PROPERTY_DIRECTION",
            "PROPERTY_CITY",
            "PROPERTY_ROOM",
            "PROPERTY_DATE_FROM",
            "PROPERTY_DATE_TO",
            "PROPERTY_PRICE",
            "PROPERTY_OPERATOR_PRICE",
            "PROPERTY_DISCOUNT",  
            "PROPERTY_HOTEL",
            "PROPERTY_PRICE_ADDITIONAL_SEATS",
            "PROPERTY_BUS_TO",
            "PROPERTY_BUS_BACK",
            "PROPERTY_DEPARTURE_CITY"
        );


        if ($arOrder["PROPERTY_TOUR_VALUE"] > 0) {


            $arFilter = array("IBLOCK_CODE"=>"TOUR","ID"=>$arOrder["PROPERTY_TOUR_VALUE"],"PROPERTY_COMPANY"=>getCurrentCompanyID());

            $tour = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
            $arTourData = $tour->Fetch();

            //для двойного тура
            if ($arOrder["PROPERTY_TYPE_BOOKING_VALUE"] == "двойной тур") {
                $secondTourID = checkDoubleTour($arTourData["ID"]);  
                $arFilter = array("IBLOCK_CODE"=>"TOUR","ID"=>$secondTourID,"PROPERTY_COMPANY"=>getCurrentCompanyID());

                $secondTour = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
                $arSecondTour = $secondTour->Fetch();  
            }

        }

        //только проезд
        else {

            $arFilter = array("IBLOCK_CODE"=>"TOUR","PROPERTY_COMPANY"=>getCurrentCompanyID());

            //проверяем автобус
            $bus = CIBlockElement::GetList(array(), array("ID"=>$arOrder["PROPERTY_BUS_ID_VALUE"]), false, false, array("PROPERTY_P_SCHEME","PROPERTY_BUS_DIRECTION"));
            $arBus = $bus->Fetch();  

            //двойной проезд 
            if ($arOrder["PROPERTY_SECOND_BUS_ID_VALUE"] > 0) {
                $busScond = CIBlockElement::GetList(array(), array("ID"=>$arOrder["PROPERTY_SECOND_BUS_ID_VALUE"]), false, false, array("PROPERTY_P_SCHEME","PROPERTY_BUS_DIRECTION"));          
                $arBusSecond = $busScond->Fetch();

            }

            $arSelect = array(
                "ID",
                "NAME",
                "PROPERTY_COMPANY",
                "PROPERTY_DIRECTION",
                "PROPERTY_DATE_FROM",
                "PROPERTY_DATE_TO",
                "PROPERTY_PRICE",
                "PROPERTY_OPERATOR_PRICE",
                "PROPERTY_DISCOUNT",    
                "PROPERTY_BUS_TO",
                "PROPERTY_BUS_BACK",
                "PROPERTY_DEPARTURE_CITY",                
            );

            switch($arBus["PROPERTY_BUS_DIRECTION_VALUE"]) {
                case "Туда": $arFilter["PROPERTY_BUS_TO"] = $arOrder["PROPERTY_BUS_ID_VALUE"]; break;
                case "Обратно": $arFilter["PROPERTY_BUS_BACK"] = $arOrder["PROPERTY_BUS_ID_VALUE"]; break;
            }



            $tour = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
            $arTourData = $tour->Fetch();                


        }   
    ?>
    <table class="data-table">
        <tr>
            <th colspan="6">ЗАКАЗ</th>
        </tr>
        <tr>
            <th>№</th>
            <th>Агентство</th>
            <th>Дата создания</th>
            <th>Тип бронирования</th>
            <th>Статус</th>
            <th>Город забора туристов</th>
        </tr>
        <tr>
            <td><?=$arResult["ELEMENT"]["ID"]?></td>
            <td><?=$arOrder["PROPERTY_COMPANY_NAME_VALUE"]?></td>
            <td><?=$arOrder["DATE_CREATE"]?></td>
            <td><?=$arOrder["PROPERTY_TYPE_BOOKING_VALUE"]?></td>
            <td><?=$arOrder["PROPERTY_STATUS_VALUE"]?></td>
            <td>
                <?if ($arOrder["PROPERTY_DEPARTURE_CITY_VALUE"] > 0){?>
                    <?=get_iblock_element_name($arOrder["PROPERTY_DEPARTURE_CITY_VALUE"])?> (<?=getTransferPrice($arOrder["PROPERTY_DEPARTURE_CITY_VALUE"])?>руб)            
                    <?}?>
            </td>
        </tr>
    </table>

    <?
        //проверяем статус пользователя (является ли он менеджером туроператора)
        $userID = $USER->GetId();
        $meStatus = checkUserStatus($userID); //статус текущего пользователя
        $status = checkUserStatus($arResult["ELEMENT"]["CREATED_BY"]); //статус создателя заказа
        $groups = getUserGroup($userID);                       
    ?> 

    <br>
    <div class="print_buttons">
        <?
            if ($arOrder["PROPERTY_STATUS_VALUE"] == "Заказ одобрен" ) {?>
            <a id="print_button" class="add_button print_button" href="/order-management/order/confirm.php?print=Y&ID=<?=$arResult["ELEMENT"]["ID"]?>" target="_blank">Печатать подтверждение</a>
            <a class="add_button print_button" href="/order-management/order/payorder.php?print=Y&ID=<?=$arResult["ELEMENT"]["ID"]?>" target="_blank">Печатать приходник</a>
            <a class="add_button print_button" href="/order-management/order/bill.php?print=Y&ID=<?=$arResult["ELEMENT"]["ID"]?>" target="_blank">Печатать<Br>счет</a>
            <?if (checkUserStatus($USER->GetID()) == "Y"){?>
                <a class="add_button print_button" href="/order-management/order/tourlist.php?print=Y&ID=<?=$arResult["ELEMENT"]["ID"]?>" target="_blank">Печатать<Br>путевку</a>
                <a class="add_button print_button" href="/order-management/order/contract.php?print=Y&ID=<?=$arResult["ELEMENT"]["ID"]?>" target="_blank">Печатать<Br>договор</a>

                <?}?>
            <?}?> 
    </div>

    <br>


    <? 
        //только проезд
        if (!$arOrder["PROPERTY_TOUR_VALUE"])
        {
        ?>
        <table class="data-table">
            <tr>
                <th>НАПРАВЛЕНИЕ ПРОЕЗДА</th>
            </tr>
            <tr>
                <th><?=$arBus["PROPERTY_BUS_DIRECTION_VALUE"]?></th>                 
            </tr>    
        </table> 
        <br>
        <?}?>


    <table class="data-table">
        <tr>
            <th colspan="8">ТУР</th>
        </tr>
        <tr>
            <th>№</th>
            <th>Название</th>
            <th>Дата отправления</th>
            <th>Дата прибытия</th>
            <th>Направление</th>  
            <th>Город</th>
            <th>Гостиница</th>
            <th>Номер</th>
        </tr>
        <tr>
            <td><?=$arOrder["PROPERTY_TOUR_VALUE"]?></td>
            <td><?=get_iblock_element_name($arOrder["PROPERTY_TOUR_VALUE"])?></td>
            <td><?=$arTourData["PROPERTY_DATE_FROM_VALUE"]?></td>
            <td>
                <?if ($arOrder["PROPERTY_TYPE_BOOKING_VALUE"] == "двойной тур"){?>
                    <?=$arSecondTour["PROPERTY_DATE_TO_VALUE"]?>
                    <?} else {?>
                    <?=$arTourData["PROPERTY_DATE_TO_VALUE"]?>
                    <?}?>
            </td>
            <td><?=get_iblock_element_name($arTourData["PROPERTY_DIRECTION_VALUE"])?></td>
            <td><?=get_iblock_element_name($arTourData["PROPERTY_CITY_VALUE"])?></td>
            <td><?=get_iblock_element_name($arTourData["PROPERTY_HOTEL_VALUE"])?></td>
            <td><?=get_iblock_element_name($arTourData["PROPERTY_ROOM_VALUE"])?></td>
        </tr>
    </table>


    <br>


    <table class="data-table">
        <tr>
            <th colspan="9">ТУРИСТЫ</th>
        </tr>
        <tr>
            <th>№</th>
            <th>ФИО</th>
            <th>Пасспорт</th>
            <th>Дата рождения</th>
            <th>Телефон</th>
            <th>Доп. место</th>
            <th>Доп. услуги</th>
            <th>Стоимость</th>
            <th>Метод расчета</th>
        </tr>

        <?
            //собираем туристов
            $touristSelect = array(
                "ID",
                "NAME",
                "PROPERTY_PASSPORT",
                "PROPERTY_PHONE",
                "PROPERTY_PRICE",
                "PROPERTY_ADD_PLACE",
                "PROPERTY_BIRTHDAY",
                "PROPERTY_MATH_METHOD"             
            );
            $tourist = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"TOURIST","PROPERTY_ORDER"=>$arResult["ELEMENT"]["ID"]),false, false, $touristSelect);

            while($arTourist = $tourist->Fetch()) {?>
            <tr>
                <td><?=$arTourist["ID"]?></td>
                <td><?=$arTourist["NAME"]?></td>
                <td><?=$arTourist["PROPERTY_PASSPORT_VALUE"]?></td>
                <td><?=$arTourist["PROPERTY_BIRTHDAY_VALUE"]?></td>
                <td><?=$arTourist["PROPERTY_PHONE_VALUE"]?></td>
                <td><?=$arTourist["PROPERTY_ADD_PLACE_VALUE"]?></td>
                <td>
                    <?
                        //собираем услуги туриста
                        $services = CIBlockElement::GetList(array(), array("ID"=>$arTourist["ID"]), false, false, array("PROPERTY_SERVICES"));
                        while($arService = $services->Fetch()) {?>
                        <?=get_iblock_element_name($arService["PROPERTY_SERVICES_VALUE"])?><br>
                        <?}?>
                </td>
                <td><?=$arTourist["PROPERTY_PRICE_VALUE"]?></td>
                <td>
                    <?  //arshow($arTourist);
                        if ($arTourist["PROPERTY_MATH_METHOD_VALUE"] > 0){
                            echo get_iblock_element_name($arTourist["PROPERTY_MATH_METHOD_VALUE"]);
                        } else {
                            echo "-";
                        }
                    ?>
                </td>

            </tr>
            <? } ?>
    </table>

    <br>

    <table class="data-table">     
        <tr>
            <th>СТОИМОСТЬ ЗАКАЗА</th>
            <th>К ОПЛАТЕ</th>
        </tr>

        <tr>               
            <td><?=$arOrder["PROPERTY_PRICE_VALUE"]?></td>
            <td>

                <?if ((!in_array("TOUR_OPERATOR",$groups) && $meStatus != "Y") || (in_array("TOUR_OPERATOR",$groups) && $status != "Y")){?>
                    <?=$arOrder["PROPERTY_OPERATOR_PRICE_VALUE"]?>
                    <?} else {echo $arOrder["PROPERTY_PRICE_VALUE"];}?>
            </td>
        </tr>
    </table>

    <?
        //получаем информацию об автобусе для данного тура BUS_ON_TOUR
        $ID = $arTourData["PROPERTY_BUS_TO_VALUE"];

        //только проезд
        if (!$arOrder["PROPERTY_TOUR_VALUE"]) {
            $ID = $arOrder["PROPERTY_BUS_ID_VALUE"];
        }

        $bus = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"BUS_ON_TOUR","ID"=>$ID), false, false, array("NAME","ID","PROPERTY_P_SCHEME"));
        $arBus = $bus->Fetch();
        // arshow($places);
        //arshow($arBus);

        if ($arTourData["PROPERTY_BUS_BACK_VALUE"] > 0) {
            $busSecond = CIBLockElement::GetLIst(array(),array("IBLOCK_CODE"=>"BUS_ON_TOUR","ID"=>$arTourData["PROPERTY_BUS_BACK_VALUE"]), false, false, array("NAME","ID","PROPERTY_P_SCHEME"));
            $arBusSecond = $busSecond->Fetch(); 
        }

        //обрабатываем схему 
        //преобразуем схему в ассоциативный массив
        $scheme = json_decode($arBus["PROPERTY_P_SCHEME_VALUE"], true);
        //перебираем схему, чтобы убрать с нее все лишние места, и оставить только места текущего заказа
        foreach($scheme as $n=>$val) {
            foreach ($val as $i=>$place){
                //все места кроме текущих показываем как свободные
                if (!in_array($i,$places) && $place == "FP") {
                    $scheme[$n][$i] = "FP"; 
                }

                else if (!in_array($i,$places) && $place == "PP") {
                    $scheme[$n][$i] = "NP"; 
                }

                else if (in_array($i,$places)){
                    $scheme[$n][$i] = "CP";
                }
            }
        }
        //кодируем схему обратно    
        $scheme_new = json_encode($scheme);  
    ?>     

    <?if (count($places) > 0){?>
        <div class="booking">
            <div class="bookingBusSchemeTop">
                <div class="bookingBusScheme">
                    <div class="twoBus">                        
                        <div class="busTable_0">
                            <?get_bus_scheme($scheme_new); ?>                         
                        </div>   
                    </div> 
                </div>

            </div>     
        </div>             
        <?}?>

    <?if (count($secondPlaces) > 0){?>
        <h3>Автобус обратно</h3>
        <?

            //преобразуем схему в ассоциативный массив
            $scheme = json_decode($arBusSecond["PROPERTY_P_SCHEME_VALUE"], true);
            //перебираем схему, чтобы убрать с нее все лишние места, и оставить только места текущего заказа
            foreach($scheme as $n=>$val) {
                foreach ($val as $i=>$place){
                    //все места кроме текущих показываем как свободные
                    if (!in_array($i,$secondPlaces) && $place == "FP") {
                        $scheme[$n][$i] = "FP"; 
                    }
                    else if (!in_array($i,$secondPlaces) && $place == "PP") {
                        $scheme[$n][$i] = "NP"; 
                    }

                    else if (in_array($i,$secondPlaces)){
                        $scheme[$n][$i] = "CP";
                    }
                }
            }
            //кодируем схему обратно    
            $scheme_new = json_encode($scheme);  
        ?>


        <div class="booking">
            <div class="bookingBusSchemeTop">
                <div class="bookingBusScheme">
                    <div class="twoBus">                        
                        <div class="busTable_0">
                            <?get_bus_scheme($scheme_new); ?>                         
                        </div>   
                    </div> 
                </div>

            </div>

        </div>


        <?}?>



    <b><a href="javascript:void(0)" onclick="window.top.location.reload()">назад к списку</a></b>

</form>