<noscript>
    <div class="row">
        <div class="medium-12 column">
            <div class="callout alert">
                <?php _e('Sie haben JavaScript in Ihrem Browser deaktivert. Damit das Formular funktioniert muss JavaScript aktiviert werden.', 'compassion'); ?>
            </div>
        </div>
    </div>
</noscript>

<script type="text/javascript">
    jQuery( document ).ready(function() {
        // Handler for .ready() called.
        if(window.location.hash){
            var hashParams = window.location.hash.substr(1).split('&'); // substr(1) to remove the `#`
            for(var i = 0; i < hashParams.length; i++){
                var p = hashParams[i].split('=');
                console.log(p);
                document.getElementById(p[0]).value = decodeURIComponent(p[1]);
            }
        }
    });
</script>

<form action="" class="compassion-letter-form" enctype="multipart/form-data">

    <div class="row">

        <div class="medium-12 large-6 column">
             <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Anrede', 'compassion-letters'); ?>*</label>
                </div>
                <div class="small-8 columns anrede radio-wrapper">
                    <input id="radio_frau" required data-msg="<?php _e('Anrede erforderlich', 'compassion-letters'); ?>" type="radio" name="salutation" value="Frau" <?php echo (isset($session_data['salutation']) && $session_data['salutation'] == 'Frau') ? 'checked' : ''; ?>><label for="radio_frau"><?php _e('Frau', 'compassion-letters'); ?></label><input id="radio_herr" required data-msg="<?php _e('Anrede erforderlich', 'compassion-letters'); ?>" type="radio" name="salutation" value="Herr" <?php echo (isset($session_data['salutation']) && $session_data['salutation'] == 'Herr') ? 'checked' : ''; ?>><label for="radio_herr"><?php _e('Herr', 'compassion-letters'); ?></label>
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('E-Mail', 'compassion-letters'); ?>*</label>
                </div>
                <div class="small-8 columns">
                    <input type="email" id="email" required data-msg="<?php _e('E-Mailadresse erforderlich', 'compassion-letters'); ?>" class="input-field clear-pdf-on-change" name="email">
                </div>
            </div>
            
             <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Vorname, Nachname', 'compassion-letters'); ?>*</label>
                </div>
                <div class="small-8 columns">
                    <input type="text" required data-msg="<?php _e('Name erforderlich', 'compassion-letters'); ?>" class="input-field clear-pdf-on-change" name="name" id="pname">
                </div>
            </div>


            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Patennummer (ohne CH)', 'compassion-letters'); ?>*</label>
                </div>
                <div class="small-8 columns">
                    <input type="text" placeholder="1510000" required data-msg-referenznummer="<?php _e('Bitte gültige Nummer eingeben (ohne CH)', 'compassion-letters') ?>" data-msg="<?php _e('Patennummer erforderlich', 'compassion-letters'); ?>" class="input-field clear-pdf-on-change" name="referenznummer" id="sponsor_ref">
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Patenkindnummer', 'compassion-letters'); ?>*</label>
                </div>
                <div class="small-8 columns">
                    <input type="text" placeholder="AB014900332" required data-msg="<?php _e('Patenkindnummer erforderlich', 'compassion-letters'); ?>" data-msg-patenkindnummer="<?php _e('Bitte gültige Patenkindnummer eingeben', 'compassion-letters') ?>" class="input-field clear-pdf-on-change" name="patenkind" id="child_ref">
                </div>
            </div>

            <div class="row">
                <div class="small-4 columns">
                    <label class="text-left middle"><?php _e('Nachricht', 'compassion-letters'); ?>*</label>
                </div>
                <div class="small-8 columns">
                    <textarea maxlength="1500" placeholder="<?php _e('Um den Verlust deines Briefes zu vermeiden, empfehlen wir dir, diesen zuerst auf einem Word-Dokument zu schreiben und ihn danach hier einzufügen.', 'compassion-letters'); ?>" required data-msg="<?php _e('Nachricht erforderlich', 'compassion-letters'); ?>" name="message" class="input-field clear-pdf-on-change"></textarea>
                    <p class="text-right letter-count-wrapper"><span class="letter-count">0</span> <?php _e('von 1300 Zeichen', 'compassion-letters'); ?></p>
                </div>
            </div>
            <div class="row">
                <div class="small-12 columns condgenerale">
	            <input class="condgene" type="checkbox" required data-msg="<?php _e('Angabe erforderlich', 'child-sponsor-lang'); ?>"  <?php echo (isset($session_data['condgen']) && $session_data['condgen']['checkbox'] == 'on') ? 'checked' : ''; ?> name="condgen[checkbox]"> <span class="marg-left-10"> <?php _e('Ja, ich habe die <a target="_blank" href="https://compassion.ch/wp-content/uploads/documents_compassion/GDPR-compassion_DE.pdf">Datenschutzbestimmungen gelesen.</a>', 'child-sponsor-lang')?> </span>
