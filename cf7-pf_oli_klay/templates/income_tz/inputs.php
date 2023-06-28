<param id="type_flag" value="income_tz"/>
<input type="hidden" id="type_flag" name="type_flag" value="income_tz">
<!--<div class="row">-->
<!--    <div class="small-12 medium-4 columns">-->
<!--        <h5 class="text-uppercase">--><?//= __('Ich möchte spenden', 'donation-form') ?><!--</h5>-->
<!--    </div>-->
<!--</div>-->
<div class="row don_unique"> <!--  style="display: none;">-->

    <div class="small-12 medium-12 columns">
        <div class="row">
            <div class="small-12 medium-4 columns">
                <button class="button button-beige buttondonation" value="122" ><? echo __('1 Familie', 'donation-form') ?></button>
            </div>
            <div class="small-12 medium-4 columns">
                <button class="button button-beige buttondonation" value="1220" ><? echo __('10 Familien', 'donation-form') ?></button>
            </div>
            <div class="small-12 medium-4 columns">
                <button id="default_bt_value" class="button button-beige buttondonation" value="2440" ><? echo __('20 Familien' , 'donation-form') ?></button>
            </div>
        </div>
        <div class="row marg-top-10">
            <div class="small-12 columns ">
                <input id="wert" type="text" step="0.01" required placeholder="<? echo __('Wähle einen Betrag oder gib einen freien Betrag', 'donation-form') ?>" class="input-field buttondonationvalue" name="wert" value="<?php echo $_SESSION["fund_amount"] ?? '' ?>">
            </div>
        </div>
    </div>
</div>
