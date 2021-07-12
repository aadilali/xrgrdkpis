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
    private $xrgTableName;
    
    /**
     * Initialize the class, set its properties and register callbacks against hooks.
     * @since   0.1
     */
    public function __construct()
    {
        $this->xrgTableName = 'wp_xrg_kpis';
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

        unset( $pObj['xrg_kpis_data_submit'] );
        unset( $pObj['xrg_period'] );
        unset( $pObj['xrg_region'] );
        unset( $pObj['xrg_data_type'] );

        if( $dataType === 'kpis' ) {
            $weeklyKPIs[XrgHelperFunctions::xrgFormatArrayKeys($pObj['xrg_week'])] = $pObj;
            //$weeklyKPIs[]['weeklyKPIs'] = $pObj;
        }

        if( $dataType === 'labor' ) {
            $weeklyLabor[]['weeklyLabor'] = $pObj;
        }

        // Get Previous stored data if any
        $exisitngData = $this->xrgGetWeeklyData( $periodName, $regionName );
        if( $exisitngData ) {
            $this->xrgUpdateWeeklyData( (int)$exisitngData->id, $exisitngData->weekly_kpis_data, $exisitngData->weekly_labor_data, $weeklyKPIs, $weeklyLabor, $dataType );
        } else {
            $this->xrgInsertWeeklyData( $periodName, $regionName, $weeklyKPIs, $weeklyLabor );
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
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->xrgTableName WHERE period_name = %s AND region_name = %s", $period, $region ) );
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
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->xrgTableName WHERE region_name = %s", $region ) );
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

        $dataColumns = [ 'period_name' => $period, 'region_name' => $region, 'weekly_kpis_data' => serialize( $weeklyKpiData ), 'weekly_labor_data' => serialize( $weeklyLaborData ) ];
        $dataFormat = [ '%s','%s', '%s','%s' ];

        return $wpdb->insert( $this->xrgTableName, $dataColumns, $dataFormat );
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
            $tempData = array_merge( $tempData, $weeklyKpiData );
            $dataColumns = ['weekly_kpis_data' => serialize( $tempData )];
            $dataFormat = [ '%s' ];
        }

        if( $dataType === 'labor' ) {
          //  $dataColumns['weekly_labor_data'] = 
          //  $dataFormat[] = '%s';
        }

        //$dataColumns = [ 'weekly_kpis_data' => $weeklyKpiData, 'weekly_labor_data' => $weeklyLaborData ];
        
        $dataWhere = [ 'id' => $id ];
        $whereFormat = [ '%d' ];

        return $wpdb->update( $this->xrgTableName, $dataColumns, $dataWhere, $dataFormat, $whereFormat );
    }
}
