<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<?if (!empty($arResult["ERRORS"])):?>
    <div class="alert alert-danger">
        <?= implode("<br />", $arResult["ERRORS"]);?>
    </div>
    <?endif?>

<?if ($arResult["MESSAGE"]):?>
    <div class="alert alert-success">
        <?= $arResult["MESSAGE"];?>
    </div>
    <?endif?>

<form name="TOUR_DESIGNER" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="TOUR_DESIGNER" value="1">
    <?=bitrix_sessid_post()?>

    <? if ($arResult["STEP"] > 1):?>
        <?= InputType("hidden", "BUS", $arResult["BUS"]);?>
        <? endif;?>

    <? if ($arResult["STEP"] > 2):?>
        <?= InputType("hidden", "DIRECTION", $arResult["DIRECTION"]);?>
        <? endif;?>

    <? if ($arResult["STEP"] > 3):?>
        <? foreach ($arResult["LIST_DIRECTION_DATA__ROOM"] as $kIdRoom => $vRoom):?>
            <?= InputType("hidden", "LIST_DIRECTION_DATA__ROOM[{$kIdRoom}][S]", $vRoom["S"]);?>
            <?= InputType("hidden", "LIST_DIRECTION_DATA__ROOM[{$kIdRoom}][NR]", $vRoom["NR"]);?>
            <?= InputType("hidden", "LIST_DIRECTION_DATA__ROOM[{$kIdRoom}][NAR]", $vRoom["NAR"]);?>
            <? endforeach;?>
        <? endif;?>

    <? if ($arResult["STEP"] > 4):?>
        <?= InputType("hidden", "DURATION", $arResult["DURATION"]);?>
        <? foreach ($arResult["LIST_DATE_DEPARTURE"] as $vDATE_DEPARTURE):?>
            <?= InputType("hidden", "LIST_DATE_DEPARTURE[]", htmlspecialcharsex($vDATE_DEPARTURE));?>
            <? endforeach;?>
        <? endif;?>

    <? if ($arResult["STEP"] > 5):?>
        <? foreach ($arResult["DIRECTION_DATA__LIST_CITY"] as $vDIRECTION_DATA__LIST_CITY):?>
            <? if (empty($arResult["DIRECTION_DATA__LIST"][$vDIRECTION_DATA__LIST_CITY["ID"]])) {continue;}?>
            <? foreach ($arResult["DIRECTION_DATA__LIST_HOTEL"] as $vDIRECTION_DATA__LIST_HOTEL):?>
                <? if (empty($arResult["DIRECTION_DATA__LIST"][$vDIRECTION_DATA__LIST_CITY["ID"]][$vDIRECTION_DATA__LIST_HOTEL["ID"]])) {continue;}?>
                <? foreach ($arResult["LIST_FORMATION_DATE"] as $kDate =>  $vDate):?>
                    <? foreach (array_keys($arResult["DIRECTION_DATA__LIST"][$vDIRECTION_DATA__LIST_CITY["ID"]][$vDIRECTION_DATA__LIST_HOTEL["ID"]]) as $vIdRoom):?>
                        <?
                            $_partKey = "[{$vIdRoom}][{$kDate}]";
                            $_item = $arResult["LIST_FORMATION_DATA"][$vIdRoom][$kDate];
                        ?>
                        <input type="hidden" name="LIST_FORMATION_DATA__FORM<?= $_partKey;?>[BUS]" value="<?= $_item["BUS"];?>">

                        <input type="hidden" name="LIST_FORMATION_DATA__FORM<?= $_partKey;?>[PRICE1]" value="<?= htmlspecialcharsex($_item["PRICE1"]);?>">
                        <? if (array_key_exists("PRICE2", $_item)):?>
                            <input type="hidden" name="LIST_FORMATION_DATA__FORM<?= $_partKey;?>[PRICE2]" value="<?= htmlspecialcharsex($_item["PRICE2"]);?>">
                            <? endif;?>
                        <? endforeach;?>
                    <? endforeach;?>
                <? endforeach;?>
            <? endforeach;?>
        <? endif;?>

    <? if ($arResult["STEP"] == 1):?>
        <div class="form-group">
            <label>Выберите автобус</label>
            <?
                $arr = array();
                foreach ($arResult["LIST_BUS"] as $vElement) {
                    $arr["REFERENCE"][] = $vElement["NAME"];
                    $arr["REFERENCE_ID"][] = $vElement["ID"];
                }

                echo SelectBoxFromArray("BUS", $arr, $arResult["BUS"], "--- выбрать ---", 'class="form-control input-xlarge"');
            ?>
        </div>
        <? endif; ?>

    <? if ($arResult["STEP"] == 2):?>
        <div class="form-group">
        <label>Выберите направление</label>
        <?
            $arr = array();
            foreach ($arResult["LIST_DIRECTION"] as $vElement) {
                $arr["REFERENCE"][] = $vElement["NAME"];
                $arr["REFERENCE_ID"][] = $vElement["ID"];
            }

        ?>

        <? echo SelectBoxFromArray("DIRECTION", $arr, $arResult["DIRECTION"], "--- выбрать ---", 'class="form-control input-xlarge"');?>
        <? endif;?>

    <? if ($arResult["STEP"] == 3):?>
        <script>
            //проверяем наличие гостиниц в направлениях. если нет ни одной, удаляем направление
            function directions_check(){
                $(".direction_block").each(function(){
                    if (!$(this).find(".hotel_block").length > 0) {
                        $(this).remove();
                    }
                })
            }
        </script>
        <div class="form-group">
            <div>
                <? foreach ($arResult["DIRECTION_DATA__LIST_CITY"] as $vDIRECTION_DATA__LIST_CITY):?>
                    <? if (empty($arResult["DIRECTION_DATA__LIST"][$vDIRECTION_DATA__LIST_CITY["ID"]])) {continue;}?>
                    <div id="d_item__direction__<?= $vDIRECTION_DATA__LIST_CITY["ID"];?>" class="direction_block">
                        <h3><?= $vDIRECTION_DATA__LIST_CITY["NAME"];?></h3>

                        <? foreach ($arResult["DIRECTION_DATA__LIST_HOTEL"] as $vDIRECTION_DATA__LIST_HOTEL):?>

                            <? if (empty($arResult["DIRECTION_DATA__LIST"][$vDIRECTION_DATA__LIST_CITY["ID"]][$vDIRECTION_DATA__LIST_HOTEL["ID"]])) {continue;}?>
                            <div class="hotel_block">

                                <?/* <h4><input type="button" class="btn btn-danger btn-xs" value="Удалить" onclick="$('#d_item__direction__<?= $vDIRECTION_DATA__LIST_CITY["ID"];?>').remove();"> <?= $vDIRECTION_DATA__LIST_HOTEL["NAME"];?></h4> */?>
                                <h4><input type="button" class="btn btn-danger btn-xs" value="Удалить" onclick="$(this).parents('.hotel_block').remove(); directions_check();"> <?= $vDIRECTION_DATA__LIST_HOTEL["NAME"];?></h4>


                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 25px;"></th>
                                            <th>Тип номера</th>
                                            <th style="width: 220px">Основных номеров</th>
                                            <th style="width: 220px">Дополнительное место</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <? foreach ($arResult["DIRECTION_DATA__LIST_ROOM"] as $vDIRECTION_DATA__LIST_ROOM):?>
                                            <? if (!($vDIRECTION_DATA__LIST_HOTEL["ID"] == $vDIRECTION_DATA__LIST_ROOM["PROPERTY_HOTEL_VALUE"])) {continue;}?>
                                            <tr class="tr_LIST_DIRECTION_DATA__ROOM">
                                                <td>
                                                    <input type="checkbox" value="1" class="LIST_DIRECTION_DATA__ROOM__S" name="LIST_DIRECTION_DATA__ROOM[<?= $vDIRECTION_DATA__LIST_ROOM["ID"];?>][S]" <?= $arResult["LIST_DIRECTION_DATA__ROOM"][$vDIRECTION_DATA__LIST_ROOM["ID"]]["S"]? 'checked="checked"': '';?>></label>
                                                </td>
                                                <td><?= $vDIRECTION_DATA__LIST_ROOM["NAME"];?></td>
                                                <td>
                                                    <select class="form-control input-sm input-medium" name="LIST_DIRECTION_DATA__ROOM[<?= $vDIRECTION_DATA__LIST_ROOM["ID"];?>][NR]">
                                                        <? for ($i = 0; $i <= $vDIRECTION_DATA__LIST_ROOM["PROPERTY_NUMBER_ROOM_VALUE"]; $i++):?>
                                                            <option value="<?= $i;?>" <?= ($i == $arResult["LIST_DIRECTION_DATA__ROOM"][$vDIRECTION_DATA__LIST_ROOM["ID"]]["NR"])? 'selected="selected"': '';?>><?= $i;?></option>
                                                            <? endfor;?>
                                                    </select>
                                                </td>
                                                <td><?= $vDIRECTION_DATA__LIST_ROOM["PROPERTY_IS_ADD_ADDITIONAL_SEATS_VALUE"];?></td>
                                            </tr>
                                            <? endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                            <? endforeach;?>
                        <hr>
                    </div>
                    <? endforeach;?>
            </div>
        </div>

        <script>
            $(function() {
                $('.LIST_DIRECTION_DATA__ROOM__S').bind('change', function() {
                    f = $(this).parents('tr');
                    f.removeClass('danger');
                    if (!$(this).is(':checked')) {
                        f.addClass('danger');
                    }
                });

                $('.LIST_DIRECTION_DATA__ROOM__S').change();
            });
        </script>
        <? endif;?>

    <? if ($arResult["STEP"] == 4):?>
        <div class="form-group">
            <label>Продолжительность тура</label>
            <?
                $arr = array();
                foreach ($arResult["LIST_DURATION"] as $vElement) {
                    $arr["REFERENCE"][] = $vElement["VALUE"] .' '. declension_of_numerals($vElement["VALUE"], array('день', 'дня', 'дней'));
                    $arr["REFERENCE_ID"][] = $vElement["ID"];
                }

                echo SelectBoxFromArray("DURATION", $arr, $arResult["DURATION"], "--- выбрать ---", 'class="form-control input-medium"');
            ?>
        </div>


        <div class="form-group">
            <label>Даты отъезда</label>
            <div>
                <input type="button" class="btn btn-default" value="Выберите дату отъезда" onclick="$('#datepicker').pickmeup('show');">
            </div>
            <div style="">
                <div id="datepicker"></div>
            </div>

            <div id="d_list__date_departure">
                <? foreach ($arResult["LIST_DATE_DEPARTURE"] as $vDATE_DEPARTURE):?>
                    <div><?= htmlspecialcharsex($vDATE_DEPARTURE);?></div>
                    <input type="hidden" name="LIST_DATE_DEPARTURE[]" value="<?= htmlspecialcharsex($vDATE_DEPARTURE);?>">
                    <? endforeach;?>
            </div>
        </div>
        <p><b>*</b>Чтобы отменить выбор даты, щелкните по ней еще раз в календаре</p>

        <script>
            $(document).ready(function() {
                $('#datepicker').pickmeup({
                    //flat: true,
                    mode: 'multiple',
                    format: 'Y-m-d',
                    calendars: 5,

                    <? if (!empty($arResult["LIST_DATE_DEPARTURE"])):?>
                        date: [<?
                            $isFirst = true;
                            foreach ($arResult["LIST_DATE_DEPARTURE"] as $vDATE_DEPARTURE) {
                                if (!$isFirst) {
                                    echo ',';
                                } else {
                                    $isFirst = false;
                                }
                                echo "'". $vDATE_DEPARTURE ."'";
                            }
                        ?>],
                        <? endif;?>
                    before_show: function() {
                        if ($('[name^=LIST_DATE_DEPARTURE]').size() == 0) {
                            $('#datepicker').pickmeup('clear');
                        }
                    },
                    change: function(listDate) {
                        listDate.sort();
                        $('#d_list__date_departure').html('');
                        for (index in listDate) {
                            date = listDate[index];
                            html = '<div>'+ date +'</div>';
                            html += '<input type="hidden" name="LIST_DATE_DEPARTURE[]" value="'+date+'">';
                            $('#d_list__date_departure').append(html);
                        }
                    }
                });
            });
        </script>
        <? endif;?>

    <? if ($arResult["STEP"] == 5):?>
        <div class="form-group">
            <?
                $arBUS = array();
                foreach ($arResult["LIST_BUS"] as $vElement) {
                    $arBUS["REFERENCE"][] = $vElement["NAME"];
                    $arBUS["REFERENCE_ID"][] = $vElement["ID"];
                }
            ?>

            <? foreach ($arResult["DIRECTION_DATA__LIST_CITY"] as $vDIRECTION_DATA__LIST_CITY):?>
                <? if (empty($arResult["DIRECTION_DATA__LIST"][$vDIRECTION_DATA__LIST_CITY["ID"]])) {continue;}?>
                <div id="d_item__direction__<?= $vDIRECTION_DATA__LIST_CITY["ID"];?>">
                    <h3><?= $vDIRECTION_DATA__LIST_CITY["NAME"];?></h3>

                    <? foreach ($arResult["DIRECTION_DATA__LIST_HOTEL"] as $vDIRECTION_DATA__LIST_HOTEL):?>

                        <? if (empty($arResult["DIRECTION_DATA__LIST"][$vDIRECTION_DATA__LIST_CITY["ID"]][$vDIRECTION_DATA__LIST_HOTEL["ID"]])) {continue;}?>
                        <div>

                            <h4><?= $vDIRECTION_DATA__LIST_HOTEL["NAME"];?></h4>

                            <table class="table table-hover table-bordered" style="width: 1%;">
                                <thead>
                                    <tr>
                                        <th>Дата заезда</th>
                                        <th>Дата отъезда</th>

                                        <? foreach (array_keys($arResult["DIRECTION_DATA__LIST"][$vDIRECTION_DATA__LIST_CITY["ID"]][$vDIRECTION_DATA__LIST_HOTEL["ID"]]) as $vIdRoom):?>
                                            <th><?= $arResult["DIRECTION_DATA__LIST_ROOM"][$vIdRoom]["NAME"];?></th>
                                            <? endforeach;?>
                                        <th>Автобус</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <? 
                                        $i = 0;
                                        foreach ($arResult["LIST_FORMATION_DATE"] as $kDate =>  $vDate):?>
                                        <tr data-id-hotel="<?= $vDIRECTION_DATA__LIST_HOTEL["ID"];?>" data-date="<?= $kDate;?>">
                                            <td style="white-space: nowrap;"><?= $vDate["DATE_FROM"];?></td>
                                            <td style="white-space: nowrap;"><?= $vDate["DATE_TO"];?></td>

                                            <? $idBus = null;?>
                                            <? foreach (array_keys($arResult["DIRECTION_DATA__LIST"][$vDIRECTION_DATA__LIST_CITY["ID"]][$vDIRECTION_DATA__LIST_HOTEL["ID"]]) as $vIdRoom):?>
                                                <?
                                                    $_partKey = "[{$vIdRoom}][{$kDate}]";
                                                    $_item = $arResult["LIST_FORMATION_DATA"][$vIdRoom][$kDate];
                                                ?>
                                                <td style="white-space: nowrap;">
                                                    <input type="hidden" name="LIST_FORMATION_DATA__FORM<?= $_partKey;?>[BUS]" value="<?= $_item["BUS"];?>" class="list_formation_data_form_bus">

                                                    <input type="text" name="LIST_FORMATION_DATA__FORM<?= $_partKey;?>[PRICE1]" value="<?= htmlspecialcharsex($_item["PRICE1"]);?>" class="form-control input-small input-sm first_place first_place_<?=$vIdRoom?>" style="display: inherit;" rel="<?=$vIdRoom?>">
                                                    <? if (array_key_exists("PRICE2", $_item)):?>
                                                        <input type="text" name="LIST_FORMATION_DATA__FORM<?= $_partKey;?>[PRICE2]" value="<?= htmlspecialcharsex($_item["PRICE2"]);?>" class="form-control input-small input-sm second_place second_place_<?=$vIdRoom?>" style="display: inherit; margin-left: 10px;" placeholder="доп. место" >
                                                        <? endif;?>
                                                    <?if ($i == 0){?>
                                                        &nbsp;&#8595;<a href="javascript:void(0)" title="скопировать цену для этого номера на все даты" onclick="copyPrice(this)"><img src="/i/copy.jpg"></a>
                                                        <?}?>
                                                </td>
                                                <?$idBus = $_item["BUS"];?>
                                                <? endforeach;?>
                                            <td><?= SelectBoxFromArray("", $arBUS, $idBus, "", 'class="form-control input-large input-sm list_formation_data_form_bus__selectAll" data-id-hotel="'. $vDIRECTION_DATA__LIST_HOTEL["ID"] .'" data-date="'. $kDate .'"');?></td>
                                        </tr>
                                        <? 
                                            $i++;
                                            endforeach;?>
                                </tbody>
                            </table>


                        </div>
                        <? endforeach;?>
                </div>
                <hr>
                <? endforeach;?>
            <script>

            //копирование цены для номера на все даты
                function copyPrice(e){
                    var first_place_price = $(e).siblings(".first_place").val();
                    var second_place_price = $(e).siblings(".second_place").val();
                    var id = $(e).siblings(".first_place").attr("rel");

                    if (first_place_price){
                        $(".first_place_" + id).val(first_place_price);
                    }
                    if (second_place_price) {
                        $(".second_place_" + id).val(second_place_price);                          
                    }

                }

                $(document).ready(function() {
                    //одновременное переключение автобусов у туров с одинаковой датой
                    /*
                    $('.list_formation_data_form_bus__selectAll').bind('change', function() {
                    date = $(this).data('date');
                    idHotel = $(this).data('id-hotel');
                    val = $(this).find('option:selected').val();
                    $('tr[data-date='+ date +'][data-id-hotel='+ idHotel +'] [name^=LIST_FORMATION_DATA__FORM].list_formation_data_form_bus').val(val);
                    });
                    */
                });
            </script>
        </div>
        <? endif;?>

    <? if ($arResult["STEP"] == 6):?>
        <div class="form-group">
            <label>Название тура</label>
            <input type="text" name="TOUR_NAME" value="<?= htmlspecialcharsex($arResult["TOUR_NAME"]);?>" class="form-control input-xxlarge">
        </div>
        <? endif; ?>
    <br>

    <input type="hidden" name="STEP" value="<?= $arResult["STEP"] + 1;?>">
    <? if ($arResult["STEP"] > 1):?>
        <button class="btn btn-info" type="button" onclick="$('[name=STEP]').val(<?= $arResult["STEP"] - 1;?>).parents('form').submit();">Назад</button>&nbsp;&nbsp;&nbsp;
        <? endif;?>

    <input type="submit" class="btn btn-primary" name="BNEXT" value="Далее">

</form>