<param id="type_flag" value="food-bf"/>
<input type="hidden" id="type_flag" name="type_flag" value="food-bf">
<!--<div class="row">-->
<!--    <div class="small-12 medium-4 columns">-->
<!--        <h5 class="text-uppercase">--><?//= __('Ich möchte spenden', 'donation-form') ?><!--</h5>-->
<!--    </div>-->
<!--</div>-->
<div class="row don_unique"> <!--  style="display: none;">-->

    <div class="small-12 medium-12 columns">
        <div class="row">
            <div class="small-12 medium-4 columns">
                <button class="button button-beige buttondonation" value="40" > 40.- CHF</button>
            </div>
            <div class="small-12 medium-4 columns">
                <button class="button button-beige buttondonation" value="120" > 120.- CHF</button>
            </div>
            <div class="small-12 medium-4 columns">
                <button class="button button-beige buttondonation" value="440" > 440.- CHF</button>
            </div>
        </div>
        <div class="row marg-top-10">
            <div class="small-12 columns ">
                <input id="wert" type="hidden" step="0.01" required placeholder="choisir un montant ci-dessus ou indiquer un montant libre" class="input-field" name="wert" value="<?php echo $_SESSION["fund_amount"] ?? '' ?>">
            </div>
        </div>
    </div>
</div>