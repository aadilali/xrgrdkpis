<?php

declare(strict_types=1);

// -*- coding: utf-8 -*-

namespace XRG\RD;

use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use XRG\RD\XrgHelperFunctions;

/**
 * Class XrgKpisReadSheet
 *
 * Responsible for reading and output excel sheets.
 *
 * @package XRG\RD
 */


class XrgKpisReadSheet
{
    /**
     * @var string
     */
    private $xrgSpreadSheet;

    /**
     * @var string
     */
   // private $xrgTemplateName;
    
    /**
     * Initialize the class, set its properties and register callbacks against hooks.
     * @since    0.1
     */
    public function __construct()
    {

    }

    /**
     * Load spreadsheet having Original and Lookup sheets from file to memory
     *
     * @since    0.1
     * @access   public
     * @return    Spreadsheet
     */
    public function xrgLoadSheet(): Spreadsheet
    {
        $sheetFile = XRG_PLUGIN_PATH . 'original-file/xrg-original-sheet-data.xlsx';
        $xrgReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($sheetFile);
        $xrgReader->setReadDataOnly(true);
        return $xrgReader->load($sheetFile);
    }

    public function xrgWriteHtmlTable(): void
    {
        $spsheet = $this->xrgLoadSheet();
       // $spsheet->setActiveSheetIndexByName('Lookup');
        
        $worksheet = $spsheet->getSheet(1);

        echo '<table>' . PHP_EOL;
        foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
            echo '<tr>' . PHP_EOL;
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
                                                            //    even if a cell value is not set.
                                                            // For 'TRUE', we loop through cells
                                                            //    only when their value is set.
                                                            // If this method is not called,
                                                            //    the default value is 'false'.
            foreach ($cellIterator as $key => $cell) {
                echo '<td>' .
                $rowIndex.$key. ' Key => val ' .$cell->getValue() .
                    '</td>' . PHP_EOL;
            }
            echo '</tr>' . PHP_EOL;
        }
        echo '</table>' . PHP_EOL;

