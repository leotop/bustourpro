<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<?//arshow($_SESSION["filter"])?>
<script>
    function select_from_filter(prop_name,value) {
        $("#property_" + prop_name ).val(value);
    }

    <?
        //если в сессии есть фильтр для текущего раздела, то по умолчанию у селектов выбираем значения из фильтра
        if ($_SESSION["filter"]["URL"] == $APPLICATION->GetCurPage()){?>
        $(function(){
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
        })

        <?}?>

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
<form name="iblock_add" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">

    <?=bitrix_sessid_post()?>

    <?if ($arParams["MAX_FILE_SIZE"] > 0):?><input type="hidden" name="MAX_FILE_SIZE" value="<?=$arParams["MAX_FILE_SIZE"]?>" /><?endif?>

    <?//arshow($arResult["ELEMENT_PROPERTIES"])?>
    <?if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])):?>
        <?foreach ($arResult["PROPERTY_LIST"] as $propertyID):?>
        
        <?  
                    //для помесячной стоимости добавляем отдельный класс, чтобы блоки выводились в 2 столбца через float
                    $class = "";
                    if ($propertyID != "NAME" && $arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"] != "TYPE") {
                        $class = "table_view_discounts";
                    }
                    
                    if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"] == "TYPE" || strpos($arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"],"PLEX") > 0) {
                        $class = "w100";
                    }
                    ?>
        
            <div class="form-group <?=$class?>">
                <label><?if (intval($propertyID) > 0):?><?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]?><?else:?><?=!empty($arParams["CUSTOM_TITLE_".$propertyID]) ? $arParams["CUSTOM_TITLE_".$propertyID] : GetMessage("IBLOCK_FIELD_".$propertyID)?><?endif?><?if(in_array($propertyID, $arResult["PROPERTY_REQUIRED"])):?><span class="starrequired">*</span><?endif?></label>

                <? //arshow($arResult["PROPERTY_LIST_FULL"][$propertyID]);?>
                <?if ($propertyID == 75 /*схема автобуса*/) { ?>
                    <script>                    
                        controllerAction = "bus/update";   
                    </script>
                    <input type="hidden" id="Bus_scheme" value="<?=$arResult["ELEMENT_PROPERTIES"][$propertyID][0]["VALUE"]?>"  name="PROPERTY[75][0]">                    

                    <table summary="" class="viewBusTable">
                        <tr>
                            <th>Схема расположения мест</th>
                            <th>Условные обозначения мест</th>
                        </tr>
                        <tr>
                            <td>
                                <div>    
                                    <table class="busTableWrap" summary="">
                                        <tr>
                                            <td>
                                                <table class="busTable" summary="">
                                                    <tr>
                                                        <td><div class="createBusItemsDiv"><div class="rowSign">Ряд 1</div><input type="hidden" name="r_1_c_1" value="FP" /></div></td>                                                        
                                                    </tr>
                                                </table>
                                            </td>
                                            <td>
                                                <div class="addDeletePlace">
                                                    <a id="addColumn" href="#">+</a>
                                                    <a id="deleteColumn" href="#">-</a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="addDeletePlace">
                                                    <a id="addRow" href="#">+</a>
                                                    <a id="deleteRow" href="#">-</a>
                                                </div>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                            <td>

                                <table summary="" class="legendTable">

                                    <tr>
                                        <td>
                                            <div class="legendPlaceWP">&nbsp;</div>
                                        </td>
                                        <td>
                                            <b>Сопровождающие</b>.<br/><sup>*</sup>Число мест для сопровождающих<br/> должно быть <b>кратно 2</b>!
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="legendPlaceDP">&nbsp;</div>
                                        </td>
                                        <td>
                                            <b>Второй выход</b>.<br/><sup>*</sup>Число мест под двери<br/>должно быть <b>кратно 2</b>.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="legendPlaceNP">&nbsp;</div>
                                        </td>
                                        <td>
                                            <b>Служебные отметки (проход)</b>.
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <div class="legendPlaceFP">&nbsp;</div>
                                        </td>
                                        <td>
                                            <b>Доступные для<br/>бронирования места</b>.
                                        </td>
                                    </tr>

                                </table>
                            </td>
                        </tr>
                    </table>
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
                                echo call_user_func_array($arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"],
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
                            <input class="form-control <?= $type_length;?>" id="property_<?=$arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"]?>" type="text" name="PROPERTY[<?=$propertyID?>][<?=$i?>]" size="25" value="<?=$value?>" /><?
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
    <b><a href="javascript:void(0)" onclick="window.top.location.reload()">назад к списку</a></b>

</form>