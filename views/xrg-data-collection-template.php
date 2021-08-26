<?php

use XRG\RD\XrgRdKpis;
use XRG\RD\XrgHelperFunctions;

// KPIs Form Data
if(isset($_POST['xrg_kpis_data_submit']) && $_POST['xrg_kpis_data_submit'] === 'SAVE') {
    //Verify Nounce
    if ( isset( $_POST['xrg_verify_kpis'] ) || wp_verify_nonce( $_POST['xrg_verify_kpis'], 'xrg_verify_kpis_data' ) ) {
        unset( $_POST['xrg_kpis_data_submit'] );
        XrgRdKpis::instance()->xrgDBInstance()->xrgSaveDataToDB($_POST);
        wp_redirect( site_url('/rd-view-sheet'));
        exit;
    }
}

// Labor Form Data
if(isset($_POST['xrg_labor_data_submit']) && $_POST['xrg_labor_data_submit'] === 'SAVE') {
     //Verify Nounce
     if ( isset( $_POST['xrg_verify_labor'] ) || wp_verify_nonce( $_POST['xrg_verify_labor'], 'xrg_verify_labor_data' ) ) {
        unset( $_POST['xrg_labor_data_submit'] );
        XrgRdKpis::instance()->xrgDBInstance()->xrgSaveDataToDB($_POST);
        wp_redirect( site_url('/rd-view-sheet'));
        exit;
    }
}

// Staffing Pars Data
if(isset($_POST['xrg_staffing_pars_data_submit']) && $_POST['xrg_staffing_pars_data_submit'] === 'SAVE') {
    //Verify Nounce
    if ( isset( $_POST['xrg_verify_staffing_pars'] ) || wp_verify_nonce( $_POST['xrg_verify_staffing_pars'], 'xrg_verify_staffing_pars_data' ) ) {
       unset( $_POST['xrg_staffing_pars_data_submit'] );
       XrgRdKpis::instance()->xrgDBInstance()->xrgSaveStaffingToDB($_POST);
       wp_redirect( site_url('/rd-view-sheet'));
       exit;
   }
}

// Is region valid
if(isset($_GET['region'])) {
    //Check regions name is store in DB
    if(! XrgHelperFunctions::xrgIsValidRegion($_GET['region'])) {
        wp_redirect( site_url('/xrg-regions-list'));
        exit;
   }

   $regionName = $_GET['region'];
   
   // Locations in a region
   $regionLocations = XrgHelperFunctions::xrgRegionLocations($regionName);
}

// Is region available
if(! isset($_GET['region'])) {
    wp_redirect( site_url('/xrg-regions-list'));
    exit;
}

//$regionLocations = ['Huntington Beach', 'Anaheim', 'Irvine', 'Yorba Linda', 'Cypress'];

// Months against periods
$periodDetails = ['Period 1', 'Period 2', 'Period 3', 'Period 4', 'Period 5', 'Period 6', 'Period 7', 'Period 8', 'Period 9', 'Period 10', 'Period 11', 'Period 12'];
$currentMonth = date('m');

$weekDetails = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
$currentWeek = ceil((date("d",strtotime('today')) - date("w",strtotime('today')) - 1) / 7) + 1;

// Staffing Pars Types
$staffingPars = [
    'Server' => 'Servers', 'Cocktail' => 'Cocktail', 'Host' => 'Host',
    'Bartender' => 'Bar', 'Busser/Runner' => 'Bus', 'Expo' => 'Expo',
    'Line Cook' => 'Cook', 'Prep Cook' => 'Prep', 'Dish' => 'Dish'
];

get_header();

