<?php
$child_data = get_child_meta($session_data['childID']);

// muskathlons
$msk = isset($_SESSION['child-sponsor']['msk_participant_name']) || isset($_SESSION['msk_participant_name']);

$wapr = isset($_SESSION['utm_source']) && $_SESSION['utm_source']=='wrpr';

// Partner must be younger than this limit for Write&Pray sponsorship
$wapr_age_limit = 25;

if ($msk) {
    $_SESSION['child-sponsor']['consumer_source'] = $_SESSION['consumer_source'];
    $_SESSION['child-sponsor']['consumer_source_text'] = $_SESSION['consumer_source_text'];
    $_SESSION['child-sponsor']['msk_participant_name'] = $_SESSION['msk_participant_name'];
}
?>

<!--  <script>
    function confirmEmail() {
        var email = document.getElementById("email").value
        var confemail = document.getElementById("confemail").value
        if(email != confemail) {
            ("#confemail-error").val((confemail).attr("data-msg"))
        }
    }



</script> -->



<div class="section background-white step-1 child-sponsor">
    <div class="row">

        <form method="POST" action="?step=2" class="large-8 large-centered medium-10 medium-centered column">

            <div class="progress-steps">
                <ul>
                    <li class="active">1</li>
                    <li>2</li>
                    <li>3</li>
                </ul>
<!--              Writeandpraystuff  -->
			<?php if (!$wapr) { ?>

                <p><?php echo sprintf( wp_kses( __('Mit CHF 42.– pro Monat kannst du %s aus der Armut befreien!', 'child-sponsor-lang'), array('br' => []) ), $child_data['name'] ); ?></p>
           <?php } ?>

