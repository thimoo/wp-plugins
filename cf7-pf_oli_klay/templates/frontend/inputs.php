<div class="row">
    <div class="small-4 columns">
        <label class="text-left middle"><?php _e('Betrag Ihrer Spende in CHF', 'donation-form'); ?></label>
    </div>
    <div class="small-8 columns">
        <input type="text" id="wert" placeholder="<?php _e('bitte nur Zahlen', 'donation-form'); ?>" required data-msg="<?php _e('Betrag erforderlich', 'donation-form'); ?>" class="input-field" name="wert" value="<?php echo (isset($session_data['wert'])) ? $session_data['wert'] : ''; ?>">
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