        $spsheet->disconnectWorksheets();
        unset($spsheet);
       /* $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spsheet);
        $writer->setSheetIndex($spsheet->getActiveSheetIndex()); 
        $hdr = $writer->generateHTMLHeader();
        $sty = $writer->generateStyles(false); // do not write <style> and </style>
        $newstyle = <<<EOF
        <style type='text/css'>
        $sty
        body {
            background-color: yellow;
        }
        </style>
        EOF;
        echo preg_replace('@</head>@', "$newstyle\n</head>", $hdr);
        echo $writer->generateSheetData();
        echo $writer->generateHTMLFooter();*/
    }

    /**
     * Create excel file from form data. Form data stored to DB as well
     *
     * @since    0.1
     * @access   public
     * @param   string $regionName Region name against a spreadsheet will be generated
     * @return    void
     */
    public function xrgGenerateSpreadSheet(string $regionName): void
    {
        // Get data from DB class
        $sheetObjs = XrgRdKpis::instance()->xrgDBInstance()->xrgGetRegionalData( $regionName );
        // Get data from DB class
        $stafffingObjs = XrgRdKpis::instance()->xrgDBInstance()->xrgStaffingParsData( $regionName );
        $stafffingObjs = unserialize($stafffingObjs->staffing_data);
        
        $spreadsheet = new Spreadsheet();

        // Generic Style Array
        $genericStyle = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'ffd9d9d9')
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ]
            ]
        ];

        $dottedStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                    'color' => ['argb' => '00000000'],
                ]
            ]
        ];
    
        // Remove Default Worksheet from file
        $spreadsheet->removeSheetByIndex(0);

        foreach($sheetObjs as $sheetObj) {
            // cell's data
            $cellIndex = 1;
            $contentStartIndex = 3;
            $cellLabor = 1;
            $contentStartIndexLabor = 3;
            $finalTotal = [];
            
            $weeklyKpisData = unserialize($sheetObj->weekly_kpis_data);

            $currentSheet = new Worksheet($spreadsheet, $sheetObj->period_name);
            if($sheetObj->period_name === 'Period 1') {
                $spreadsheet->addSheet($currentSheet, 0);
            } else {
                $spreadsheet->addSheet($currentSheet);
            }

            // Create Sheet with Data
            foreach($weeklyKpisData as $keyName => $weeklyData) {
                $currentSheet->mergeCells("B$cellIndex:V$cellIndex");
                $currentSheet = $this->xrgSetSheetKPIHead($currentSheet, $weeklyData['xrg_week'], $cellIndex);
                $cellIndex++;

                foreach($weeklyData['xrg_locations'] as $location) {
                    $cellIndex++;
                    $currentSheet->setCellValue("B$cellIndex", $location);

                    // Format location name to use as array keys
                    $location = XrgHelperFunctions::xrgFormatArrayKeys($location);

                    $currentSheet->setCellValue("C$cellIndex", $weeklyData[$location]['net_sales_wtd']);
                    $finalTotal[$location]['net_sales_wtd'][] =  "C$cellIndex";
                    $currentSheet->setCellValue("D$cellIndex", $weeklyData[$location]['var_bgt_sale']);
                    $finalTotal[$location]['var_bgt_sale'][] =  "D$cellIndex";
                    $currentSheet->setCellValue("E$cellIndex", $weeklyData[$location]['net_profit']);
                    $finalTotal[$location]['net_profit'][] =  "E$cellIndex";
                    $currentSheet->setCellValue("F$cellIndex", $weeklyData[$location]['var_bgt_net_profit']);
                    $finalTotal[$location]['var_bgt_net_profit'][] =  "F$cellIndex";
        
                    $currentSheet->setCellValue("G$cellIndex", "=(F$cellIndex / D$cellIndex)");  // =F3/D3
        
                    $currentSheet->setCellValue("K$cellIndex", ($weeklyData[$location]['theo_food_var'] / 100 ));
                    $finalTotal[$location]['theo_food_var'][] =  "K$cellIndex";
                    $currentSheet->setCellValue("L$cellIndex", ($weeklyData[$location]['theo_liq_var'] / 100));
                    $finalTotal[$location]['theo_liq_var'][] =  "L$cellIndex";
                    $currentSheet->setCellValue("M$cellIndex", $weeklyData[$location]['end_food_inv']);
                    $finalTotal[$location]['end_food_inv'][] =  "M$cellIndex";
                    $currentSheet->setCellValue("N$cellIndex", $weeklyData[$location]['end_liq_inv']);
                    $finalTotal[$location]['end_liq_inv'][] =  "N$cellIndex";
                    $currentSheet->setCellValue("O$cellIndex", ($weeklyData[$location]['theo_labor_wtd'] / 100));
                    $finalTotal[$location]['theo_labor_wtd'][] =  "O$cellIndex";
                    $currentSheet->setCellValue("Q$cellIndex", $weeklyData[$location]['training_pay_wtd']);
                    $finalTotal[$location]['training_pay_wtd'][] =  "Q$cellIndex";
                    $currentSheet->setCellValue("R$cellIndex", $weeklyData[$location]['training_weekly_bgt']);
                    $finalTotal[$location]['training_weekly_bgt'][] =  "R$cellIndex";
                
                    $currentSheet->setCellValue("S$cellIndex", "=(Q$cellIndex - R$cellIndex)");  // =Q3-R3
                }
                
                // Totals 
                $contentLastIndex = $cellIndex;
                $cellIndex ++;
                $currentSheet->setCellValue("B$cellIndex", 'Total');
                $currentSheet->setCellValue("C$cellIndex", "=SUM(C$contentStartIndex:C$contentLastIndex)");  //=SUM(C3:C11)
                $currentSheet->setCellValue("D$cellIndex", "=SUM(D$contentStartIndex:D$contentLastIndex)");  //=SUM(D3:D11)
                $currentSheet->setCellValue("E$cellIndex", "=SUM(E$contentStartIndex:E$contentLastIndex)");  //=SUM(E3:E11)
                $currentSheet->setCellValue("F$cellIndex", "=SUM(F$contentStartIndex:F$contentLastIndex)");  //=SUM(F3:F11)
        
                $currentSheet->setCellValue("G$cellIndex", "=(F$cellIndex / D$cellIndex)");  // =F12/D12
        
                $currentSheet->setCellValue("K$cellIndex", "=AVERAGE(K$contentStartIndex:K$contentLastIndex)"); // =AVERAGE(K3:K11)
                $currentSheet->setCellValue("L$cellIndex", "=AVERAGE(L$contentStartIndex:L$contentLastIndex)");  // =AVERAGE(L3:L11)
                $currentSheet->setCellValue("M$cellIndex", '');
                $currentSheet->setCellValue("N$cellIndex", '');
                $currentSheet->setCellValue("O$cellIndex", "=AVERAGE(O$contentStartIndex:O$contentLastIndex)");  // =AVERAGE(O3:O11)
                $currentSheet->setCellValue("Q$cellIndex", "=SUM(Q$contentStartIndex:Q$contentLastIndex)");  // =SUM(Q3:Q11)
                $currentSheet->setCellValue("R$cellIndex", "=SUM(R$contentStartIndex:R$contentLastIndex)");  // =SUM(R3:R11)
                
                $currentSheet->setCellValue("S$cellIndex", "=SUM(S$contentStartIndex:S$contentLastIndex)");  // =SUM(S3:S11)
        
                $currentSheet->getStyle("B$cellIndex:S$cellIndex")->applyFromArray($genericStyle);
                $currentSheet->getStyle("B$contentStartIndex:S$contentLastIndex")->applyFromArray($dottedStyle);

                $cellIndex++;
                $currentSheet->mergeCells("B$cellIndex:V$cellIndex");
                $cellIndex++;
                $contentStartIndex = $cellIndex + 2;
            }

           // Create KPIs Till Date Section
            $cellIndex += 2;
            $currentSheet->mergeCells("B$cellIndex:V$cellIndex");
            $currentSheet = $this->xrgSetSheetKPIHead($currentSheet, 'KPI Period to Date', $cellIndex);

            // cell's data
            $cellIndex++;
            $contentStartIndex = $cellIndex + 1;
            
            foreach($weeklyData['xrg_locations'] as $location) {
                $cellIndex++;
                $currentSheet->setCellValue("B$cellIndex", $location);

                // Format location name to use as array keys
                $location = XrgHelperFunctions::xrgFormatArrayKeys($location);

                $currentSheet->setCellValue("C$cellIndex", "=SUM(".implode(',', $finalTotal[$location]['net_sales_wtd']).")");
                $currentSheet->setCellValue("D$cellIndex", "=SUM(".implode(',', $finalTotal[$location]['var_bgt_sale']).")");
                $currentSheet->setCellValue("E$cellIndex", "=SUM(".implode(',', $finalTotal[$location]['net_profit']).")");
                $currentSheet->setCellValue("F$cellIndex", "=SUM(".implode(',', $finalTotal[$location]['var_bgt_net_profit']).")");

                $currentSheet->setCellValue("G$cellIndex", "=(F$cellIndex / D$cellIndex)");  // =F3/D3

                $currentSheet->setCellValue("K$cellIndex", "=AVERAGE(".implode(',', $finalTotal[$location]['theo_food_var']).")");
                $currentSheet->setCellValue("L$cellIndex", "=AVERAGE(".implode(',', $finalTotal[$location]['theo_liq_var']).")");

                $currentSheet->setCellValue("M$cellIndex", "=SUM(".implode(',', $finalTotal[$location]['end_food_inv']).")");
                $currentSheet->setCellValue("N$cellIndex", "=SUM(".implode(',', $finalTotal[$location]['end_liq_inv']).")");

                $currentSheet->setCellValue("O$cellIndex", "=AVERAGE(".implode(',', $finalTotal[$location]['theo_labor_wtd']).")");
                $currentSheet->setCellValue("Q$cellIndex", "=SUM(".implode(',', $finalTotal[$location]['training_pay_wtd']).")");
                $currentSheet->setCellValue("R$cellIndex", "=SUM(".implode(',', $finalTotal[$location]['training_weekly_bgt']).")");
            
                $currentSheet->setCellValue("S$cellIndex", "=(Q$cellIndex - R$cellIndex)");  //=Q3-R3
            }

            // Totals 
            $contentLastIndex = $cellIndex;
            $cellIndex += 1;
            $currentSheet->setCellValue("B$cellIndex", 'Total');
            $currentSheet->setCellValue("C$cellIndex", "=SUM(C$contentStartIndex:C$contentLastIndex)");  //=SUM(C3:C11)
            $currentSheet->setCellValue("D$cellIndex", "=SUM(D$contentStartIndex:D$contentLastIndex)");  //=SUM(D3:D11)
            $currentSheet->setCellValue("E$cellIndex", "=SUM(E$contentStartIndex:E$contentLastIndex)");  //=SUM(E3:E11)
            $currentSheet->setCellValue("F$cellIndex", "=SUM(F$contentStartIndex:F$contentLastIndex)");  //=SUM(F3:F11)
            $currentSheet->setCellValue("G$cellIndex", "=(F$cellIndex / D$cellIndex)");  // =F12/D12
            $currentSheet->setCellValue("K$cellIndex", "=AVERAGE(K$contentStartIndex:K$contentLastIndex)"); // =AVERAGE(K3:K11)
            $currentSheet->setCellValue("L$cellIndex", "=AVERAGE(L$contentStartIndex:L$contentLastIndex)");  // =AVERAGE(L3:L11)
            $currentSheet->setCellValue("M$cellIndex", '');
            $currentSheet->setCellValue("N$cellIndex", '');
            $currentSheet->setCellValue("O$cellIndex", "=AVERAGE(O$contentStartIndex:O$contentLastIndex)");  // =AVERAGE(O3:O11)
            $currentSheet->setCellValue("Q$cellIndex", "=SUM(Q$contentStartIndex:Q$contentLastIndex)");  // =SUM(Q3:Q11)
            $currentSheet->setCellValue("R$cellIndex", "=SUM(R$contentStartIndex:R$contentLastIndex)");  // =SUM(R3:R11)
            $currentSheet->setCellValue("S$cellIndex", "=SUM(S$contentStartIndex:S$contentLastIndex)");  // =SUM(S3:S11)

            $currentSheet->getStyle("B$contentStartIndex:S$contentLastIndex")->applyFromArray($dottedStyle);
            $currentSheet->getStyle("B$cellIndex:S$cellIndex")->applyFromArray($genericStyle);

            // Set Labor Data
            if(!empty($sheetObj->weekly_labor_data)) {
                $weeklyLaborData = unserialize($sheetObj->weekly_labor_data);
                foreach($weeklyLaborData as $keyName => $weeklyLaborData) {
                    $currentSheet->mergeCells("X$cellLabor:AH$cellLabor");
                    $currentSheet = $this->xrgSetSheetLaborHead($currentSheet, $weeklyLaborData['xrg_week'], $cellLabor);
                    $cellLabor++;

                    foreach($weeklyLaborData['xrg_locations'] as $laborLocation) {
                        $cellLabor++;
                        $currentSheet->setCellValue("X$cellLabor", $laborLocation);

                        // Format location name to use as array keys
                        $laborLocation = XrgHelperFunctions::xrgFormatArrayKeys($laborLocation);

                        $currentSheet->setCellValue("Y$cellLabor", $weeklyLaborData[$laborLocation]['forecasted_sales']);
                        $currentSheet->setCellValue("AB$cellLabor", ($weeklyLaborData[$laborLocation]['forecasted_labor'] / 100));
                        $currentSheet->setCellValue("AC$cellLabor", ($weeklyLaborData[$laborLocation]['budgeted_labor'] / 100));
                        $currentSheet->setCellValue("AD$cellLabor", "=(AB$cellLabor - AC$cellLabor)");  //=AB3-AC3        
                        $currentSheet->setCellValue("AE$cellLabor", ($weeklyLaborData[$laborLocation]['theo_labor'] / 100));
                        $currentSheet->setCellValue("AF$cellLabor", $weeklyLaborData[$laborLocation]['scheduled_leader_hours']);
                        $currentSheet->setCellValue("AG$cellLabor", $weeklyLaborData[$laborLocation]['budgeted_leader_hours']);
                        $currentSheet->setCellValue("AH$cellLabor", "=(AF$cellLabor - AG$cellLabor)");  // =AF3-AG3
                    }
                    
                    // Totals 
                    $contentLastIndexLabor = $cellLabor;
                    $cellLabor++;
                    $currentSheet->setCellValue("X$cellLabor", 'Total');
                    $currentSheet->setCellValue("Y$cellLabor", "=SUM(Y$contentStartIndexLabor:Y$contentLastIndexLabor)");  //=SUM
                    $currentSheet->setCellValue("AB$cellLabor", "=AVERAGE(AB$contentStartIndexLabor:AB$contentLastIndexLabor)");  //=AVERAGE
                    $currentSheet->setCellValue("AC$cellLabor", "=AVERAGE(AC$contentStartIndexLabor:AC$contentLastIndexLabor)");  //=AVERAGE
                    $currentSheet->setCellValue("AD$cellLabor", "=(AC$cellLabor - AB$cellLabor)");  //=AC12-AB12
                    $currentSheet->setCellValue("AE$cellLabor", "=AVERAGE(AE$contentStartIndexLabor:AE$contentLastIndexLabor)");  // =AVERAGE
                    $currentSheet->setCellValue("AF$cellLabor", "=SUM(AF$contentStartIndexLabor:AF$contentLastIndexLabor)"); // =Sum
                    $currentSheet->setCellValue("AG$cellLabor", "=SUM(AG$contentStartIndexLabor:AG$contentLastIndexLabor)");  // =Sum
                    $currentSheet->setCellValue("AH$cellLabor", "=(AF$cellLabor - AG$cellLabor)");   // =AF12-AG12
            
                    $currentSheet->getStyle("X$cellLabor:AH$cellLabor")->applyFromArray($genericStyle);
                    $currentSheet->getStyle("X$contentStartIndexLabor:AH$contentLastIndexLabor")->applyFromArray($dottedStyle);

                    $cellLabor++;
                    $currentSheet->mergeCells("X$cellLabor:AH$cellLabor");
                    $cellLabor++;
                    $contentStartIndexLabor = $cellLabor + 2;
                }
            }
            // Set active worksheet and write to file
            $spreadsheet->setActiveSheetIndexByName($sheetObj->period_name);
        }

        // Create Staffing Data worksheets
        foreach($stafffingObjs['xrg_locations'] as $staffObj) {
            $currentStafftSheet = new Worksheet($spreadsheet, $staffObj);
            $currentStafftSheet->getTabColor()->setRGB('92d050');
            $spreadsheet->addSheet($currentStafftSheet);
            $spreadsheet->setActiveSheetIndexByName($staffObj);
            $this->xrgStaffingParsTotalSheets($staffObj, $stafffingObjs, $currentStafftSheet);
        }

        // Regional Staffing Pars Total
        $activeIndex = $spreadsheet->getActiveSheetIndex();
        $locationIndex = count($stafffingObjs['xrg_locations']) - 1;

        $currentStafftSheet = new Worksheet($spreadsheet, $regionName);
        $currentStafftSheet->getTabColor()->setRGB('2f8232');
        $spreadsheet->addSheet($currentStafftSheet, ($activeIndex - $locationIndex));
        $spreadsheet->setActiveSheetIndexByName($staffObj);
        $this->xrgStaffingTotalRegionSheet($regionName, $stafffingObjs, $currentStafftSheet);


        // Embed Orignal File work sheets
        $originalFile = $this->xrgLoadSheet();

        $activeIndex = $spreadsheet->getActiveSheetIndex();

        $clonedLookup = clone $originalFile->getSheet(1);
        $clonedLookup->getTabColor()->setRGB('ffbf00');
        $spreadsheet->addExternalSheet($clonedLookup);

        $clonedOriginal = clone $originalFile->getSheet(0);
        $clonedOriginal->getTabColor()->setRGB('00b0f0');
        $spreadsheet->addExternalSheet($clonedOriginal);

        $originalFile->disconnectWorksheets();
        unset($originalFile);
        
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->save( XRG_PLUGIN_PATH . 'data/ASantana.xlsx' );

        // Clear memory
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        

    }

    /**
     * Set sheet headings, format and style for KPIs data
     *
     * @since    0.1
     * @access   public
     * @param   Worksheet $sheet current active sheet in memory
     * @param   string $weekTitle week title
     * @param   int $cellIndex cell index
     * @return    Worksheet
     */
    public function xrgSetSheetKPIHead(Worksheet $sheet, string $weekTitle, int $cellIndex): Worksheet
    {
        $headingIndex = $cellIndex + 1;
        
        $headingArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ]
        ];

        $bgColor = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'ffd9d9d9')
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ]
            ]
        ];

        $genericStyle = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'ffbfbfbf')
            ]
        ];
        
        $sheet->setCellValue("B$cellIndex", 'KPI ' . $weekTitle);
        $sheet->getStyle("B$cellIndex")->getFont()->setBold(true);
        $sheet->getStyle("B$cellIndex")->getFont()->setSize('16');

        $sheet->getStyle('R')->applyFromArray($genericStyle);
        $sheet->getStyle('W')->applyFromArray($genericStyle);

        $sheet->getStyle("A$cellIndex:V$headingIndex")->applyFromArray($headingArray);
        $sheet->getStyle("B$headingIndex:V$headingIndex")->applyFromArray($bgColor);
        $sheet->getStyle('A2:V2')->getFont()->setSize(11);
        
        foreach (range('B','Z') as $col) {
            $sheet->getColumnDimension($col)->setWidth(18, 'px');
        }
        
        $sheet->getColumnDimension('A')->setWidth(4, 'px');

        $arrayData = [
            ['RD', 'Net Sales $ WTD', 'Var - Budgeted $ Sales', 'Net Profit $', 'Var - Budgeted Net Profit',
             'Flow Thru %' , 'LBWs vs PY Quantity', 'Guest Sat', 'Mystery Shopper', 'Theo Food Variance', 'Theo Liquor Variance',
             'Ending Food Inventory', 'Ending Liquor Inventory', 'Theo Labor WTD', 'Shift Leader Actual to Budget', 'Training Pay WTD',
             'Training Weekly $ Budget', 'Difference', '# Break Violation', '# of OT Hours', 'Total Premium Pay'
            ]
        ];

        $sheet->fromArray(
                $arrayData,  // The data to set
                NULL,        // Array values with this value will not be set
                "B$headingIndex"         // Top left coordinate of the worksheet range where
        );

        // Hide empty columns 
        $sheet->getColumnDimension('H')->setVisible(false);
        $sheet->getColumnDimension('I')->setVisible(false);
        $sheet->getColumnDimension('J')->setVisible(false);
        $sheet->getColumnDimension('P')->setVisible(false);
        $sheet->getColumnDimension('T')->setVisible(false);
        $sheet->getColumnDimension('U')->setVisible(false);
        $sheet->getColumnDimension('V')->setVisible(false);

        // Format Cells accoring to there heads
        $sheet->getStyle('C:F')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $sheet->getStyle('G')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('Q:R')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $sheet->getStyle('K:L')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('O')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('S')->getNumberFormat()->setFormatCode('$#,##0.00_);[Red]($#,##0.00)');

        return $sheet;
    }

    /**
     * Set sheet headings, format and style for labor forecast data
     *
     * @since    0.1
     * @access   public
     * @param   Worksheet $sheet current active sheet in memory
     * @param   string $weekTitle week title
     * @param   int $cellIndex cell index
     * @return    Worksheet
     */
    public function xrgSetSheetLaborHead(Worksheet $sheet, string $weekTitle, int $cellIndex): Worksheet
    {
        $headingIndex = $cellIndex + 1;
        
        $headingArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ]
        ];

        $bgColor = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'ffd9d9d9')
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ]
            ]
        ];
        
        $sheet->setCellValue("X$cellIndex", $weekTitle . ' Forecasted Labor');
        $sheet->getStyle("X$cellIndex")->getFont()->setBold(true);
        $sheet->getStyle("X$cellIndex")->getFont()->setSize('16');

        $sheet->getStyle("X$cellIndex:AH$headingIndex")->applyFromArray($headingArray);
        $sheet->getStyle("X$headingIndex:AH$headingIndex")->applyFromArray($bgColor);
        $sheet->getStyle('X2:AH2')->getFont()->setSize(11);
        
        $sheet->getColumnDimension('AA')->setWidth(18, 'px');
        $sheet->getColumnDimension('AB')->setWidth(18, 'px');
        $sheet->getColumnDimension('AC')->setWidth(18, 'px');
        $sheet->getColumnDimension('AD')->setWidth(18, 'px');
        $sheet->getColumnDimension('AE')->setWidth(18, 'px');
        $sheet->getColumnDimension('AF')->setWidth(18, 'px');
        $sheet->getColumnDimension('AG')->setWidth(18, 'px');
        $sheet->getColumnDimension('AH')->setWidth(18, 'px');

        $arrayData = [
            [$weekTitle, 'Forecasted Sales', 'Budgeted Sales', 'Difference', 'Forecasted Labor %', 'Budgeted Labor %', 'Difference',
             'Projected Theoretical Labor' , 'Scheduled Shift Leader Hours', 'Budgeted Shift Leader Hours', 'Difference'
            ]
        ];

        $sheet->fromArray(
                $arrayData,  // The data to set
                NULL,        // Array values with this value will not be set
                "X$headingIndex"         // Top left coordinate of the worksheet range where
        );

        // Hide empty columns 
        $sheet->getColumnDimension('Z')->setVisible(false);
        $sheet->getColumnDimension('AA')->setVisible(false);

        // Format Cells accoring to there heads
        $sheet->getStyle('Y')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $sheet->getStyle('AB:AE')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('AH')->getNumberFormat()->setFormatCode('#,##0.00_);[Red](#,##0.00)');

        return $sheet;
    }

    /**
     * Create Staffing Pars worksheets, styles and Formulas
     *
     * @since    0.1
     * @access   public
     * @param    string $staffLocation
     * @param    array $staffData Date object get from DB
     * @param    Worksheet $sheet current active sheet in memory
     * @return    Worksheet
     */
    public function xrgStaffingParsTotalSheets(string $staffLocation, array $staffData, Worksheet $sheet): Worksheet
    {
        // Staffing Jobs group
        $staffingPars = [
            'Servers' => 'Server/Cocktail', 'Host' => 'Host',
            'Bar' => 'Bartender', 'Bus' => 'Busser/Runner', 'Expo' => 'Expo',
            'Cook' => 'Line Cook', 'Prep' => 'Prep Cook', 'Dish' => 'Dish'
        ];

        // Set Headers
        $headerData = [
            [$staffLocation, 'Have', 'In Training', 'Par', 'Variance', '']
        ];

        $sheet->fromArray(
                $headerData,  // The data to set
                NULL,        // Array values with this value will not be set
                "A3"         // Top left coordinate of the worksheet range where
        );

        $headingArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'ffffc107')
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                    'color' => ['argb' => '00000000'],
                ]
            ]
        ];

        $borderArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                    'color' => ['argb' => '00000000'],
                ]
            ]
        ];

        $contentIndex = 4;
        $contentStart = 4;
        $staffLocation = XrgHelperFunctions::xrgFormatArrayKeys($staffLocation);
        $maxTables = $staffData[$staffLocation]['max_tables'];
        unset($staffData[$staffLocation]['max_tables']);

        foreach($staffData[$staffLocation] as $staffType => $staffVal ) {
            $staffParType = $staffingPars[$staffType];
            $staffInTraining = $staffVal['in_training'];
            $staffPars = $staffVal['total'];

            if($staffType === 'Servers') :
                $staffInTraining += $staffData[$staffLocation]['Cocktail']['in_training'];
                $staffPars += $staffData[$staffLocation]['Cocktail']['total'];
            endif;
            if($staffType === 'Cocktail') :
                continue;
            endif;

            $haveFormula = '=IF($A$1="RD",COUNTIFS(Original!M:M,$A$3,Original!O:O,A'.$contentIndex.'),COUNTIFS(Original!L:L,$A$3,Original!O:O,A'.$contentIndex.'))';
            $sheet->setCellValue("A$contentIndex", $staffParType);
            $sheet->setCellValue("B$contentIndex", $haveFormula);
            $sheet->setCellValue("C$contentIndex", $staffInTraining);
            $sheet->setCellValue("D$contentIndex", $staffPars);
            $sheet->setCellValue("E$contentIndex", "=B$contentIndex - D$contentIndex");
            
            // Apply Style
            $sheet->getStyle("A$contentIndex:E$contentIndex")->applyFromArray($headingArray);

            $contentIndex += 2;
        }
        $contentEnd = $contentIndex - 2;
        $sheet->setCellValue("A$contentIndex", 'Total Staff');
        $sheet->setCellValue("B$contentIndex", "=SUM(B$contentStart:B$contentEnd)");
        $sheet->setCellValue("C$contentIndex", "=SUM(C$contentStart:C$contentEnd)");
        $sheet->setCellValue("D$contentIndex", "=SUM(D$contentStart:D$contentEnd)");
        $sheet->setCellValue("E$contentIndex", "=B$contentIndex - D$contentIndex");

         // Styling of Sheet
         $sheet->getStyle("A$contentIndex:E$contentIndex")->getFont()->setBold(TRUE);
         $sheet->getStyle("A3")->getFont()->setBold(TRUE);
         $sheet->getStyle("A3:E$contentIndex")->applyFromArray($borderArray);

         // Max Table Data
         $contentIndex += 3;
         $sheet->mergeCells("A$contentIndex:B$contentIndex");
         $sheet->setCellValue("A$contentIndex", 'Max Tables to seat in restaurant:');
         $sheet->setCellValue("C$contentIndex", $maxTables);
         $sheet->getStyle("C$contentIndex")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');
         
         $contentIndex += 2;
         $sheet->mergeCells("A$contentIndex:B$contentIndex");
         $sheet->setCellValue("A$contentIndex", '4 Table Sections = Ser/Ctkl:');
         $sheet->setCellValue("C$contentIndex", ceil($maxTables/4));
         $sheet->getStyle("C$contentIndex")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');


         // Styling Header
         $sheet->getStyle("B3:E3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');

         foreach (range('A','F') as $col) {
            $sheet->getColumnDimension($col)->setWidth(18, 'px');
        }

        // Hide Rows
        $sheet->getRowDimension(1)->setVisible(FALSE);

        // Staffing Pars Data sheet
        $sheet = $this->xrgStaffingParsDataSheet($staffLocation, $staffData, $sheet);

        $sheet->getStyle('E')->getNumberFormat()->setFormatCode('0_);[Red](0)');

        return $sheet;
    }

    /**
     * Create Staffing Pars worksheets, styles and Formulas
     *
     * @since    0.1
     * @access   public
     * @param    string $staffLocation
     * @param    array $staffData Date object get from DB
     * @param    Worksheet $sheet current active sheet in memory
     * @return    Worksheet
     */
    public function xrgStaffingParsDataSheet(string $staffLocation, array $staffData, Worksheet $sheet): Worksheet
    {
        // Set Headers
        $headerData = [
             ['', '', 'Mon' , 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun', '']
         ];

        foreach (range('G','P') as $col) {
            $sheet->getColumnDimension($col)->setWidth(14, 'px');
        }

        $topCellBorders = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ]
            ]
        ];

        $leftCellBorders = [
            'borders' => [
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ]
            ]
        ];

        $rightCellBorders = [
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ]
            ]
        ];

        $bottomCellBorders = [
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ]
            ]
        ];

        $allCellBorders = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];

        $sheet->fromArray(
                $headerData,  // The data to set
                NULL,        // Array values with this value will not be set
                "G7"         // Top left coordinate of the worksheet range where
        );
        $sheet->getStyle("I7:O7")->applyFromArray($allCellBorders);

        $contentIndex = 8;
        $contentStart = $contentIndex;
        $grandTotal = [
            'am' => ['Mon' => 0, 'Tues' => 0, 'Wed' => 0, 'Thurs' => 0, 'Fri' => 0, 'Sat' => 0, 'Sun' => 0],
            'pm' => ['Mon' => 0, 'Tues' => 0, 'Wed' => 0, 'Thurs' => 0, 'Fri' => 0, 'Sat' => 0, 'Sun' => 0]
        ];
        $staffTotal = 0;

        foreach($staffData[$staffLocation] as $staffType => $staffVal ) {

            // For Grand Totals
            $grandTotal = XrgHelperFunctions::xrgSumKeysValue($staffVal, $grandTotal);
            $staffTotal += $staffVal['total'];

            $sheet->setCellValue("G$contentIndex", $staffType);
            $sheet->setCellValue("H$contentIndex", 'am');
            $sheet->setCellValue("I$contentIndex", $staffVal['am']['Mon']);
            $sheet->setCellValue("J$contentIndex", $staffVal['am']['Tues']);
            $sheet->setCellValue("K$contentIndex", $staffVal['am']['Wed']);
            $sheet->setCellValue("L$contentIndex", $staffVal['am']['Thurs']);
            $sheet->setCellValue("M$contentIndex", $staffVal['am']['Fri']);
            $sheet->setCellValue("N$contentIndex", $staffVal['am']['Sat']);
            $sheet->setCellValue("O$contentIndex", $staffVal['am']['Sun']);
            $sheet->setCellValue("P$contentIndex", array_sum($staffVal['am']));
            $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($allCellBorders);
            $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($topCellBorders);
            $sheet->getStyle("G$contentIndex")->applyFromArray($leftCellBorders);
            $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);
            $sheet->getStyle("G$contentIndex")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('8ef2ff');
           
            $contentIndex += 1;
            $sheet->setCellValue("G$contentIndex", '');
            $sheet->setCellValue("H$contentIndex", 'pm');
            $sheet->setCellValue("I$contentIndex", $staffVal['pm']['Mon']);
            $sheet->setCellValue("J$contentIndex", $staffVal['pm']['Tues']);
            $sheet->setCellValue("K$contentIndex", $staffVal['pm']['Wed']);
            $sheet->setCellValue("L$contentIndex", $staffVal['pm']['Thurs']);
            $sheet->setCellValue("M$contentIndex", $staffVal['pm']['Fri']);
            $sheet->setCellValue("N$contentIndex", $staffVal['pm']['Sat']);
            $sheet->setCellValue("O$contentIndex", $staffVal['pm']['Sun']);
            $sheet->setCellValue("P$contentIndex", array_sum($staffVal['pm']));
            $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($allCellBorders);
            $sheet->getStyle("G$contentIndex")->applyFromArray($leftCellBorders);
            $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);
            $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($bottomCellBorders);
           
            $contentIndex += 1;
            $sheet->setCellValue("P$contentIndex", 'Ttl ' . $staffType);
            $sheet->getStyle("P$contentIndex")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');
            $sheet->getStyle("P$contentIndex")->applyFromArray($allCellBorders);
            $sheet->getStyle("P$contentIndex")->applyFromArray($leftCellBorders);
            $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);

            $contentIndex += 1;
            $sheet->setCellValue("P$contentIndex", $staffVal['total']);
            $sheet->getStyle("P$contentIndex")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');
            $sheet->getStyle("P$contentIndex")->applyFromArray($allCellBorders);
            $sheet->getStyle("P$contentIndex")->applyFromArray($leftCellBorders);
            $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);
            $sheet->getStyle("P$contentIndex")->applyFromArray($bottomCellBorders);

            $contentIndex += 1;
        }

        // Header area
        $sheet->mergeCells('G2:P2')->setCellValue('G2', 'Staffing Pars');
        $sheet->getStyle('G2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('c1c1c1');
        $sheet->getStyle('G2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $contentIndex = 3;
        $sheet->setCellValue("G$contentIndex", 'Total Count');
        $sheet->setCellValue("H$contentIndex", 'am');
        $sheet->setCellValue("I$contentIndex", $grandTotal['am']['Mon']);
        $sheet->setCellValue("J$contentIndex", $grandTotal['am']['Tues']);
        $sheet->setCellValue("K$contentIndex", $grandTotal['am']['Wed']);
        $sheet->setCellValue("L$contentIndex", $grandTotal['am']['Thurs']);
        $sheet->setCellValue("M$contentIndex", $grandTotal['am']['Fri']);
        $sheet->setCellValue("N$contentIndex", $grandTotal['am']['Sat']);
        $sheet->setCellValue("O$contentIndex", $grandTotal['am']['Sun']);
        $sheet->setCellValue("P$contentIndex", array_sum($grandTotal['am']));
        $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($allCellBorders);
        $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($topCellBorders);
        $sheet->getStyle("G$contentIndex")->applyFromArray($leftCellBorders);
        $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);
        $sheet->getStyle("G$contentIndex")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('8ef2ff');
        
        $contentIndex += 1;
        $sheet->setCellValue("G$contentIndex", '');
        $sheet->setCellValue("H$contentIndex", 'pm');
        $sheet->setCellValue("I$contentIndex", $grandTotal['pm']['Mon']);
        $sheet->setCellValue("J$contentIndex", $grandTotal['pm']['Tues']);
        $sheet->setCellValue("K$contentIndex", $grandTotal['pm']['Wed']);
        $sheet->setCellValue("L$contentIndex", $grandTotal['pm']['Thurs']);
        $sheet->setCellValue("M$contentIndex", $grandTotal['pm']['Fri']);
        $sheet->setCellValue("N$contentIndex", $grandTotal['pm']['Sat']);
        $sheet->setCellValue("O$contentIndex", $grandTotal['pm']['Sun']);
        $sheet->setCellValue("P$contentIndex", array_sum($grandTotal['pm']));
        $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($allCellBorders);
        $sheet->getStyle("G$contentIndex")->applyFromArray($leftCellBorders);
        $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);
        $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($bottomCellBorders);
        
        $contentIndex += 1;
        $sheet->setCellValue("P$contentIndex", 'Ttl Staff');
        $sheet->getStyle("P$contentIndex")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');
        $sheet->getStyle("P$contentIndex")->applyFromArray($allCellBorders);
        $sheet->getStyle("P$contentIndex")->applyFromArray($leftCellBorders);
        $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);

        $contentIndex += 1;
        $sheet->setCellValue("P$contentIndex", $staffTotal);
        $sheet->getStyle("P$contentIndex")->applyFromArray($allCellBorders);
        $sheet->getStyle("P$contentIndex")->applyFromArray($leftCellBorders);
        $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);
        $sheet->getStyle("P$contentIndex")->applyFromArray($bottomCellBorders);

        return $sheet;
    }

    /**
     * Create Staffing Pars worksheets, styles and Formulas
     *
     * @since    0.1
     * @access   public
     * @param    string $staffLocation
     * @param    array $staffData Date object get from DB
     * @param    Worksheet $sheet current active sheet in memory
     * @return    Worksheet
     */
    public function xrgStaffingTotalRegionSheet(string $staffRegion, array $staffData, Worksheet $sheet): Worksheet
    {
         // Staffing Pars Data sheet
         $staffLocation = XrgHelperFunctions::xrgFormatArrayKeys($staffData['xrg_locations'][0]);
         unset($staffData[$staffLocation]['max_tables']);
         $parTotalArr = $this->xrgStaffingParsRegionData($staffData, $sheet);

        // Staffing Jobs group
        $staffingPars = [
            'Servers' => 'Server/Cocktail', 'Host' => 'Host',
            'Bar' => 'Bartender', 'Bus' => 'Busser/Runner', 'Expo' => 'Expo',
            'Cook' => 'Line Cook', 'Prep' => 'Prep Cook', 'Dish' => 'Dish'
        ];

        // Set Headers
        $headerData = [
            [$staffRegion, 'Have', 'In Training', 'Par', 'Variance', '']
        ];

        $sheet->fromArray(
                $headerData,  // The data to set
                NULL,        // Array values with this value will not be set
                "A3"         // Top left coordinate of the worksheet range where
        );

        $headingArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'ffffc107')
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                    'color' => ['argb' => '00000000'],
                ]
            ]
        ];

        $borderArray = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                    'color' => ['argb' => '00000000'],
                ]
            ]
        ];

        $sheet->setCellValue("A1", "RD");

        $contentIndex = 4;
        $contentStart = 4;

        foreach($staffData[$staffLocation] as $staffType => $staffVal ) {
            $staffParType = $staffingPars[$staffType];
            $staffParSum = "=$parTotalArr[$staffType]['par_total']";
            if($staffType === 'Servers') {
                $staffParSum = "=SUM(" . $parTotalArr['Servers']['par_total'] . "," . $parTotalArr['Cocktail']['par_total'] . ")";
            }
            if($staffType === 'Cocktail') {
                continue;
            }

            $haveFormula = '=IF($A$1="RD",COUNTIFS(Original!M:M,$A$3,Original!O:O,A'.$contentIndex.'),COUNTIFS(Original!L:L,$A$3,Original!O:O,A'.$contentIndex.'))';
            $inTrainingFormula = XrgHelperFunctions::xrgGenerateFoumula("C$contentIndex", $staffData['xrg_locations']);
            $sheet->setCellValue("A$contentIndex", $staffParType);
            $sheet->setCellValue("B$contentIndex", $haveFormula);
            $sheet->setCellValue("C$contentIndex", "=SUM($inTrainingFormula)");
            $sheet->setCellValue("D$contentIndex", $staffParSum);
            $sheet->setCellValue("E$contentIndex", "=B$contentIndex - D$contentIndex");
            
            // Apply Style
            $sheet->getStyle("A$contentIndex:E$contentIndex")->applyFromArray($headingArray);

            $contentIndex += 2;
        }
        $contentEnd = $contentIndex - 2;
        $sheet->setCellValue("A$contentIndex", 'Total Staff');
        $sheet->setCellValue("B$contentIndex", "=SUM(B$contentStart:B$contentEnd)");
        $sheet->setCellValue("C$contentIndex", "=SUM(C$contentStart:C$contentEnd)");
        $sheet->setCellValue("D$contentIndex", "=SUM(D$contentStart:D$contentEnd)");
        $sheet->setCellValue("E$contentIndex", "=B$contentIndex - D$contentIndex");

         // Styling of Sheet
         $sheet->getStyle("A$contentIndex:E$contentIndex")->getFont()->setBold(TRUE);
         $sheet->getStyle("A3")->getFont()->setBold(TRUE);
         $sheet->getStyle("A3:E$contentIndex")->applyFromArray($borderArray);

         // Max Table Data
         $contentIndex += 3;
         $sheet->mergeCells("A$contentIndex:B$contentIndex");
         $sheet->setCellValue("A$contentIndex", 'Max Tables to seat in restaurant:');
         $sheet->setCellValue("C$contentIndex", "=SUM(".XrgHelperFunctions::xrgGenerateFoumula("C$contentIndex", $staffData['xrg_locations']).")");
         $sheet->getStyle("C$contentIndex")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');
         
         $contentIndex += 2;
         $sheet->mergeCells("A$contentIndex:B$contentIndex");
         $sheet->setCellValue("A$contentIndex", '4 Table Sections = Ser/Ctkl:');
         $sheet->setCellValue("C$contentIndex", "=SUM(".XrgHelperFunctions::xrgGenerateFoumula("C$contentIndex", $staffData['xrg_locations']).")");
         $sheet->getStyle("C$contentIndex")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');


         // Styling Header
         $sheet->getStyle("B3:E3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');

         foreach (range('A','F') as $col) {
            $sheet->getColumnDimension($col)->setWidth(18, 'px');
        }

        // Hide Rows
        $sheet->getRowDimension(1)->setVisible(FALSE);

        $sheet->getStyle('E')->getNumberFormat()->setFormatCode('0_);[Red](0)');

        return $sheet;
    }

    /**
     * Create Staffing Pars Origin worksheets, styles and Formulas
     *
     * @since    0.1
     * @access   public
     * @param    array $staffData Date object get from DB
     * @param    Worksheet $sheet current active sheet in memory
     * @return    array
     */
    public function xrgStaffingParsRegionData(array $staffData, Worksheet $sheet): array
    {
        // Return Array
        $parTotal = [];
        // Set Headers
        $headerData = [
             ['', '', 'Mon' , 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun', '']
         ];

        foreach (range('G','P') as $col) {
            $sheet->getColumnDimension($col)->setWidth(14, 'px');
        }

        $topCellBorders = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ]
            ]
        ];

        $leftCellBorders = [
            'borders' => [
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ]
            ]
        ];

        $rightCellBorders = [
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ]
            ]
        ];

        $bottomCellBorders = [
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ]
            ]
        ];

        $allCellBorders = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];

        $sheet->fromArray(
                $headerData,  // The data to set
                NULL,        // Array values with this value will not be set
                "G7"         // Top left coordinate of the worksheet range where
        );
        $sheet->getStyle("I7:O7")->applyFromArray($allCellBorders);

        $contentIndex = 8;

        $staffLocation = XrgHelperFunctions::xrgFormatArrayKeys($staffData['xrg_locations'][0]);

        foreach($staffData[$staffLocation] as $staffType => $staffVal ) {
            // For Grand Totals
            $sheet->setCellValue("G$contentIndex", $staffType);
            $sheet->setCellValue("H$contentIndex", 'am');
            foreach(range('I', 'P') as $ind) {
                $sheet->setCellValue($ind.$contentIndex, "=SUM(".XrgHelperFunctions::xrgGenerateFoumula($ind.$contentIndex, $staffData['xrg_locations']).")");
            }
           
            $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($allCellBorders);
            $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($topCellBorders);
            $sheet->getStyle("G$contentIndex")->applyFromArray($leftCellBorders);
            $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);
            $sheet->getStyle("G$contentIndex")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('8ef2ff');
           
            $contentIndex += 1;
            $sheet->setCellValue("G$contentIndex", '');
            $sheet->setCellValue("H$contentIndex", 'pm');
            foreach(range('I', 'P') as $ind) {
                $sheet->setCellValue($ind.$contentIndex, "=SUM(".XrgHelperFunctions::xrgGenerateFoumula($ind.$contentIndex, $staffData['xrg_locations']).")");
            }

            $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($allCellBorders);
            $sheet->getStyle("G$contentIndex")->applyFromArray($leftCellBorders);
            $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);
            $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($bottomCellBorders);
           
            $contentIndex += 1;
            $sheet->setCellValue("P$contentIndex", 'Ttl ' . $staffType);
            $sheet->getStyle("P$contentIndex")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');
            $sheet->getStyle("P$contentIndex")->applyFromArray($allCellBorders);
            $sheet->getStyle("P$contentIndex")->applyFromArray($leftCellBorders);
            $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);

            $contentIndex += 1;
            $sheet->setCellValue("P$contentIndex", "=SUM(".XrgHelperFunctions::xrgGenerateFoumula("P$contentIndex", $staffData['xrg_locations']).")");
            $sheet->getStyle("P$contentIndex")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');
            $sheet->getStyle("P$contentIndex")->applyFromArray($allCellBorders);
            $sheet->getStyle("P$contentIndex")->applyFromArray($leftCellBorders);
            $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);
            $sheet->getStyle("P$contentIndex")->applyFromArray($bottomCellBorders);

            // Populate Par Total array
            $parTotal[$staffType]['par_total'] = "P$contentIndex";

            $contentIndex += 1;
        }

        // Header area
        $sheet->mergeCells('G2:P2')->setCellValue('G2', 'Staffing Pars');
        $sheet->getStyle('G2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('c1c1c1');
        $sheet->getStyle('G2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $contentIndex = 3;
        $sheet->setCellValue("G$contentIndex", 'Total Count');
        $sheet->setCellValue("H$contentIndex", 'am');
        foreach(range('I', 'P') as $ind) {
            $sheet->setCellValue($ind.$contentIndex, "=SUM(".XrgHelperFunctions::xrgGenerateFoumula($ind.$contentIndex, $staffData['xrg_locations']).")");
        }
        $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($allCellBorders);
        $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($topCellBorders);
        $sheet->getStyle("G$contentIndex")->applyFromArray($leftCellBorders);
        $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);
        $sheet->getStyle("G$contentIndex")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('8ef2ff');
        
        $contentIndex += 1;
        $sheet->setCellValue("G$contentIndex", '');
        $sheet->setCellValue("H$contentIndex", 'pm');
        foreach(range('I', 'P') as $ind) {
            $sheet->setCellValue($ind.$contentIndex, "=SUM(".XrgHelperFunctions::xrgGenerateFoumula($ind.$contentIndex, $staffData['xrg_locations']).")");
        }
        $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($allCellBorders);
        $sheet->getStyle("G$contentIndex")->applyFromArray($leftCellBorders);
        $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);
        $sheet->getStyle("G$contentIndex:P$contentIndex")->applyFromArray($bottomCellBorders);
        
        $contentIndex += 1;
        $sheet->setCellValue("P$contentIndex", 'Ttl Staff');
        $sheet->getStyle("P$contentIndex")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ffff00');
        $sheet->getStyle("P$contentIndex")->applyFromArray($allCellBorders);
        $sheet->getStyle("P$contentIndex")->applyFromArray($leftCellBorders);
        $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);

        $contentIndex += 1;
        $sheet->setCellValue("P$contentIndex", "=SUM(".XrgHelperFunctions::xrgGenerateFoumula("P$contentIndex", $staffData['xrg_locations']).")");
        $sheet->getStyle("P$contentIndex")->applyFromArray($allCellBorders);
        $sheet->getStyle("P$contentIndex")->applyFromArray($leftCellBorders);
        $sheet->getStyle("P$contentIndex")->applyFromArray($rightCellBorders);
        $sheet->getStyle("P$contentIndex")->applyFromArray($bottomCellBorders);

        return $parTotal;
    }

    /**
     * Set Original Sheet format, style and Formulas
     *
     * @since    0.1
     * @access   public
     * @param   Worksheet $sheet current active sheet in memory
     * @return    Worksheet
     */
    public function xrgSetSheetOriginal(Worksheet $sheet): Worksheet
    {
        
        
       /* $tempSheet = new Worksheet(null, 'Original');

        $temp = 0;
       
        foreach ($sheet->getRowIterator() as $rowIndex => $row) {
            $temp++;
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(TRUE);

            echo "<pre>";
            foreach ($cellIterator as $coordinate => $cell) {
                echo $rowIndex.$coordinate;
              // $tempSheet->setCellValue($rowIndex.$coordinate, $cell->getValue());
            }
            echo "</pre>";
        }

        echo "<pre>";
        echo $temp;
        echo "</pre>";
        wp_die();*/
      /*  $totalIndex = count($tempSheet->toArray());
        $tempSheet->getStyle("A1:O1")->applyFromArray($headingArray);
        $tempSheet->getStyle("A2:O$totalIndex")->applyFromArray($genericStyle);
        
        $tempSheet->getColumnDimension("A1:O$totalIndex")->setWidth(18, 'px');*/


        // Format Cells accoring to there heads
       /* $sheet->getStyle('Y')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $sheet->getStyle('AB:AE')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('AH')->getNumberFormat()->setFormatCode('#,##0.00_);[Red](#,##0.00)');*/
        
        $totalIndex = count($sheet->toArray());

        // for($i=2; $i <= $totalIndex; $i++) {
            
        //     $cellVal = '=IFERROR(VLOOKUP($J'.$i.',Lookup!$I:$M,Lookup!J$2,0),"")';
        //     $sheet->setCellValue("K$i", $cellVal);
        //     $sheet->getCell("K$i")->getStyle()->setQuotePrefix(true);

        // }
        
        
        foreach( range('A', 'O') as $col ) {
            $sheet->getColumnDimension($col)->setWidth(20, 'px');
        }
        
        return $sheet;
    }
}
