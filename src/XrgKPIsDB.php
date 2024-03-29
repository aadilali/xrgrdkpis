<?php

declare(strict_types=1);

// -*- coding: utf-8 -*-

namespace XRG\RD;

use XRG\RD\XrgHelperFunctions; 

/**
 * Class XrgKPIsDB
 *
 * Responsible for reading and output excel sheets.
 *
 * @package XRG\RD
 */


class XrgKPIsDB
{

     /**
     * @var string
     */
    private $xrgKPIsTable;

    private $xrgStaffingTable;
    
    /**
     * Initialize the class, set its properties and register callbacks against hooks.
     * @since   0.1
     */
    public function __construct()
    {
        global $wpdb;

        $this->xrgKPIsTable = $wpdb->prefix . 'xrg_kpis';
        $this->xrgStaffingTable = $wpdb->prefix . 'xrg_staffing_pars';
    }

    /**
     * Save data to database 
     *
     * @since   0.1
     * @access   public
     * @param   array $pObj array with submitted form data
     * @return   void
     */
    public function xrgSaveDataToDB(array $pObj): void
    {
        
        $weeklyKPIs = [];
        $weeklyLabor = [];
        $periodName = $pObj['xrg_period'];
        $regionName = $pObj['xrg_region'];
        $dataType = $pObj['xrg_data_type'];

        unset( $pObj['xrg_period'] );
        unset( $pObj['xrg_region'] );
        unset( $pObj['xrg_data_type'] );

        if( $dataType === 'kpis' ) {
            $weeklyKPIs[XrgHelperFunctions::xrgFormatArrayKeys($pObj['xrg_week'])] = $pObj;
        }

        if( $dataType === 'labor' ) {
            $weeklyLabor[XrgHelperFunctions::xrgFormatArrayKeys($pObj['xrg_week'])] = $pObj;
        }

        // Get Previous stored data if any
        $exisitngData = $this->xrgGetWeeklyData( $periodName, $regionName );
        if( $exisitngData ) {
            $this->xrgUpdateWeeklyData((int)$exisitngData->id, $exisitngData->weekly_kpis_data, $exisitngData->weekly_labor_data, $weeklyKPIs, $weeklyLabor, $dataType);
        } else {
            $this->xrgInsertWeeklyData($periodName, $regionName, $weeklyKPIs, $weeklyLabor);
        }
    }

    /**
     * Get data against Period name and Region name from database 
     *
     * @since   0.1
     * @access   public
     * @param   string $period Period name
     * @param   string $region Region name
     * @return   object|null|void Comment object, otherwise false
     */
    public function xrgGetWeeklyData(string $period, string $region)
    {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->xrgKPIsTable WHERE period_name = %s AND region_name = %s", $period, $region));
    }

