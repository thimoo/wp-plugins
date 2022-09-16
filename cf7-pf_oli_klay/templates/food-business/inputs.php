<param id="type_flag" value="food-business"/>
<input type="hidden" id="type_flag" name="type_flag" value="food-business">
<div class="row">
    <div class="small-12 medium-4 columns">
        <h5 class="text-uppercase"><?= __('Ich möchte spenden', 'donation-form') ?></h5>
    </div>

</div>

<div class="row don_unique"> <!--  style="display: none;">-->
    <div class="small-12 medium-4 columns">
        <label class="text-left middle"><?php _e('Betrag deiner Spende in CHF', 'donation-form'); ?></label>
    </div>
    <div class="small-12 medium-8 columns">
        <input id="wert" placeholder="<?= __('Betrag angeben', 'donation-form') ?>" type="number" step="0.01" required class="input-field" name="wert" value="<?php echo $_SESSION["fund_amount"] ?? '' ?>">
    </div>

</div>
