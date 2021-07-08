<?php

//use BrunchSheet\BrunchData;
//(BrunchData::instance());
//echo BrunchData::instance()->getData();
use XRG\RD\XrgRdKpis;

get_header();

if(isset($_POST['xrg_kpis_data_submit']) && $_POST['xrg_kpis_data_submit'] === 'SAVE') {
    print_r($_POST);
    XrgRdKpis::instance()->xrgLoadSpreadSheet()->xrgWriteFormData($_POST);
}

// Locations in a region
$regionLocations = ['Huntington Beach', 'Anaheim', 'Irvine', 'Yorba Linda', 'Cypress'];

?>
<div class="alignwide xrg-wrapper">
    <div class="flex-container-form xrg-kpi-data">
        <form method="post" action="" id="kpi_sheet">
            <div class="flex-body">
                <div class="flex-col-form">
                    <span class="heading_text">
                        Select Period
                    </span> 
                    <span class="field_val">
                        <select name="xrg_period">
                            <option value="0">Select Period</option>
                            <option value="Period 1">Period 1</option>
                            <option value="Period 2">Period 2</option>
                            <option value="Period 3">Period 3</option>
                            <option value="Period 4">Period 4</option>
                        </select>
                    </span>
                </div>
                <div class="flex-col-form">
                    <span class="heading_text">
                        Select Week
                    </span> 
                    <span class="field_val">
                        <select name="xrg_week">
                            <option value="0">Select Week</option>
                            <option value="Week 1">Week 1</option>
                            <option value="Week 2">Week 2</option>
                            <option value="Week 3">Week 3</option>
                            <option value="Week 4">Week 4</option>
                        </select>
                    </span>
                </div>
            </div>
            <div class="flex-head">
                <div class="flex-col-form-head">
                    <span class="heading_text">RD</span>
                </div>
                <div class="flex-col-form-head">
                    <span class="heading_text">Net Sales $ WTD</span>
                </div>
                <div class="flex-col-form-head">
                    <span class="heading_text">Var - Budgeted $ Sale</span> 
                </div>
                <div class="flex-col-form-head">
                    <span class="heading_text">Net Profit $</span> 
                </div>
                <div class="flex-col-form-head">
                    <span class="heading_text">Var - Budgeted Net Profit</span> 
                </div>
                <div class="flex-col-form-head">
                    <span class="heading_text">Theo Food Variance</span> 
                </div>
                <div class="flex-col-form-head">
                    <span class="heading_text">Theo Liquor Variance</span> 
                </div>
                <div class="flex-col-form-head">
                    <span class="heading_text">Ending Food Inventory</span> 
                </div>
                <div class="flex-col-form-head">
                    <span class="heading_text">Ending Liquor Inventory</span> 
                </div>
                <div class="flex-col-form-head">
                    <span class="heading_text">Theo Labor WTD</span> 
                </div>
                <div class="flex-col-form-head">
                    <span class="heading_text">Training Pay WTD</span> 
                </div>
                <div class="flex-col-form-head">
                    <span class="heading_text">Training Weekly $ Budget</span> 
                </div>
            </div>
            <?php foreach($regionLocations as $location) : ?>
            <div class="flex-body">
                <div class="flex-col-form">
                    <span class="field_val">
                        <input type="text" name="xrg_locations[]" value="<?php echo $location; ?>" />
                    </span>
                </div>
                <div class="flex-col-form">
                    <span class="field_val">
                        <input type="text" name="net_sales_wtd[<?php echo $location; ?>]" />
                    </span>
                </div>
                <div class="flex-col-form">
                    <span class="field_val">
                        <input type="text" name="var_bgt_sale[<?php echo $location; ?>]" />
                    </span> 
                </div>
                <div class="flex-col-form">
                    <span class="field_val">
                        <input type="text" name="net_profit[<?php echo $location; ?>]" />
                    </span> 
                </div>
                <div class="flex-col-form">
                    <span class="field_val">
                        <input type="text" name="var_bgt_net_profit[<?php echo $location; ?>]" />
                    </span> 
                </div>
                <div class="flex-col-form">
                    <span class="field_val">
                        <input type="text" name="theo_food_var[<?php echo $location; ?>]" />
                    </span>
                </div>
                <div class="flex-col-form">
                    <span class="field_val">
                        <input type="text" name="theo_liq_var[<?php echo $location; ?>]" />
                    </span>
                </div>
                <div class="flex-col-form">
                    <span class="field_val">
                        <input type="text" name="end_food_inv[<?php echo $location; ?>]" />
                    </span>
                </div>
                <div class="flex-col-form">
                    <span class="field_val">
                        <input type="text" name="end_liq_inv[<?php echo $location; ?>]" />
                    </span>
                </div>
                <div class="flex-col-form">
                    <span class="field_val">
                        <input type="text" name="theo_labor_wtd[<?php echo $location; ?>]" />
                    </span>
                </div>
                <div class="flex-col-form">
                    <span class="field_val">
                        <input type="text" name="training_pay_wtd[<?php echo $location; ?>]" />
                    </span>
                </div>
                <div class="flex-col-form">
                    <span class="field_val">
                        <input type="text" name="training_weekly_bgt[<?php echo $location; ?>]" />
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
            <div class="flex-button-body">
                <div class="flex-col-form border-none"><input type="submit" id="save_record" class="btn" name="xrg_kpis_data_submit" value="SAVE" /></div>
            </div>
        </form>
    </div>
</div>
<?php
get_footer();