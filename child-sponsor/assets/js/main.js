jQuery(document).ready(function($) {

    $.validator.addMethod("validIban", function(value, element) {
        var iban = element.value.replace(/\s+/g, '');

        return IBAN.isValid(iban);
    }, "Please enter a valid IBAN");

    $('.child-sponsor form').validate({
        ignore: ".ignore, .ignore *",
        rules: {
            iban: {
                validIban: true
            },
            hiddenRecaptcha: {
                required: function () {
                    if (grecaptcha.getResponse() == '') {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        },
        errorPlacement: function(error, element) {
            if((element.attr('type') === 'radio')){
                element.parent().before(error);
            }
            else{
                element.after(error);
            }
        }
    });

    $('select[name=consumer_source]').change(function() {
        var value = $(this).val();
		if ($.inArray(value, ['Muskathlon', 'Internet', 'Konzert/Veranstaltung', 'Kontakt durch Pate/Botschafter', 'Kontakt durch Compassion Advokate','Gottesdienst/Kirche','Freunde & Verwandte','Anzeige in Zeitschrift','Andere']) >= 0) {
//         if(value == 'Muskathlon' || value == 'Internet' || value == 'Konzert'){
            $('.consumer-source-text-wrapper').removeClass('hide');
            $('.consumer-source-text-wrapper').find('input').removeClass('ignore');
        } else {
            $('.consumer-source-text-wrapper').addClass('hide');
            $('.consumer-source-text-wrapper').find('input').addClass('ignore');
        }
    });

    $('.campaign input[type=checkbox]').on('ifToggled', function() {
       $('.campaign .hidden-form').toggleClass('ignore hidden');
    });
    
    

     $('.reco-checkbox input[type=checkbox]').on('ifToggled', function() {
      //  $('.reco-checkbox .hidden-form').toggleClass('ignore hidden');
      $("ul[id=" + $(this).attr("data-related") + "]").toggleClass('ignore hidden');
      
          //   $(this).parents().find('.hidden-form').toggleClass('ignore hidden');
    });

	$('#consumer_select').on('change', function() {
    var placeholder = $(this).find(':selected').data('placeholder');
    $('.place').attr('placeholder', placeholder);
});


	
/*
   $('.reco input[type=checkbox]').on('ifToggled', function() {
    // in case the 'ul' is next to the checkbox:
	//$(this).next('ul').toggleClass('ignore hidden');
    // in case the 'ul' is in the same element but not next to the checkox:
   //$(this).parents('li').find('ul').toggleClass('ignore hidden');
    // or maybe even better:
     $(this).closest('li').find('ul').toggleClass('ignore hidden');
  });
*/

});