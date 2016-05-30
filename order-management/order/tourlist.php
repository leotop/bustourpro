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

    $cur_month = get_month_name(date("m"));

    //arshow($arOrder);

    $price = explode(".",$arOrder["PRICE"]);
    if (strlen($price[1]) == 1) {
        $price[1] = $price[1]*10;
    }

    $priceNew = implode(".",$price);

    $dateFrom = explode(".",$arOrder["TOUR"]["DATE_FROM"]);
    $dateTo = explode(".",$arOrder["TOUR"]["DATE_TO"]);
    $dateCreate = explode(".",substr($arOrder["DATE_CREATE"],0,10));
?>
<style>

    body {padding:0; margin:0; color:#000 !important; font-family: arial; font-size:11px !important;} 
    table {border-collapse: separate;}    
    .tour_list_main {width:1000px; height:700px;  background: #fff;}
    .tour_list_main td.main_td {padding:10px; vertical-align: top;}
    .tour_list_left_part {width:650px;  border-right:1px dashed black}
    .tour_list_left_part table {margin: 0;}
    .tour_list_left_part td {padding:0}

    .tour_list_right_part table {margin:0}
    .tour_list_right_part td {padding:0}

    .line2_table  {width:100%; border-color:black}
    .line2_table td {padding:0; text-align: center; border-color:black}

    .tour_list_logo {width:650px; height:111px; overflow:hidden;}
    .tour_list_logo_s {width:300px; height:90px; overflow:hidden;}
    .tour_list_logo p {text-align:center;font-size:14px;}

    .tour_list_logo_s p {text-align:center;font-size:12px;}


</style>
<div class="printCenter">
    <table class="tour_list_main">
        <tr>
            <!--Левая часть -->
            <td class="tour_list_left_part main_td">                                                                                                                            

                <div class="tour_list_logo">
                    <p><?=$arOrder["COMPANY"]["COMPANY_FULL_NAME"]?>, ИНН <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_INN"]?>, ОКПО <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_COMPANY_OKPO"]?>. тел. <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_COMPANY_PHONE"]?></p> 
                </div>
                <h1 style="text-transform: uppercase; font-size: 16px !important; text-align: center; margin:0; font-weight: bold;">Туристическая путевка №<?=$arOrder["ID"]?></h1> 
                <br>
                <table style="width:100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="width: 50px;"><b>Продавец:</b></td>
                        <td style="border-bottom: 1px solid black; text-align: center; font-weight: bold;"><?=$arOrder["COMPANY"]["COMPANY_FULL_NAME"]?></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td style="text-align: center; font-weight: bold; vertical-align: top;"><?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_COMPANY_ADRESS"]?></td>
                    </tr>

                    <tr>
                        <td colspan="2" style="border-bottom: 1px solid black; text-align: center; font-weight: bold; height: 18px; vertical-align: bottom;">РЕЕСТРОВЫЙ НОМЕР ТУРОПЕРАТОРА ВНТ <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_COMPANY_VNT"]?></td>                              
                    </tr>

                </table>

                <table style="width: 100%; margin:10px 0 0;">
                    <tr>
                        <td style="width:30px; font-size:14px !important; height: 25px; vertical-align: bottom; font-weight:bold">ТУР: &nbsp; </td>
                        <td style="text-decoration:underline; vertical-align: top">&nbsp;г. <?=$arOrder["CITY"]["NAME"]?>, гостиница "<?=$arOrder["HOTEL"]["NAME"]?>", номер <?=$arOrder["TOUR"]["ROOM"]["NAME"]?></td>
                    </tr>
                </table>

                <table style="width:100%">
                    <tr>
                        <td style="width:100px; height: 40px; vertical-align: bottom; font-size: 10px;"><b>Покупатель:</b><br>юридическое лицо:</td>
                        <td style="border-bottom: 1px solid black; vertical-align: bottom; text-align: left;">&nbsp;</td>
                    </tr>

                    <tr>
                        <td style="width:100px; vertical-align: bottom; font-size: 10px;">физическое лицо:</td>
                        <td style="border-bottom: 1px solid black; vertical-align: bottom; text-align: left;"><?=$arOrder["TOURISTS"][0]["NAME"]?></td>
                    </tr>

                    <tr>
                        <td style="width:100px; height:25px; vertical-align: bottom; text-decoration: underline;"><b>список туристов:</b></td>
                        <td style=" vertical-align: bottom; text-align: center;"></td>  
                    </tr>
                    <!--здесь возможно будет цикл, для вывода туристов (каждого на отдельной строке) -->

                    <?foreach ($arOrder["TOURISTS"] as $key=>$val) {?>                                
                        <tr>   
                            <td colspan="2" style="border-bottom: 1px solid black;"><?=$val["NAME"]?>, <?=$val["BIRTHDAY"]?>, <?=$val["PASSPORT"]?></td>
                        </tr>                                  
                        <?}?>

                </table>


                <table style="width:250px">
                    <tr>
                        <td style="height:50px; width:180px; vertical-align: bottom;"><b>Наличие руковолителя группы:</b></td>
                        <td style="border-bottom:1px solid black">&nbsp;</td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td style="width:210px; vertical-align: bottom;"><b>Продолжительность путешествия: </b> с "</td>
                        <td style="border-bottom: 1px solid black; width:20px; vertical-align: bottom; text-align: center;"><?=$dateFrom[0]?></td>
                        <td style="width:10px; vertical-align: bottom; text-align: center;">"</td>
                        <td style="border-bottom: 1px solid black; width:100px; text-align: center;"><?=get_month_name($dateFrom[1])?></td>
                        <td style="width:25px; text-align: center;">по "</td>
                        <td style="border-bottom: 1px solid black; width:20px; vertical-align: bottom; text-align: center;"><?=$dateTo[0]?></td>
                        <td style="width:10px; vertical-align: bottom; text-align: center;">"</td>
                        <td style="border-bottom: 1px solid black; width:100px; text-align: center;"><?=get_month_name($dateTo[1])?></td>
                        <td style="width:20px; text-align: center; vertical-align: bottom;">&nbsp;<b><?=$dateTo[2]?></b></td>                           
                        <td style="vertical-align: bottom;">&nbsp;г.</td>
                    </tr>
                </table>

                <table style="width:100%;">
                    <tr>
                        <td style="width:375px;"><b>Маршрут поездки и страны (пункты) пребывания:</b><br>начало путешствия (город РФ, а/п, ж/д, авт., речн., мор. вокз. отъезда)</td>
                        <td style="border-bottom:1px solid black; vertical-align: bottom;">&nbsp;<?=get_iblock_element_name($arOrder["DEPARTURE_CITY"])?></td>
                    </tr>
                </table>

                <table style="width:100%;">
                    <tr>
                        <td style="width:110px;">пункты пребывания:</td>
                        <td style="border-bottom:1px solid black; vertical-align: bottom;">&nbsp;<?=$arOrder["CITY"]["NAME"]?></td>
                    </tr>
                </table>

                <table style="width:100%;">
                    <tr>
                        <td style="width:390px;">окончание маршрута (город РФ, а/п, ж/д, авт., речн., мор. вокз. прибытия)</td>
                        <td style="border-bottom:1px solid black; vertical-align: bottom;">&nbsp;<?=get_iblock_element_name($arOrder["DEPARTURE_CITY"])?></td>
                    </tr>
                </table>

                <table style="width:100%; font-size: 11px !important;">
                    <tr>
                        <td style="width:180px;">Пакет услуг: катег. проезд. билета</td>
                        <td style="border-bottom:1px solid black; vertical-align: bottom; text-align: center; width:30px;"><b>&nbsp;</b></td>
                        <td style="text-align: center;">гостиница</td>
                        <td style="border-bottom:1px solid black; vertical-align: bottom; text-align: center; width:20px;">&nbsp;</td>
                        <td style="text-align: center;">номер</td>
                        <td style="border-bottom:1px solid black; vertical-align: bottom; text-align: center; width:20px;">&nbsp;</td>
                        <td style="text-align: center;">питание</td>
                        <td style="border-bottom:1px solid black; vertical-align: bottom; text-align: center; width:20px;">&nbsp;</td>
                        <td style="text-align: center;">виза</td>
                        <td style="border-bottom:1px solid black; vertical-align: bottom; text-align: center; width:20px;">&nbsp;</td>
                        <td style="text-align: center;">страховка</td>
                        <td style="border-bottom:1px solid black; vertical-align: bottom; text-align: center; width:20px;">&nbsp;</td>
                        <td style="text-align: center;">трансфер</td>
                        <td style="border-bottom:1px solid black; vertical-align: bottom; text-align: center; width:20px;">&nbsp;</td>
                    </tr>
                </table>

                <table style="width:100%;">
                    <tr>
                        <td style="width:155px; font-weight: bold;">Экскурсионная программа:</td>
                        <td style="border-bottom:1px solid black; vertical-align: bottom;">&nbsp;</td>
                    </tr>
                </table>

                <table style="width:100%;">
                    <tr>
                        <td style="width:200px; height:20px; vertical-align: bottom;">Дополнительные оплаченные услуги</td>
                        <td style="border-bottom:1px solid black; vertical-align: bottom;">&nbsp;</td>
                    </tr>
                </table>

                <table style="width:100%;">
                    <tr>
                        <td style="width:110px; height:20px; vertical-align: bottom;">Стоимость путевки:</td>
                        <td style="border-bottom:1px solid black; vertical-align: bottom; width: 520px; text-align: center;"><?//=$priceNew." - ".num2str($priceNew)?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="vertical-align: top; text-align: center; font-size:10px !important;">(сумма прописью)</td>
                        <td></td>
                    </tr>

                </table>

                <table >
                    <tr>
                        <td style="width:90px; height:20px; vertical-align: bottom; font-weight: bold;">Дата продажи: "</td>
                        <td style="border-bottom: 1px solid black; width:20px; vertical-align: bottom; text-align: center;"><?=$dateCreate[0]?></td>
                        <td style="width:10px; vertical-align: bottom; text-align: center; font-weight: bold;">"</td>
                        <td style="border-bottom: 1px solid black; width:100px; text-align: center; vertical-align: bottom;"><?=get_month_name($dateCreate[1])?></td>
                        <td style="width:20px; text-align: center; vertical-align: bottom;">&nbsp;<b><?=$dateCreate[2]?></b></td>
                        <td style="vertical-align: bottom;">&nbsp;г.</td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td style="width:70px; font-weight: bold;  vertical-align: bottom; height:20px;">Продавец</td>
                        <td style="width:150px; border-bottom: 1px solid black;"></td>
                        <td style="width:50px;">&nbsp;</td>
                        <td style="width:70px; font-weight: bold;  vertical-align: bottom; height:20px;">Покупатель</td>
                        <td style="width:150px; border-bottom: 1px solid black;"></td>
                    </tr>
                    <tr>
                        <td colspan="3">М.П.</td>
                        <td colspan="2">М.П. (для организаций)</td>
                    </tr>

                </table>

                <table style="width:100%;">
                    <tr>
                        <td style="padding:0 0 0 50px; width:130px;">Отправление автобуса:</td>
                        <td style="text-align: left;"><?=$arOrder["TOUR"]["DATE_FROM"]?></td>
                    </tr>
                    <tr>
                        <td style="padding:0 0 0 50px; width:130px;">Места в автобусе:</td>
                        <td style="text-align: left;">
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
                        </td>
                    </tr>
                </table>



            </td>
            <!--Левая часть -->

            <!--Правая часть -->
            <td class="tour_list_right_part main_td">
                <div class="tour_list_logo_s">
                    <p><?=$arOrder["COMPANY"]["COMPANY_FULL_NAME"]?>, ИНН <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_INN"]?>, ОКПО <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_COMPANY_OKPO"]?>. тел. <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_COMPANY_PHONE"]?></p>
                </div>  
                <div style="width:100%; height:1px; overflow:hidden; background: #000;"></div>  
                <h3 style="margin:5px 0 0; text-align: center;"><?=$arOrder["COMPANY"]["COMPANY_FULL_NAME"]?></h3>
                <h4 style="margin:3px 0 0; text-align: center; text-transform: uppercase;">отрывной талон</h4>
                <h5 style="margin: 0; text-align: center;">к туристической путевке №<?=$arOrder["ID"]?></h5>

                <table style="width:100%">
                    <tr>
                        <td style="font-weight: bold; width:30px ; vertical-align: bottom; font-size: 11px !important;">Тур:</td>
                        <td style=""></td>
                    </tr>

                    <tr>                              
                        <td colspan="2" style="text-decoration:underline; line-height:20px; height:25px;">&nbsp;г. <?=$arOrder["CITY"]["NAME"]?>, гостиница "<?=$arOrder["HOTEL"]["NAME"]?>", номер <?=$arOrder["TOUR"]["ROOM"]["NAME"]?></td>
                    </tr>


                </table>
                <br>
                <b>Продолжительность путешествия</b>

                <table>
                    <tr>
                        <td style="width:20px; vertical-align: bottom;">c "</td>
                        <td style="border-bottom: 1px solid black; width:20px; vertical-align: bottom; text-align: center;"><?=$dateFrom[0]?></td>
                        <td style="width:10px; vertical-align: bottom; text-align: center;">"</td>
                        <td style="border-bottom: 1px solid black; width:100px; text-align: center;"><?=get_month_name($dateFrom[1])?></td>
                        <td style="width:25px; text-align: center;">&nbsp;по "</td>
                        <td style="border-bottom: 1px solid black; width:20px; vertical-align: bottom; text-align: center;"><?=$dateTo[0]?></td>
                        <td style="width:10px; vertical-align: bottom; text-align: center;">"</td>
                        <td style="border-bottom: 1px solid black; width:100px; text-align: center;"><?=get_month_name($dateTo[1])?></td>
                        <td style="width:20px; text-align: center; vertical-align: bottom;">&nbsp;<b><?=$dateTo[2]?></b></td>
                        <td>&nbsp;г.</td>
                    </tr>
                </table>


                <table style="width:100%">
                    <tr>
                        <td style="border-bottom: 1px solid black; vertical-align: bottom; height: 20px; text-align: center;"><?=$arOrder["TOURISTS"][0]["NAME"]?></td>
                    </tr>
                    <tr>
                        <td style=" vertical-align: top; height: 15px; text-align: center;">(Ф.И.О.)</td>
                    </tr>
                </table>

                <table style="width:100%;">
                    <tr>
                        <td style="font-weight:bold; font-size:13px !important; text-transform: uppercase; vertical-align: bottom; width:85px;">ТЕЛЕФОН<br>ТУРИТСТОВ</td>
                        <td style="border-bottom: 1px solid black; vertical-align: bottom;">&nbsp;<?=$arOrder["TOURISTS"][0]["PHONE"]?></td>
                    </tr>
                </table>

                <table style="width:100%;">
                    <tr>
                        <td style="width:120px; font-weight:bold;vertical-align: bottom;">Оплаченные услуги:</td>
                        <td style="border-bottom: 1px solid black; vertical-align: bottom;">&nbsp;</td>
                    </tr>

                    <tr>
                        <td colspan="2" style="border-bottom: 1px solid black; vertical-align: bottom; height:15px;">&nbsp;</td>
                    </tr>

                    <tr>
                        <td colspan="2" style="border-bottom: 1px solid black; vertical-align: bottom; height:15px;"></td>
                    </tr>

                    <tr>
                        <td colspan="2" style="border-bottom: 1px solid black; vertical-align: bottom; height:15px;"></td>
                    </tr>

                    <tr>
                        <td colspan="2" style="border-bottom: 1px solid black; vertical-align: bottom; height:15px;"></td>    
                    </tr>

                </table>

                <b>Стоимость путевки:</b>

                <table style="width: 100%;">
                    <tr>
                        <td colspan="2" style="border-bottom: 1px solid black; vertical-align: bottom; height:15px;"><?//=$priceNew." - ".num2str($priceNew)?></td>
                    </tr>

                    <tr>
                        <td colspan="2" style="vertical-align: top; text-align: center; font-size:10px !important;">(сумма прописью)</td>
                    </tr>


                </table>


                <table style="width: 100%;">
                    <tr>
                        <td style="font-weight:bold; width:60px;">Продавец</td>
                        <td style="border-bottom:1px solid black"></td>
                    </tr>
                </table>
                М.П.<br><br>
                C условиями предоставления<br>
                туристических услуг ознакомлен<br>
                и согласен.

                <table style="width: 100%;">
                    <tr>
                        <td style="font-weight:bold; width:60px;">Покупатель</td>
                        <td style="border-bottom:1px solid black"></td>
                    </tr>
                </table>

                М.П. (для организаций)<br> <br>


                <table >
                    <tr>
                        <td style="width:90px; height:20px; vertical-align: bottom; font-weight: bold;">Дата продажи: "</td>
                        <td style="border-bottom: 1px solid black; width:20px; vertical-align: bottom; text-align: center;"><?=$dateCreate[0]?></td>
                        <td style="width:10px; vertical-align: bottom; text-align: center; font-weight: bold;">"</td>
                        <td style="border-bottom: 1px solid black; width:100px; text-align: center; vertical-align: bottom;"><?=get_month_name($dateCreate[1])?></td>
                        <td style="width:20px; text-align: center; vertical-align: bottom;">&nbsp;<b><?=$dateCreate[2]?></b></td>
                        <td style="vertical-align: bottom;">&nbsp;г.</td>
                    </tr>
                </table>

            </td> 
            <!--Правая часть -->        
        </tr>

    </table>
   </div> 
           
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>

