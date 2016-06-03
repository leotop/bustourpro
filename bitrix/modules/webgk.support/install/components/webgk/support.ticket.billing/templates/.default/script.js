$(function() {
    $('.billingTableControl').click(function() {
        $('.billingTimeTable').toggle();
        $('.addHours').toggle();
        $('.hideBillingTable').toggle();
    })
    
      
   $('body').on('keyup', 'input[name="billingHour"], input[name="billingMinute"]',  function(){
       console.log($(this).val());
       var val = $(this).val().replace(/\D/g, "");
       $(this).val(val);
   }) 
                      
   $("body").on('blur','.fieldsForBilling',function(){
       if ($(this).val() == '') {
           $(this).addClass("wrongData");
       } 
       else {
           $(this).removeClass("wrongData");
       }
   })  
})