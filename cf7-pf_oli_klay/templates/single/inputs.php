<div class="row">
    <div class="small-4 columns">
        <label class="text-left middle"><?php _e('Betrag deiner Spende in CHF', 'donation-form'); ?></label>
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
        <input name="fonds" id="fonds" type="hidden" value="<?php echo $fonds ?>">
        <label class="text-left middle"><?php echo $bank_transfer_reason; ?></label>
    </div>
</div>