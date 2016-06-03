$(function() {
    $('.billingTableControl').click(function() {
        $('.billingTimeTable').toggle();
        $('.addHours').toggle();
        $('.hideBillingTable').toggle();
    })
    ////
    $('body').on('click', '.submitBilling,.deleteBillingPosition', function(e) {
        e.preventDefault();

        var record_id = e.target.previousElementSibling.value;

        switch(e.target.dataset.action) {
            case 'add':

                if ($('input[name="billingHour"]').val().replace(/\D/g, "") == '' || $('input[name="billingMinute"]').val().replace(/\D/g, "") == '' || $('textarea[name="billingComment"]').val().replace(/\s/g, "") == '' || parseInt($('input[name="billingMinute"]').val()) < 0 || parseInt($('input[name="billingMinute"]').val()) > 59) {
                    alert("Поля часы и минуты не могут быть пусты и могут содержать только цифры ! Поле комментарий также не должно быть пустым.");
                    throw new Error("Поля часы и минуты не могут быть пусты и могут содержать только цифры !");
                }

                var hours = $('input[name="billingHour"]').val();
                var minutes = $('input[name="billingMinute"]').val();
                var comment = $('textarea[name="billingComment"]').val();
                var user_id = $('input[name="supportUserID"]').val();
                var ticket_id = $('input[name="supportTicketID"]').val();
                var client = $('input[name="supportClientID"]').val();
                var service_id = $('select[name="serviceID"]').val();

                $.post("/ajax/billingHoursHandler.php", {
                    user_id : user_id,
                    hours : hours,
                    minutes : minutes,
                    comment : comment,
                    ticket_id : ticket_id,
                    record_id : record_id,
                    client : client,
                    service_id: service_id,
                    action : e.target.dataset.action
                    }, function(data) {
                        //console.log(data);
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

                break;
            case 'delete':
                delete_time = $(e.target).closest('tr').children('td.tableHours').children('p').text().split(':');
                billingTotalTime = $('.billingTotal').text().split(':');
                $(e.target).closest('tr').remove();

                var ticket_id = $('input[name="supportTicketID"]').val();

                hoursAfterDelete = parseInt(billingTotalTime[0]) - parseInt(delete_time[0]);
                minutesAfterDelete = parseInt(billingTotalTime[1]) - parseInt(delete_time[1]);

                if (minutesAfterDelete < 0) {
                    hoursAfterDelete -= 1;
                    minutesAfterDelete = 60 - (parseInt(delete_time[1]) - parseInt(billingTotalTime[1]));
                }

                $('.billingTotal').text(hoursAfterDelete + ':' + minutesAfterDelete);

                $.post("/ajax/billingHoursHandler.php", {
                    record_id : record_id,
                    ticket_id : ticket_id,
                    action : e.target.dataset.action
                    }, function(data) {
                });

                break;
        }
        $('.fieldsForBilling').each(function() {
            $(this).val('');
        })
    })
})