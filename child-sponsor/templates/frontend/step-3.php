<?php
$child_data = get_child_meta($session_data['childID']);
?>

<div class="section background-white step-3 child-sponsor">
    <div class="row">

        <div class="large-8 large-centered medium-10 medium-centered column">

            <h3 class="text-center"><?php _e('Vielen Dank für Ihre Anfrage!', 'child-sponsor-lang'); ?></h3><br />

        </div>
    </div>

</div>

<?php

unset($_SESSION['child-sponsor']);

?>