    /**
     * Get data against and Region name from database 
     *
     * @since   0.1
     * @access   public
     * @param   string $region Region name
     * @return   object|null|void Comment object, otherwise false
     */
    public function xrgGetRegionalData(string $region)
    {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare( "SELECT * FROM $this->xrgKPIsTable WHERE region_name = %s ORDER BY id ASC", $region));
    }

    /**
     * Insert new data to database 
     *
     * @since   0.1
     * @access   public
     * @param   string $period Period name
     * @param   string $region Region name
     * @param   array $weeklyKpiData Post array having submitted form data about KPIs
     * @param   array $weeklyLaborData Post array having submitted form data about Labor Forecast
     */
    public function xrgInsertWeeklyData(string $period, string $region, array $weeklyKpiData, array $weeklyLaborData)
    {
        global $wpdb;

        $dataColumns = ['period_name' => $period, 'region_name' => $region, 'weekly_kpis_data' => serialize( $weeklyKpiData ), 'weekly_labor_data' => serialize( $weeklyLaborData )];
        $dataFormat = ['%s','%s', '%s','%s'];

        return $wpdb->insert($this->xrgKPIsTable, $dataColumns, $dataFormat);
    }

    /**
     * update existing data to database
     *
     * @since    0.1
     * @access   public
     * @param   int $id ID of the existing record
     * @param   string $existingKpiData Post array having submitted form data about KPIs
     * @param   string $existingLaborData Post array having submitted form data about Labor Forecast
     * @param   array $weeklyKpiData Post array having submitted form data about KPIs
     * @param   array $weeklyLaborData Post array having submitted form data about Labor Forecast
     * @param   string $dataType use to check form type either KPIs or Labor data
     */
    public function xrgUpdateWeeklyData(int $id, string $existingKpiData, string $existingLaborData, array $weeklyKpiData, array $weeklyLaborData, string $dataType)
    {
        global $wpdb;
        if( $dataType === 'kpis' ) {
            $tempData = unserialize($existingKpiData);
            $tempData = array_merge($tempData, $weeklyKpiData);
            $dataColumns = ['weekly_kpis_data' => serialize( $tempData )];
            $dataFormat = ['%s'];
        }

        if( $dataType === 'labor' ) {
            $tempData = unserialize($existingLaborData);
            $tempData = array_merge( $tempData, $weeklyLaborData );
            $dataColumns = ['weekly_labor_data' => serialize( $tempData )];
            $dataFormat = ['%s'];
        }
        
        $dataWhere = ['id' => $id];
        $whereFormat = ['%d'];

        return $wpdb->update( $this->xrgKPIsTable, $dataColumns, $dataWhere, $dataFormat, $whereFormat );
    }

    /**
     * Get staffing data, here code decide to insert or update the data 
     *
     * @since   0.1
     * @access   public
     * @param   array $pObj array with submitted form data
     * @return   void
     */
    public function xrgSaveStaffingToDB(array $pObj): void
    {
        
        $staffingPars = [];
        $regionName = $pObj['xrg_region'];

        unset( $pObj['xrg_region'] );
        $staffingPars = $pObj;

        // Get Previous stored data if any
        $exisitngData = $this->xrgStaffingParsData($regionName);
        if($exisitngData) {
            $this->xrgInsertStaffingPars((int)$exisitngData->id, '', $staffingPars, $exisitngData->staffing_data);
        } else {
            $this->xrgInsertStaffingPars(0, $regionName, $staffingPars, '');
        }

    }

    /**
     * Get data against Region name for Staffing Pars from database 
     *
     * @since   0.1
     * @access   public
     * @param   string $region Region name
     * @return   object|null|void Comment object, otherwise false
     */
    public function xrgStaffingParsData(string $region)
    {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->xrgStaffingTable WHERE region_name = %s", $region ) );
    }

    /**
     * Insert Staffing data to database 
     *
     * @since   0.1
     * @access   public
     * @param   int $id id of staffing data in case of updating existing row
     * @param   string $region Region name
     * @param   array $staffingPars Post array having submitted form data about Staffing Pars
     * @param   string $existingStaffingData Post array having existing data about Staffing Pars
     */
    public function xrgInsertStaffingPars(int $id=0, string $region, array $staffingPars, string $existingStaffingData)
    {
        global $wpdb;
       
        if($id) {  // Update existing staffing pars data
            $tempData = unserialize($existingStaffingData);
            $tempData = array_merge( $tempData, $staffingPars );
            $dataColumns = ['staffing_data' => serialize( $tempData )];
            $dataFormat = ['%s'];
            $dataWhere = ['id' => $id];
            $whereFormat = ['%d'];

            return $wpdb->update( $this->xrgStaffingTable, $dataColumns, $dataWhere, $dataFormat, $whereFormat );
        }

        $dataColumns = ['region_name' => $region, 'staffing_data' => serialize( $staffingPars )];
        $dataFormat = ['%s','%s'];

        return $wpdb->insert( $this->xrgStaffingTable, $dataColumns, $dataFormat );
    }
}
