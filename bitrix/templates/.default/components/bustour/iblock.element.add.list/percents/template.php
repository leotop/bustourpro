<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    //echo "<pre>"; print_r($arParams); echo "</pre>";
    //echo "<pre>"; print_r($arResult); echo "</pre>";
    $colspan = 2;
    if ($arResult["CAN_EDIT"] == "Y") $colspan++;
    if ($arResult["CAN_DELETE"] == "Y") $colspan++;
?>
<script>

    $(function(){
        //заглушка от введения неверных символов (не цифр)
        $(".percent_field").keyup(function(){
            $(this).val($(this).val().replace(/\D+/,''));
        })
    })

    //обновляем проценты для выбранного типа и направления
    function checkPercent(type,direction) {
        var new_val = $("#percent_" + type + "_" + direction).val();

        $.post("/ajax/percentsChange.php",
            {type:type,direction:direction,value:new_val},
            function(data){
                /*
                $("#success").css("display","block");
                $("#success").html("Изменения сохранены");
                $("#success").fadeOut(2000);   
                */
            } 
        )
    }

</script>
<?//arshow($arResult["ELEMENTS"])?>
<?if (strlen($arResult["MESSAGE"]) > 0):?>
    <?=ShowNote($arResult["MESSAGE"])?>
    <?endif?>   

<table class="data-table">
    <?if($arResult["NO_USER"] == "N"):?>

        <?
            //соберем направление и варианты услуг
            $companyDirections = array();
            $companyServices = array();
            $directions = CIBlockElement::GetList(array("ID"=>"ASC"),array("IBLOCK_CODE"=>"DIRECTION","PROPERTY_COMPANY"=>getCurrentCompanyID()), false, false, array("ID","NAME"));
            while($arDirection = $directions->Fetch()) {
                $companyDirections[$arDirection["ID"]] = array("ID"=>$arDirection["ID"], "NAME"=>$arDirection["NAME"]);  
            }
            ////
            $iblock = CIBLock::GetList(array(),array("CODE"=>"PERCENTS"));
            $arIblock = $iblock->Fetch();
            $services = CIBlockPropertyEnum::GetList(array("ID"=>"ASC"),array("CODE"=>"TYPE_BOOKING","IBLOCK_ID"=>$arIblock["ID"]));
            while($arService = $services->Fetch()) {
                $companyServices[$arService["ID"]] = array("ID"=>$arService["ID"],"NAME"=>$arService["VALUE"]);  
            }



        ?>
        <tr>
            <td rowspan="2">
                <b>Тип услуг</b> 
            </td>
            <td colspan="<?=count($companyDirections)?>">
                <b>Скидка по направлениям, %</b>
            </td>
        </tr>

        <tr>
            <? foreach ($companyDirections as $direction) { ?>
                <td><?=$direction["NAME"]?></td>  
                <?}?>
        </tr>


        <?
            foreach ($companyServices as $service) {?>
            <tr>
                <td><?=$service["NAME"]?></td>

                <? foreach ($companyDirections as $direction) { 
                        //проверяем, существует ли запись для текущей пары тип/направление
                        $typeCheck = CIBlockElement::GetList(array(),array("IBLOCK_CODE"=>"PERCENTS","PROPERTY_COMPANY"=>getCurrentCompanyID(),"PROPERTY_DIRECTION"=>$direction["ID"],"PROPERTY_TYPE_BOOKING"=>$service["ID"]),false ,false, array("PROPERTY_DISCOUNT"));
                        if ($arTypeCheck = $typeCheck->Fetch()) {
                            $discount = $arTypeCheck["PROPERTY_DISCOUNT_VALUE"];
                        }  
                        else {
                            $discount = "";
                        }
                    ?>
                    <td align="center"><input id="percent_<?=$service["ID"]?>_<?=$direction["ID"]?>" type="text" value="<?=$discount?>" class="percent_field" onchange="checkPercent(<?=$service["ID"]?>,<?=$direction["ID"]?>)"></td>  
                    <?}?>

            </tr>    
            <?}?>



        <?endif?>
</table>
<br>
<p>*Если значение скидки не указано, оно берется из значения "минимальная скидка для турагентств"</p>
<br>
<p id="success"></p>

<a class="save_button" href="javascript:void(0)" onclick="window.location.reload()">Сохранить</a>
<?if (strlen($arResult["NAV_STRING"]) > 0):?><?=$arResult["NAV_STRING"]?><?endif?>