<br/>	  <br/>	                  <small><a href="mailto:info@compassion.ch?subject=<?php _e('Ich finde meine Nummer gerade nicht! Bitte sendet mir die Informationen per E-Mail', 'compassion-letters'); ?>&body=<?php _e('Vorname, Nachname', 'compassion-letters'); ?>:"><?php _e('Ich finde meine Nummer gerade nicht! Bitte sendet mir die Informationen per E-Mail', 'compassion-letters'); ?></a> </small>


                </div>
                </div>
            <hr>

        </div>

<div class="medium-12 large-6 column">

<!--
            <div class="row file-upload-wrapper">
                <div class="small-3 columns">
                    <label class="text-left middle"><?php _e('Bild', 'compassion-letters'); ?></label>
                </div>
                <div class="small-9 columns">
                    <div class="file-upload input-field">
                        <a href="#" id="clear-file-input" class="has-tip right" data-tooltip aria-haspopup="true" data-disable-hover="false" tabindex="1" title="<?php _e('Feld leeren', 'compassion-letters'); ?>"></a><label for="fileUpload" class="button button-blue button-medium"><?php _e('Bild hochladen', 'compassion-letters'); ?></label>
                       <p class="text-right"><?php _e('Format: JPG/PNG, max. Grösse: 2MB', 'compassion-letters') ?></p>
                        <input type="text" id="filename" readonly value="" />
                        <input type="file" id="fileUpload" class="input-field clear-pdf-on-change show-for-sr" name="image">
                    </div>
                </div>
            </div>
-->

    <div class="row choose-template radio-wrapper">
        <div class="large-12 columns">
        </div>
        <div class="large-12 columns">
                    <input required data-msg="<?php _e('Vorlage erforderlich', 'compassion-letters'); ?>" type="radio" id="template1" class="clear-pdf-on-change" checked="checked"  value="christmas_web_2019" name="template" />
                    <label><?php _e('Vorlage 1', 'compassion-letters'); ?></label><label for="template1" class="template-label">
                    <img src="<?php echo COMPASSION_LETTERS_PLUGIN_DIR_URL; ?>assets/images/christmas_web_2019-preview.jpg" />
                    </label>
        </div>
    </div>
    </div>

</div>

<!-- modal pour les preview avec jquery pour afficher la bonne image dans le modal -->
<div id="preview-modal2" class="reveal" data-reveal aria-hidden="true" >
        <div class="content"></div>
        <!-- attention: la balise img doit être présente pour que tout fonctionne -->
        <img/>
        <a class="close-reveal-modal" aria-label="Close"></a>
