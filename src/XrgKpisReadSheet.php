<?php

declare(strict_types=1);

// -*- coding: utf-8 -*-

namespace XRG\RD;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;


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
        $sheetFile = PLUGIN_PATH . 'data/Antonio-RD-KPI-2021.xlsx';
        return IOFactory::load($sheetFile);
    }

    public function xrgWriteHtmlTable(): void
    {
        $spsheet = $this->xrgLoadSheet();
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spsheet);
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
        echo $writer->generateHTMLFooter();
    }

}
