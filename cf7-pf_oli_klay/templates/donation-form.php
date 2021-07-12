<?php
/**
 * This template use variables that must be declared before it is included:
 *  - $donation_inputs_template : the absolute path to template which specify the fields needed for a particular donation.
 *  - $bank_transfer_comment (optional)
 *  - $bank_transfer_reason (optional)
 *  - CF7PF_PLUGIN_DIR_URL
 */
?>
<script type="text/javascript">
    jQuery( document ).ready(function($) {
        // Set the reason for the donnation.
        if(window.location.hash){
            var hashParams = window.location.hash.substr(1).split('&'); // substr(1) to remove the `#`
            for(var i = 0; i < hashParams.length; i++){
                var p = hashParams[i].split('=');
                document.getElementById(p[0]).value = decodeURIComponent(p[1]);}}

        // Validate the form
        $('.child-sponsor form').validate({
            ignore: ".ignore, .ignore *",
            rules: {
                wert: {
                    number: true
                },
                email: {
                    email: true
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
    });
</script>

<ul class="tabs" data-tabs id="donation-tabs">
    <li class="tabs-title is-active">
        <a href="#panel-online-donation" aria-selected="true">
            <i class="fa fa-credit-card-alt" aria-hidden="true"></i>
            <?php _e('Online spenden', 'donation-form') ?>
        </a>
    </li>
    <li class="tabs-title">
        <a href="#panel-bank-transfer-donation">
            <i class="fa fa-university" aria-hidden="true"></i>
            <?php _e('Spende per Banküberweisung', 'donation-form') ?>
        </a>
    </li>
</ul>
<div class="tabs-content section step-1 child-sponsor" data-tabs-content="donation-tabs">
    <div class="tabs-panel is-active" id="panel-online-donation">
        <div class="row">
            <form method="POST" action="?step=redirect"  class="large-12 large-centered medium-12 medium-centered column" >
                <p><?php _e('Spende über eine sichere Online-Zahlung, mit Postfinance, Kreditkarte oder Twint.','donation-form' )?></p>

                <?php include($donation_inputs_template); ?>

                <h4 class="text-uppercase"><?php _e('Meine persönlichen Daten', 'child-sponsor-lang'); ?></h4>
                <div class="row">
                    <div class="small-12 medium-4 columns">
                        <label class="text-left middle"><?php _e('Vorname, Nachname', 'donation-form'); ?></label>
                    </div>
                    <div class="small-12 medium-8 columns">
                        <input type="text" required data-msg="<?php _e('Name erforderlich', 'child-sponsor-lang'); ?>" class="input-field" id="pname" name="pname" value="<?php echo $_SESSION["pname"]?>">
                    </div>
                </div>
                <div class="row">
                    <div class="small-12 medium-4 columns">
                        <label class="text-left middle"><?php _e('Strasse/Hausnr.', 'child-sponsor-lang'); ?></label>
                    </div>
                    <div class="small-12 medium-8 columns">
                        <input type="text" required data-msg="<?php _e('Strasse erforderlich', 'child-sponsor-lang'); ?>" class="input-field" id="street" name="street" value="<?php echo $_SESSION["pstreet"]?>">
                    </div>
                </div>
                <div class="row">
                    <div class="small-12 medium-4 columns">
                        <label class="text-left middle"><?php _e('PLZ/Ort', 'child-sponsor-lang'); ?></label>
                    </div>
                    <div class="small-6 medium-2 columns">
                        <input type="text" required data-msg="<?php _e('PLZ erforderlich', 'child-sponsor-lang'); ?>" class="input-field" id="zipcode" name="zipcode" value="<?php echo $_SESSION["pzip"]?>">
                    </div>
                    <div class="small-6 medium-6 columns no-padding-left">
                        <input type="text" required data-msg="<?php _e('Stadt erforderlich', 'child-sponsor-lang'); ?>" class="input-field" id="city" name="city" value="<?php echo $_SESSION["pcity"]?>">
                    </div>
                </div>
                <div class="row">
                    <div class="small-12 medium-4 columns">
                        <label class="text-left middle"><?php _e('Land', 'child-sponsor-lang'); ?></label>
                    </div>
                    <div class="mall-12 medium-8 columns">
                        <input type="text" required data-msg="<?php _e('Länd erforderlich', 'child-sponsor-lang'); ?>" class="input-field" id="country" name="country" value="<?php echo $_SESSION["pcountry"]?>">
                    </div>
                </div>
                <div class="row">
                    <div class="small-12 medium-4 columns">
                        <label class="text-left middle"><?php _e('E-Mail-Adresse', 'child-sponsor-lang'); ?></label>
                    </div>
                    <div class="small-12 medium-8 columns">
                        <input type="email" class="input-field" required data-msg="<?php _e('E-Mail-Adresse erforderlich', 'child-sponsor-lang'); ?>" id="email" name="email" value="<?php echo $_SESSION["email"]?>">
                <p><?php _e('Deine Spende an Compassion ist in der Schweiz steuerabzugsberechtigt.','donation-form' )?></p>
                    </div>
                </div>

                <div class="form-action">
                    <input type="submit" class="button button-blue button-small click_donate" value="<?php _e('Jetzt spenden', 'donation-form'); ?>"/>
                </div>
            </form>
        </div>
    </div>

    <div class="tabs-panel" id="panel-bank-transfer-donation">
        <div class="row">
            <div class="large-12 large-centered medium-12 medium-centered column">
                <p class="text-center middle"><?php _e('Du kannst per Post- oder Banküberweisung spenden.<br/>  Nachfolgend findest du die Informationen für die Zahlung. ', 'donation-form') ?></p>
                <h5 class="text-center middle">
                    Postfinance CCP 17-312562-0 <br/>
                    IBAN CH07 0900 0000 1731 2562 0<br/>
                    BIC POFICHBEXXX
                </h5>
                <?php if (isset($bank_transfer_comment)) : ?>
                <p class="text-center middle"><?php echo $bank_transfer_comment ?></p>
                <?php endif; ?>
                <div class="text-center">
                    <img class="text-center" src="<?php
                        $qs = http_build_query(array(
                            'reason' => $bank_transfer_reason,
                            'account' => "17-312562-0",
                            'for' => '<tspan x="0">' . __('Compassion Schweiz', 'compassion') . '</tspan><tspan x="0" dy="1.4em">1400 Yverdon-les-Bains</tspan>',
                        ));
                        echo CF7PF_PLUGIN_DIR_URL . "/templates/bank-transfer.php?" . $qs
                    ?>">
                </div>
            </div>
        </div>
    </div>
</div>