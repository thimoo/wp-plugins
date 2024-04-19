<param id="type_flag" value="food-2023"/>
<input type="hidden" id="type_flag" name="type_flag" value="food-2023">
<!--<div class="row">-->
<!--    <div class="small-12 medium-4 columns">-->
<!--        <h5 class="text-uppercase">--><?//= __('Ich möchte spenden', 'donation-form') ?><!--</h5>-->
<!--    </div>-->
<!--</div>-->

<script>//add an active state to the button amount we want to feature in the form
    document.addEventListener("DOMContentLoaded", function(event) {
        document.getElementById("default_bt_value").classList.add("active");
        document.getElementById("wert").value = "40";
    });
</script>
<div class="row don_unique"> <!--  style="display: none;">-->

    <div class="small-12 medium-12 columns">
        <div class="row">
            <div class="small-12 medium-4 columns">
                <button class="button button-beige buttondonation" value="20" >20.- CHF</button>
            </div>
            <div class="small-12 medium-4 columns">
                <button id="default_bt_value" class="button button-beige buttondonation" value="40" >40.- CHF</button>
            </div>
            <div class="small-12 medium-4 columns">
                <button class="button button-beige buttondonation" value="120" >120.- CHF</button>
            </div>
        </div>
        <div class="row marg-top-10">
            <div class="small-12 columns ">
                <input id="wert" type="text" step="0.01" required placeholder="<? echo __('Wähle einen Betrag oder gib einen freien Betrag', 'donation-form') ?>" class="input-field buttondonationvalue" name="wert" value="<?php echo $_SESSION["fund_amount"] ?? '' ?>">
            </div>
        </div>
    </div>
</div>
