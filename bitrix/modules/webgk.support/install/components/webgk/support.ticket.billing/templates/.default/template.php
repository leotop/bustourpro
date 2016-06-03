<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?global $USER;
$uID = $USER->GetId();
?>
<?if (GKSupportUsers::GetClientId() > 0 || in_array(GKSupport::GetSupportEmployerGroupID(),CUser::GetUserGroup($uID)) || $USER->IsAdmin()){?>     

    <a href="javascript:void(0)" class="billingTableControl">
        <span class="addHours"><?=GetMessage('BILLING_TITLE')?></span>
        <span class="hideBillingTable"><?=GetMessage('BILLING_HIDE')?></span>
    </a>
    <br><br>

    <form name="ticketBilling" id="ticketBilling" method="post">
        <div class="billingTimeTable">
            <table>
                <tr>
                    <th><?=GetMessage('BILLING_DATE')?></th>
                    <th><?=GetMessage('BILLING_USER')?></th>
                    <th><?=GetMessage('BILLING_SPENT_TIME')?></th>
                    <th><?=GetMessage('BILLING_SERVICE')?></th>
                    <th><?=GetMessage('BILLING_COMMENT')?></th>
                    <th><?=GetMessage('BILLING_ACTIONS')?></th>                                        
                </tr>

                <?
                    foreach ($arResult["ITEMS"] as $iID=>$arItem){?>

                    <tr>
                        <td><?=$arItem['DATE']?></td>
                        <td><?
                            $rsUser = CUser::GetByID($arItem['USER_ID']);
                            $arUser = $rsUser->Fetch();
                            echo $arUser['NAME'].' '.$arUser['LAST_NAME'];
                        ?></td>
                        <td class="tableHours">
                            <p><?=$arItem['HOURS']?>:<?=$arItem['MINUTES']?></p>
                        </td>
                        <td>
                            <?=$arResult["SERVICES"][$arItem["SERVICE_ID"]]["NAME"]?>
                        </td>
                        <td>
                            <p><?=$arItem['COMMENT']?></p>
                        </td>                                             

                        <td>
                            <?if($uID==$arItem['USER_ID'] || $USER->IsAdmin()){?>
                                <input class="deleteBillingPosition" data-action="delete" value="X" type="submit" rel="<?=$arItem["ID"]?>"/>
                                <?}?>
                        </td>
                    </tr>    

                    <?}?>

                <?if(in_array(GKSupport::GetSupportEmployerGroupID(),CUser::GetUserGroup($uID)) || $USER->IsAdmin()){?>
                    <tr class="billingEditRow">
                        <td></td>
                        <td></td>
                        <td class="tableHours">                                            
                            <div>
                                <label for="billingHour">
                                    <input name="billingHour" class="fieldsForBilling" type="text" autocomplete="off"/>
                                    <?=GetMessage("BILLING_HH")?>
                                </label>
                            </div>
                            <div>
                                <label for="billingMinute">
                                    <input name="billingMinute" class="fieldsForBilling" type="text" autocomplete="off"/>
                                    <?=GetMessage("BILLING_MM")?>
                                </label>
                            </div>
                        </td>
                        <td>
                            <?
                                $service = GKSupportServices::GetList($by="ID",$sort="ASC",array());
                            ?>
                            <select name="serviceID" id="serviceID" autocomplete="off">
                                <?foreach ($arResult["SERVICES"] as $arService){?>
                                    <option value="<?=$arService["ID"]?>"><?=$arService["NAME"]?></option>
                                    <?}?>
                            </select>
                        </td>
                        <td>
                            <textarea name="billingComment" class="fieldsForBilling" id="billingComment" cols="30" rows="3"></textarea>
                        </td>                                              
                        <td>                      
                            <input class="submitBilling" data-action="add" value="<?=GetMessage("BILLING_ADD")?>" type="submit" name="submitBilling"/>
                        </td>
                    </tr>
                    <?}?>
            </table>
            <br>
            <br>
        </div>
    </form>
    <div>
        <?=GetMessage("BILLING_TOTAL_TIME");?>
        <span class="billingTotal">
            <?=$arResult["TOTAL_TIME"];?>
        </span>
    </div>
    <br>
    <!--End of billing table-->    

    <script>
        ////
        $(function(){

            $('body').on('click', '.submitBilling,.deleteBillingPosition', function(e) {

                e.preventDefault();

                switch(e.target.dataset.action) {
                    case 'add':

                        $(".fieldsForBilling").each(function(){
                            if ($(this).val() == "") {
                                return false;
                            }
                        })

                        $(".fieldsForBilling").each(function(){
                            if ($(this).val() == '') {
                                $(this).addClass("wrongData");
                            } 
                            else {
                                $(this).removeClass("wrongData");
                            }
                        })

                        var hours = $('input[name="billingHour"]').val();
                        var minutes = $('input[name="billingMinute"]').val();
                        var comment = $('textarea[name="billingComment"]').val();
                        var ticket_id = <?=$arResult["TICKET_ID"]?>;
                        var service_id = $('select[name="serviceID"]').val();

                        if (hours && minutes && comment && ticket_id && service_id) {


                            $.post("<?=$this->GetFolder()?>/billingAjax.php", {
                                hours : hours,
                                minutes : minutes,
                                comment : comment,
                                ticket_id : ticket_id,
                                service_id: service_id,
                                action : e.target.dataset.action
                                }, function(data) {
                                    hoursFromMinutes = 0;
                                    $(data).insertBefore('.billingEditRow');
                                    billingTotalTime = $('.billingTotal').text().split(':');

                                    newMinutesSum = parseInt(billingTotalTime[1]) + parseInt(minutes);
                                    if (newMinutesSum > 59) {
                                        hoursFromMinutes = parseInt(newMinutesSum / 60);
                                        newMinutesSum = newMinutesSum % 60;
                                    }
                                    $('.billingTotal').text((parseInt(billingTotalTime[0]) + hoursFromMinutes + parseInt(hours)) + ':' + newMinutesSum);


                            });  

                            $('.fieldsForBilling').each(function() {
                                $(this).val('');
                            })

                        }



                        break;
                    case 'delete':

                        var itemId = $(e.target).attr("rel");                    
                        delete_time = $(e.target).closest('tr').children('td.tableHours').children('p').text().split(':');
                        billingTotalTime = $('.billingTotal').text().split(':');
                        $(e.target).closest('tr').remove();

                        var ticket_id = <?=$arResult["TICKET_ID"]?>;

                        hoursAfterDelete = parseInt(billingTotalTime[0]) - parseInt(delete_time[0]);
                        minutesAfterDelete = parseInt(billingTotalTime[1]) - parseInt(delete_time[1]);

                        if (minutesAfterDelete < 0) {
                            hoursAfterDelete -= 1;
                            minutesAfterDelete = 60 - (parseInt(delete_time[1]) - parseInt(billingTotalTime[1]));
                        }

                        $('.billingTotal').text(hoursAfterDelete + ':' + minutesAfterDelete);

                        $.post("<?=$this->GetFolder()?>/billingAjax.php", {
                            ticket_id : ticket_id,
                            action : e.target.dataset.action,
                            item_id: itemId
                            }, function(data) {
                                $('.fieldsForBilling').each(function() {
                                    $(this).val('');
                                })   
                        });



                        break;
                }


            })

        })
    </script> 


    <?}?>  
    
       