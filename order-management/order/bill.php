<?
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
    $APPLICATION->SetTitle("Заказы");
?>  

<style>
    /*---bill---<---*/


    body {padding:0; margin:0; color:#000 !important; font-family: arial; font-size:11px !important;} 
    table {border-collapse: separate;} 

    th, td, caption {
        padding: 4px 10px 4px 5px;
    }

    .printCenter {
        background: none repeat scroll 0 0 #fff;
        margin: 0 auto;
        padding: 0 10px;
        text-align: center;
        width: 980px;
    }        


    .printCenter h1 {
        color: #000;
        font: bold 20px Tahoma,Arial,Helvetica,sans-serif;
        padding: 15px 0 20px;
        text-align: center;
    }  


    .printCenter h2 {
        color: #000;
        font: bold 16px Tahoma,Arial,Helvetica,sans-serif;
        padding: 15px 0 20px;
        text-align: center;
    }  

    .printCenter p {
        color: #000;
        font: 14px Tahoma,Arial,Helvetica,sans-serif;
        text-align: left;
    }  

    .tour_list_main {width:1000px; height:700px;  background: #fff; margin:0 auto}
    .tour_list_main td.main_td {padding:10px; vertical-align: top;}



    .billTable {
        border-collapse: collapse;
        width:100%;
    }

    .billTable td {
        border: 1px solid #000;
        text-align: left;
        vertical-align: middle;
    }

    .billTable td+td+td {
        text-align: center;
    }


    .billTable th {
        border: 1px solid #000;
        text-align: center;
        vertical-align: middle;
        font-weight: 400;

    }

    .billTableEmptyTr td[colspan="4"] {
        border: none;
    }

    .billTableEmptyTr td {
        text-align: right;
    }

    .billTableEmptyTr td+td+td {
        text-align: center;
    }

    .billStamp {
        width: 194px;
        height: 190px;
        margin: 50px 0 0;
    }
    
    .billStamp img {
        width:150px;
    }

    .afterBillTableTr td {
        border: none;
    }

    .billStampTd {
        text-align: right;
    }

    .billTableSign span{
        width: 120px;
        height: 58px;
        display: inline-block;
        *display: inline;
        *zoom: 1;
        position: relative;
        top: 25px;
    }

    .billOrderRecieverTable td {
        padding: 5px;
    }

    .billOrderRecieverTable {
        width: auto;
    }

    /*---bill---<---*/


</style>

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
<?//arshow($arOrder)?>

<div class="printCenter">

    <table >
        <tr>
            <td>
                <h1>
                    <?=$arOrder["COMPANY"]["COMPANY_FULL_NAME"]?>
                </h1>
                <p>
                    <b>Адрес: <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_COMPANY_ADRESS"]?>, тел.:<?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_COMPANY_PHONE"]?></b>    
                </p>

                <table class = "billTable" summary = "">
                    <tr>
                        <td>ИНН <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_INN"]?></td>
                        <td>КПП <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_KPP"]?></td>
                        <td rowspan="2">Сч. №</td>
                        <td rowspan="2"><?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_BILL"]?></td>
                    </tr>
                    <tr>
                        <td colspan = "2">Получатель <?=$arOrder["COMPANY"]["COMPANY_FULL_NAME"]?></td>
                    </tr>
                    <tr>
                        <td colspan = "2" rowspan = "2"> Банк получателя <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_BANK"]?></td>
                        <td>БИК</td>
                        <td><?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_BIK"]?></td>
                    </tr>
                    <tr>
                        <td>Сч. №</td>
                        <td><?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_KOR_BILL"]?></td>
                    </tr>
                </table>

                <h2>СЧЕТ №<?=$arOrder["ID"]?> от <?date("d.m.Y")?> г.</h2>

                <table class="billOrderRecieverTable" summary="">
                    <tr>
                        <td>Заказчик:</td>
                        <td><?=$arOrder["CREATED_BY"]["NAME"]?></td>
                    </tr>
                    <tr>
                        <td>Плательщик:</td>
                        <td><?=$arOrder["CREATED_BY"]["NAME"]?></td>
                    </tr>
                </table>

                <table class = "billTable" summary = "">
                    <tr>
                        <th>№</th>
                        <th>Наименование товара</th>
                        <th>Единица измерения</th>
                        <th>Количество</th>
                        <th>Цена</th>
                        <th>Сумма</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Оплата оплата  за <?if ($arOrder["CITY"]["NAME"]){?>тур в г.<?=$arOrder["CITY"]["NAME"]?>, гостиница "<?=$arOrder["HOTEL"]["NAME"]?>", номер <?=$arOrder["TOUR"]["ROOM"]["NAME"]?> с <?=$arOrder["TOUR"]["DATE_FROM"]?> по <?=$arOrder["TOUR"]["DATE_TO"]?><?} else { echo $arOrder["TYPE_BOOKING"]["NAME"].", направление: ".$arOrder["DIRECTION"]["NAME"];}?><br> 
                            <?=count($arOrder["TOURISTS"])?> человек(а):<br>
                            <?
                                $tourists = array();
                                foreach ($arOrder["TOURISTS"] as $tourist){
                                    $tourists[]= $tourist["NAME"];
                                }
                                echo implode("<br>",$tourists);
                            ?>
                        </td>
                        <td class="ac">шт.</td>
                        <td class="ac">1</td>
                        <td class="ac"><?=$arOrder["PRICE"]?></td>
                        <td><?=$arOrder["PRICE"]?></td>
                    </tr>
                    <tr class="billTableEmptyTr">
                        <td colspan="4"></td>
                        <td><b>Итого:</b></td>
                        <td><b><?=$arOrder["PRICE"]?></b></td>
                    </tr>
                    <tr class="billTableEmptyTr">
                        <td colspan="4"></td>
                        <td><b>Агентское<br/>вознаграждение:</b></td>
                        <td><b><?=$arOrder["PRICE"]-$arOrder["PRICE_AGENCY"]?> руб.</b></td>
                    </tr>
                    <tr class="billTableEmptyTr">
                        <td colspan="4"></td>
                        <td><b>Всего к оплате:</b></td>
                        <td><b><?=$arOrder["PRICE_AGENCY"]?></b></td>
                    </tr>
                    <tr class = "afterBillTableTr">
                        <td colspan = "4">
                            <p>Всего наименований 1, на сумму <?=$arOrder["PRICE_AGENCY"]?> руб</p>
                            <p><b><?=num2str($arOrder["PRICE_AGENCY"])?></b></p>
                            <p class="billTableSign">Руководитель предприятия <span></span>(<?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_DIRECTOR"]?>)</p>
                            <p class="billTableSign">Главный бухгалтер <span></span>(<?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_DIRECTOR"]?>)</p>
                        </td>
                        <td colspan = "2" class="billStampTd">
                            <div class="billStamp">
                                <?if ($arOrder["COMPANY"]["STAMP"]){?>
                                    <img src="<?=CFile::GetPath($arOrder["COMPANY"]["STAMP"])?>" />
                                    <?}?>
                            </div>            
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>




</div> 
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>

