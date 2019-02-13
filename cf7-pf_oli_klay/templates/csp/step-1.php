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

<ul class="tabs" data-tabs id="example-tabs">
    <li class="tabs-title is-active"><a href="#panel1" aria-selected="true"><i class="fa fa-credit-card-alt"
                                                                               aria-hidden="true"></i> <?php _e('Online spenden', 'donation-form') ?>
        </a></li>
    <li class="tabs-title"><a href="#panel2"><i class="fa fa-university"
                                                aria-hidden="true"> </i> <?php _e('Spende per Banküberweisung', 'donation-form') ?>
        </a></li>
</ul>
<div class="tabs-content section  step-1 child-sponsor" data-tabs-content="example-tabs">
    <div class="tabs-panel is-active" id="panel1">
        <div class="row">
	        <h4 style="text-transform: uppercase;text-align: center; color:#0054a6"><?php _e('Ich möchte das Kinder-Überlebensprogramm unterstützen', 'donation-form') ?></h4>
            <form method="POST" action="?step=2" class="large-12 large-centered medium-12 medium-centered column">


                <div class="row">
                    <div class="small-4 columns">
<!--                         <label class="text-left middle"><?=__('Choix du don', 'donation-form')?></label> -->
                    </div>
                    <div class="small-8 columns">
                        <div style="margin-bottom: 16px;">
                              <label style="display:inline-block; margin-right:8px;">
                                <input type="radio" name="choix_don_unique_mensuel" value="don_mensuel" id="don_mensuel" checked >
                                <?=__('Monatlich', 'donation-form')?>
                            </label>
                           <label style="display:inline-block">
                                <input type="radio" name="choix_don_unique_mensuel" value="don_unique" id="don_unique" >
                                <?=__('Einmalige Spende', 'donation-form')?>
                            </label>

                        </div>
<!--
                        <div style="margin-bottom: 16px;" onclick="changer_don('mensuel');">
	                          <label>
                                <input type="radio" name="choix_don_unique_mensuel" value="don_unique" id="don_unique" >
                                <?=__('Don unique', 'donation-form')?>
                            </label>
                        </div>
-->
                    </div>
                </div>
                <script>
                    jQuery(function() {
                        if (jQuery('input[name="wert"]').val() !== '') {
                            jQuery('#don_unique').prop('checked', 'checked');
                            jQuery('.don_unique').show();
                            jQuery('.don_mensuel').hide();
                            jQuery('#fonds').attr('name', 'fonds_disabled').attr('id', 'fonds_disabled');
                            jQuery('#fonds_disabled').after('<input name="fonds" id="hi_fonds" type="hidden" value="csp">');
                        }
                        else {
                            jQuery('#don_mensuel').prop('checked', 'checked');
                        }

                        jQuery('#don_unique').on('ifChanged', function() {
                            if (jQuery(this).prop('checked')) {
                                jQuery('.don_unique').show();
                                jQuery('.don_mensuel').hide();
                                jQuery('#fonds').attr('name', 'fonds_disabled').attr('id', 'fonds_disabled');
                                jQuery('#fonds_disabled').after('<input name="fonds" id="hi_fonds" type="hidden" value="csp">');
                            } else {
                                jQuery('.don_mensuel').show();
                                jQuery('.don_unique').hide();
                                jQuery('#fonds_disabled').attr('name', 'fonds').attr('id', 'fonds');
                                jQuery('#hi_fonds').remove();
                            }
                        });
                    });
                </script>
                <div class="row don_unique" style="display: none;">
                    <div class="small-4 columns">
<!--                         <label class="text-left middle"><?php _e('Je désire faire un don unique', 'donation-form'); ?></label> -->
                    </div>
                    <div class="small-8 columns">
                        <input type="text"
                               placeholder="<?php _e('bitte nur Zahlen', 'donation-form'); ?>"
                               data-msg="<?php _e('Betrag erforderlich', 'donation-form'); ?>" class="input-field"
                               name="wert"
                               value="<?php echo (isset($session_data['wert'])) ? $session_data['wert'] : ''; ?>">
                    </div>
                </div>

                <div class="row don_mensuel">
                    <div class="small-4 columns">
<!--                         <label class="text-left middle"><?php _e('Je désire faire un don mensuel', 'donation-form'); ?></label> -->
                    </div>
                    <div class="small-8 columns">
                        <div class="select-wrapper">
                            <select name="fonds" id="fonds" class="input-field">
                                <option value="csp_mensuel_30" selected="selected" <?php echo (isset($session_data['fonds']) && $session_data['fonds'] == "mensuel 30") ? 'selected' : '' ?>><?php _e('Monatliche Spende von CHF 30.-', 'donation-form'); ?></option>
                                <option value="csp_mensuel_60"  <?php echo (isset($session_data['fonds']) && $session_data['fonds'] == "mensuel 60") ? 'selected' : '' ?>><?php _e('Monatliche Spende von CHF 60.-', 'donation-form'); ?></option>
                                <option value="csp_mensuel_90" <?php echo (isset($session_data['fonds']) && $session_data['fonds'] == "mensuel 90") ? 'selected' : '' ?>><?php _e('Monatliche Spende von CHF 90.-', 'donation-form'); ?></option>
                            </select>
                        </div>
                        <p class="small"> <?php _e('Nach Ihrer ersten Spende wird Compassion Schweiz Ihnen einen Einzahlungsschein für die weiteren Spenden zusenden.', 'donation-form'); ?></p>
                    </div>
                </div>
                <!--
            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Patenkindnummer', 'donation-form'); ?></label>
                </div>
                <div class="small-8 columns">
                    <input type="text" required data-msg="<?php _e('Patenkindnummer erforderlich', 'donation-form'); ?>" placeholder="<?php _e('ie : BO012301234', 'donation-form'); ?>" class="input-field" name="refenfant" value="<?php echo (isset($session_data['refenfant'])) ? $session_data['refenfant'] : ''; ?>">
                </div>
            </div>