</div>
<script>
	jQuery('[data-open="preview-modal2"]').click(function(event) {
		var src = jQuery(event.target).parent().find('img').attr('src');
		jQuery('#preview-modal2').find('img').attr('src', src);
	});
	
	</script>




    <input type="hidden" name="pdf_path" id="pdf_path" value="" />

    <div class="form-actions">
      <a href="#" id="preview-button" class="button button-medium button-beige"><?php _e('Vorschau', 'compassion-letters'); ?> </a><input type="submit" value="<?php _e('Absenden', 'compassion-letters'); ?>" class="button button-medium button-blue" />
      <br/>  <br/>
<!--       <p style="margin-top:8px;"><a href="#conseil"><?php _e('Tipps fürs Briefeschreiben','compassion-letters');?></a></p> -->
    </div>

    <div id="preview-modal" class="reveal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
        <div class="content"></div>

        <div class="form-action">
            <a href="#" class="button button-medium button-blue" id="preview-send-button"><?php _e('Absenden', 'compassion-letters'); ?></a>
        </div>

        <a class="close-reveal-modal" aria-label="Close"></a>
    </div>

    <div id="loading-modal" class="reveal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
        <div class="content">

            <h4 class="preview-loading"><?php _e('Deine Vorschau wird generiert...', 'compassion-letters') ?></h4>
            <h4 class="send-loading"><?php _e('Dein Brief wird generiert...', 'compassion-letters') ?></h4>
            <h4 class="send-success"><?php _e('Dein Brief wurde verschickt. Du erhältst eine Kopie per E-Mail.', 'compassion-letters') ?></h4>
            <h4 class="send-fail"><?php _e('Es ist ein Fehler!', 'compassion-letters') ?></h4>
            <p class="send-fail"><?php _e('Wir haben derzeit ein Problem mit dem Online Senden von Briefen. Unsere Informatiker arbeiten an dem Fehler. Vielen Dank für dein Verständnis. In der Zwischenzeit sende bitte deinen Brief an info@compassion.ch.', 'compassion-letters') ?></p>

            <div class="loading-icon">
                <svg version="1.1" id="Ebene_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 25.754 29.612" enable-background="new 0 0 25.754 29.612" xml:space="preserve" width="100%" height="100%">
                    <g>

                        <path clip-path="url(#SVGID_2_)" fill="#0054a6" d="M8.383,7.149c2.159,0.101,3.982-1.393,4.074-3.337
                            c0.092-1.944-1.585-3.602-3.744-3.703C6.554,0.008,4.731,1.502,4.641,3.446C4.548,5.39,6.226,7.048,8.383,7.149"/>
                        <path clip-path="url(#SVGID_2_)" fill="#0054a6" d="M18.917,0.141c0,0-2.026,3.586-6.545,6.153C7.853,8.86,3.839,10.352,0,16.297
                            c0,0,1.141-0.527,0.351,0.748c0,0,0.592-0.56,1.097-0.728c0,0,0.307-0.064,0.175,0.309c0,0,2.027-1.571,1.514-0.812
                            c-0.31,0.458,1.607-1.448,1.837-1.261c0,0,0.066,0.115-0.222,0.551c0,0,4.572-2.753,6.282-3.48
                            c7.404-3.149,8.993-8.357,8.312-11.297C19.347,0.327,19.32-0.262,18.917,0.141"/>
                        <path clip-path="url(#SVGID_2_)" fill="#0054a6" d="M3.477,25.509c0,0,0.318-7.721,5.122-11.669
                            c4.804-3.948,9.388-3.224,11.297-3.356c0,0,3.904,0.11,5.857-0.548c0,0,0.285,3.444-9.301,5.089c0,0-3.159-0.044-5.857,3.553
                            c0,0-2.676,3.005-5.155,9.213c0,0-0.022-0.898-0.373-0.263l-0.921,2.084l-0.033-1.963c0,0-0.164-0.494-0.384,0.186
                            c0,0-0.175,0.231,0.121-2.38C3.85,25.455,3.773,24.896,3.477,25.509"/>
                    </g>
                </svg>
            </div>
        </div>



<!--         <a class="close-reveal-modal" aria-label="Close"></a> -->
    </div>
    
</form>
