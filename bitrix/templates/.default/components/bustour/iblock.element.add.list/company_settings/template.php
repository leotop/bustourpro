<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    //echo "<pre>"; print_r($arParams); echo "</pre>";
    //echo "<pre>"; print_r($arResult); echo "</pre>";
    $colspan = 2;
    if ($arResult["CAN_EDIT"] == "Y") $colspan++;
    if ($arResult["CAN_DELETE"] == "Y") $colspan++;
?>
<?//arshow($arResult["ELEMENTS"])?>
<?if (strlen($arResult["MESSAGE"]) > 0):?>
    <?=ShowNote($arResult["MESSAGE"])?>
    <?endif?>      


<?foreach ($arResult["ELEMENTS"] as $arElement):
    if ($arElement["ID"] == getCurrentCompanyID()) {
    ?>
    <table class="data-table">
        <tr>
            <td><b>Мои настройки</b></td>
            <?if ($arResult["CAN_EDIT"] == "Y"):?>
                <td><?if ($arElement["CAN_EDIT"] == "Y"):?><a class="fancybox" href="<?=$arParams["EDIT_URL"]?>?edit=Y&amp;CODE=<?=$arElement["ID"]?>"><?=GetMessage("IBLOCK_ADD_LIST_EDIT")?><?else:?>&nbsp;<?endif?></a></td>
                <?endif?>   
        </tr>
        <?foreach ($arElement["PROPERTIES"] as $prop) {?>
            <tr>
                <td><?=$prop["NAME"]?></td>
                <td>
                    <?//arshow($prop)?>
                    <?if ($prop["VALUE_ENUM"] != "" && $prop["PROPERTY_TYPE"] != "N") {  
                            echo $prop["VALUE_ENUM"]; 
                        } 
                        else if ($prop["VALUE_ENUM"] == "" && $prop["PROPERTY_TYPE"] != "N" && !$prop["VALUE"]) {
                            echo "Нет";
                        }                          
                        else {       
                            if ($prop["PROPERTY_TYPE"] == "F")   //файл
                            {?> 
                            <a href="<?=CFile::GetPath($prop["VALUE"])?>" target="_blank">посмотреть</a>
                            <?}
                            else {  
                                echo $prop["VALUE"];
                            }
                        }
                    ?>
                </td>
            </tr>       
            <?}?>
    </table>
    <?break; }?>
    <?endforeach?>
   

<?if (strlen($arResult["NAV_STRING"]) > 0):?><?=$arResult["NAV_STRING"]?><?endif?>