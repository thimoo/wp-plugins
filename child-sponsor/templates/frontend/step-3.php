<?php
$child_data = get_child_meta($session_data['childID']);
?>

<div class="section background-white step-3 child-sponsor">
    <div class="row">

        <div class="large-12 large-centered medium-10 medium-centered column">

            <h4 class="text-center"><?php _e('Herzlichen Dank, dass du dich entschieden hast, ein Kind in extremer Armut durch eine Patenschaft zu unterstützen! Kannst du dir die Riesenfreude vorstellen, wenn unsere Mitarbeitenden vor Ort dem Kind mitteilen können, dass es jetzt auf der anderen Seite der Welt eine Patin oder einen Paten hat?', 'child-sponsor-lang'); ?></h4><br />
            <h4 class="text-center"><?php _e('Die Informationen zum Start der Patenschaft wirst du innerhalb der nächsten Tage per E-Mail erhalten. Sollte dies nicht der Fall sein, ', 'child-sponsor-lang'); ?><br />
                <?php if(ICL_LANGUAGE_CODE=='de'): ?>
                <a href="<?php echo site_url( '/de/kontakt')?>"><?php _e('melde dich bitte hier', 'child-sponsor-lang'); ?></a> <?php _e('oder unter: +41 (0)31 552 21 21.', 'child-sponsor-lang'); ?> </h4><br />
                <?php elseif(ICL_LANGUAGE_CODE=='fr'): ?>
                    <a href="<?php echo site_url( 'contact')?>"><?php _e('melde dich bitte hier', 'child-sponsor-lang'); ?></a> <?php _e('oder unter: +41 (0)31 552 21 21.', 'child-sponsor-lang'); ?> </h4><br />
                <?php elseif(ICL_LANGUAGE_CODE=='it'): ?>
                    <a href="<?php echo site_url( '/it/contatti/')?>"><?php _e('melde dich bitte hier', 'child-sponsor-lang'); ?></a> <?php _e('oder unter: +41 (0)31 552 21 21.', 'child-sponsor-lang'); ?> </h4><br />
                <?php endif; ?>

        </div>
    </div>

</div>

<?php

unset($_SESSION['child-sponsor']);

?>
