<param id="type_flag" value="food"/>
<input type="hidden" id="type_flag" name="type_flag" value="food">
<div class="row">
    <div class="small-12 medium-4 columns">
        <h5 class="text-uppercase"><?= __('Ich möchte spenden', 'donation-form') ?></h5>
    </div>
    <div class="small-12 medium-8 columns">
        <div style="margin-bottom: 16px;">
            <label style="display:inline-block; margin-right:8px;">
                <input type="radio" name="choix_don_unique_mensuel" value="don_mensuel" id="don_mensuel" checked>
                <?= __('Monatlich', 'donation-form') ?>
            </label>
            <label style="display:inline-block">
                <input type="radio" name="choix_don_unique_mensuel" value="don_unique" id="don_unique">
                <?= __('Einmalige Spende', 'donation-form') ?>
            </label>
        </div>
    </div>
</div>
<div class="row don_mensuel">
    <div class="small-12 medium-4 columns">
        <!-- <label class="text-left middle"><?php _e('Je désire faire un don mensuel', 'donation-form'); ?></label> -->
    </div>
    <div class="small-12 medium-8 columns">
        <div class="select-wrapper">
            <select name="fonds" id="fonds" class="input-field">
                <option data-v="10" value="drf_food_crisis_mensuel_ 10" <?php echo (isset($_SESSION["fund_code"]) && $_SESSION["fund_code"] == "mensuel 10") ? 'selected' : '' ?>><?php _e('Monatliche Spende von CHF 10.-', 'donation-form'); ?></option>
                <option data-v="20" value="drf_food_crisis_mensuel_ 20" <?php echo (isset($_SESSION["fund_code"]) && $_SESSION["fund_code"] == "mensuel 20") ? 'selected' : '' ?>><?php _e('Monatliche Spende von CHF 20.-', 'donation-form'); ?></option>
                <option data-v="40" selected="selected" value="drf_food_crisis_mensuel_ 40" <?php echo (isset($_SESSION["fund_code"]) && $_SESSION["fund_code"] == "mensuel 40") ? 'selected' : '' ?>><?php _e('Monatliche Spende von CHF 40.-', 'donation-form'); ?></option>
                <option data-v="120" value="drf_food_crisis_mensuel_120" <?php echo (isset($_SESSION["fund_code"]) && $_SESSION["fund_code"] == "mensuel 120") ? 'selected' : '' ?>><?php _e('Monatliche Spende von CHF 120.-', 'donation-form'); ?></option>
                <option data-v="240" value="drf_food_crisis_mensuel_240" <?php echo (isset($_SESSION["fund_code"]) && $_SESSION["fund_code"] == "mensuel 240") ? 'selected' : '' ?>><?php _e('Monatliche Spende von CHF 240.-', 'donation-form'); ?></option>
            </select>
        </div>
        <p class="small">
            <?php _e('Nach deiner ersten Spende wird dir Compassion Schweiz einen Einzahlungsschein für die weiteren Spenden zusenden.', 'donation-form'); ?>
        </p>
    </div>
</div>
<div class="row don_unique" style="display: none;">
    <div class="small-12 medium-4 columns">
        <!-- <label class="text-left middle"><?php _e('Je désire faire un don unique', 'donation-form'); ?></label> -->
    </div>
    <div class="small-12 medium-8 columns">
        <input id="wert" placeholder="<?= __('Betrag angeben', 'donation-form') ?>" type="number" step="0.01" required class="input-field" name="wert" value="<?php echo $_SESSION["fund_amount"] ?? '' ?>">
    </div>

</div>

<script>
    function select_monthly(jQuery) {
        jQuery('.don_mensuel').show();
        jQuery('.don_unique').hide();
        jQuery('input[name="wert"]').attr('required', false);
    }

    function select_unique(jQuery) {
        jQuery('.don_mensuel').hide();
        jQuery('.don_unique').show();
        jQuery('input[name="wert"]').attr('required', true);
    }

    jQuery(function () {
        jQuery('#don_unique').on('ifChanged', function () {
            if (jQuery(this).prop('checked')) {
                select_unique(jQuery);
            } else {
                select_monthly(jQuery);
            }
        });
        select_monthly(jQuery);
        if (jQuery('input[name="wert"]').val() !== '') {
            select_unique(jQuery);
        } else {
            select_monthly(jQuery);
        }
    });
</script>
