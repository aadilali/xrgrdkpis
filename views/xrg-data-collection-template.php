<?php

use XRG\RD\XrgRdKpis;
use XRG\RD\XrgHelperFunctions;

get_header();

// KPIs Form Data
if(isset($_POST['xrg_kpis_data_submit']) && $_POST['xrg_kpis_data_submit'] === 'SAVE') {
    unset( $_POST['xrg_kpis_data_submit'] );
    XrgRdKpis::instance()->xrgDBInstance()->xrgSaveDataToDB($_POST);
}

// Labor Form Data
if(isset($_POST['xrg_labor_data_submit']) && $_POST['xrg_labor_data_submit'] === 'SAVE') {
    unset( $_POST['xrg_labor_data_submit'] );
    XrgRdKpis::instance()->xrgDBInstance()->xrgSaveDataToDB($_POST);
}

// Locations in a region
$regionLocations = ['Huntington Beach', 'Anaheim', 'Irvine', 'Yorba Linda', 'Cypress'];

// Months against periods
$periodDetails = ['Period 1', 'Period 2', 'Period 3', 'Period 4', 'Period 5', 'Period 6', 'Period 7', 'Period 8', 'Period 9', 'Period 10', 'Period 11', 'Period 12'];
$currentMonth = date('m');

$weekDetails = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
$currentWeek = ceil((date("d",strtotime('today')) - date("w",strtotime('today')) - 1) / 7) + 1;

?>
<div class="alignwide xrg-wrapper">
    <div class="flex-container-form xrg-kpi-data">
        <!-- LABOR FORECAST FORM  -->
        <div id="kpis_data_form" class="period-tab-content">
            <h2>KPIs DATA FORM</h2>
            <form method="post" action="" id="labor_sheet">
                <input type="hidden" name="xrg_region" value="ASantana" />
                <input type="hidden" name="xrg_data_type" value="kpis" />
                <div class="flex-body">
                    <div class="flex-col-form">
                        <span class="heading_text">
                            Select Period
                        </span> 
                        <span class="field_val">
                            <select name="xrg_period" required>
                                <option value="">Select Period</option>
                                <?php 
                                foreach($periodDetails as $pd) {
                                    $selectedPeriod = ($periodDetails[$currentMonth - 1] === $pd) ? 'selected' : '';
                                    echo '<option value="'.$pd.'" '.$selectedPeriod.'>'.$pd.'</option>'; 
                                }
                                ?>
                            </select>
                        </span>
                    </div>
                    <div class="flex-col-form">
                        <span class="heading_text">
                            Select Week
                        </span> 
                        <span class="field_val">
                            <select name="xrg_week" required>
                                <option value="">Select Week</option>
                                <?php 
                                foreach($weekDetails as $index => $wd) {
                                    $selectedWeek = ( $currentWeek == ($index + 1)) ? 'selected' : '';
                                    echo '<option value="'.$wd.'" '.$selectedWeek.'>'.$wd.'</option>'; 
                                }
                                ?>
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
                            <input type="text" name="xrg_locations[]" value="<?php echo $location; ?>" readonly />
                        </span>
                    </div>
                    <?php  $location = XrgHelperFunctions::xrgFormatArrayKeys($location); ?>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[net_sales_wtd]" />
                        </span>
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[var_bgt_sale]" />
                        </span> 
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[net_profit]" />
                        </span> 
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[var_bgt_net_profit]" />
                        </span> 
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[theo_food_var]" />
                        </span>
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[theo_liq_var]" />
                        </span>
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[end_food_inv]" />
                        </span>
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[end_liq_inv]" />
                        </span>
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[theo_labor_wtd]" />
                        </span>
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[training_pay_wtd]" />
                        </span>
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[training_weekly_bgt]" />
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
                <div class="flex-button-body">
                    <div class="flex-col-form border-none"><input type="submit" id="save_record" class="btn" name="xrg_kpis_data_submit" value="SAVE" /></div>
                </div>
            </form>
        </div>
        <!-- LABOR FORECAST FORM  -->
        <div id="labor_data_form" class="period-tab-content">
            <h2>LABOR FORECAST DATA FORM</h2>
            <form method="post" action="" id="kpi_sheet">
                <input type="hidden" name="xrg_region" value="ASantana" />
                <input type="hidden" name="xrg_data_type" value="labor" />
                <div class="flex-body">
                    <div class="flex-col-form">
                        <span class="heading_text">
                            Select Period
                        </span> 
                        <span class="field_val">
                            <select name="xrg_period" required>
                                <option value="">Select Period</option>
                                <?php 
                                foreach($periodDetails as $pd) {
                                    $selectedPeriod = ($periodDetails[$currentMonth - 1] === $pd) ? 'selected' : '';
                                    echo '<option value="'.$pd.'" '.$selectedPeriod.'>'.$pd.'</option>'; 
                                }
                                ?>
                            </select>
                        </span>
                    </div>
                    <div class="flex-col-form">
                        <span class="heading_text">
                            Select Week
                        </span> 
                        <span class="field_val">
                            <select name="xrg_week" class="labor-form-week" required>
                                <option value="">Select Week</option>
                                <?php 
                                foreach($weekDetails as $index => $wd) {
                                    $selectedWeek = ( $currentWeek == ($index + 1)) ? 'selected' : '';
                                    echo '<option value="'.$wd.'" '.$selectedWeek.'>'.$wd.'</option>'; 
                                }
                                ?>
                            </select>
                        </span>
                    </div>
                </div>
                <div class="flex-head">
                    <div class="flex-col-form-head">
                        <span class="heading_text" id="week-value">Week 1</span>
                    </div>
                    <div class="flex-col-form-head">
                        <span class="heading_text">Forecasted Sales</span>
                    </div>
                    <div class="flex-col-form-head">
                        <span class="heading_text">Forecasted Labor %</span> 
                    </div>
                    <div class="flex-col-form-head">
                        <span class="heading_text">Budgeted Labor %</span> 
                    </div>
                    <div class="flex-col-form-head">
                        <span class="heading_text">Projected Theoretical Labor</span> 
                    </div>
                    <div class="flex-col-form-head">
                        <span class="heading_text">Scheduled Shift Leader Hours</span> 
                    </div>
                    <div class="flex-col-form-head">
                        <span class="heading_text">Budgeted Shift Leader Hours</span> 
                    </div>
                </div>
                <?php foreach($regionLocations as $location) : ?>
                <div class="flex-body">
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="xrg_locations[]" value="<?php echo $location; ?>" readonly />
                        </span>
                    </div>
                    <?php  $location = XrgHelperFunctions::xrgFormatArrayKeys($location); ?>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[forecasted_sales]" />
                        </span>
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[forecasted_labor]" />
                        </span> 
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[budgeted_labor]" />
                        </span> 
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[theo_labor]" />
                        </span> 
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[scheduled_leader_hours]" />
                        </span>
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="<?php echo $location; ?>[budgeted_leader_hours]" />
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
                <div class="flex-button-body">
                    <div class="flex-col-form border-none"><input type="submit" id="save_record" class="btn" name="xrg_labor_data_submit" value="SAVE" /></div>
                </div>
            </form>
        </div>

        <!--  Tabs Button  -->
        <div class="tabs-btn-wrapper">
            <button class="periods-tab entry-template-tabs" data-period-id="kpis_data_form" >KPIs Data</button>
            <button class="periods-tab entry-template-tabs" data-period-id="labor_data_form" >Labor Forecast Data</button>
        </div>

    </div>
</div>
<?php
get_footer();
