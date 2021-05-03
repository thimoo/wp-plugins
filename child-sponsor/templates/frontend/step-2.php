<?php
$child_data = get_child_meta($session_data['childID']);
$my_current_lang = apply_filters( 'wpml_current_language', NULL );
$wapr = isset($_SESSION['utm_source']) && $_SESSION['utm_source']=='wrpr';
?>

<div class="section background-white step-2 child-sponsor">
    <div class="row">

        <form method="POST" action="?step=3" class="large-8 large-centered medium-10 medium-centered column">

            <div class="progress-steps">
                <ul>
                    <li class="active">1</li>
                    <li class="active">2</li>
                    <li>3</li>
                </ul>
<!--              Writeandpraystuff  -->
			<?php if (!$wapr) { ?> 

                <p><?php echo sprintf( wp_kses( __('Mit CHF 42.– pro Monat kannst du %s aus der Armut befreien!', 'child-sponsor-lang'), array('br' => []) ), $child_data['name'] ); ?></p>
           <?php } ?>
           
<!--       emd Writeandpraystuff  -->
            </div>

            <hr>

            <h4><?php _e('Hier eine Übersicht deiner angegebenen Daten', 'child-sponsor-lang'); ?></h4>


            <h4 class="text-uppercase"><?php _e('Persönliche Daten', 'child-sponsor-lang'); ?></h4>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Anrede', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <?php
	                    $salutation = $session_data['salutation'];
	                    echo _e($salutation, 'child-sponsor-lang');
	                ?>
	        </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Nachname', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <?php echo $session_data['last_name']; ?>
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Vorname', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <?php echo $session_data['first_name']; ?>
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Strasse/Hausnr.', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <?php echo $session_data['street']; ?>
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Ort', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <?php echo $session_data['city']; ?>
                </div>
            </div>
            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('PLZ', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <?php echo $session_data['zipcode']; ?>
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Land', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                   <?php echo $session_data['land']; ?>
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Geburtsdatum', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <?php echo $session_data['birthday']; ?>
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('E-Mail-Adresse', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <?php echo $session_data['email']; ?>
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Telefon', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <?php echo $session_data['phone']; ?>
                </div>
            </div>
            
            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Kirche/Gemeinde', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <?php echo $session_data['kirchgemeinde']; ?>
                </div>
            </div>
            
              <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Beruf', 'child-sponsor-lang'); ?></label>
                </div>
                <div class="small-8 columns">
                    <?php echo $session_data['Beruf']; ?>
                </div>
            </div>


            <a href="?step=1" class="edit"><?php _e('Bearbeiten', 'child-sponsor-lang'); ?></a>
            
             <hr>
             
               <?php if ($wapr) { ?> 

            
            <h4 class="text-uppercase"><?php _e('Write & Pray', 'child-sponsor-lang'); ?></h4>
            <div class="row">
			<div class="small-12 columns">
           <?php if (isset($session_data['writepray'])) {
	          echo _e('JA', 'child-sponsor-lang'); 
	          } else {echo _e('NEIN', 'child-sponsor-lang');}
           ?> 
            </div>
            </div>
                   <hr>
    <?php } else { ?>

            

            
            <h4 class="text-uppercase"><?php _e('Patenschaft plus', 'child-sponsor-lang'); ?></h4>
            <div class="row">
			<div class="small-12 columns">
           <?php if (isset($session_data['patenschaftplus'])) {
	          echo _e('Ich wähle den Betrag CHF 50.-', 'child-sponsor-lang');
	          } else {echo _e('Ich wähle den Betrag CHF 42.-', 'child-sponsor-lang');}
           ?> 
            </div>
            </div>
          
            <hr>
			<h4 class="text-uppercase"><?php _e('Zahlungsweise', 'child-sponsor-lang'); ?></h4>
			
            <div class="row">
                               <div class="small-8 columns">
                     <?php
                        switch($session_data['zahlungsweise']) {
                            case 'dauerauftrag': echo _e('Monatlicher Dauerauftrag', 'child-sponsor-lang'); break;
                            case 'lsv': echo _e('Direct Debit - LSV', 'child-sponsor-lang');
                        }
                        ?>

                </div>
            </div>
        <?php if($session_data['show-payer']){ ?>
            <!-- different payer  -->

                   <h4 class="text-uppercase"><?php _e('Persönliche Daten', 'child-sponsor-lang'); ?></h4>

                   <div class="row">
                       <div class="small-4 columns">
                           <label class="text-left middle"><?php _e('Anrede', 'child-sponsor-lang'); ?></label>
                       </div>
                       <div class="small-8 columns">
                           <?php
                        //   $salutation $session_data['payer']['salutation_payer'];
                        //   echo _e($salutation, 'child-sponsor-lang');
                           ?>
                       </div>
                   </div>

                   <div class="row">
                       <div class="small-4 columns">
                           <label class="text-left middle"><?php _e('Nachname', 'child-sponsor-lang'); ?></label>
                       </div>
                       <div class="small-8 columns">
                           <?php echo $session_data['last_name_payer']; ?>
                       </div>
                   </div>

                   <div class="row">
                       <div class="small-4 columns">
                           <label class="text-left middle"><?php _e('Vorname', 'child-sponsor-lang'); ?></label>
                       </div>
                       <div class="small-8 columns">
                           <?php echo $session_data['first_name_payer']; ?>
                       </div>
                   </div>

                   <div class="row">
                       <div class="small-4 columns">
                           <label class="text-left middle"><?php _e('Strasse/Hausnr.', 'child-sponsor-lang'); ?></label>
                       </div>
                       <div class="small-8 columns">
                           <?php echo $session_data['street_payer']; ?>
                       </div>
                   </div>

                   <div class="row">
                       <div class="small-4 columns">
                           <label class="text-left middle"><?php _e('Ort', 'child-sponsor-lang'); ?></label>
                       </div>
                       <div class="small-8 columns">
                           <?php echo $session_data['city_payer']; ?>
                       </div>
                   </div>
                   <div class="row">
                       <div class="small-4 columns">
                           <label class="text-left middle"><?php _e('PLZ', 'child-sponsor-lang'); ?></label>
                       </div>
                       <div class="small-8 columns">
                           <?php echo $session_data['zipcode_payer']; ?>
                       </div>
                   </div>

                   <div class="row">
                       <div class="small-4 columns">
                           <label class="text-left middle"><?php _e('Land', 'child-sponsor-lang'); ?></label>
                       </div>
                       <div class="small-8 columns">
                           <?php echo $session_data['land_payer']; ?>
                       </div>
                   </div>

                   <div class="row">
                       <div class="small-4 columns">
                           <label class="text-left middle"><?php _e('Geburtsdatum', 'child-sponsor-lang'); ?></label>
                       </div>
                       <div class="small-8 columns">
                           <?php echo $session_data['birthday_payer']; ?>
                       </div>
                   </div>

                   <div class="row">
                       <div class="small-4 columns">
                           <label class="text-left middle"><?php _e('E-Mail-Adresse', 'child-sponsor-lang'); ?></label>
                       </div>
                       <div class="small-8 columns">
                           <?php echo $session_data['email_payer']; ?>
                       </div>
                   </div>

                   <div class="row">
                       <div class="small-4 columns">
                           <label class="text-left middle"><?php _e('Telefon', 'child-sponsor-lang'); ?></label>
                       </div>
                       <div class="small-8 columns">
                           <?php echo $session_data['phone_payer']; ?>
                       </div>
                   </div>

            <?php } ?>

                   <!-- end different payer -->

              <?php } ?>

            <a href="?step=1#bank" class="edit"><?php _e('Bearbeiten', 'child-sponsor-lang'); ?></a>

            <hr>
			 <h4 class="text-uppercase" id="briefwechsel"><?php echo sprintf( wp_kses( __('Briefwechsel mit %s', 'child-sponsor-lang'), array('br' => []) ), $child_data['name'] ); ?></h4>
			 <div class="row">
                <div class="small-12 columns">
			    <?php
	            if(!empty($session_data['language'])) {
				foreach($session_data['language'] as $check) {
				echo'<ul>';
				echo  '<li>';
				switch ($check) {

				case 'französich':
				echo _e('Französisch','child-sponsor-lang');
				break;

				case 'italienisch':
				echo _e('Italienisch','child-sponsor-lang');
				break;
				
				case 'spanisch':
				echo _e('Spanisch','child-sponsor-lang');
				break;
				
				case 'englisch':
				echo _e('Englisch','child-sponsor-lang');
				break;
				
				case 'portugiesisch':
				echo _e('Portugiesisch','child-sponsor-lang');
				break;
				}
				echo'</li></ul> ';
				}
				}
				?>

                </div>
              </div>
			  <hr>
            <h4 class="text-uppercase" id="bank"><?php _e('Wie haben Sie von Compassion erfahren?', 'child-sponsor-lang'); ?></h4>

            <?php if(!empty($session_data['consumer_source']) || !empty($session_data['consumer_source_text'])) { ?>
            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Ich habe von Compassion erfahren durch', 'child-sponsor-lang'); ?> </label>
                </div>
                <div class="small-8 columns">
                    <?php // sponsorship from muskathlon
                        if (strpos($session_data['consumer_source'], 'msk_') !== false) {
                            echo _e('Muskathlon', 'child-sponsor-lang').' : ';
                            echo $session_data['msk_participant_name'];
                        } else {
                            echo _e($session_data['consumer_source'], 'child-sponsor-lang').' : ';
                            echo _e($session_data['consumer_source_text'], 'child-sponsor-lang');
                        }
                    ?>
                </div>
            </div>
            <?php } ?> 

            <hr>
            
            <h4 class="text-uppercase"><?php _e('Freiwillig mithelfen', 'child-sponsor-lang'); ?></h4>
            <div class="row">
			<div class="small-12 columns">
           <?php if (isset($session_data['mithelfen'])) {
	          echo _e('JA', 'child-sponsor-lang'); 
	          } else {echo _e('NEIN', 'child-sponsor-lang');}
           ?> 
            </div>
            </div>
			 <hr>
             	<div class="row">
                <div class="small-12 columns condgenerale">
	            <input class="condgene" type="checkbox" required data-msg="<?php _e('Angabe erforderlich', 'child-sponsor-lang'); ?>"  <?php echo (isset($session_data['condgen']) && $session_data['condgen']['checkbox'] == 'on') ? 'checked' : ''; ?> name="condgen[checkbox]"> <span class="marg-left-10"> <?php _e('Ja, ich habe die <a target="_blank" href="https://compassion.ch/wp-content/uploads/documents_compassion/GDPR-compassion_DE.pdf">Datenschutzbestimmungen gelesen.</a>', 'child-sponsor-lang')?> </span>

                </div>
                </div>
            <hr>


            <div class="form-action">
                <a href="?step=1" class="button button-beige button-small"><?php _e('Zurück', 'child-sponsor-lang'); ?></a>
                <input type="submit" class="button button-blue button-small sponsor_ok" value="<?php _e('Bestätigen', 'child-sponsor-lang'); ?>"/>
            </div>

        </form>
    </div>

</div>
