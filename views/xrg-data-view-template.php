<?php

use XRG\RD\XrgRdKpis;
use XRG\RD\XrgHelperFunctions;

// Action to download File
if(isset($_GET['gen-sheet']) && $_GET['gen-sheet'] == 1) {
    XrgRdKpis::instance()->xrgLoadSpreadSheet()->xrgGenerateSpreadSheet('ASantana');
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
    <?php if($sheetData) : ?>
        <div class="btn-container">
            <a href="?gen-sheet=1" class="btn">Download File</a>
        </div>
        <div class="xrg-table-container">
            <table class="periods-sheet">
                <?php 

               /*  foreach($sheetData as $sheetObj) {
                    // cell's data
                    $cellIndex = 1;
                    $contentStartIndex = 3;*/
                    $finalTotal = [];
                    $weeklyKpisData = unserialize($sheetData[0]->weekly_kpis_data);
        
                  /*  if($sheetObj->period_name === 'Period 1') {
                        $spreadsheet->addSheet($currentSheet, 0);
                    } else {
                        $spreadsheet->addSheet($currentSheet);
                    }
                */
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
                                <th></th>
                                <th colspan="14"><?php echo $weeklyData['xrg_week']; ?></th>
                            </tr>
                            <tr class="header-bg">
                                <th></th>
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
                                <td></td>
                                <td><?php echo $location; ?></td>
                                <?php
                                // Format location name to use as array keys
                                $location = XrgHelperFunctions::xrgFormatArrayKeys($location); 
                    
                                $netSaleWTD += $weeklyData[$location]['net_sales_wtd'];
                                $varBudgetSale += $weeklyData[$location]['var_bgt_sale'];
                                $netProfit += $weeklyData[$location]['net_profit'];
                                $varBudgetProfit += $weeklyData[$location]['var_bgt_net_profit'];
                                $flowThru += ($weeklyData[$location]['var_bgt_net_profit'] / $weeklyData[$location]['var_bgt_sale'] );
                                $tFoodVar += $weeklyData[$location]['theo_food_var'];
                                $tLiquorVar += $weeklyData[$location]['theo_liq_var'];
                                $foodInv += $weeklyData[$location]['end_food_inv'];
                                $liquorInv += $weeklyData[$location]['end_liq_inv'];
                                $tLaborWTD += $weeklyData[$location]['theo_labor_wtd'];
                                $trainingPay += $weeklyData[$location]['training_pay_wtd'];
                                $trainingWeekly += $weeklyData[$location]['training_weekly_bgt'];
                                $difference += ($weeklyData[$location]['training_pay_wtd'] - $weeklyData[$location]['training_weekly_bgt']);

                                // Push to Final Total Array
                                $finalTotal[$location]['net_sales_wtd'] += $weeklyData[$location]['net_sales_wtd'];
                                $finalTotal[$location]['var_bgt_sale'] += $weeklyData[$location]['var_bgt_sale'];
                                $finalTotal[$location]['net_profit'] += $weeklyData[$location]['net_profit'];
                                $finalTotal[$location]['var_bgt_net_profit'] += $weeklyData[$location]['var_bgt_net_profit'];
                                $finalTotal[$location]['theo_food_var'] += $weeklyData[$location]['theo_food_var'];
                                $finalTotal[$location]['theo_liq_var'] += $weeklyData[$location]['theo_liq_var'];
                                $finalTotal[$location]['end_food_inv'] += $weeklyData[$location]['end_food_inv'];
                                $finalTotal[$location]['end_liq_inv'] += $weeklyData[$location]['end_liq_inv'];
                                $finalTotal[$location]['theo_labor_wtd'] += $weeklyData[$location]['theo_labor_wtd'];
                                $finalTotal[$location]['training_pay_wtd'] += $weeklyData[$location]['training_pay_wtd'];
                                $finalTotal[$location]['training_weekly_bgt'] += $weeklyData[$location]['training_weekly_bgt'];
                                $finalTotal[$location]['difference'] += ($weeklyData[$location]['training_pay_wtd'] - $weeklyData[$location]['training_weekly_bgt']);
                                ?>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($weeklyData[$location]['net_sales_wtd'], 'currency'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($weeklyData[$location]['var_bgt_sale'], 'currency'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($weeklyData[$location]['net_profit'], 'currency'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($weeklyData[$location]['var_bgt_net_profit'], 'currency'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue(($weeklyData[$location]['var_bgt_net_profit'] / $weeklyData[$location]['var_bgt_sale'] * 100), 'percentage'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($weeklyData[$location]['theo_food_var'], 'percentage'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($weeklyData[$location]['theo_liq_var'], 'percentage'); ?></td>
                                <td><?php echo $weeklyData[$location]['end_food_inv']; ?></td>
                                <td><?php echo $weeklyData[$location]['end_liq_inv']; ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($weeklyData[$location]['theo_labor_wtd'], 'percentage'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($weeklyData[$location]['training_pay_wtd'], 'currency'); ?></td>
                                <td class="weekly-budget"><?php echo XrgHelperFunctions::xrgFormatValue($weeklyData[$location]['training_weekly_bgt'], 'currency'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue(($weeklyData[$location]['training_pay_wtd'] - $weeklyData[$location]['training_weekly_bgt']), 'currency'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <!--  TOTALs  -->
                        <tr class="total-bg">
                            <td class=""></td>
                            <td>TOTAL</td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($netSaleWTD, 'currency'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($varBudgetSale, 'currency'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($netProfit, 'currency'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($varBudgetProfit, 'currency'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($flowThru, 'percentage'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue(($tFoodVar/$totalLocation), 'percentage'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($tLiquorVar, 'percentage'); ?></td>
                            <td><?php echo $foodInv; ?></td>
                            <td><?php echo $liquorInv; ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($tLaborWTD, 'percentage'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($trainingPay, 'currency'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($trainingWeekly, 'currency'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($difference, 'currency'); ?></td>
                        </tr>
                        <!--  Empty Row  -->
                        <tr class="empty-row">
                            <td></td>
                            <td colspan="14"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!--  Table for KPIs Till Date  -->
            <table class="periods-sheet-till-date">
                <thead>
                    <tr class="weekly-heading">
                        <th></th>
                        <th colspan="14">KPI Period to Date</th>
                    </tr>
                    <tr class="header-bg">
                        <th></th>
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
                            $tFoodVar += $locationData['theo_food_var'];
                            $tLiquorVar += $locationData['theo_liq_var'];
                            $foodInv += $locationData['end_food_inv'];
                            $liquorInv += $locationData['end_liq_inv'];
                            $tLaborWTD += $locationData['theo_labor_wtd'];
                            $trainingPay += $locationData['training_pay_wtd'];
                            $trainingWeekly += $locationData['training_weekly_bgt'];
                            $difference += $locationData['difference'];
                        ?>
                            <tr class="weekly-content">
                                <td></td>
                                <td><?php echo $key; ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($locationData['net_sales_wtd'], 'currency'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($locationData['var_bgt_sale'], 'currency'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($locationData['net_profit'], 'currency'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($locationData['var_bgt_net_profit'], 'currency'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue(($locationData['var_bgt_net_profit'] / $locationData['var_bgt_sale']) * 100, 'percentage'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($locationData['theo_food_var'], 'percentage'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($locationData['theo_liq_var'], 'percentage'); ?></td>
                                <td><?php echo $locationData['end_food_inv']; ?></td>
                                <td><?php echo $locationData['end_liq_inv']; ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($locationData['theo_labor_wtd'], 'percentage'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($locationData['training_pay_wtd'], 'currency'); ?></td>
                                <td class="weekly-budget"><?php echo XrgHelperFunctions::xrgFormatValue($locationData['training_weekly_bgt'], 'currency'); ?></td>
                                <td><?php echo XrgHelperFunctions::xrgFormatValue($locationData['difference'], 'currency'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <!--  TOTALs  -->
                        <tr class="total-bg">
                            <td class=""></td>
                            <td>TOTAL</td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($netSaleWTD, 'currency'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($varBudgetSale, 'currency'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($netProfit, 'currency'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($varBudgetProfit, 'currency'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue(($varBudgetProfit / $varBudgetSale) * 100 , 'percentage'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue(($tFoodVar/$totalLocation), 'percentage'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($tLiquorVar, 'percentage'); ?></td>
                            <td><?php echo $foodInv; ?></td>
                            <td><?php echo $liquorInv; ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($tLaborWTD, 'percentage'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($trainingPay, 'currency'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($trainingWeekly, 'currency'); ?></td>
                            <td><?php echo XrgHelperFunctions::xrgFormatValue($difference, 'currency'); ?></td>
                        </tr>
                        <!--  Empty Row  -->
                        <tr class="empty-row">
                            <td></td>
                            <td colspan="14"></td>
                        </tr>
                </tbody>
            </table>
        </div>
      
        <?php else : ?>
            <div class="no-result">No Report Found At The Moment!</div>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
