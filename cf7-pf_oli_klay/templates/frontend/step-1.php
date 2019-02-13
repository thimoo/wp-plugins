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
  <li class="tabs-title is-active"><a href="#panel1" aria-selected="true"><i class="fa fa-credit-card-alt" aria-hidden="true"></i>  <?php _e('Online-Zahlung','donation-form')?></a></li>
  <li class="tabs-title"><a href="#panel2"><i class="fa fa-university" aria-hidden="true"> </i> <?php _e('Banküberweisung','donation-form')?></a></li>
</ul>
<div class="tabs-content section  step-1 child-sponsor" data-tabs-content="example-tabs">
	 <div class="tabs-panel is-active" id="panel1">
		 <div class="row">
        <form method="POST" action="?step=2"  class="large-12 large-centered medium-12 medium-centered column" >
	         <p><?php _e('Spenden Sie über eine sichere Online-Zahlung, mit Postfinance oder Kreditkarte.','donation-form' )?></p>

            <h4 class="text-uppercase"><?php _e('Meine persönlichen Daten', 'child-sponsor-lang'); ?></h4>
			
			 <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Betrag Ihrer Spende in CHF', 'donation-form'); ?></label>
                </div>
                <div class="small-8 columns">
                    <input type="text" placeholder="<?php _e('bitte nur Zahlen', 'donation-form'); ?>" required data-msg="<?php _e('Betrag erforderlich', 'donation-form'); ?>" class="input-field" name="wert" value="<?php echo (isset($session_data['wert'])) ? $session_data['wert'] : ''; ?>">
                </div>
            </div>
            
             <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Spendenzweck', 'donation-form'); ?></label>
                </div>
                <div class="small-8 columns">
                    <div class="select-wrapper">
                        <select name="fonds" id="fonds" class="input-field">
	                        <option value="humanitaire" <?php echo (isset($session_data['fonds']) && $session_data['fonds'] == "humanitaire") ? 'selected' : '' ?>><?php _e('Aktuelle Nothilfe', 'donation-form'); ?></option>
	                        <option value="noel" <?php echo (isset($session_data['fonds']) && $session_data['fonds'] == "noel") ? 'selected' : '' ?>><?php _e('Weihnachtsgeschenk', 'donation-form'); ?></option>
                            <option value="eau" <?php echo (isset($session_data['fonds']) && $session_data['fonds'] == "eau") ? 'selected' : '' ?>><?php _e('Sauberes Wasser', 'donation-form'); ?></option>
							<option value="survie" <?php echo (isset($session_data['fonds']) && $session_data['fonds'] == "survie") ? 'selected' : '' ?>><?php _e('Babys und Müttern helfen', 'donation-form'); ?></option>
							<option value="sansparrain" <?php echo (isset($session_data['fonds']) && $session_data['fonds'] == "sansparrain") ? 'selected' : '' ?>><?php _e('Kinder, die ihren Paten verloren haben', 'donation-form'); ?></option>
							<option value="medical" <?php echo (isset($session_data['fonds']) && $session_data['fonds'] == "medical") ? 'selected' : '' ?>><?php _e('Medizinische Hilfe', 'donation-form'); ?></option>
							<option value="bible" <?php echo (isset($session_data['fonds']) && $session_data['fonds'] == "bible") ? 'selected' : '' ?>><?php _e('Bibelfonds', 'donation-form'); ?></option>

                        </select>
                    </div>
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
	            <input type="text" required data-msg="<?php _e('Länd erforderlich', 'child-sponsor-lang'); ?>" class="input-field" name="country" value="<?php echo (isset($session_data['country'])) ? $session_data['country'] : ''; ?>">
<!--
                    <div class="select-wrapper">
                        <select name="country" class="input-field">
	                        <option value="Schweiz" <?php echo (isset($session_data['country']) && $session_data['country'] == "Schweiz") ? 'selected' : '' ?>><?php _e('Schweiz', 'child-sponsor-lang'); ?></option>
                            <option value="Deutschland" <?php echo (isset($session_data['country']) && $session_data['country'] == "Deutschland") ? 'selected' : '' ?>><?php _e('Deutschland', 'child-sponsor-lang'); ?></option>
                            <option value="Österreich" <?php echo (isset($session_data['country']) && $session_data['country'] == "Österreich") ? 'selected' : '' ?>><?php _e('Österreich', 'child-sponsor-lang'); ?></option>
                        </select>
                    </div>
-->
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('E-Mail-Adresse', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <input type="email" class="input-field" required data-msg="<?php _e('E-Mail-Adresse erforderlich', 'child-sponsor-lang'); ?>" name="email" value="<?php echo (isset($session_data['email'])) ? $session_data['email'] : ''; ?>">
                </div>
            </div>

<!--
            <div class="row">
                <div class="large-12 column recaptcha-wrapper">
                    <div class="g-recaptcha" data-sitekey="6Lf1_AcUAAAAADEdnn5_Rmu_LlHyrMPKKs0fVH3l"></div>
                    <input type="hidden" class="hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha" data-msg="<?php _e('Bitte zeigen Sie, dass Sie ein Mensch sind.', 'child-sponsor-lang'); ?>">
                </div>
            </div>
-->

            <div class="form-action">
<!--                 <a href="?step=" class="button button-beige button-small"><?php _e('Zurück', 'child-sponsor-lang'); ?></a> -->
                <input type="submit" class="button button-blue button-small" value="<?php _e('Jetzt Spenden', 'donation-form'); ?>"/>
            </div>
        </form>
    </div>
</div>
<div class="tabs-panel" id="panel2">
	 <div class="row">
	 <div class="large-12 large-centered medium-12 medium-centered column">
		  <h4 class="text-uppercase"><?php _e('Banküberweisung', 'donation-form'); ?></h4>
		  <p class="text-center middle"><?php _e('Sie können per Post- oder Banküberweisung spenden.<br/>  Nachfolgend finden Sie die Informationen für die Zahlung. ','donation-form' )?></p>
		  <h5 class="text-center middle">Postfinance CCP 17-312562-0 <br/>
IBAN CH07 0900 0000 1731 2562 0<br/>
BIC POFICHBEXXX</h5>
  <p class="text-center middle"><?php _e('Vielen Dank, dass Sie nicht vergessen, den Spendenzweck zu erwähnen.','donation-form' )?></p>
<img class="text-center" src="<?php bloginfo('template_directory'); ?>/assets/img/bv_compassion-<?php echo ICL_LANGUAGE_CODE ?>.jpg";
</div>
</div>
</div>
</div>

