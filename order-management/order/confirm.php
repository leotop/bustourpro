<?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
    $APPLICATION->SetTitle("Заказы");
?>  

<?
    $arOrder = getOrderInfo(intval($_GET["ID"]));

    if (!is_array($arOrder)) {
        echo $arOrder;
        die();
    }   

    // arshow($arOrder);
?>       

<table class = "confirmTable">
    <tr>
        <td>
            <div class="confirmHeadDivPrint">
                <?/* <img src="/i/rating.jpg"> */?>
            </div>

            <div class="conformCompanyInfo">
                <div>
                    <div><?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_COMPANY_ADRESS"]?></div>
                    <div><?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_COMPANY_PHONE"]?></div>
                    <div>email: <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_COMPANY_EMAIL"]?></div>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="confirmTitleDivPrint">
                <b>Подтверждение</b>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="confirmClientDivPrint">
                <b>Заказчик</b>
            </div>
        </td>
    </tr>
    <tr>
        <td class="confirmClientTablePrint">
            <table summary = "">
                <tr>
                    <td>название фирмы, город</td>
                    <td>
                        <b><?=$arOrder["CREATED_BY"]["NAME"]?>
                            <?if ($arOrder["CREATED_BY"]["UF_COMPANY_CITY"]){?>, 
                                <?=$arOrder["CREATED_BY"]["UF_COMPANY_CITY"]?>
                                <?}?>
                        </b>
                    </td>
                </tr>
                <tr>
                    <td>телефон</td>
                    <td>
                        <b><?=$arOrder["CREATED_BY"]["PERSONAL_PHONE"]?></b>
                    </td>
                </tr>
                <tr>
                    <td>телефон компании</td>
                    <td>
                        <b><?=$arOrder["CREATED_BY"]["PERSONAL_PHONE"]?></b>
                    </td>
                </tr>
                <tr>
                    <td>факс</td>
                    <td>
                        <b></b>
                    </td>
                </tr>
                <tr>
                    <td>e-mail</td>
                    <td>
                        <b><?=$arOrder["CREATED_BY"]["EMAIL"]?></b>
                    </td>
                </tr>
                <tr>
                    <td>менеджер</td>
                    <td>
                        <b><?=$arOrder["CREATED_BY"]["SECOND_NAME"]?></b>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <div class="confirmRequestDivPrint">
                <b>Заявка</b>
            </div>
        </td>
    </tr>
    <tr>
        <td class="confirmRequestTablePrint">
            <table summary ="" width="900">
                <tr>
                    <td>название тура</td>
                    <td width="500">
                        <b><?=$arOrder["NAME"]?></b>
                    </td>
                </tr>
                <tr>
                    <td>даты тура</td>
                    <td>
                        <b>
                            <?=$arOrder["TOUR"]["DATE_FROM"]?>
                            <?if ($arOrder["TOUR"]["DATE_FROM"]) {?>
                                - <?=$arOrder["TOUR"]["DATE_TO"]?>,
                                <?}?> <?=$arOrder["TYPE_BOOKING"]["NAME"]?>
                            <?if ($arOrder["BUS_DIRECTION"] && $arOrder["TYPE_BOOKING"]["NAME"] != "только проезд (туда и обратно)"){?>,
                                <?=$arOrder["BUS_DIRECTION"]?>
                                <?}?>
                        </b>
                    </td>
                </tr>
                <tr>
                    <td>размещение</td>
                    <td>
                        <b>
                            <?if ($arOrder["HOTEL"]["NAME"]){?>
                                гостиница "<?=$arOrder["HOTEL"]["NAME"]?>", г. <?=$arOrder["CITY"]["NAME"]?>
                                <?}?>
                        </b>
                    </td>
                </tr>
                <tr>
                    <td>категория номера</td>
                    <td>
                        <b><?=$arOrder["TOUR"]["ROOM"]["NAME"]?></b>
                    </td>
                </tr>                
            </table>
        </td>
    </tr>
    <tr>
        <td class = "confirmTouristTablePrint">
            <table summary = "">
                <tr>
                    <th>№</th>
                    <th>Туристы</th>
                    <th width="100">Дата<br>рождения</th>
                    <th width="150">Паспорт/<br>свидетельство</th>
                    <th width="130">Телефон</th>
                    <th width="200">Доп. услуги</th>
                </tr>
                <?  $i = 1;
                    foreach ($arOrder["TOURISTS"] as $tourist) {?>
                    <tr>  
                        <td><?=$i?></td>
                        <td><?=$tourist["NAME"]?></td>
                        <td><?=$tourist["BIRTHDAY"]?></td>
                        <td><?=$tourist["PASSPORT"]?></td>
                        <td><?=$tourist["PHONE"]?></td>
                        <td>
                            <?foreach ($tourist["SERVICES"] as $service) {?>
                                <?=get_iblock_element_name($service)."<br>"?>
                                <?}?>
                        </td>
                    </tr>    
                    <?$i++;}?>
            </table>
        </td>
    </tr>
    <tr>
        <td class = "confirmPlaceTablePrint">
            <table summary = "">
                <tr>
                    <td>места в автобусе</td>
                    <td style="width:680px;">
                        <b>
                            <?if ($arOrder["TYPE_BOOKING"]["NAME"] == "двойной тур" || $arOrder["TYPE_BOOKING"]["NAME"] == "только проезд (туда и обратно)"){?>
                                туда: 
                                <?}?>
                            <?                          
                                foreach ($arOrder["PLACES"] as $p=>$place) {
                                    echo getPlaceName($place);
                                    if ($p != count($arOrder["PLACES"])- 1) {
                                        echo ", ";
                                    }
                            }?>
                            <?if ($arOrder["TYPE_BOOKING"]["NAME"] == "двойной тур" || $arOrder["TYPE_BOOKING"]["NAME"] == "только проезд (туда и обратно)"){?>
                                ; обратно: 
                                <?foreach ($arOrder["SECOND_PLACES"] as $p=>$place) {
                                    echo getPlaceName($place);
                                    if ($p != count($arOrder["PLACES"])- 1) {
                                        echo ", ";
                                    }
                                }?>
                                <?}?>   
                        </b>
                    </td>
                </tr>

                <?if ($arOrder["DEPARTURE_CITY"] > 0) {?>
                    <tr>
                        <td>город забора туристов</td>
                        <td>
                            <b><?=get_iblock_element_name($arOrder["DEPARTURE_CITY"]);?></b>
                        </td>
                    </tr> 
                    <?}?>

                <tr>
                    <td>ответственный менеджер</td>
                    <td>
                        <b><?=$arOrder["CREATED_BY"]["SECOND_NAME"]?></b>
                    </td>
                </tr>
                <tr>
                    <td>стоимость тура</td>
                    <td>
                        <b><?=$arOrder["PRICE"]?></b>
                    </td>
                </tr>                 
                <tr>
                    <td>к оплате</td>
                    <td>
                        <b><?=$arOrder["PRICE_AGENCY"]?></b>
                    </td>
                </tr>
                <tr> 
                    <td>
                        <div class="confirmDateDivPrint">
                            <b>Дата <?=substr($arOrder["DATE_CREATE"],0,10)?></b> 
                        </div>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <div class="confirmStampDivPrint">
                            <div>М.П.</div>
                            <?if ($arOrder["COMPANY"]["STAMP"]){?>
                                <img src="<?=CFile::GetPath($arOrder["COMPANY"]["STAMP"])?>" />
                                <?}?>                                
                        </div>
                    </td>
                    <td></td>        
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td>
            <p class="attention">
                Перевозчик оставляет за собой право без предварительного предупреждения поменять место пассажира по причине смены автобуса во время поездки, 
                в целях обеспечения безопасности пассажиров и экипажа и /или с целью максимально качественного осуществления перевозки, 
                в связи с возникновением обстоятельств по изменению в комплектации групп(ы) пассажиров.
            </p>
        </td>
    </tr>

</table>



<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>