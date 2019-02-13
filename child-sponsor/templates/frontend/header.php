<?php
$child_data = get_child_meta($session_data['childID']);

//vous identifiez la langue courante
$my_current_lang = apply_filters('wpml_current_language', NULL);


?>
<script type="text/javascript">
jQuery(document).ready(function($) {
    // Validate the forms
    $.validator.addMethod("slashDate", function(value, element) {
        return /(0[1-9]|[1-2][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d\d\d\d/.test(value);
    }, "Please enter a valid date");

    $('.child-sponsor form').validate({
        ignore: ".ignore, .ignore *",
        rules: {
            birthday: {
                slashDate: true
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

    // Show detail input based on user.
    $('#consumer_select').change( function() {
        var placeholder = $(this).find(':selected').data('placeholder');
        if (placeholder === undefined) {
            $('.consumer-source-text-wrapper').addClass('hide');
            $('.consumer-source-text-wrapper').find('input').addClass('ignore');
	} else {
      	    $('.place').attr('placeholder', placeholder);
	    
	    $('.consumer-source-text-wrapper').removeClass('hide');
            $('.consumer-source-text-wrapper').find('input').removeClass('ignore');
	}
    });
});
</script>

<div class="section background-blue section_abgerissen_unten has_breadcrumb">
    <div class="row section_breadcrumb">
        <?php compassion_breadcrumb(false); ?>
    </div>

    <div class="row">
        <div class="child-image" style="background-image: url(<?php echo $child_data['portrait']; ?>);">

        </div>

        <?php
        if ($my_current_lang == "fr") {
            $msk = isset($_SESSION['msk_name']) && isset($_SESSION['msk_participant_name']);
            if ($msk) { ?>
                <h2 style="text-align: center;">
                    <?= __('Danke, dass Sie sich für', 'child-sponsor-lang') ?> <?= $child_data['name'] ?> <?= __('einsetzen. Die Patenschaft wird das Leben dieses Kindes nachhaltig verändern.', 'child-sponsor-lang'); ?>
                </h2>
            <?php } else { ?>
                <h2 style="text-align: center;"><?php _e('Schön, dass Sie ', 'child-sponsor-lang'); ?> <?php echo $child_data['name']; ?></h2>
            <?php }
        } elseif ($my_current_lang == "de") { ?>
            <h2 style="text-align: center;"><?php _e('Schön, dass Sie Pate von', 'child-sponsor-lang'); ?> <?php echo $child_data['name']; ?>
                <?php _e('werden möchten', 'child-sponsor-lang'); ?></h2>
        <?php } elseif ($my_current_lang == "it") {
            ?>
            <h2 style="text-align: center;"><?php _e('Schön, dass Sie', 'child-sponsor-lang'); ?> <?php echo $child_data['name']; ?></h2>
        <?php } ?>
        <p style="text-align: center;" class="subtitle">
            <?php if ($msk) { ?>
                <?= $_SESSION['msk_participant_name'] ?>, <?= __('nimmt am', 'child-sponsor-lang') ?> <?= $_SESSION['msk_name'] ?><?= __(' teil.', 'child-sponsor-lang') ?> <?= __('und dankt Ihnen für Ihre Unterstützung!', 'child-sponsor-lang') ?>
            <?php } else {
                _e('Sie werden das Leben des Kindes für immer verändern.', 'child-sponsor-lang');
            } ?>
        <p>
	       
    </div>

</div>