-->


                <h4 style="color:#0054a6" class="text-uppercase"><?php _e('Meine persönlichen Daten', 'child-sponsor-lang'); ?></h4>


                <div class="row">
                    <div class="small-4 columns">
                        <label class="text-left middle"><?php _e('Nachname', 'child-sponsor-lang'); ?></label>
                    </div>
                    <div class="small-8 columns">
                        <input type="text" required
                               data-msg="<?php _e('Nachname erforderlich', 'child-sponsor-lang'); ?>"
                               class="input-field" name="last_name"
                               value="<?php echo (isset($session_data['last_name'])) ? $session_data['last_name'] : ''; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="small-4 columns">
                        <label class="text-left middle"><?php _e('Vorname', 'child-sponsor-lang'); ?></label>
                    </div>
                    <div class="small-8 columns">
                        <input type="text" required
                               data-msg="<?php _e('Vorname erforderlich', 'child-sponsor-lang'); ?>" class="input-field"
                               name="first_name"
                               value="<?php echo (isset($session_data['first_name'])) ? $session_data['first_name'] : ''; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="small-4 columns">
                        <label class="text-left middle"><?php _e('Strasse/Hausnr.', 'child-sponsor-lang'); ?></label>
                    </div>
                    <div class="small-8 columns">
                        <input type="text" required
                               data-msg="<?php _e('Strasse erforderlich', 'child-sponsor-lang'); ?>" class="input-field"
                               name="street"
                               value="<?php echo (isset($session_data['street'])) ? $session_data['street'] : ''; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="small-4 columns">
                        <label class="text-left middle"><?php _e('PLZ/Ort', 'child-sponsor-lang'); ?></label>
                    </div>
                    <div class="small-2 columns">
                        <input type="text" required data-msg="<?php _e('PLZ erforderlich', 'child-sponsor-lang'); ?>"
                               class="input-field" name="zipcode"
                               value="<?php echo (isset($session_data['zipcode'])) ? $session_data['zipcode'] : ''; ?>">
                    </div>
                    <div class="small-6 columns no-padding-left">
                        <input type="text" required data-msg="<?php _e('Stadt erforderlich', 'child-sponsor-lang'); ?>"
                               class="input-field" name="city"
                               value="<?php echo (isset($session_data['city'])) ? $session_data['city'] : ''; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="small-4 columns">
                        <label class="text-left middle"><?php _e('Land', 'child-sponsor-lang'); ?></label>
                    </div>
                    <div class="small-8 columns">
                        <input type="text" required data-msg="<?php _e('Länd erforderlich', 'child-sponsor-lang'); ?>"
                               class="input-field" name="country"
                               value="<?php echo (isset($session_data['country'])) ? $session_data['country'] : ''; ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="small-4 columns">
                        <label class="text-left middle"><?php _e('E-Mail-Adresse', 'child-sponsor-lang'); ?></label>
                    </div>
                    <div class="small-8 columns">
                        <input type="email" class="input-field" required
                               data-msg="<?php _e('E-Mail-Adresse erforderlich', 'child-sponsor-lang'); ?>" name="email"
                               value="<?php echo (isset($session_data['email'])) ? $session_data['email'] : ''; ?>">
                    </div>
                </div>


                <div class="form-action">
                    <!--                 <a href="?step=" class="button button-beige button-small"><?php _e('Zurück', 'child-sponsor-lang'); ?></a> -->
                    <input type="submit" class="button button-blue button-small"
                           value="<?php _e('Jetzt Spenden', 'donation-form'); ?>"/>
                </div>
            </form>
        </div>
    </div>

    <!--   postcheck -->

    <div class="tabs-panel" id="panel2">
        <div class="row">
            <div class="large-12 large-centered medium-12 medium-centered column">
	            <h4 style="text-transform: uppercase;text-align: center; color:#0054a6"><?php _e('Ich möchte das Kinder-Überlebensprogramm unterstützen', 'donation-form') ?></h4>

<!--                 <h4 class="text-uppercase"><?php _e('Banküberweisung', 'donation-form'); ?></h4> -->
                <p class="text-center middle"><?php _e('Sie können per Post- oder Banküberweisung spenden.<br/>  Nachfolgend finden Sie die Informationen für die Zahlung. ', 'donation-form') ?></p>
                <h5 class="text-center middle">Postfinance CCP 17-312562-0 <br/>
                    IBAN CH07 0900 0000 1731 2562 0<br/>
                    BIC POFICHBEXXX</h5>
                <p class="text-center middle"><?php _e('Bitte geben Sie an, ob Sie regelmässig oder einmalig für das Kinder-Überlebensprogramm spenden möchten. Spendenzweck (monatlich oder einmalig): Überlebensprogramm', 'donation-form') ?></p>
                <img class="text-center"
                     src="<?php bloginfo('template_directory'); ?>/assets/img/bv_compassion_csp-<?php echo ICL_LANGUAGE_CODE ?>.jpg";
            </div>
        </div>
    </div>

    <!-- endpostcheck -->
</div>