<!--       emd Writeandpraystuff  -->
                            </div>

            <hr>

            <h4 class="text-uppercase"><?php _e('Meine persönlichen Daten', 'child-sponsor-lang'); ?></h4>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Anrede', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns radio-wrapper1">
                    <input id="radio_frau" required data-msg="<?php _e('Anrede erforderlich', 'child-sponsor-lang'); ?>" type="radio" name="salutation" value="Frau" <?php echo (isset($session_data['salutation']) && $session_data['salutation'] == 'Frau') ? 'checked' : ''; ?>><label for="radio_frau"><?php _e('Frau', 'child-sponsor-lang'); ?></label>
                    <input id="radio_herr" required data-msg="<?php _e('Anrede erforderlich', 'child-sponsor-lang'); ?>" type="radio" name="salutation" value="Herr" <?php echo (isset($session_data['salutation']) && $session_data['salutation'] == 'Herr') ? 'checked' : ''; ?>><label for="radio_herr"><?php _e('Herr', 'child-sponsor-lang'); ?></label>
                    <input id="radio_familie" required data-msg="<?php _e('Anrede erforderlich', 'child-sponsor-lang'); ?>" type="radio" name="salutation" value="Familie" <?php echo (isset($session_data['salutation']) && $session_data['salutation'] == 'Familie') ? 'checked' : ''; ?>><label for="radio_herr"><?php _e('Familie', 'child-sponsor-lang'); ?></label>
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Nachname', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <input type="text" required data-msg="<?php _e('Nachname erforderlich', 'child-sponsor-lang'); ?>" class="input-field" name="last_name" value="<?php echo (isset($session_data['last_name'])) ? $session_data['last_name'] : ''; ?>">
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Vorname', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <input type="text" required data-msg="<?php _e('Vorname erforderlich', 'child-sponsor-lang'); ?>" class="input-field" name="first_name" value="<?php echo (isset($session_data['first_name'])) ? $session_data['first_name'] : ''; ?>">
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Strasse/Hausnr.', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <input type="text" required data-msg="<?php _e('Strasse erforderlich', 'child-sponsor-lang'); ?>" class="input-field" name="street" value="<?php echo (isset($session_data['street'])) ? $session_data['street'] : ''; ?>">
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('PLZ/Ort', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-2 columns">
                    <input type="text" required data-msg="<?php _e('PLZ erforderlich', 'child-sponsor-lang'); ?>" class="input-field" name="zipcode" value="<?php echo (isset($session_data['zipcode'])) ? $session_data['zipcode'] : ''; ?>">
                </div>
                <div class="small-6 columns no-padding-left">
                    <input type="text" required data-msg="<?php _e('Stadt erforderlich', 'child-sponsor-lang'); ?>" class="input-field" name="city" value="<?php echo (isset($session_data['city'])) ? $session_data['city'] : ''; ?>">
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Land', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
	                <input type="text" required data-msg="<?php _e('Länd erforderlich', 'child-sponsor-lang'); ?>" class="input-field" name="land" value="<?php echo (isset($session_data['land'])) ? $session_data['land'] : ''; ?>">

                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Geburtsdatum', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <input type="text"
                           id="datepicker"
                           name="birthday"
                           placeholder="31/12/2000"
                           class="input-field"
                           required
                           value="<?php echo (isset($session_data['birthday'])) ? $session_data['birthday'] : ''; ?>"
                           data-default-msg="<?php _e('Geburtsdatum erforderlich', 'child-sponsor-lang'); ?>"
                           data-wrpr="<?= $wapr ? "true" : "false"; ?>"
                           data-wrpr-age-limit="<?= $wapr_age_limit; ?>"
                           data-wrpr-age-limit-msg="<?php echo sprintf( wp_kses( __("Write&Pray-Patenschaften sind für Personen unter 25 Jahren vorgesehen. Klicke hier, um %s für CHF 42.-/Monat zu unterstützen.", 'child-sponsor-lang'), array('br' => []) ), $child_data['name'] ); ?>"
                </div></div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('E-Mail-Adresse', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <input type="email" id="email" class="input-field"  required data-msg-error="<?php _e('Ungültige E-Mail Adresse', 'child-sponsor-lang'); ?>" data-msg-required="<?php _e('E-Mail-Adresse erforderlich', 'child-sponsor-lang'); ?>" name="email" value="<?php echo (isset($session_data['email'])) ? $session_data['email'] : ''; ?>">
                </div>
            </div>

           <!--  <div class="row">
                 <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Confirm address', 'child-sponsor-lang'); ?></label>
                 </div>
                <div class="small-8 columns">
                    <input type="email" onblur="confirmEmail()" id="confemail" class="input-field" required data-msg-error="<?php _e('Ungültige E-Mail Adresse', 'child-sponsor-lang'); ?>" data-msg-required="<?php _e('E-Mail-Adresse erforderlich', 'child-sponsor-lang'); ?>" name="emailConfirm" >
                </div>
             </div> -->

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Telefon', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <input type="text" class="input-field" required data-msg="<?php _e('Telefon erforderlich', 'child-sponsor-lang'); ?>" name="phone" value="<?php echo (isset($session_data['phone'])) ? $session_data['phone'] : ''; ?>">
                </div>
            </div>
              <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Kirche/Gemeinde', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <input type="text" class="input-field" name="kirchgemeinde" value="<?php echo (isset($session_data['kirchgemeinde'])) ? $session_data['kirchgemeinde'] : ''; ?>">
                </div>
            </div>
                  <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Beruf', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <input type="text" class="input-field" name="Beruf" value="<?php echo (isset($session_data['Beruf'])) ? $session_data['Beruf'] : ''; ?>">
                </div>
            </div>

			<hr>

			<!--              Writeandpraystuff  -->

<?php if (!$wapr) { ?>

            <h4 class="text-uppercase" id="bank"><?php _e('Zahlungsweise', 'child-sponsor-lang'); ?></h4>
				<div class="row">
					 <div class="small-12 columns radio-wrapper">
						   <label><input type="radio" required data-msg="<?php _e('Zahlungsweise erforderlich', 'child-sponsor-lang'); ?>" name="zahlungsweise" value="dauerauftrag"> <?php _e('Monatlicher Dauerauftrag', 'child-sponsor-lang') ?></label>
                           <label><input type="radio" required data-msg="<?php _e('Zahlungsweise erforderlich', 'child-sponsor-lang'); ?>" name="zahlungsweise" value="lsv"> <?php _e('Direct Debit - LSV', 'child-sponsor-lang') ?></label>
					 </div>
					<div class="small-12 columns zahlung">
						<p class="marg-top-10"><?php _e('Wenn du eine andere Zahlungsart wünschst, melde dich bitte unter: info@compassion.ch / +41 (0)31 552 21 21', 'child-sponsor-lang' )?></p>
					</div>
				</div>

    <!-- different payer
    <div class="row">

        <div class="small-12 columns">
            <div style="margin-bottom: 16px;">
                <label style="display:inline-block">
                    <input type="checkbox" name="show-payer" value="show-payer" <?php echo (isset($session_data['show-payer']) && $session_data['show-payer'] ) ? 'checked' : ''; ?> id="show-payer">
                    <?= __('Une autre personne finance ce parrainage', 'donation-form') ?>
                </label>
            </div>
        </div>
    </div>

    <script>
        jQuery('#show-payer').on('ifChanged', function () {
            if (jQuery(this).prop('checked')) {
                jQuery('#payer_delete_button').show();
                jQuery("#payer_delete_button input").prop('required',true);
            } else {
                jQuery('#payer_delete_button').hide();
                jQuery("#payer_delete_button input").prop('required',false);
            }
        });
    </script>

    <div id="payer_delete_button" style="<?php echo (isset($session_data['show-payer']) && $session_data['show-payer'] ) ? '' : 'display:none;'; ?>">

        <h4 class="text-uppercase"><?php _e('PAYER Daten', 'child-sponsor-lang'); ?></h4>
        <div class="row">
            <div class="small-4 columns">
                <label class="text-left middle"><?php _e('Anrede', 'child-sponsor-lang'); ?></label>
            </div>
            <div class="small-8 columns radio-wrapper1">
                <input id="radio_frau"  data-msg="<?php _e('Anrede erforderlich', 'child-sponsor-lang'); ?>" type="radio" name="salutation_payer" value="Frau" <?php echo (isset($session_data['salutation_payer']) && $session_data['salutation_payer'] == 'Frau') ? 'checked' : ''; ?>><label for="radio_frau"><?php _e('Frau', 'child-sponsor-lang'); ?></label>
                <input id="radio_herr"  data-msg="<?php _e('Anrede erforderlich', 'child-sponsor-lang'); ?>" type="radio" name="salutation_payer" value="Herr" <?php echo (isset($session_data['salutation_payer']) && $session_data['salutation_payer'] == 'Herr') ? 'checked' : ''; ?>><label for="radio_herr"><?php _e('Herr', 'child-sponsor-lang'); ?></label>
                <input id="radio_familie" data-msg="<?php _e('Anrede erforderlich', 'child-sponsor-lang'); ?>" type="radio" name="salutation_payer" value="Familie" <?php echo (isset($session_data['salutation_payer']) && $session_data['salutation_payer'] == 'Familie') ? 'checked' : ''; ?>><label for="radio_herr"><?php _e('Familie', 'child-sponsor-lang'); ?></label>
            </div>
        </div>

        <div class="row">
            <div class="small-4 columns">
                <label class="text-left middle"><?php _e('Nachname', 'child-sponsor-lang'); ?></label>
            </div>
            <div class="small-8 columns">
                <input type="text"  data-msg="<?php _e('Nachname erforderlich', 'child-sponsor-lang'); ?>" class="input-field" name="last_name_payer" value="<?php echo (isset($session_data['last_name_payer'])) ? $session_data['last_name_payer'] : ''; ?>">
            </div>
        </div>

        <div class="row">
            <div class="small-4 columns">
                <label class="text-left middle"><?php _e('Vorname', 'child-sponsor-lang'); ?></label>
            </div>
            <div class="small-8 columns">
                <input type="text"  data-msg="<?php _e('Vorname erforderlich', 'child-sponsor-lang'); ?>" class="input-field" name="first_name_payer" value="<?php echo (isset($session_data['first_name_payer'])) ? $session_data['first_name_payer'] : ''; ?>">
            </div>
        </div>

        <div class="row">
            <div class="small-4 columns">
                <label class="text-left middle"><?php _e('Strasse/Hausnr.', 'child-sponsor-lang'); ?></label>
            </div>
            <div class="small-8 columns">
                <input type="text"  data-msg="<?php _e('Strasse erforderlich', 'child-sponsor-lang'); ?>" class="input-field" name="street_payer" value="<?php echo (isset($session_data['street_payer'])) ? $session_data['street_payer'] : ''; ?>">
            </div>
        </div>

        <div class="row">
            <div class="small-4 columns">
                <label class="text-left middle"><?php _e('PLZ/Ort', 'child-sponsor-lang'); ?></label>
            </div>
            <div class="small-2 columns">
                <input type="text" data-msg="<?php _e('PLZ erforderlich', 'child-sponsor-lang'); ?>" class="input-field" name="zipcode_payer" value="<?php echo (isset($session_data['zipcode_payer'])) ? $session_data['zipcode_payer'] : ''; ?>">
            </div>
            <div class="small-6 columns no-padding-left">
                <input type="text" data-msg="<?php _e('Stadt erforderlich', 'child-sponsor-lang'); ?>" class="input-field" name="city_payer" value="<?php echo (isset($session_data['city_payer'])) ? $session_data['city_payer'] : ''; ?>">
            </div>
        </div>

        <div class="row">
            <div class="small-4 columns">
                <label class="text-left middle"><?php _e('Land', 'child-sponsor-lang'); ?></label>
            </div>
            <div class="small-8 columns">
                <input type="text"  data-msg="<?php _e('Länd erforderlich', 'child-sponsor-lang'); ?>" class="input-field" name="land_payer" value="<?php echo (isset($session_data['land_payer'])) ? $session_data['land_payer'] : ''; ?>">

            </div>
        </div>

        <div class="row">
            <div class="small-4 columns">
                <label class="text-left middle"><?php _e('Geburtsdatum', 'child-sponsor-lang'); ?></label>
            </div>
            <div class="small-8 columns">
                <input type="text"
                       id="datepicker1"
                       name="birthday_payer"
                       placeholder="31/12/2000"
                       class="input-field"
                       value="<?php echo (isset($session_data['birthday_payer'])) ? $session_data['birthday_payer'] : ''; ?>"
                       data-default-msg="<?php _e('Geburtsdatum erforderlich', 'child-sponsor-lang'); ?>"
                       data-wrpr="<?= $wapr ? "true" : "false"; ?>"
                       data-wrpr-age-limit="<?= $wapr_age_limit; ?>"
                       data-wrpr-age-limit-msg="<?php echo sprintf( wp_kses( __("Write&Pray-Patenschaften sind für Personen unter 25 Jahren vorgesehen. Klicke hier, um %s für CHF 42.-/Monat zu unterstützen.", 'child-sponsor-lang'), array('br' => []) ), $child_data['name'] ); ?>"
            </div>
        </div></div>


    <div class="row">
        <div class="small-4 columns">
            <label class="text-left middle"><?php _e('E-Mail-Adresse', 'child-sponsor-lang'); ?></label>
        </div>
        <div class="small-8 columns">
            <input type="email" id="email" class="input-field"  data-msg-error="<?php _e('Ungültige E-Mail Adresse', 'child-sponsor-lang'); ?>" data-msg-required="<?php _e('E-Mail-Adresse erforderlich', 'child-sponsor-lang'); ?>" name="email_payer" value="<?php echo (isset($session_data['email_payer'])) ? $session_data['email_payer'] : ''; ?>">
        </div>
    </div>

    <div class="row">
        <div class="small-4 columns">
            <label class="text-left middle"><?php _e('Confirm address', 'child-sponsor-lang'); ?></label>
        </div>
        <div class="small-8 columns">
            <input type="email" onblur="confirmEmail()" id="confemail" class="input-field"  data-msg-error="<?php _e('Ungültige E-Mail Adresse', 'child-sponsor-lang'); ?>" data-msg-required="<?php _e('E-Mail-Adresse erforderlich', 'child-sponsor-lang'); ?>" name="emailConfirm_payer" >
        </div>
    </div>

    <div class="row">
        <div class="small-4 columns">
            <label class="text-left middle"><?php _e('Telefon', 'child-sponsor-lang'); ?></label>
        </div>
        <div class="small-8 columns">
            <input type="text" class="input-field"  data-msg="<?php _e('Telefon erforderlich', 'child-sponsor-lang'); ?>" name="phone_payer" value="<?php echo (isset($session_data['phone_payer'])) ? $session_data['phone_payer'] : ''; ?>">
        </div>
    </div>


</div>
end different payer -->

<hr>

             <h4 class="text-uppercase" id="Patenschaftplus"><?php _e('Patenschaft plus', 'child-sponsor-lang'); ?></h4>
             	<div class="row">
                <div class="small-12 columns">
                <input class="" type="checkbox" <?php echo (isset($session_data['patenschaftplus']) && $session_data['patenschaftplus']['checkbox'] == 'on') ? 'checked' : ''; ?> name="patenschaftplus[checkbox]"> <span class="marg-left-10 strong-statment">  <?php _e('JA', 'child-sponsor-lang'); ?></span>

                  <p class="marg-top-10"><?php _e('Die Patenschaft "Plus" beinhaltet die "Basis" der Patenschaft von CHF 42.- und eine zusätzliche Spende von CHF 8.- pro Monat. Die CHF 8.- ermöglichen die Finanzierung dringender Bedürfnisse oder von Bedürfnissen, die durch die Patenschaftsgelder nicht gedeckt sind (z.B. Naturkatastrophen, chirurgische Eingriffe, Malariaprävention, usw.). Dieser Solidaritätsfonds ist für alle von Compassion unterstützten Kinder da.', 'child-sponsor-lang')?></p>

                </div>
                </div>
<?php } else { ?>
       <h4 class="text-uppercase" id="Patenschaftplus"><?php _e('Write & Pray', 'child-sponsor-lang'); ?></h4>
            <div class="row">
                <div class="small-12 columns radio-wrapper">
                    <label><input type="radio" required data-msg="<?php _e('Angabe erforderlich', 'child-sponsor-lang'); ?>" name="writepray" value="WRPR">
                      <?php _e('Ich engagiere mich für mein Patenkind zu beten und ihm regelmässsig zu schreiben.  Ich habe verstanden, dass eine andere Person die Finanzierung dieser Patenschaft übernimmt und ich gegenüber dem Kind der/die offizielle Pate/Patin bin.', 'child-sponsor-lang')?></label>
                    <label><input type="radio" required data-msg="<?php _e('Angabe erforderlich', 'child-sponsor-lang'); ?>" name="writepray" value="WRPR+DON">
                        <?php _e('Ich erkläre mich bereit, regelmässig für mein Patenkind zu beten und zu schreiben, und ich kann das Kind monatlich mit folgenden Betrage unterstützen:', 'child-sponsor-lang')?></label>
                </div>
            </div>
            <div class="row hide" id="writepray-contribution" >
                    <div class="row wp-contr">
                    <div class="small-4  columns marg-top-10">
                    <input class="ignore" type="number" min='1' max='42' placeholder="10.- CHF" required data-msg="<?php _e('Bitte wähle einen Betrag zwischen 1 und 42 Franken.', 'child-sponsor-lang'); ?>" name="writepray-contribution" value="<?php echo (isset($session_data['writepray-contribution'])) ? $session_data['writepray-contribution'] : ''; ?>">
                    </div>
                    <div class="small-12 columns">
                    <label><?php _e('Ich habe verstanden, dass eine andere Person den Rest der Finanzierung für diese Patenschaft übernimmt, dass ich aber der offizielle Pate/ die Patin des Kindes bin.', 'child-sponsor-lang')?></label>
                    </div>
                    </div>

            </div>



<?php } ?>

          <!--        end Writeandpraystuff  -->


			<hr>

            <h4 class="text-uppercase" id="briefwechsel"><?php echo sprintf( wp_kses( __('Briefwechsel mit %s', 'child-sponsor-lang'), array('br' => []) ), $child_data['name'] ); ?></h4>
            	<div class="row">
	            	<div class="small-12 columns">
						<p><?php _e('Für den Briefverkehr mit dem Patenkind verstehe ich ausser Deutsch auch:', 'child-sponsor-lang')?></p>
	            	</div>
            	</div>
           	<div class="row">
                <div class="small-12 medium-6 columns language">
	                 <ul>
					 <li><input id="language" type="checkbox" name="language[]"  value="französich" <?php echo (isset($session_data['language']) && $session_data['language'] == 'französich') ? 'checked' : ''; ?>><label><?php _e('Französisch', 'child-sponsor-lang') ?></label>
		            </li>
					<li><input id="language" type="checkbox" name="language[]"   value="italienisch" <?php echo (isset($session_data['language']) && $session_data['language'] == 'italienisch') ? 'checked' : ''; ?>><label><?php _e('Italienisch', 'child-sponsor-lang') ?></label>
		            </li>

	                 </ul>

		        </div>
                <div class="small-12 medium-6 columns language " >
	                <ul>
		          <li><input id="language" type="checkbox"   name="language[]" value="spanisch" <?php echo (isset($session_data['language']) && $session_data['language'] == 'spanisch') ? 'checked' : ''; ?>><label><?php _e('Spanisch', 'child-sponsor-lang') ?></label>
		            </li>
	                 <li><input id="language" type="checkbox" name="language[]"  value="englisch" <?php echo (isset($session_data['language']) && $session_data['language'] == 'englisch') ? 'checked' : ''; ?>><label><?php _e('Englisch', 'child-sponsor-lang') ?></label>
		            </li>
					<li><input id="language" type="checkbox" name="language[]"  value="portugiesisch" <?php echo (isset($session_data['language']) && $session_data['language'] == 'portugiesisch') ? 'checked' : ''; ?>><label><?php _e('Portugiesisch', 'child-sponsor-lang') ?></label>
		            </li>

	                </ul>
				</div>

			</div>
    <div class="row">
        <div class="small-12 columns">
            <p><?php _e('Schreiben kannst du deine Briefe auf Deutsch', 'child-sponsor-lang')?></p>
        </div>
    </div>

            <hr>

            <h4 class="text-uppercase" id="bank"><?= __('WIE HAST DU VON COMPASSION ERFAHREN?', 'child-sponsor-lang') ?></h4>
            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?= __('Ich habe von Compassion erfahren durch', 'child-sponsor-lang') ?></label>
                </div>
                <div class="small-8 columns">
                    <?php if (!$msk) { ?>
                    <div class="select-wrapper">
                        <select name="consumer_source" id="consumer_select" required data-msg="<?php _e('Angabe erforderlich', 'child-sponsor-lang'); ?>" class="input-field">
                            <option value=""><?php _e('Bitte wählen', 'child-sponsor-lang'); ?></option>
                            <option value="Konzert/Veranstaltung" data-placeholder="<?php _e('Geben Sie das Konzert/Event an', 'child-sponsor-lang'); ?>"<?php echo (isset($session_data['consumer_source']) && $session_data['consumer_source'] == "Konzert") ? 'selected' : '' ?>><?php _e('Konzert/Veranstaltung', 'child-sponsor-lang'); ?></option>
                            <option value="Kontakt durch Pate/Botschafter"  data-placeholder="<?php _e('Geben Sie Vor- und Nachnamen an', 'child-sponsor-lang'); ?>" <?php echo (isset($session_data['consumer_source']) && $session_data['consumer_source'] == "Kontakt durch Pate/Botschafter") ? 'selected' : '' ?>><?php _e('Kontakt durch Pate/Botschafter', 'child-sponsor-lang'); ?></option>
                            <option value="Kontakt durch Compassion Advokate" data-placeholder="<?php _e('Geben Sie Vor- und Nachnamen an', 'child-sponsor-lang'); ?>" <?php echo (isset($session_data['consumer_source']) && $session_data['consumer_source'] == "Kontakt durch Compassion Advokate") ? 'selected' : '' ?>><?php _e('Kontakt durch Compassion Advokat', 'child-sponsor-lang'); ?></option>
                            <option value="Gottesdienst/Kirche" data-placeholder="<?php _e('Geben Sie den Namen der Kirche an', 'child-sponsor-lang'); ?>"<?php echo (isset($session_data['consumer_source']) && $session_data['consumer_source'] == "Gottesdienst/Kirche") ? 'selected' : '' ?>><?php _e('Gottesdienst/Kirche', 'child-sponsor-lang'); ?></option>
                            <option value="Freunde & Verwandte" data-placeholder="<?php _e('Geben Sie Vor- und Nachnamen an', 'child-sponsor-lang'); ?>"<?php echo (isset($session_data['consumer_source']) && $session_data['consumer_source'] == "Freunde & Verwandte") ? 'selected' : '' ?>><?php _e('Freunde & Verwandte', 'child-sponsor-lang'); ?></option>
                            <option value="Compassion Magazin" <?php echo (isset($session_data['consumer_source']) && $session_data['consumer_source'] == "Compassion Magazin") ? 'selected' : '' ?>><?php _e('Compassion Magazin', 'child-sponsor-lang'); ?></option>

                            <option value="Anzeige in Zeitschrift" data-placeholder="<?php _e('Geben Sie das Magazin/die Broschüre an', 'child-sponsor-lang'); ?>"<?php echo (isset($session_data['consumer_source']) && $session_data['consumer_source'] == "Anzeige in Zeitschrift") ? 'selected' : '' ?>><?php _e('Anzeige in Zeitschrift', 'child-sponsor-lang'); ?></option>
                            <option value="Facebook, Youtube, Vimeo…" <?php echo (isset($session_data['consumer_source']) && $session_data['consumer_source'] == "Facebook, Youtube, Vimeo…") ? 'selected' : '' ?>><?php _e('Facebook, Youtube, Vimeo…', 'child-sponsor-lang'); ?></option>
                            <option value="Internet" data-placeholder="<?php _e('Geben Sie die Website an', 'child-sponsor-lang'); ?>" <?php echo (isset($session_data['consumer_source']) && $session_data['consumer_source'] == "Internet") ? 'selected' : '' ?>><?php _e('Internet', 'child-sponsor-lang'); ?></option>
                            <option value="Youversion" <?php echo (isset($session_data['consumer_source']) && $session_data['consumer_source'] == "Youversion") ? 'selected' : '' ?>><?php _e('YouVersion Bible Plan', 'child-sponsor-lang'); ?></option>

                            <option value="Muskathlon" data-placeholder="<?php _e('Name der Läuferin/des Läufers', 'child-sponsor-lang'); ?>" <?php echo (isset($session_data['consumer_source']) && $session_data['consumer_source'] == "Muskathlon") ? 'selected' : '' ?>><?php _e('Muskathlon', 'child-sponsor-lang'); ?></option>
                            <option value="TOGETHER" data-placeholder="<?php _e('Name der Läuferin/des Läufers oder des Projekts', 'child-sponsor-lang'); ?>" <?php echo (isset($session_data['consumer_source']) && $session_data['consumer_source'] == "TOGETHER") ? 'selected' : '' ?>><?php _e('TOGETHER', 'child-sponsor-lang'); ?></option>

                            <!--                             <option value="Librairies" data-placeholder="<?php _e('nom de la librairie', 'child-sponsor-lang'); ?>" <?php echo (isset($session_data['consumer_source']) && $session_data['consumer_source'] == "Librairies") ? 'selected' : '' ?>><?php _e('Librairies', 'child-sponsor-lang'); ?></option> -->

                            <option value="Andere" data-placeholder="<?php _e('Noch genauer', 'child-sponsor-lang'); ?>" <?php echo (isset($session_data['consumer_source']) && $session_data['consumer_source'] == "Andere") ? 'selected' : '' ?>><?php _e('Andere', 'child-sponsor-lang'); ?></option>
                        </select>
                    </div>
                    <div class="consumer-source-text-wrapper hide">
                        <input type="text" class="input-field place ignore"  name="consumer_source_text" required data-msg="<?php _e('Angabe erforderlich', 'child-sponsor-lang'); ?>" value="">
                    </div>
                    <?php } else { ?>
                    <div class="select-wrapper">
                        <select name="consumer_source_info" required disabled selected data-msg="<?php _e('Angabe erforderlich', 'child-sponsor-lang'); ?>" class="input-field">
                            <option value="<?= $_SESSION['consumer_source'] ?>">Muskathlon</option>
                        </select>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php if ($msk) { ?>
            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?= __('Name des Teilnehmers', 'child-sponsor-lang') ?></label>
                </div>
                <div class="small-8 columns">
                    <input type="text" class="input-field place ignore"
                           name="consumer_source_text_info" required disabled
                           data-msg="<?php _e('Angabe erforderlich', 'child-sponsor-lang'); ?>"
                           value="<?= $_SESSION['msk_participant_name'] ?>"
                    />
                </div>
            </div>
            <?php } ?>
            <hr>

             <h4 class="text-uppercase" id="Patenschaftplus"><?php _e('Freiwillig mithelfen', 'child-sponsor-lang'); ?></h4>
             	<div class="row">
                <div class="small-12 columns">
	            <input class="" type="checkbox" <?php echo (isset($session_data['mithelfen']) && $session_data['mithelfen']['checkbox'] == 'on') ? 'checked' : ''; ?> name="mithelfen[checkbox]"> <span class="marg-left-10">  <?php _e('Ich möchte freiwillig mithelfen (z.B. Übersetzung, Events, Gebet), bitte kontaktiert mich.', 'child-sponsor-lang')?></span>
                </div>
                </div>
            <hr>

            <div class="form-action">
<!--                 <a href="?step=" class="button button-beige button-small"><?php _e('Zurück', 'child-sponsor-lang'); ?></a> -->
                <input type="submit" class="button button-blue button-small" value="<?php _e('Weiter', 'child-sponsor-lang'); ?>"/>
            </div>

        </form>
    </div>

</div>

<script>
    jQuery(document).ready(function($) {
        $.datepicker.setDefaults({
            changeYear: true,
            yearRange : "c-70:c+0",
            dateFormat: "dd/mm/yy",
            constrainInput: false
        });
        $("#datepicker,#datepicker1").datepicker({
          dateFormat: "dd/mm/yy",
        });
    });


</script>