?>
<div class="alignwide xrg-wrapper">
    <div class="flex-container-form xrg-kpi-data">
        <!-- LABOR FORECAST FORM  -->
        <div id="kpis_data_form" class="period-tab-content">
            <h2>KPIs DATA FORM</h2>
            <form method="post" action="" id="kpi_sheet">
                <input type="hidden" name="xrg_region" value="<?php echo esc_attr($regionName); ?>" />
                <input type="hidden" name="xrg_data_type" value="kpis" />
                <?php wp_nonce_field( 'xrg_verify_kpis_data', 'xrg_verify_kpis' ); ?>
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
                <div id='kpis_data_container'>
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
                </div>
                <div class="flex-button-body">
                    <div class="flex-col-form border-none"><input type="submit" id="save_record" class="btn" name="xrg_kpis_data_submit" value="SAVE" /></div>
                </div>
            </form>
        </div>
        <!-- LABOR FORECAST FORM  -->
        <div id="labor_data_form" class="period-tab-content">
            <h2>LABOR FORECAST DATA FORM</h2>
            <form method="post" action="" id="labor_sheet">
                <input type="hidden" name="xrg_region" value="<?php echo esc_attr($regionName); ?>" />
                <input type="hidden" name="xrg_data_type" value="labor" />
                <?php wp_nonce_field( 'xrg_verify_labor_data', 'xrg_verify_labor' ); ?>
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
                <div id="labor_data_container">
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
                </div>
                <div class="flex-button-body">
                    <div class="flex-col-form border-none"><input type="submit" id="save_record" class="btn" name="xrg_labor_data_submit" value="SAVE" /></div>
                </div>
            </form>
        </div>

        <!-- STAFFING PARS DATA FORM  -->
        <div id="staffing_pars_data_form" class="period-tab-content">
            <h2>STAFFING PARS FORM</h2>
            <form method="post" action="" id="staffing_pars_sheet">
                <input type="hidden" name="xrg_region" value="<?php echo esc_attr($regionName); ?>" />
                <?php wp_nonce_field( 'xrg_verify_staffing_pars_data', 'xrg_verify_staffing_pars' ); ?>
                <div id="staffing_data_container">
                    <?php foreach($regionLocations as $location) : ?>
                    <div class="location-staffing-container">
                        <div class="flex-col-form">
                            <span class="field_val">
                                <input type="text" class="location-name" name="xrg_locations[]" value="<?php echo $location; ?>" readonly />
                            </span>
                        </div>
                        <?php  $location = XrgHelperFunctions::xrgFormatArrayKeys($location); ?>
                        <?php foreach($staffingPars as $staffingPar) : ?>
                        <div class="staffing-type-row">   <!--  Reapter Row  -->

                            <div class="flex-head">
                                <div class="flex-col-form-head">
                                    <span class="heading_text"></span>
                                </div>
                                <div class="flex-col-form-head">
                                    <span class="heading_text"></span>
                                </div>
                                <div class="flex-col-form-head">
                                    <span class="heading_text">Mon</span>
                                </div>
                                <div class="flex-col-form-head">
                                    <span class="heading_text">Tues</span> 
                                </div>
                                <div class="flex-col-form-head">
                                    <span class="heading_text">Wed</span> 
                                </div>
                                <div class="flex-col-form-head">
                                    <span class="heading_text">Thurs</span> 
                                </div>
                                <div class="flex-col-form-head">
                                    <span class="heading_text">Fri</span> 
                                </div>
                                <div class="flex-col-form-head">
                                    <span class="heading_text">Sat</span> 
                                </div>
                                <div class="flex-col-form-head">
                                    <span class="heading_text">Sun</span> 
                                </div>
                            </div>

                            <div class="flex-body">
                                <div class="flex-body-left">
                                    <div class="flex-col-form">
                                        <span class="field_val staffing-par-head">
                                            <?php echo $staffingPar; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-body-right">
                                    <div class="flex-body-right-content">
                                        <!-- AM DATA -->
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                am
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[am][Mon]" value="0" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[am][Tues]" value="0" />
                                            </span> 
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[am][Wed]" value="0" />
                                            </span> 
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[am][Thurs]" value="0" />
                                            </span> 
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[am][Fri]" value="0" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[am][Sat]" value="0" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[am][Sun]" value="0" />
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-body-right-content">
                                        <!-- PM DATA -->
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                pm
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[pm][Mon]" value="0" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[pm][Tues]" value="0" />
                                            </span> 
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[pm][Wed]" value="0" />
                                            </span> 
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[pm][Thurs]" value="0" />
                                            </span> 
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[pm][Fri]" value="0" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[pm][Sat]" value="0" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[pm][Sun]" value="0" />
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-body-right-content">
                                        <!-- Total Par Field -->
                                        <div class="flex-col-form par-type-total-label">
                                            <span class="field_label">
                                                <?php echo $staffingPar; ?> In Training
                                            </span>
                                        </div>
                                        <div class="flex-col-form par-type-total-val">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[in_training]" value="0" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form par-type-total-label">
                                            <span class="field_label">
                                                Total <?php echo $staffingPar; ?>
                                            </span>
                                        </div>
                                        <div class="flex-col-form par-type-total-val">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[' . $staffingPar . ']'; ?>[total]" value="0" />
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <?php endforeach; ?>
                        <div class="staffing-type-row">   <!--  Max Table to Seat  -->
                            <div class="flex-body">
                                <div class="flex-body-right">
                                    <div class="flex-body-right-content">
                                        <!-- AM DATA -->
                                        <div class="flex-col-form">
                                            <span class="field_val" style="font-weight: bold;">
                                                Max Table to Seat
                                            </span>
                                        </div>
                                        <div class="flex-col-form" style="flex-grow: 10;">
                                            <span class="field_val">
                                                <input type="text" name="<?php echo $location . '[max_tables]'; ?>" value="0" />
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="flex-button-body">
                    <div class="flex-col-form border-none"><input type="submit" id="save_record" class="btn" name="xrg_staffing_pars_data_submit" value="SAVE" /></div>
                </div>
            </form>
        </div>

        <!--  Tabs Button  -->
        <div class="tabs-btn-wrapper">
            <button class="periods-tab entry-template-tabs active-tab" data-period-id="kpis_data_form" >KPIs Data</button>
            <button class="periods-tab entry-template-tabs" data-period-id="labor_data_form" >Labor Forecast Data</button>
            <button id="staffing_tab" class="periods-tab entry-template-tabs" data-period-id="staffing_pars_data_form" >Staffing Pars</button>
        </div>

    </div>
</div>
<div id="xrg_overlay" class="xrg-overlay">
    <div class="loader_container">
        <span>
            <?php echo XrgHelperFunctions::xrgSvgLoader(XRG_PLUGIN_PATH . 'assets/imgs/loader.svg'); ?>
            Please Wait...
        </span>
    </div>
</div>
<?php
get_footer();
