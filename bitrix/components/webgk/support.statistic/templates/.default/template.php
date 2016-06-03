<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent("bitrix:menu", "deskmanMenu", Array(
    "ROOT_MENU_TYPE" => "deskman",    
    "MENU_CACHE_TYPE" => "N",    
    "MENU_CACHE_TIME" => "3600",    
    "MENU_CACHE_USE_GROUPS" => "Y",   
    "MENU_CACHE_GET_VARS" => "",   
    "MAX_LEVEL" => "1",    
    "CHILD_MENU_TYPE" => "",    
    "USE_EXT" => "Y",   
    ),
    false
); 
?>   
<? if(count($arResult)>=1) { ?>
    <h1><?=GetMessage('SUPPORT_MAIN_TITLE')?></h1><br>
    
    <form id="staff-form" method="get">
        <?=GetMessage('SUPPORT_INTERVAL_TIME')?> 
        <div class="date-input">
            <?
                if (!empty($_REQUEST["date_fld"])){
                    $inputValue=$arResult["FILTER"][">=DATE"];
                }  else {
                    $inputValue="01.01.".date(Y);
                }
                if (!empty($_REQUEST["date_fld_finish"])){
                    $inputValueFinish=$arResult["FILTER"]["<=DATE"];
                }  else {
                    $inputValueFinish="31.12.".date(Y);
                }  
            ?>
            <?$APPLICATION->IncludeComponent(
                    "bitrix:main.calendar", 
                    ".default", 
                    array(
                        "COMPONENT_TEMPLATE" => ".default",
                        "SHOW_INPUT" => "Y",
                        "FORM_NAME" => "date_filter",
                        "INPUT_NAME" => "date_fld",
                        "INPUT_NAME_FINISH" => "date_fld_finish",
                        "INPUT_VALUE" => $inputValue,
                        "INPUT_VALUE_FINISH" => $inputValueFinish,
                        "SHOW_TIME" => "N",
                        "HIDE_TIMEBAR" => "Y"
                    ),
                    false
                );?> 
        </div>
        &nbsp; <br><br>
        <? if (empty($arResult["IS_CLIENT"])) {?>
        <?=GetMessage('SUPPORT_CLIENT')?> :
        <select id="user" name="user">
            <option value=""><?=GetMessage('SUPPORT_STAFF_ALL')?></option>
            <?  foreach ($arResult["CLIENT"] as $arUser) {?>
                <option value="<?=$arUser["ID"]?>" class="<?=$arUser["PROJECT_NAME"]?>" <? if($arResult["FILTER"]["ID"]==$arUser["ID"]) {?>selected="selected"<?}?>><?=$arUser["PROJECT_NAME"]?></option>
                <?}?>
        </select><br><br>
        <? } ?>
        
        <button type="submit"><?=GetMessage('SUPPORT_FIND')?></button>
        <button type="submit" name="IS_RESET" value="Y"><?=GetMessage('SUPPORT_RESET')?></button><br><br>

    </form><br>
    <?  if(!empty($arResult["FILTER"]["ID"])) { ?>
        <table id="statTable" class="statTable">
            <?   
                foreach  ($arResult["STAT"] as $yID => $year) {
                    krsort($year);
                ?>
                <tr>
                    <th colspan="6" class="yearTitle" style="text-transform: uppercase;"><?=$yID.' '.GetMessage('SUPPORT_YEAR')?></th>                 
                </tr>
                <?foreach  ($year as $mID => $month) {  ?>
                    <tr>
                        <th><?=GetMessage('SUPPORT_ID')?></th>
                        <th><?=GetMessage('SUPPORT_TITLE')?></th>
                        <th><?=GetMessage('SUPPORT_WASTED_TIME')?></th>
                        <th><?=GetMessage('SUPPORT_PRICE')?></th>
                        <th><?=GetMessage('SUPPORT_IN_PAY')?></th>
                        <th><?=GetMessage('SUPPORT_PAYED')?></th>
                    </tr>
                    <tr>
                        <th colspan="5" class="monthName" style="text-transform: uppercase;"><?=GetMessage('SUPPORT_MONTH_'.$mID)?></th>
                        <th class="monthName">
                            <?if ($USER->IsAdmin()) {?>
                                <input type="checkbox" class="month_tickets_month_<?=$mID?>_year_<?=$yID?>" month="<?=$mID?>" year="<?=$yID?>" onchange="payTickets('<?=$mID?>','<?=$yID?>','<?=$arResult["FILTER"]["ID"]?>',$(this).prop('checked'))" <?if ($month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["IS_PAYED"]!="N"){?> checked="checked"<?}?>>
                                <?} else {
                                    if ($month["MONTH_TOTAL_PAYED"] == "Y") {echo GetMessage('SUPPORT_YES');} else {echo GetMessage('SUPPORT_NO');}    
                            }?>
                        </th>
                    </tr>
                    <?
                        krsort($month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["TIME"]);
                        $i = 0;
                    ?>
                    <?foreach ($month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["TIME"] as $tID=>$ticketInfo){?>

                        <tr <?if ($i%2 != 0){?> style="background: #efefef;"<?}?>>
                            <td><?=$tID?></td>
                            <td><a href="<?=$ticketInfo["TICKET_DETAIL_PAGE"]?>" target="_blank"><?=$ticketInfo["TITLE"]?></a></td>  
                            <td align="center">
                                <?
                                    if (strlen($ticketInfo["MINUTES"]) == 1) {$ticketInfo["MINUTES"] = "0".$ticketInfo["MINUTES"];}
                                    echo $ticketInfo["HOURS"].":".$ticketInfo["MINUTES"];
                                ?>
                            </td>
                            <td align="center">
                                <?if ($ticketInfo["IN_PAYMENT"]=="Y"){ echo $ticketInfo["PRICE"];} else {echo 0;}?>
                            </td>
                            <td align="center">
                                <?switch ($ticketInfo["IN_PAYMENT"]){case "Y": echo GetMessage('SUPPORT_YES'); break; default: echo GetMessage('SUPPORT_NO'); break;}?>
                            </td>

                            <td align="center">    
                                <?if ($USER->IsAdmin()){?>                    
                                    <input type="checkbox" class="ticket_month_<?=$mID?>_year_<?=$yID?>" month="<?=$mID?>" year="<?=$yID?>" onchange="payTickets('<?=$mID?>','<?=$yID?>','<?=$arResult["FILTER"]["ID"]?>',$(this).prop('checked'), '<?=$tID?>')" <?if ($ticketInfo["IS_PAYED"] == "Y" || $ticketInfo["IN_PAYMENT"]=="N"){?> checked="checked"<?}?>>
                                    <?} else {
                                        if ($ticketInfo["IS_PAYED"] == "Y") {echo GetMessage('SUPPORT_YES');} else {echo GetMessage('SUPPORT_NO');}
                                }?>
                            </td>
                        </tr>
                        <? $i++; 
                        }
                    ?>
                    <tr>                
                        <td colspan="2" align="right"><b><?=GetMessage('SUPPORT_SUM_TICKET')?></b></td>
                        <td colspan="4"><b><?echo count($month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["TIME"])?></b></td>
                    </tr>
                    <tr>
                        <?
                            if (strlen($month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["MINUTES"]) == 1) {$month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["MINUTES"] = "0".$month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["MINUTES"];}
                        ?>
                        <td colspan="2" align="right"><b><?=GetMessage('SUPPORT_SUM_TIME')?></b></td>
                        <td colspan="4"><b><?echo $month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["HOURS"].":".$month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["MINUTES"];?></b></td>
                    </tr>
                    <tr >
                        <?
                            if (strlen($month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["MINUTES_TO_PAY"]) == 1) {$month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["MINUTES_TO_PAY"] = "0".$month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["MINUTES_TO_PAY"];}
                            if (strlen($month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["MINUTES_TO_PAY"]) == 0) {$month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["MINUTES_TO_PAY"] = "00";}
                            if (strlen($month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["HOURS_TO_PAY"]) == 0) {$month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["HOURS_TO_PAY"] = "0".$month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["HOURS_TO_PAY"];}
                        ?>
                        <td colspan="2" align="right"><b><?=GetMessage('SUPPORT_SUM_TIME_PAY')?></b></td>
                        <td colspan="4"><b><?echo $month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["HOURS_TO_PAY"].":".$month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["MINUTES_TO_PAY"];?></b></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="right"><b><?=GetMessage('SUPPORT_SUM_TO_PAY')?>:</b></td>
                        <td colspan="4">
                            <b><?=number_format($month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["PRICE"], 0, ',', ' ');?></b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="right"><b><?=GetMessage('SUPPORT_SUM_PAYED')?>:</b></td>
                        <td colspan="4">
                            <b><?=number_format($month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["PRICE_PAYED"], 0, ',', ' ');?></b>
                        </td>
                    </tr>
                    <tr <?if ($month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["PRICE"] != $month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["PRICE_PAYED"]){?> style="background: #FFA8C1;"<?}?>>
                        <td colspan="2" align="right"><b><?=GetMessage('SUPPORT_DEBT')?>:</b></td>
                        <td colspan="4">                     
                            <b><?=number_format($month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["PRICE"] - $month[$arResult["CLIENT"][$arResult["FILTER"]["ID"]]["PROJECT_NAME"]]["PRICE_PAYED"], 0, ',', ' ');?></b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6"></td>
                    </tr>
                    <?}?>
                <?}?>
        </table>
        <?
        } else if (count($arResult["STAT"])>=1){?>  
        <table class="statTable" >
            <?      
                foreach  ($arResult["STAT"] as $yID => $year) {
                    krsort($year);
                ?>
                <tr>
                    <th colspan="8" class="yearTitle" style="text-transform: uppercase;"><?=$yID.' '.GetMessage('SUPPORT_YEAR')?></th>                 
                </tr>
                <?foreach  ($year as $mID => $month) {  ?>
                    <tr>
                        <th colspan="8" class="monthName" style="text-transform: uppercase;"><?=GetMessage('SUPPORT_MONTH_'.$mID)?></th>                 
                    </tr>
                    <tr>
                        <th><?=GetMessage('SUPPORT_PROJECT')?></th>
                        <th><?=GetMessage('SUPPORT_TICKET')?></th>
                        <th><?=GetMessage('SUPPORT_HOURS')?></th>
                        <th><?=GetMessage('SUPPORT_IN_PAY')?></th>
                        <th><?=GetMessage('SUPPORT_HOURS_PAYED')?></th>
                        <th><?=GetMessage('SUPPORT_SUM_TO_PAY')?></th>
                        <th><?=GetMessage('SUPPORT_SUM_PAYED')?></th>
                        <th><?=GetMessage('SUPPORT_DEBT')?></th>
                    </tr>   
                    <?      
                        ksort($month);
                        foreach ($month as $uID => $user) {                     
                        ?>
                        <tr <?if ($i%2 != 0){?> style="background: #efefef;"<?}?>>
                            <td><a href="javascript:void(0)" onclick="clientClick(<?=$arResult["FILTER_CLIENT"][$uID]["ID"]?>);" title="<?=GetMessage('SUPPORT_TITLE_STAFF')?> <?=$user["NAME"]." ".$user["LAST_NAME"]?>"><?=$uID?></a></td>
                            <td align="center">
                                <?                                      
                                    echo $user["TICKET_TOTAL"];?>
                            </td>
                            <td align="center">
                                <?
                                    if (strlen($user["MINUTES"]) == 1 || strlen($user["MINUTES"]) == 0) {$user["MINUTES"] = "0".$user["MINUTES"];}
                                    echo $user["HOURS"].":".$user["MINUTES"];
                                ?>
                            </td>
                            <td align="center">
                                <?
                                    if (strlen($user["MINUTES_TO_PAY"]) == 1) {$user["MINUTES_TO_PAY"] = "0".$user["MINUTES_TO_PAY"];}
                                    if (strlen($user["MINUTES_TO_PAY"]) == 0) {$user["MINUTES_TO_PAY"] = "00";}
                                    if (strlen($user["HOURS_TO_PAY"]) == 0) {$user["HOURS_TO_PAY"] = "0".$user["HOURS_TO_PAY"];}
                                    echo $user["HOURS_TO_PAY"].":".$user["MINUTES_TO_PAY"];
                                ?>
                            </td>
                            <?
                                if (strlen($user["MINUTES_PAYED"]) == 1) {$user["MINUTES_PAYED"] = "0".$user["MINUTES_PAYED"];}
                                if (strlen($user["MINUTES_PAYED"]) == 0) {$user["MINUTES_PAYED"] = "00";}
                                if (strlen($user["HOURS_PAYED"]) == 0) {$user["HOURS_PAYED"] = "0".$user["HOURS_PAYED"];}    
                            ?>
                            <td align="center" <?if ($user["HOURS_TO_PAY"] != $user["HOURS_PAYED"] || $user["MINUTES_TO_PAY"]!=$user["MINUTES_PAYED"]){?> style="background: #FFA8C1;"<?}?>>
                                <?
                                    echo $user["HOURS_PAYED"].":".$user["MINUTES_PAYED"];
                                ?>
                            </td>
                            <td align="center">
                                <?
                                    echo $user["PRICE"];
                                ?>
                            </td>
                            <? if(!empty($user["PRICE_PAYED"])){
                                    $toPay = $user["PRICE_PAYED"];  
                                } else {
                                    $toPay = 0;
                                }
                            ?>
                            <td align="center">
                                <?
                                    echo $toPay;
                                ?>
                            </td>
                            <td align="center">
                                <?
                                    $debt = $user["PRICE"] - $toPay;
                                    echo $debt;
                                ?>
                            </td>
                        </tr>
                        <?}?>
                    <tr style="background: #91D1DB;">
                        <td align="right" ><b><?=GetMessage('SUPPORT_TOTAL')?></b></td>
                        <td align="center" ><b><?=count($arResult["TICKET_TOTAL_ALL"][$mID])?></b></td>
                        <td align="center"><b>
                                <?
                                    if (strlen($arResult["STAT_TIME"][$yID][$mID]["MINUTES"]) == 1) {$arResult["STAT_TIME"][$yID][$mID]["MINUTES"] = "0".$arResult["STAT_TIME"][$yID][$mID]["MINUTES"];}
                                    if (strlen($arResult["STAT_TIME"][$yID][$mID]["MINUTES"]) == 0) {$arResult["STAT_TIME"][$yID][$mID]["MINUTES"] = "00";}
                                    if (strlen($arResult["STAT_TIME"][$yID][$mID]["HOURS"]) == 0) {$arResult["STAT_TIME"][$yID][$mID]["HOURS"] = "0";}
                                    echo $arResult["STAT_TIME"][$yID][$mID]["HOURS"].":".$arResult["STAT_TIME"][$yID][$mID]["MINUTES"];
                                ?>
                            </b>
                        </td>
                        <td align="center"><b>
                                <?
                                    if (strlen($arResult["STAT_TIME"][$yID][$mID]["MINUTES_TO_PAY"]) == 1) {$arResult["STAT_TIME"][$yID][$mID]["MINUTES_TO_PAY"] = "0".$arResult["STAT_TIME"][$yID][$mID]["MINUTES_TO_PAY"];}
                                    if (strlen($arResult["STAT_TIME"][$yID][$mID]["MINUTES_TO_PAY"]) == 0) {$arResult["STAT_TIME"][$yID][$mID]["MINUTES_TO_PAY"] = "00";}
                                    if (strlen($arResult["STAT_TIME"][$yID][$mID]["HOURS_TO_PAY"]) == 0) {$arResult["STAT_TIME"][$yID][$mID]["HOURS_TO_PAY"] = "0";}
                                    echo $arResult["STAT_TIME"][$yID][$mID]["HOURS_TO_PAY"].":".$arResult["STAT_TIME"][$yID][$mID]["MINUTES_TO_PAY"];
                                ?>
                            </b>
                        </td>
                        <td align="center" <?if ($arResult["STAT_TIME"][$yID][$mID]["HOURS_PAYED"] != $arResult["STAT_TIME"][$yID][$mID]["HOURS_TO_PAY"] || $arResult["STAT_TIME"][$yID][$mID]["MINUTES_PAYED"]!=$arResult["STAT_TIME"][$yID][$mID]["MINUTES_TO_PAY"]){?> style="background: #FFA8C1;"<?}?>><b>
                                <?
                                    if (strlen($arResult["STAT_TIME"][$yID][$mID]["MINUTES_PAYED"]) == 1) {$arResult["STAT_TIME"][$yID][$mID]["MINUTES_PAYED"] = "0".$arResult["STAT_TIME"][$yID][$mID]["MINUTES_PAYED"];}
                                    if (strlen($arResult["STAT_TIME"][$yID][$mID]["MINUTES_PAYED"]) == 0) {$arResult["STAT_TIME"][$yID][$mID]["MINUTES_PAYED"] = "00";}
                                    if (strlen($arResult["STAT_TIME"][$yID][$mID]["HOURS_PAYED"]) == 0) {$arResult["STAT_TIME"][$yID][$mID]["HOURS_PAYED"] = "0";}
                                    echo $arResult["STAT_TIME"][$yID][$mID]["HOURS_PAYED"].":".$arResult["STAT_TIME"][$yID][$mID]["MINUTES_PAYED"];
                                ?>
                            </b></td>
                        <td align="center" ><b><?=$arResult["STAT_TIME"][$yID][$mID]["PRICE"]?></b></td>
                        <td align="center" ><b><?=$arResult["STAT_TIME"][$yID][$mID]["PRICE_PAYED"]?></b></td>
                        <td align="center" ><b><?=$arResult["STAT_TIME"][$yID][$mID]["PRICE"]-$arResult["STAT_TIME"][$yID][$mID]["PRICE_PAYED"]?></b></td>
                    </tr>    
                    <tr>
                        <td colspan="8"></td>
                    </tr>
                    <? } ?>
                <? } ?>
        </table>
        <?    
        } else {
            echo GetMessage('SUPPORT_EMPTY_LIST');
        }
    } else {
        echo GetMessage('SUPPORT_ATTENTION');    
    }
?>

<script>
    function payTickets(month, year, userID, checked, ticketID) {
        var url ="<?=$this->GetFolder()?>"+"/ajax.php";
        $.ajax({
            type: "GET",
            url: url,
            data: { month: month,
                year: year,
                userID: userID,
                checked: checked,
                ticketID: ticketID    
            }
        }).done(function(strResult) {
            var ticket = '.ticket_month_'+month+'_year_'+year;
            var monthTickets = '.month_tickets_month_'+month+'_year_'+year;
            var monthChecked = true;
            if(jQuery.isEmptyObject(ticketID)==false) {
                $(ticket).each(function(indx, element){
                    if ($(this).prop('checked')==false ) {
                        monthChecked = false;
                    }
                }); 
                $(monthTickets).prop('checked', monthChecked);
            } else if (jQuery.isEmptyObject(ticketID)==true) {
                $(ticket).prop('checked', $(monthTickets).prop('checked'));
            }
            $("#statTable").load("<?=$_SERVER["REQUEST_URI"]?> #statTable > *");
        }).error(function() {
            alert('<?=GetMessage("SUPPORT_ERROR")?>');
        });

    }       

    function clientClick(id){
        $('#user').val(id);
        $('#staff-form').submit();
    }
</script>