<?php

use XRG\RD\XrgRdKpis;
use XRG\RD\XrgHelperFunctions;

// Action to download File
if(isset($_GET['gen-sheet']) && $_GET['gen-sheet'] == 1) {
    XrgRdKpis::instance()->xrgLoadSpreadSheet()->xrgGenerateSpreadSheet('ASantana');
  //XrgRdKpis::instance()->xrgLoadSpreadSheet()->xrgWriteHtmlTable();
    $file =  XRG_PLUGIN_PATH . 'data/ASantana.xlsx';
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
    } else {
        echo 'file not found';
    }
}
// Get data from DB class
$sheetData = XrgRdKpis::instance()->xrgDBInstance()->xrgGetRegionalData( 'ASantana' );

get_header();

?>

<section>
    <div class="xrg-wrapper">
        <div class="notification"><?php echo isset($_GET['message']) ? 'Sheet has been emailed successfully!' : ''; ?></div>
        <!--  DOWNLOAD FILE LINK  -->
    <?php if($sheetData) : ?>
        <div class="btn-container button">
            <a href="?gen-sheet=1" class="btn">Download File</a>
        </div>
        <!--  Create Period's Tabs  -->
        <?php 
        foreach($sheetData as $sheetObj) : 
            $period = XrgHelperFunctions::xrgFormatArrayKeys($sheetObj->period_name); 
        ?>
        <div id="<?php echo $period; ?>" class="period-tab-content">
            <div class="xrg-table-container">
                <!-- Main Table  -->
                <table class="main-table-wrapper">
                    <tr>
                        <td class="table-containers kpi-table">
                            <table class="periods-sheet">
                                <?php 
                                $finalTotal = [];
                                $weeklyKpisData = unserialize($sheetObj->weekly_kpis_data);

                                $totalWeeks = count($weeklyKpisData);
                                // Create Sheet with Data
                                foreach($weeklyKpisData as $weeklyData) :
                                    $netSaleWTD = 0;
                                    $varBudgetSale = 0;
                                    $netProfit = 0;
                                    $varBudgetProfit = 0;
                                    $flowThru = 0;
                                    $tFoodVar = 0;
                                    $tLiquorVar = 0;
                                    $foodInv = 0;
                                    $liquorInv = 0;
                                    $tLaborWTD = 0;
                                    $trainingPay = 0;
                                    $trainingWeekly = 0;
                                    $difference = 0;

                                    $totalLocation = count($weeklyData['xrg_locations']);
                                    ?>
                                    <thead>
                                        <tr class="weekly-heading">
                                            <th colspan="14">KPI <?php echo $weeklyData['xrg_week']; ?></th>
                                        </tr>
                                        <tr class="header-bg">
                                            <th>RD</th>
                                            <th>Net Sales $ WTD</th>
                                            <th>Var - Budgeted $ Sales</th>
                                            <th>Net Profit $</th>
                                            <th>Var - Budgeted Net Profit</th>
                                            <th>Flow Thru %</th>
                                            <th>Theo Food Variance</th>
                                            <th>Theo Liquor Variance</th>
                                            <th>Ending Food Inventory</th>
                                            <th>Ending Liquor Inventory</th>
                                            <th>Theo Labor WTD</th>
                                            <th>Training Pay WTD</th>
                                            <th class="weekly-budget">Training Weekly $ Budget</th>
                                            <th>Difference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($weeklyData['xrg_locations'] as $location) : ?>
                                        <tr class="weekly-content">
                                            <td><?php echo $location; ?></td>
                                            <?php
                                            
                                            // Format location name to use as array keys
                                            $location = XrgHelperFunctions::xrgFormatArrayKeys($location); 
                                            
                                            // Fill array keys with default value
                                            if(!(isset($finalTotal[$location]))) {
                                            //  array_push($finalTotal, $location);
                                                $finalTotal[$location] = XrgHelperFunctions::xrgFillLocationKeys('kpis');
                                            }
                                
                                            //Temp Variables for calculations
                                            $netSaleTemp = is_numeric($weeklyData[$location]['net_sales_wtd']) ? $weeklyData[$location]['net_sales_wtd'] : 0;
                                            $netProfitTemp = is_numeric($weeklyData[$location]['net_profit']) ? $weeklyData[$location]['net_profit'] : 0;
                                            $bdgtProfitTemp = is_numeric($weeklyData[$location]['var_bgt_net_profit']) ? $weeklyData[$location]['var_bgt_net_profit'] : 0;
                                            $bdgtSaleTemp = is_numeric($weeklyData[$location]['var_bgt_sale']) ? $weeklyData[$location]['var_bgt_sale'] : 0;
                                            $tFoodVarTemp = is_numeric($weeklyData[$location]['theo_food_var']) ? $weeklyData[$location]['theo_food_var'] : 0;
                                            $tLiquorVarTemp = is_numeric($weeklyData[$location]['theo_liq_var']) ? $weeklyData[$location]['theo_liq_var'] : 0;
                                            $foodInvTemp = is_numeric($weeklyData[$location]['end_food_inv']) ? $weeklyData[$location]['end_food_inv'] : 0;
                                            $liquorInvTemp = is_numeric($weeklyData[$location]['end_liq_inv']) ? $weeklyData[$location]['end_liq_inv'] : 0;
                                            $tLaborWTDTemp = is_numeric($weeklyData[$location]['theo_labor_wtd']) ? $weeklyData[$location]['theo_labor_wtd'] : 0;
                                            $trainingPayTemp = is_numeric($weeklyData[$location]['training_pay_wtd']) ? $weeklyData[$location]['training_pay_wtd'] : 0;
                                            $trainingWeeklyTemp = is_numeric($weeklyData[$location]['training_weekly_bgt']) ? $weeklyData[$location]['training_weekly_bgt'] : 0;

                                            $netSaleWTD += $netSaleTemp;
                                            $varBudgetSale += $bdgtSaleTemp;
                                            $netProfit += $netProfitTemp;
                                            $varBudgetProfit += $bdgtProfitTemp;
                                            $tFoodVar += $tFoodVarTemp;
                                            $tLiquorVar += $tLiquorVarTemp;
                                            $foodInv += $foodInvTemp;
                                            $liquorInv += $liquorInvTemp;
                                            $tLaborWTD += $tLaborWTDTemp;
                                            $trainingPay += $trainingPayTemp;
                                            $trainingWeekly += $trainingWeeklyTemp;
                                            $difference += ($trainingPayTemp - $trainingWeeklyTemp);

                                            // Push to Final Total Array
                                            $finalTotal[$location]['net_sales_wtd'] += $netSaleTemp;
                                            $finalTotal[$location]['var_bgt_sale'] += $bdgtSaleTemp;
                                            $finalTotal[$location]['net_profit'] += $netProfitTemp;
                                            $finalTotal[$location]['var_bgt_net_profit'] += $bdgtProfitTemp;
                                            $finalTotal[$location]['theo_food_var'] += $tFoodVarTemp;
                                            $finalTotal[$location]['theo_liq_var'] += $tLiquorVarTemp;
                                            $finalTotal[$location]['end_food_inv'] +=$foodInvTemp;
                                            $finalTotal[$location]['end_liq_inv'] += $liquorInvTemp;
                                            $finalTotal[$location]['theo_labor_wtd'] += $tLaborWTDTemp;
                                            $finalTotal[$location]['training_pay_wtd'] += $trainingPayTemp;
                                            $finalTotal[$location]['training_weekly_bgt'] += $trainingWeeklyTemp;
                                            $finalTotal[$location]['difference'] += ($trainingPayTemp - $trainingWeeklyTemp);
                                            ?>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue($netSaleTemp, 'currency'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue($bdgtSaleTemp, 'currency'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue($netProfitTemp, 'currency'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue($bdgtProfitTemp, 'currency'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue(($bdgtSaleTemp > 0) ? ($bdgtProfitTemp / $bdgtSaleTemp * 100) : 'INF', 'percentage'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue($tFoodVarTemp, 'percentage'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue($tLiquorVarTemp, 'percentage'); ?></td>
                                            <td><?php echo $weeklyData[$location]['end_food_inv']; ?></td>
                                            <td><?php echo $weeklyData[$location]['end_liq_inv']; ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue($tLaborWTDTemp, 'percentage'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue($trainingPayTemp, 'currency'); ?></td>
                                            <td class="weekly-budget"><?php echo XrgHelperFunctions::xrgFormatValue($trainingWeeklyTemp, 'currency'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue(($trainingPayTemp - $trainingWeeklyTemp), 'currency'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <!--  TOTALs  -->
                                    <tr class="total-bg">
                                        <td>TOTAL</td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue($netSaleWTD, 'currency'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue($varBudgetSale, 'currency'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue($netProfit, 'currency'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue($varBudgetProfit, 'currency'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue(($varBudgetProfit / $varBudgetSale), 'percentage'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue(($tFoodVar / $totalLocation), 'percentage'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue(($tLiquorVar / $totalLocation), 'percentage'); ?></td>
                                        <td><?php echo $foodInv; ?></td>
                                        <td><?php echo $liquorInv; ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue(($tLaborWTD / $totalLocation), 'percentage'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue($trainingPay, 'currency'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue($trainingWeekly, 'currency'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue($difference, 'currency'); ?></td>
                                    </tr>
                                    <!--  Empty Row  -->
                                    <tr class="empty-row">
                                        <td colspan="14"></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <!--  Table for KPIs Till Date  -->
                            <table class="periods-sheet-till-date">
                                <thead>
                                    <tr class="weekly-heading">
                                        <th colspan="14">KPI Period to Date</th>
                                    </tr>
                                    <tr class="header-bg">
                                        <th>RD</th>
                                        <th>Net Sales $ WTD</th>
                                        <th>Var - Budgeted $ Sales</th>
                                        <th>Net Profit $</th>
                                        <th>Var - Budgeted Net Profit</th>
                                        <th>Flow Thru %</th>
                                        <th>Theo Food Variance</th>
                                        <th>Theo Liquor Variance</th>
                                        <th>Ending Food Inventory</th>
                                        <th>Ending Liquor Inventory</th>
                                        <th>Theo Labor WTD</th>
                                        <th>Training Pay WTD</th>
                                        <th class="weekly-budget">Training Weekly $ Budget</th>
                                        <th>Difference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $netSaleWTD = 0;
                                    $varBudgetSale = 0;
                                    $netProfit = 0;
                                    $varBudgetProfit = 0;
                                    $tFoodVar = 0;
                                    $tLiquorVar = 0;
                                    $foodInv = 0;
                                    $liquorInv = 0;
                                    $tLaborWTD = 0;
                                    $trainingPay = 0;
                                    $trainingWeekly = 0;
                                    $difference = 0;

                                    $totalLocation = count($finalTotal);
                                    foreach($finalTotal as $key => $locationData) : 
                                    
                                        $netSaleWTD += $locationData['net_sales_wtd'];
                                        $varBudgetSale += $locationData['var_bgt_sale'];
                                        $netProfit += $locationData['net_profit'];
                                        $varBudgetProfit += $locationData['var_bgt_net_profit'];
                                        $tFoodVar += ($locationData['theo_food_var'] / $totalWeeks);
                                        $tLiquorVar += ($locationData['theo_liq_var'] / $totalWeeks);
                                        $foodInv += $locationData['end_food_inv'];
                                        $liquorInv += $locationData['end_liq_inv'];
                                        $tLaborWTD += ($locationData['theo_labor_wtd'] / $totalWeeks);
                                        $trainingPay += $locationData['training_pay_wtd'];
                                        $trainingWeekly += $locationData['training_weekly_bgt'];
                                        $difference += $locationData['difference'];
                                    ?>
                                        <tr class="weekly-content">
                                            <td><?php echo $key; ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue($locationData['net_sales_wtd'], 'currency'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue($locationData['var_bgt_sale'], 'currency'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue($locationData['net_profit'], 'currency'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue($locationData['var_bgt_net_profit'], 'currency'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue(($locationData['var_bgt_net_profit'] / $locationData['var_bgt_sale']) * 100, 'percentage'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue(($locationData['theo_food_var'] / $totalWeeks), 'percentage'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue(($locationData['theo_liq_var'] / $totalWeeks), 'percentage'); ?></td>
                                            <td><?php echo $locationData['end_food_inv']; ?></td>
                                            <td><?php echo $locationData['end_liq_inv']; ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue(($locationData['theo_labor_wtd'] / $totalWeeks), 'percentage'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue($locationData['training_pay_wtd'], 'currency'); ?></td>
                                            <td class="weekly-budget"><?php echo XrgHelperFunctions::xrgFormatValue($locationData['training_weekly_bgt'], 'currency'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue($locationData['difference'], 'currency'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <!--  TOTALs  -->
                                    <tr class="total-bg">
                                        <td>TOTAL</td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue($netSaleWTD, 'currency'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue($varBudgetSale, 'currency'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue($netProfit, 'currency'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue($varBudgetProfit, 'currency'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue(($varBudgetProfit / $varBudgetSale) * 100 , 'percentage'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue(($tFoodVar / $totalLocation), 'percentage'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue(($tLiquorVar / $totalLocation), 'percentage'); ?></td>
                                        <td><?php echo $foodInv; ?></td>
                                        <td><?php echo $liquorInv; ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue(($tLaborWTD / $totalLocation), 'percentage'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue($trainingPay, 'currency'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue($trainingWeekly, 'currency'); ?></td>
                                        <td><?php echo XrgHelperFunctions::xrgFormatValue($difference, 'currency'); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="table-divider table-containers"></td>
                        <td class="table-containers labor-table">
                            <table class="periods-sheet">
                                <?php 
                                $weeklyLaborData = unserialize($sheetObj->weekly_labor_data);

                                if(!empty($weeklyLaborData)) :

                                    $totalWeeks = count($weeklyLaborData);
                                    // Create Sheet with Data
                                    foreach($weeklyLaborData as $laborData) :
                                        $forecastedSale = 0;
                                        $forecastedLabor = 0;
                                        $bdgtLabor = 0;
                                        $theoLabor = 0;
                                        $schedHours = 0;
                                        $bdgtHours = 0;
                                        $totalLocation = count($laborData['xrg_locations']);
                                        ?>
                                        <thead>
                                            <tr class="weekly-heading">
                                                <th colspan="9"><?php echo $laborData['xrg_week']; ?> Forecasted Labor</th>
                                            </tr>
                                            <tr class="header-bg">
                                                <th><?php echo $laborData['xrg_week']; ?></th>
                                                <th>Forecasted Sales</th>
                                                <th>Forecasted Labor %</th>
                                                <th>Budgeted Labor %</th>
                                                <th>Difference</th>
                                                <th>Projected Theoretical Labor</th>
                                                <th>Scheduled Shift Leader Hours</th>
                                                <th>Budgeted Shift Leader Hours</th>
                                                <th>Difference</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($laborData['xrg_locations'] as $location) : ?>
                                            <tr class="weekly-content">
                                                <td><?php echo $location; ?></td>
                                                <?php
                                                
                                                // Format location name to use as array keys
                                                $location = XrgHelperFunctions::xrgFormatArrayKeys($location);

                                                //Temp Variables for calculations  class_exists(XrgRdKpis::class) && XrgRdKpis::instance();
                                                $forecastedSaleTemp = is_numeric($laborData[$location]['forecasted_sales']) ? $laborData[$location]['forecasted_sales'] : 0;
                                                $forecastedLaborTemp = is_numeric($laborData[$location]['forecasted_labor']) ? $laborData[$location]['forecasted_labor'] : 0;
                                                $bdgtLaborTemp = is_numeric($laborData[$location]['budgeted_labor']) ? $laborData[$location]['budgeted_labor'] : 0;
                                                $differenceForecastTemp = $forecastedLaborTemp - $bdgtLaborTemp;
                                                $theoLaborTemp = is_numeric($laborData[$location]['theo_labor']) ? $laborData[$location]['theo_labor'] : 0;
                                                $schedHoursTemp = is_numeric($laborData[$location]['scheduled_leader_hours']) ? $laborData[$location]['scheduled_leader_hours'] : 0;
                                                $bdgtHoursTemp = is_numeric($laborData[$location]['budgeted_leader_hours']) ? $laborData[$location]['budgeted_leader_hours'] : 0;
                                                $differenceHoursTemp = $schedHoursTemp - $bdgtHoursTemp;

                                                $forecastedSale += $forecastedSaleTemp;
                                                $forecastedLabor += $forecastedLaborTemp;
                                                $bdgtLabor += $bdgtLaborTemp;
                                                $theoLabor += $theoLaborTemp;
                                                $schedHours += $schedHoursTemp;
                                                $bdgtHours += $bdgtHoursTemp;
                                                ?>
                                                <td><?php echo XrgHelperFunctions::xrgFormatValue($forecastedSaleTemp, 'currency'); ?></td>
                                                <td><?php echo XrgHelperFunctions::xrgFormatValue($forecastedLaborTemp, 'percentage'); ?></td>
                                                <td><?php echo XrgHelperFunctions::xrgFormatValue($bdgtLaborTemp, 'percentage'); ?></td>
                                                <td><?php echo XrgHelperFunctions::xrgFormatValue($differenceForecastTemp, 'percentage'); ?></td>
                                                <td><?php echo XrgHelperFunctions::xrgFormatValue($theoLaborTemp, 'percentage'); ?></td>
                                                <td><?php echo $schedHoursTemp; ?></td>
                                                <td><?php echo $bdgtHoursTemp; ?></td>
                                                <td><?php echo XrgHelperFunctions::xrgFormatValue($differenceHoursTemp, 'abs-numeric'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <!--  TOTALs  -->
                                        <tr class="total-bg">
                                            <td>TOTAL</td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue($forecastedSale, 'currency'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue(($forecastedLabor / $totalLocation), 'percentage'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue(($bdgtLabor / $totalLocation), 'percentage'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue(($bdgtLabor / $totalLocation) - ($forecastedLabor / $totalLocation), 'percentage'); ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue(($theoLabor / $totalLocation), 'percentage'); ?></td>
                                            <td><?php echo $schedHours; ?></td>
                                            <td><?php echo $bdgtHours; ?></td>
                                            <td><?php echo XrgHelperFunctions::xrgFormatValue(($schedHours - $bdgtHours), 'abs-numeric'); ?></td>
                                        </tr>
                                        <!--  Empty Row  -->
                                        <tr class="empty-row">
                                            <td colspan="9"></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                <?php endif; ?>
                            </table>
                        </td>
                    <tr>
                </table>
            </div>
        </div>
        <?php endforeach; ?>
            <!--  Tabs Button  -->
            <div class="tabs-btn-wrapper">
            <?php
                foreach($sheetData as $sheetObj) : 
                    $period = XrgHelperFunctions::xrgFormatArrayKeys($sheetObj->period_name); 
            ?>
                <button class="periods-tab view-template-tabs" data-period-id="<?php echo $period; ?>" ><?php echo $sheetObj->period_name; ?></button>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="no-result">No Report Found At The Moment!</div>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
