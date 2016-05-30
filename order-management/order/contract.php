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



    $price = explode(".",$arOrder["PRICE"]);
    if (strlen($price[1]) == 1) {
        $price[1] = $price[1]*10;
    }

    $priceNew = implode(".",$price);

    $dateFrom = explode(".",$arOrder["TOUR"]["DATE_FROM"]);
    $dateTo = explode(".",$arOrder["TOUR"]["DATE_TO"]);
    $dateCreate = explode(".",substr($arOrder["DATE_CREATE"],0,10));

    //arshow($arOrder);
?>    
<style>
    * {margin:0 ; padding:0 ;}
    p {margin:0}
    .contractPrintCenter {width:1000px; margin:0 auto; font-size:17px; color:#000;} 

    .contractPrintCenter, h3, h4 {font-family: "Times New Roman" !important}

    .contractPrintCenter h3 {font-size:14px; font-weight:bold; text-align:center; text-transform: uppercase; margin: 0 0 5px;}
    .contractPrintCenter h4 {font-size:12px; font-weight:bold; text-align:center; text-transform: uppercase; margin: 0 0 5px;}

    .underline {border-bottom: 1px solid #000;}

    .db {display:block;}
    .fl {float:left;}
    .fr {float:right;}

    .w100 {width:100%; clear:both; position: relative;}

    .tac {text-align: center;}

    .h30 {height:20px; line-height:17px;}

    .bb1 {border-bottom: 1px solid #000;}

    .b5 {bottom:-5px; background: #fff; position: relative;}

    .b3 {bottom:-5px; position: relative;}

    .info_users tr td {height:20px;}
    .info_users tr th:last-child {text-align: center;}
    .info_users tr td:last-child {border-bottom:1px solid #000; text-align: center; }

    .text_between {height: 20px; line-height: 27px;}

</style>


<div class="contractPrintCenter">


    <h3><?=$arOrder["COMPANY"]["COMPANY_FULL_NAME"]?></h3>

    <h4>договор №<?=$arOrder["ID"]?></h4> 

    <div class="w100 h30">
        <div class="fl" style="margin-left: 20px;">г. </div> 
        <div class="fr">
            <div class="fl">«</div><div class="underline fl tac" style="width: 30px;"> <?=date("d")?></div><div class="fl">»</div> 
            <div class="underline fl tac" style="width:100px; "><?=get_month_name(date("m"))?></div> <?=date("Y")?> г.
        </div>
    </div>     

    <div class="w100 h30 bb1">
        <div class="fl b5"><b>ЗАКАЗЧИК:</b>&nbsp;</div><div class="fl b3">&nbsp; <b><?=$arOrder["TOURISTS"][0]["NAME"]?></b></div>
    </div>

    <div class="w100 h30 bb1" style="width:210px;">
        <div class="fl b5">Дата рождения:&nbsp;</div><div class="fl b3">&nbsp; <b><?=$arOrder["TOURISTS"][0]["BIRTHDAY"]?></b></div>
    </div>

    <div class="w100 h30 bb1">
        <div class="fl b5">Паспорт:&nbsp;</div><div class="fl b3">&nbsp; <b><?=$arOrder["TOURISTS"][0]["PASSPORT"]?></b></div>
    </div>

    <div class="w100 h30 bb1">
        <div class="fl b5">Адрес:&nbsp;</div><div class="fl b3">&nbsp; </div>
    </div>

    <div class="w100 h30 bb1">
        <div class="fl b5">Телефон:&nbsp;</div><div class="fl b3">&nbsp; <b><?=$arOrder["TOURISTS"][0]["PHONE"]?></b></div>
    </div>

    <div class="w100 h30 bb1">
        <div class="fl b5">Данные туристов:&nbsp;</div><div class="fl b3">&nbsp; <b><?=$arOrder["TOURISTS"][0]["NAME"]?>, <?=$arOrder["TOURISTS"][0]["BIRTHDAY"]?>, <?=$arOrder["TOURISTS"][0]["PASSPORT"]?></b></div>
    </div>

    <?foreach ($arOrder["TOURISTS"] as $n=>$tourist){
            if ($n > 0) {
            ?>
            <div class="w100 h30 bb1">
                <div class="b3">&nbsp; <b><?=$tourist["NAME"]?>, <?=$tourist["BIRTHDAY"]?>, <?=$tourist["PASSPORT"]?></b></div>
            </div>

            <?}?>
        <?}?>   

    <div class="w100 h30 " style="width:162px;">
        <div class="fl b5">Итого:&nbsp;</div><div class="fl tac bb1 text_between" style="width: 45px;  "><b><?=count($arOrder["TOURISTS"])?></b></div><div class="fl b5">&nbsp;человек</div>
    </div>

    <div class="w100 h30">
        <b class="b3">Данные о поездке</b>
    </div>

    <div class="w100 h30 bb1">
        <div class="fl b5">Название тура:&nbsp;</div><div class="fl b3">&nbsp; <b><?=$arOrder["CITY"]["NAME"]?></b></div>
    </div>

    <div class="w100 h30 bb1">
        <div class="fl b5">Дата отправления:&nbsp;</div><div class="fl b3" style="width:400px">&nbsp; <b><?=$arOrder["TOUR"]["DATE_FROM"]?></b></div>
        <div class="fl b5">&nbsp;Дата прибытия:&nbsp;</div><div class="fl b3">&nbsp; <b><?=$arOrder["TOUR"]["DATE_TO"]?></b></div>
    </div>

    <div class="w100 h30 bb1">
        <div class="fl b5">Даты отдыха:&nbsp;</div><div class="fl b3">&nbsp; <b><?=$arOrder["TOUR"]["REST_DATE"]["FROM"]?> - <?=$arOrder["TOUR"]["REST_DATE"]["TO"]?></b></div>
    </div>

    <div class="w100 h30 bb1">
        <div class="fl b5">Продолжительность (дней):&nbsp;</div><div class="fl b3">&nbsp; <b><?=$arOrder["TOUR"]["REST_LENGTH"]?></b></div>
    </div>

    <div class="w100 h30 bb1">
        <div class="fl b5">Вид размещения:&nbsp;</div><div class="fl b3">&nbsp; <b><?if ($arOrder["CITY"]["NAME"]){?>г.<?=$arOrder["CITY"]["NAME"]?>, гостиница "<?=$arOrder["HOTEL"]["NAME"]?>", номер <?=$arOrder["TOUR"]["ROOM"]["NAME"]?><?} else { echo $arOrder["TYPE_BOOKING"]["NAME"].", направление: ".$arOrder["DIRECTION"]["NAME"];}?></b></div>
    </div>

    <div class="w100 h30 bb1">
        <div class="fl b5">Питание:&nbsp;</div><div class="fl b3">&nbsp; </div>
    </div>

    <div class="w100 h30 bb1">
        <div class="fl b5">Вид транспорта:&nbsp;</div><div class="fl b3" style="width:200px">&nbsp; </div>
        <div class="fl b5">&nbsp;Места:&nbsp;</div><div class="fl b3">&nbsp;
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

        </div>
    </div>

    <div class="w100 h30 bb1" style="width:350px;">
        <div class="fl b5">Трансфер:&nbsp;</div><div class="fl b3">&nbsp; </div>
    </div>

    <div class="w100 h30 bb1">
        <div class="fl b5">Предварительный расчет стоимости путевки:&nbsp;</div><div class="fl b3">&nbsp; <?//=$priceNew." - ".num2str($priceNew)?></div>
    </div>

    <div class="w100 h30 bb1">

    </div>

    <div class="w100 h30 bb1">
        <div class="fl b5">Предоплата:&nbsp;</div><div class="fl b3">&nbsp; </div>
    </div>


    <div div class="w100 h30">                                                                                                                                                                                      
        <div class="fl b3">Оплата тура:&nbsp;</div><div class="fl bb1" style="width: 300px;">&nbsp;</div><div class="fl b3">&nbsp;руб. произведена</div>
    </div>
    <br>

    <p ><b><span >!</span></b><span > <b>Дети до 14 лет должны иметь при себе свидетельство о рождении с вкладышем о гражданстве.</b></span></p>

    <p ><b><span >! Все туристы должны при себе иметь оригиналы документов, удостоверяющих личность.</span></b></p>

    <p ><b><span >! На несовершеннолетних детей (до 18 лет), пересекающих границу без  родителей, 
        оформляется доверенность от родителей (опекунов), заверенная нотариусом.</span></b></p>

    <p ><b><span >ИСПОЛНИТЕЛЬ:</span></b></p>

    <p style="text-align: justify;">Общество с ограниченной ответственностью туристическая фирма «Рейтинг», являющееся туроператором, внесённое в Единый федеральный реестр туроператоров, реестровый номер ВНТ 008497, размер финансового обеспечения 500 000 руб., банковская гарантия по договору страхования гражданской ответственности за неисполнение или ненадлежащие исполнение обязательств по договору о реализации туристского продукта с Тульским филиалом СОАО «Военно-страховая компания» (г. Тула, ул. Демонстрации, 1г) от 04 февраля 2014 года № 14740В6000476, срок действия финансового обеспечения – до 31.05.2015 г.   </p>

    <p style="text-align: center; "><b><span >Права </span></b><b><span >и обязанности Исполнителя</span></b></p>

    <p style="text-align: justify; "><b><span >Исполнитель обязуется:</span></b></p>

    <p style="text-align: justify; "><span >- Предоставить Заказчику необходимую достоверную информацию, обеспечивающую возможность правильного выбора вида туристического обслуживания.</span></p>

    <p style="text-align: justify; "><span >-  Обеспечить оформление медицинской страховки.</span></p>

    <p style="text-align: justify; "><span >- Предоставить услуги по трансферам, встречам и проводам, проживанию, питанию, экскурсионному и иному обслуживанию в соответствии с Договором.</span></p>

    <p style="text-align: justify; "><span >- Предоставить другие услуги по соглашению Сторон, в соответствии с Договором.</span></p>

    <p style="text-align: justify; "><span >- По желанию Заказчика Исполнитель обязан предоставить: медицинскую страховку, трансфер, встречу, проводы, проживание, питание в соответствии с Договором.</span></p>

    <p style="text-align: justify; "><span >-  Обеспечить сохранность принятых на оформление документов.</span></p>

    <p style="text-align: justify; "><b><span >Исполнитель имеет право:</span></b></p>

    <p style="text-align: justify; "><span >- В случае неполной оплаты услуг за 7 дней до начала тура, Исполнитель имеет право отказать Заказчику в предоставлении туристических услуг. </span></p>

    <p style="text-align: justify; "><span >- В исключительных случаях заменить гостиницу при условии, что новая гостиница будет аналогичного или более высокого класса.</span></p>

    <p style="text-align: justify; "><b><span >Исполнитель или Заказчик вправе потребовать расторжения настоящего </span></b><b><span >Договора в следующих</span></b><span > <b>случаях:</b></span></p>

    <p style="text-align: justify; "><span >- ухудшения условий поездки, оговоренных предварительно Сторонами</span></p>

    <p style="text-align: justify; "><span >- изменение сроков совершения путешествия;</span></p>

    <p style="text-align: justify; "><span >- недобор указанного в договоре минимального количества туристов в группе, необходимого для того, чтобы путешествие состоялось;</span></p>

    <p style="text-align: justify; "><span >- непредвиденного роста транспортных тарифов;</span></p>

    <p style="text-align: justify; "><span >- введения новых или повышения старых таможенных сборов;</span></p>

    <p style="text-align: justify; "><span >- резкого изменения курсов национальных валют.</span></p>

    <p style="text-align: justify; "><b><span >Примечание: </span></b><span > действует в соответствии с Законом "Об основах туристской деятельности в РФ".</span></p>

    <p style="text-align: justify; "><span >- В случае изменения каких-либо условий поездки, ухудшающих их по сравнению с теми, которые изложены в настоящем Договоре, ее сроков либо возникновения иных обстоятельств, препятствующих полностью или частично выполнению Исполнителем своих обязательств по настоящему Договору, либо удорожания стоимости оплаченных Заказчиком туристических услуг. Исполнитель обязан незамедлительно проинформировать о вышеизложенном Заказчика для принятия последним решения об отказе от поездки без применения штрафных санкций либо о  доплате разницы в цене.</span></p>

    <p style="text-align: justify; "><b><span >Права и обязанности заказчика: Заказчик обязан:</span></b></p>

    <p style="text-align: justify; "><span >- Сообщить полную и достоверную информацию о себе и о лицах, выезжающих с Заказчиком.</span></p>

    <p style="text-align: justify; "><span >- Полностью согласовать с Исполнителем условия туристического обслуживания, с учетом личных возможностей и пожеланий.</span></p>

    <p style="text-align: justify; "><span >- Своевременно и в полном объеме оплатить услуги, предоставляемые ему Исполнителем.</span></p>

    <p style="text-align: justify; "><span >- Прибыть в назначенное место встречи группы не позднее, чем за 15 минут до отправления указанного в программе тура транспорта (при опоздании Заказчика, Исполнитель имеет право отправить основную группу туристов без него).</span></p>

    <p style="text-align: justify; "><span >- Соблюдать законодательство и правила поведения, принятые в месте пребывания, уважительно относиться к обычаям и верованиям местного населения.</span></p>

    <p style="text-align: justify; "><span >- Сохранять окружающую природу, бережно относиться к памятникам природы, истории и культуры в стране (месте) временного пребывания.</span></p>

    <p style="text-align: justify; "><span >- Соблюдать во время путешествия правила личной безопасности.</span></p>

    <p style="text-align: justify; "><span >- В случае нанесения ущерба принимающей стороне, или третьим лицам (гостинице, столовой, ресторану, транспортному средству и т.п.) возместить его на месте в полном объеме.</span></p>

    <p style="text-align: justify; "><b><span >Заказчик имеет право:</span></b></p>

    <p style="text-align: justify; "><span >- Владеть необходимой в полном объеме информацией о правилах поведения в месте временного пребывания, об обычаях местного населения, о<b> </b>религиозных обрядах, святынях, памятниках природы, истории, культуры и других объектах туристического показа, находящихся под охраной, состоянии окружающей природной среды.</span></p>

    <p style="text-align: justify; "><span >- Свободного передвижения, свободного доступа к туристическим ресурсам с учетом принятых в месте временного пребывания ограничительных мер.</span></p>

    <p style="text-align: justify; "><span >- На обеспечение личной безопасности своих потребительских прав.</span></p>

    <p style="text-align: justify; "><span >- Заказчик вправе требовать от Исполнителя, оказания ему всех услуг, входящих в тур, независимо от того, кем эти услуги оказываются.</span></p>

    <p style="text-align: justify; "><span >- На возмещение убытков и компенсацию морального вреда в случае невыполнения Исполнителем условий договора розничной купли-продажи туристического продукта в порядке, установленном законодательством РФ.</span></p>

    <p style="text-align: justify; "><span >- На содействие органов власти (органов местного самоуправления) места временного пребывания в получении правовой и иных видов неотложной помощи.</span></p>

    <p style="text-align: justify; "><span >- На беспрепятственный доступ к средствам связи.</span></p>

    <p style="text-align: justify; "><span >- Расторгнуть настоящий Договор в любое время.</span></p>

    <p style="text-align: justify; "><b><span >Ответственность сторон:</span></b></p>

    <p style="text-align: justify; "><span >- При неисполнении или ненадлежащем исполнении своих обязательств по настоящему Договору, Исполнитель производит полное возмещение убытков Заказчика, за исключением случаев, когда Заказчик не является потребителем по закону РФ "О защите прав потребителей".</span></p>

    <p style="text-align: justify; "><span >- В случае не предоставления какой-либо услуги, согласованной в Договоре, Исполнитель обязуется компенсировать Заказчику стоимость не предоставленной услуги.</span></p>

    <p style="text-align: justify; "><span >- В случае ненадлежащего предоставления какой-либо услуги Исполнитель компенсирует Заказчику лишь часть ее стоимости, определяемой по соглашению Сторон, а при не достижении такого соглашения - судом.</span></p>

    <p style="text-align: justify; "><span >- В случае отказа от поездки по инициативе Заказчика, Заказчику возвращается стоимость поездки, за вычетом фактически понесенных расходов за оформление заявки, подтвержденных документально (оплаты контрагенту/отелю за бронирование мест, заказа и бронирование транспорта, бронирование билетов в музеи, страховки и др.).</span></p>

    <p style="text-align: justify; "><span >- Отказ от поездки или изменение каких-либо условий поездки по сравнению с теми, которые содержатся в согласованном между сторонами Договоре, осуществляемые по инициативе Заказчика, оформляются в письменном виде с обязательным указанием даты подачи заявления об отказе или изменений условий. Либо изменение условий поездки по сравнению с теми, которые содержатся в согласованном между Сторонами Договоре, осуществляемое по инициативе Заказчика, рассматривается как его отказ от первоначального бронирования и заключение нового договора.</span></p>

    <p style="text-align: justify; "><span >- Исполнитель не несет ответственность <b>за </b>нарушение Заказчиком во время тура законодательства РФ. а также внутренних правил в местах пребывания (проживания) и не возмещает никаких возникших в связи с этим расходов и убытков Заказчика.</span></p>

    <p style="text-align: justify; "><span >- В случае, если происходит снятие Заказчика с поездки какими-либо компетентными органами без вины Исполнителя, Исполнитель не несет за это никакой ответственности и не возмещает никаких затрат Заказчика по настоящему Договору и в связи с ним.</span></p>

    <p style="text-align: justify; "><span >- В случае не предоставления сведений или предоставления недостоверных сведений Заказчиком о себе и лицах, выезжающих с ним, согласно настоящего Договору, повлекших срыв каких-либо туристических услуг, договор считается нарушенным со стороны Заказчика.</span></p>

    <p style="text-align: justify; "><span >- В случаях, не урегулированных настоящим Договором, ответственность наступает в соответствии с законодательством РФ.</span></p>

    <p style="text-align: justify; "><span >- Исполнитель оставляет за собой право в крайних случаях на замену места проживания и способов проезда на равнозначное или более лучшее, предварительно согласовав такую замену <b>с </b>Заказчиком.</span></p>

    <p style="text-align: justify; "><b><span >Форс-мажор</span></b></p>

    <p style="text-align: justify; "><span >- Стороны освобождаются от ответственности за частичное или полное неисполнение обязательств по настоящему Договору, если его неисполнение явилось следствием обстоятельств непреодолимой силы, возникших после заключения Договора в результате событий чрезвычайного характера, которые данная сторона не могла ни предвидеть, ни предотвратить различными мерами, например: землетрясение, ливни, сход лавин или другие стихийные бедствия, а также правительственные постановления или распоряжения государственных органов, война, вооруженные конфликты, и т.п.</span></p>

    <p style="text-align: justify; "><b><span >Вступление в силу, срок действия и порядок прекращения действия договора</span></b></p>

    <p style="text-align: justify; "><span >-  Настоящий Договор вступает в силу со дня его подписания Сторонами и действует до полного исполнения Сторонами своих обязательств по настоящему Договору. Лицо, подписавшее настоящий Договор от имени туристов, указанных в Заявке, представляет интересы всех лиц, включенных в Заявку. Исполнитель отвечает по настоящему Договору перед лицом, подписавшим данный Договор.</span></p>

    <p style="text-align: justify; "><span >-  В случае расторжения настоящего Договора Исполнитель возвращает Заказчику полную стоимость поездки, за исключением фактически понесенных затрат Сторон.</span></p>

    <p style="text-align: justify; "><span >- Настоящий Договор может быть расторгнут в случаях и в порядке, предусмотренном действующим законодательством и настоящим Договором.</span></p>

    <p style="text-align: justify; "><b><span >Порядок рассмотрения споров</span></b></p>

    <p style="text-align: justify; "><span >- Все споры и разногласия, которые могут возникнуть между сторонами по настоящему Договору, будут по возможности разрешаться путем переговоров между Сторонами на основании письменной претензии, поданной Заказчиком в течение 20 дней с момента окончания действия настоящего Договора. Данная претензия подлежит рассмотрению в 10-дневный срок со дня ее получения.</span></p>

    <p style="text-align: justify; "><span >- Возмещение убытков при расторжении договора осуществляется в соответствии с фактическими затратами сторон.</span></p>

    <p style="text-align: justify; "><span >- В случае наличия обоснованных претензий, Заказчик должен обратиться (в письменном виде) к представителю Исполнителя - принимающей фирме или сопровождающему группы. В случае невозможности решения проблемы на месте Заказчик имеет право обратиться к Исполнителю.</span></p>

    <p style="text-align: justify; "><span >-  В случае, если стороны не пришли к согласию по различным спорам и спорам и разногласиям, все споры подлежат рассмотрению в порядке, установленном Законодательством.</span></p>

    <p style="text-align: center; "><b><span >Подписи договаривающихся сторон:</span></b></p>


    <table width="100%" class="info_users">
        <tr>
            <th width="70%">ИСПОЛНИТЕЛЬ</th>
            <th>ЗАКАЗЧИК</th>
        </tr>

        <tr>
            <td><b><?=$arOrder["COMPANY"]["COMPANY_FULL_NAME"]?></b></td>
            <td><?=$arOrder["TOURISTS"][0]["NAME"]?></td>
        </tr>

        <tr>
            <td>Адрес: <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_COMPANY_ADRESS"]?></td>
            <td>дата рождения: <?=$arOrder["TOURISTS"][0]["BIRTHDAY"]?></td>
        </tr>

        <tr>
            <td><?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_COMPANY_PHONE"]?></td>
            <td>паспорт: <?=$arOrder["TOURISTS"][0]["PASSPORT"]?></td>
        </tr>

        <tr>
            <td>р/с:  <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_BILL"]?></td>
            <td></td>
        </tr>

        <tr>
            <td>в: <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_BANK"]?></td>
            <td></td>
        </tr>

        <tr>
            <td>ОГРН: <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_OGRN"]?></td>
            <td></td>
        </tr>

        <tr>
            <td>ИНН: <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_INN"]?></td>
            <td></td>
        </tr>

        <tr>
            <td>КПП: <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_KPP"]?></td>
            <td></td>
        </tr>

        <tr>
            <td>ОКПО: <?=$arOrder["COMPANY"]["ALL_PROPS"]["UF_COMPANY_OKPO"]?></td>
            <td></td>
        </tr>

        <tr>
            <td>
                <div class="w100 h30 bb1" style="width:300px;">                    
                </div>
            </td>
            <td></td>
        </tr>

    </table>

    <div class="w100 h30">
        <div class="fl">«</div><div class="underline fl tac" style="width: 30px;"> <?=date("d")?></div><div class="fl">»</div> 
        <div class="underline fl tac" style="width:100px; "><?=get_month_name(date("m"))?></div> 
        <div class="fl"><?=date("Y")?> г.</div>
    </div>



    <div class="w100">
        <div class="fr">
            <table class="MsoTableGrid" style="border-collapse: collapse; width: 545pt; border-collapse: collapse; border: none;"><tbody><tr><td style="width: 511px; width: 383pt;">
                            <p style="text-align: right; text-align: right;"><b> </b></p>
                        </td>
                        <td style="width: 216px; width: 162pt;">
                            <p style="text-align: center;"><b>Приложение </b></p>
                            <div class="w100 h30">
                                <div class="fr">
                                    <div class="fl">к договору №</div><div class="underline fl tac" style="width: 70px;margin:0 10px 0 0" ><?=$arOrder["ID"]?></div>
                                </div>
                            </div>
                            <div class="w100 h30">
                                <div class="fr">
                                    <div class="fl">от «</div><div class="underline fl tac" style="width: 30px;"> <?=date("d")?></div><div class="fl">»</div> 
                                    <div class="underline fl tac" style="width:100px; "><?=get_month_name(date("m"))?></div> <?=date("Y")?> г.
                                </div>
                            </div>
                            <p style="text-align: right; text-align: right;"><b> </b></p>
                        </td>
                    </tr></tbody>
            </table>
        </div>
    </div>

    <div class="w100 h30"></div>



    <h3>ЗАЯВЛЕНИЕ О СОГЛАСИИ НА ОБРАБОТКУ ПЕРСОНАЛЬНЫХ ДАННЫХ</h3>       

    <p style="text-align: justify;">Я, <span style="text-transform: uppercase; font-weight: bold;"><?=$arOrder["TOURISTS"][0]["NAME"]?></span>, в целях исполнения Договора, даю согласие фирме <b><?=$arOrder["COMPANY"]["COMPANY_FULL_NAME"]?></b>, в лице генерального директора Ильиной Л.В., на обработку моих персональных данных, содержащих сведения 
        о дате моего  рождения, адресе  моего  места жительства (регистрации), данных паспорта или другого документа, удостоверяющего личность, номере моего домашнего и/или мобильного телефона, адресе электронной почты.</p>

    <p style="text-align: justify;">Я осведомлен и согласен, что мои персональные данные могут обрабатываться в моем интересе методом смешанной (в том числе автоматизированной) обработки, систематизироваться, храниться, распространяться и передаваться третьим лицам, в том числе с использованием трансграничной передачи данных.</p>

    <p style="text-align: justify;">Настоящее согласие дается мною на срок действия Договора.</p>

    <p style="text-align: justify;">После окончания действия Договора я обязываю фирму <b><?=$arOrder["COMPANY"]["COMPANY_FULL_NAME"]?></b> <span >незамедлительно прекратить обработку моих персональных данных и уничтожить содержание моих персональных данных в информационной системе и на материальных носителях, либо, если для документов, содержащих мои</span> персональные данные, законодательством установлен срок их хранения, то в срок, не превышающий трех рабочих дней со дня окончания срока их хранения, установленного законом. </p>

    <p style="text-align: justify;"><b><span style="color: red;"> </span></b></p>

    <p style="text-align: justify;"><b><span style="color: red;"> </span></b></p>
    <br>

    <div class="w100 h30">
        <div class="fr">
            <div class="fl">«</div><div class="underline fl tac" style="width: 30px;"> <?=date("d")?></div><div class="fl">»</div> 
            <div class="underline fl tac" style="width:100px; "><?=get_month_name(date("m"))?></div> <?=date("Y")?> г.
        </div>
    </div> 

    <p style="text-align: right; text-align: right;"> </p>
    <br>
    <p style="text-align: right; text-align: right;">                                                                    
        Подпись: __________________</p>



</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>