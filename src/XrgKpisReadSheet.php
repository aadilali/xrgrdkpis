<?php

declare(strict_types=1);

// -*- coding: utf-8 -*-

namespace XRG\RD;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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
     * Create re-write rule for arbitrary url.
     *
     * @since    0.1
     * @access   public
     * @return    Spreadsheet
     */
    public function xrgLoadSheet(): Spreadsheet
    {
        $sheetFile = XRG_PLUGIN_PATH . 'data/Antonio-RD-KPI-2021.xlsx';
        return IOFactory::load($sheetFile);
    }

    public function xrgWriteHtmlTable(): void
    {
        $spsheet = $this->xrgLoadSheet();
        $spsheet->setActiveSheetIndexByName('Period 1');
        echo $spsheet->getActiveSheetIndex();
        
        $worksheet = $spsheet->getActiveSheet();

        echo '<table>' . PHP_EOL;
        foreach ($worksheet->getRowIterator() as $row) {
            echo '<tr>' . PHP_EOL;
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
                                                            //    even if a cell value is not set.
                                                            // For 'TRUE', we loop through cells
                                                            //    only when their value is set.
                                                            // If this method is not called,
                                                            //    the default value is 'false'.
            foreach ($cellIterator as $cell) {
                if($cell->getValue() == 'Huntington Beach') {
                    
                }
                echo '<td>' .
                    $cell->getValue() .
                    '</td>' . PHP_EOL;
            }
            echo '</tr>' . PHP_EOL;
        }
        echo '</table>' . PHP_EOL;
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
        
        $spreadsheet = new Spreadsheet();
        $finalTotal = [];

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
                $currentSheet = $this->xrgSetSheetHead($currentSheet, $weeklyData['xrg_week'], $cellIndex);
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
            $currentSheet = $this->xrgSetSheetHead($currentSheet, 'KPI Period to Date', $cellIndex);

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

            // Set active worksheet and write to file
            $spreadsheet->setActiveSheetIndexByName($sheetObj->period_name);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save( XRG_PLUGIN_PATH . 'data/ASantana.xlsx' );
    }

    
    /**
     * Create excel file from form data. Form data stored to DB as well
     *
     * @since    0.1
     * @access   public
     * @param   Worksheet $sheet current active sheet in memory
     * @param   string $weekTitle week title
     * @param   int $cellIndex cell index
     * @return    Worksheet
     */
    public function xrgSetSheetHead(Worksheet $sheet, string $weekTitle, int $cellIndex): Worksheet
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

        $sheet->getStyle("A$cellIndex:V$headingIndex")->applyFromArray($headingArray);
        $sheet->getStyle("B$headingIndex:V$headingIndex")->applyFromArray($bgColor);
        $sheet->getStyle('A2:V2')->getFont()->setSize(11);
        
        foreach (range('B','V') as $col) {
            $sheet->getColumnDimension($col)->setWidth(18, 'px');
        }
        
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
}
