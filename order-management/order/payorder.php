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
    $cur_month;

    //arshow($arOrder);
    
    $price = explode(".",$arOrder["PRICE"]);
    if (strlen($price[1]) == 1) {
        $price[1] = $price[1]*10;
    }
    $priceNew = implode(".",$price);
?>
<style>
    * {font-size:12px !important;}
    body {padding:0; margin:0; font-size:12px !important; color:#000 !important}
    table {border-collapse: separate !important; border-spacing:0px 0px ;}
    .pay_order_main {width:1000px; height:520px;}
    .pay_order_main td.main_td {padding:10px !important; vertical-align: top;}
    .pay_order_left_part {width:66%; border-right:1px dashed black}

    .pay_order_left_table {width:100%; border-right:2px solid black; padding:0 2px;}    
    .pay_order_left_table table td {padding:0} 

    .pay_order_right_table {width:100%; border-left:2px solid black; padding:0 3px;}
    .pay_order_right_table table td {padding:0} 

    .line2_table  {width:100%; border-color:black}
    .line2_table td {padding:0; text-align: center; border-color:black}

    .main_payorder_table td {border:1px solid #000}

</style>



<div class="printCenter">
    <table class="pay_order_main confirmTable">
        <tr>
            <!--Левая часть -->
            <td class="pay_order_left_part main_td">

                <table class="pay_order_left_table">

                    <tr>
                        <td style="font-size:12px; text-align: right; vertical-align: bottom; height:40px; padding:0 10px 10px 0; border-bottom:1px solid gray">
                            Унифицированная форма № КО-1 <br>  
                            Утверждена постановлением Госкомстата России от 18.08.98 №88
                        </td>
                    </tr>

                    <!--///////////////////////////////////////////////// -->

                    <tr>
                        <td style="height:100px; border-bottom:1px solid gray">
                            <table class="line2_table" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td rowspan="2" style="width:60%; vertical-align: bottom; border-bottom: 2px solid black; font-size:14px;"><?=$arOrder["COMPANY"]["COMPANY_FULL_NAME"]?></td>
                                    <td></td>
                                    <td style="border:2px solid black; border-bottom:0">Код</td>
                                </tr>

                                <tr>
                                    <td style="width:20%; text-align: right; padding: 0 3px 0;">Форма по ОКУД</td>
                                    <td style="width:20%; border:2px solid black; border-bottom:0">032165</td>
                                </tr>

                                <tr>
                                    <td style="vertical-align: top; font-size:10px;">организация</td>
                                    <td style="text-align: right; padding: 0 3px 0;">по ОКПО</td>
                                    <td style="border:2px solid black; border-bottom:0"></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="vertical-align: bottom; border-bottom: 2px solid black;">&nbsp;</td>
                                    <td style="border:2px solid black; "></td>                                      
                                </tr>                                     

                                <tr>
                                    <td colspan="2" style="vertical-align: top; font-size:10px;">Структурное подразделение</td>
                                    <td></td>

                                </tr>


                            </table>
                        </td>
                    </tr>

                    <!--///////////////////////////////////////////////// -->

                    <tr>
                        <td style="height:50px;">
                            <table class="line2_table" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td rowspan="2" style="width:60%; font-size:20px; font-weight:bold">ПРИХОДНЫЙ КАССОВЫЙ ОРДЕР</td>
                                    <td style="width:20%; border:2px solid black">Номер документа</td>
                                    <td style="width:20%; border:2px solid black; border-left:0;">Дата составления</td>
                                </tr>

                                <tr>
                                    <td style="border:2px solid black; border-top:0;"><?=$arOrder["ID"]?></td>
                                    <td style="border:2px solid black; border-top:0; border-left:0;"><?echo date("d.m.Y")?><?//=$date_arr[2].".".$date_arr[1].".".$date_arr[0]." ".substr($ordersArr["order"]["dateCreate"],11);?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!--///////////////////////////////////////////////// -->

                    <tr>
                        <td style="height:100px; ">
                            <table class="line2_table main_payorder_table" cellpadding="0" cellspacing="0" style="height:100px; border:1px solid #000;">
                                <tr>
                                    <td rowspan="2" style="width:7%">Дебет</td>
                                    <td colspan="4" style="width:50%">Кредит</td>
                                    <td rowspan="2" colspan="2" style="width:22%">Сумма, руб. коп.</td>
                                    <td rowspan="2" style="width:14%">Код целевого назначения</td>
                                    <td rowspan="2">&nbsp;</td>
                                </tr>

                                <tr>
                                    <td>&nbsp;</td>
                                    <td style="width:14%">Код структкрного подразделения</td>
                                    <td style="width:16%">корреспондирую-<br>щий счет, субсчет</td>
                                    <td style="width:14%">код аналити-<br>ческого<br>учета</td>

                                </tr>

                                <tr>
                                    <td style="height:25px;">&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td style="width:14%"><?//=$price[0]?></td>                                  
                                    <td><?//=$price[1]?></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>

                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!--///////////////////////////////////////////////// -->

                    <tr>
                        <td style="height:85px; vertical-align: top;">
                            <div style="width:100%; border-bottom:2px solid black; height:18px; font-size:16px; line-height: 18px;">Принято от <span style="text-transform: uppercase;"><?=$arOrder["TOURISTS"][0]["NAME"]?></span></div>
                            <div style="width:100%; height:25px; line-height: 25px; font-size:14px; text-decoration:underline">
                            Основание: оплата  за <?if ($arOrder["CITY"]["NAME"]){?>тур в г.<?=$arOrder["CITY"]["NAME"]?>, гостиница "<?=$arOrder["HOTEL"]["NAME"]?>", номер <?=$arOrder["TOUR"]["ROOM"]["NAME"]?> с <?=$arOrder["TOUR"]["DATE_FROM"]?> по <?=$arOrder["TOUR"]["DATE_TO"]?><?} else { echo $arOrder["TYPE_BOOKING"]["NAME"].", направление: ".$arOrder["DIRECTION"]["NAME"];}?>
                            </div>
                        </td>
                    </tr>

                    <!--///////////////////////////////////////////////// -->

                    <tr>
                        <td style="height:100px;">
                            <table class="line2_table">
                                <tr>
                                    <td style="height:30px; text-align: left; font-size: 16px; border-bottom:1px solid gray">Сумма: <?//=$priceNew." - ".num2str($priceNew)?>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="height:30px; text-align: center; vertical-align: top;font-size: 12px;">
                                        прописью
                                    </td>
                                </tr>

                                <tr>
                                    <td style="height:20px; border-bottom:2px solid black; text-align: left; font-size: 16px;">
                                        В том числе
                                    </td>
                                </tr>

                                <tr>
                                    <td style="height:20px; border-bottom:2px solid black; text-align: left; font-size: 16px;">
                                        Приложение
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!--///////////////////////////////////////////////// -->

                    <tr>
                        <td style="height:100px;">
                            <table class="line2_table" cellpadding="5">
                                <tr>
                                    <td style="width:25%; height:25px; font-size: 16px; text-align: left;">Главный бухгалтер</td>
                                    <td style="width:25%; border-bottom:2px solid black">&nbsp;</td>
                                    <td style="width:50%; border-bottom:2px solid black">&nbsp;</td>                                        
                                </tr>    

                                <tr>
                                    <td style="height:25px;"></td>
                                    <td style="vertical-align: top;">подпись</td>
                                    <td style="vertical-align: top;">расшифровка подписи</td>                                        
                                </tr>


                                <tr>
                                    <td style="height:25px; font-size: 16px; text-align: left;">Получил кассир</td>
                                    <td style="border-bottom:2px solid black"></td>
                                    <td style="border-bottom:2px solid black"></td>                                        
                                </tr>

                                <tr>
                                    <td style="height:25px;"></td>
                                    <td style="vertical-align: top;">подпись</td>
                                    <td style="vertical-align: top;">расшифровка подписи</td>                                        
                                </tr>

                            </table>
                        </td>
                    </tr>

                </table>     

            </td>
            <!--Левая часть -->

            <!--Правая часть -->
            <td class="pay_order_right_part main_td">
                <table class="pay_order_right_table">

                    <tr>
                        <td style="height:70px;">
                            <table style="width:100%; ">
                                <tr>
                                    <td style="width:100%; height:18px; border-bottom:2px solid black; text-align: center; font-size: 16px;"><?=$arOrder["COMPANY"]["COMPANY_FULL_NAME"]?></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; text-align: center;">организация</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="height:90px;">
                            <table style="width:100%; text-align:center; font-size:14px;">
                                <tr>
                                    <td colspan="4" style="font-size:18px; font-weight: bold; text-align: center;">КВИТАНЦИЯ</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="">к приходному кассовому ордеру №</td>
                                    <td style="border-bottom:2px solid black; width:30%; text-align: center;"><?=$arOrder["ID"]?></td>
                                </tr>
                                <tr>
                                    <td style="height:35px; vertical-align: bottom;width:15%;">от &nbsp;"<?echo date("d")?><?//=$date_arr[2]?>"</td>
                                    <td style="border-bottom:2px solid black; width: 30%;  vertical-align: bottom; text-align: center;"><?=$cur_month?></td>
                                    <td style="vertical-align: bottom; text-align: left"> &nbsp;<?echo date("Y")?><?//=$date_arr[0]?> г.</td>
                                    <td></td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td style="height:80px">
                            <table style="width:100%; text-align:left; font-size:14px;">
                                <tr>
                                    <td style="vertical-align:top; height:60px;"><br>Принято от <span style="text-transform: uppercase;"><?=$arOrder["TOURISTS"][0]["NAME"]?></span></td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:bottom">Основание: 
                                     оплата за <?if ($arOrder["CITY"]["NAME"]){?>тур в г.<?=$arOrder["CITY"]["NAME"]?>, гостиница "<?=$arOrder["HOTEL"]["NAME"]?>", номер <?=$arOrder["TOUR"]["ROOM"]["NAME"]?> с <?=$arOrder["TOUR"]["DATE_FROM"]?> по <?=$arOrder["TOUR"]["DATE_TO"]?><?} else { echo $arOrder["TYPE_BOOKING"]["NAME"].", направление: ".$arOrder["DIRECTION"]["NAME"];}?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="height:130px">

                            <table style="width:100%; ">
                                <tr>
                                    <td style="text-align: left; font-size: 14px; border-bottom:1px solid #000"><br><span style="position: relative; bottom: -2px; background: #fff;">Сумма:</span> <?//=$priceNew?></td>
                                </tr>

                                <tr>
                                    <td style="text-align: center; vertical-align: top; font-size: 10px; "></td>
                                </tr>

                                <tr>
                                    <td style="text-align: center; vertical-align: bottom; font-size: 10px;"><br>
                                    <div style="width: 100%; border-bottom: 1px solid #000;"><?//=num2str($priceNew)?></div>
                                    <span style="font-size: 10px !important;">прописью</span></td>
                                </tr>

                                <tr>
                                    <td style="text-align: left; font-size: 14px; vertical-align: bottom;"><br>В том числе:<br>&nbsp;</td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td style="height:50px;">
                            <table style="width:100%">
                                <tr>
                                    <td style="height:35px; vertical-align: bottom;width:15%;">от &nbsp;"<?echo date("d")?><?//=$date_arr[2]?>"</td>
                                    <td style="border-bottom:2px solid black; width: 30%;  vertical-align: bottom; text-align: center;"><?=$cur_month?></td>
                                    <td style="vertical-align: bottom; border-bottom:0 !important;"> &nbsp;<?echo date("Y")?><?//=$date_arr[0]?> г.</td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td style="text-align: center"><br><b>М.П.</b></td>
                                    <td colspan="2"> <br>(штампа)</td>

                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="height:120px;">
                            <table style="width:100%;">
                                <tr>
                                    <td style="font-size:14px; font-weight: bold; width:25%;"><br>Главный<br>бухгалтер</td>
                                    <td style="border-bottom: 2px solid black;"></td>
                                    <td style="border-bottom: 2px solid black;"></td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td style="text-align: center; vertical-align: top;">подпись</td>
                                    <td style="text-align: center; vertical-align: top;">расшифровка</td>
                                </tr>

                                <tr>
                                    <td style="font-size:14px; font-weight: bold;">Кассир</td>
                                    <td style="border-bottom: 2px solid black;"></td>
                                    <td style="border-bottom: 2px solid black;"></td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td style="text-align: center; vertical-align: top;">подпись</td>
                                    <td style="text-align: center; vertical-align: top;">расшифровка</td>
                                </tr>
                            </table>
                        </td>
                    </tr>


                </table>
            </td> 
            <!--Правая часть -->        
        </tr>

    </table>         

        </div>           
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>

