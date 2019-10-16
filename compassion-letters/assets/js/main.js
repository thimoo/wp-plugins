jQuery(document).ready(function($) {

    $.ajaxSetup({ cache: false });

    jQuery.validator.addMethod("referenznummer", function(value, element) {
        // allow any non-whitespace characters as the host part
        return /^[0-9]{6,8}$/.test( value );
    }, 'Please enter a valid reference.');

    jQuery.validator.addMethod("patenkindnummer", function(value, element) {
        // allow any non-whitespace characters as the host part
        return /^[A-Z]{2}[0-9]{7,9}$/.test( value );
    }, 'Please enter a valid child number.');

    $.validator.addMethod(
        "maxfilesize",
        function (value, element) {
            return this.optional(element) || (element.files && element.files[0] && element.files[0].size < 1024 * 1024 * 10 && (element.files[0].type == 'image/png' || element.files[0].type == 'image/jpg' || element.files[0].type == 'image/jpeg'));
        },
        'Bitte beachten Sie die Vorgaben für den Bild-Upload/Merci de respecter les indications pour l\'envoi d\'une photo'
    );
    
    var showLoadingModal = function(classname) {
        $('.send-fail').hide();
        $('#loading-modal').removeClass('preview-loading send-loading send-success send-fail').addClass(classname).foundation('open');
        $('.loading-icon').show();
    };

    var failModal = function() {
        $('#loading-modal').removeClass('preview-loading send-loading send-success');
        $('.send-fail').show();
        $('.loading-icon').hide();
    };

    var hideLoadingModal = function() {
        $('#loading-modal').removeClass('preview-loading send-loading send-success send-fail').foundation('close');
    };

    var clearForm = function() {
        $('.compassion-letter-form')[0].reset();
        $(".iradio_flat-aero").removeClass('checked');
        $(".icheckbox_flat-aero").removeClass('checked');
        $('.letter-count').text(0);
    };

    $('.compassion-letter-form').validate({
        rules: {
            referenznummer: "referenznummer",
            patenkind: "patenkindnummer",
            image: 'maxfilesize'
        },
        errorPlacement: function(error, element) {
            if((element.attr('type') === 'radio')){
                element.parents('.radio-wrapper').first().prepend(error);
            }
            else{
                element.after(error);
            }
        },
        submitHandler: function(form) {
            var data = new FormData(jQuery('.compassion-letter-form')[0]);

            showLoadingModal('send-loading');

            data.append('action', 'compassion_letters_send');
            data.append('lang', wp_data.lang);

            $.ajax({
                url: wp_data.admin_ajax,
                data: data,
                type: 'POST',
                contentType: false,
                processData: false,
                lang: wp_data.lang,
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        showLoadingModal('send-success');
                        $('.loading-icon').hide();
                        clearForm();
                    } else {
                        failModal();
                    }
                },
                error: function(data) {
                    console.log('Error: ' + data);
                    failModal();
                }
            });

            return false;
        }
    });

    function countChar(val) {
        var len = val.value.length;
        if (len >= 1300) {
            val.value = val.value.substring(0, 1300);
            $('.letter-count').text(1300);
        } else {
            $('.letter-count').text(len);
        }
    };
    $(".compassion-letter-form textarea").on('keyup', function() {
        countChar(this);
    });

    $('#fileUpload').change(function(e) {

        var fileName = '';

        if( this.files && this.files.length > 1 )
            fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
        else
            fileName = e.target.value.split( '\\' ).pop();

        if( fileName )
            $('#filename').val(fileName);
        else
            $('#filename').val('');
    });

    $('#clear-file-input').click(function(){
        $('#fileUpload').val('');
        $('#filename').val('');

        var $el = $('#fileUpload');
        $el.wrap('<form>').closest('form').get(0).reset();
        $el.unwrap();
        $('#pdf_path').val('');

        return false;
    });

    /**
     * onChange handler to clear pdf_path
     */
    $('.compassion-letter-form .clear-pdf-on-change, .compassion-letter-form input[name=template]:radio').on('change', function() {
       $('#pdf_path').val('');
    });
    $('input[name=template]').on('ifToggled', function() {
        $('#pdf_path').val('');
    });

    $('body').on('click', '#preview-send-button', function() {
        var data = new FormData(jQuery('.compassion-letter-form')[0]);

        $('#preview-modal').foundation('close');
        showLoadingModal('send-loading');

        data.append('action', 'compassion_letters_send');
        data.append('lang', wp_data.lang);

        $.ajax({
            url: wp_data.admin_ajax,
            data: data,
            type: 'POST',
            contentType: false,
            lang: wp_data.lang,
            processData: false,
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    showLoadingModal('send-success');
                    $('.loading-icon').hide();
                    clearForm();
                } else {
                    failModal();
                }
            },
            error: function(data) {
                failModal();
                console.log('Error: ' + data);
            }
        });

        return false;
    });

    /**
     * Preview button handler
     */
    $('body').on('click', '#preview-button', function() {

        if($('.compassion-letter-form').valid()) {
            showLoadingModal('preview-loading');

            var data = new FormData(jQuery('.compassion-letter-form')[0]);

            data.append('action', 'compassion_letters_preview');
            data.append('lang', wp_data.lang);

            $.ajax({
                url: wp_data.admin_ajax,
                data: data,
                type: 'POST',
                lang: wp_data.lang,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(data) {
                    var thumbnails = data.thumbnails;
                    var thumbURL = data.thumbnailURL;

                    $('#preview-modal .content').html('');

                    $.each(thumbnails, function(i, thumb) {
                        $('#preview-modal .content').append('<div class="page"><h4>Seite ' + parseInt(i + 1) + '</h4><img src="' + thumbURL + thumb + '" /></div>')
                    });

                    if(thumbnails.length > 1) {
                        $('#preview-modal').addClass('large')
                    } else {
                        $('#preview-modal').removeClass('large');
                    }

                    hideLoadingModal();

                    $('#preview-modal').foundation('open');

                    $('#pdf_path').val(data.pdf);
                },
                error: function(data) {
                    console.log('Error: ' + data);
                }
            });
        }

        return false;
    });

    $('body').on('click', '.close-reveal-modal', function() {
       $(this).parents('.reveal').foundation('close');
    });

});