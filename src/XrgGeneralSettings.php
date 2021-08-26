<?php

declare(strict_types=1);

// -*- coding: utf-8 -*-

namespace XRG\RD;

/**
 * Class XrgGeneralSettings
 *
 * Responsible for creating arbitrary URLs and load templates.
 *
 * @package XRG\RD
 */


class XrgGeneralSettings
{
    /**
     * register callbacks against hooks.
     * @since    0.1
     * @access   public
     * @return   void
     */
    public function xrgHandleHooks(): void
    {
        // Create table and Default Page on plugin activation
        register_activation_hook(XRG_PLUGIN_PATH.'xrg-rd-kpis.php', [$this, 'xrgAfterActivation']);

        // Create settings page in admin panel
        add_action('admin_menu', [$this, 'xrgCreateAdminMenu']);

        // Register Settings
        add_action('admin_init', [$this, 'xrgRegistersettings']);

        // Enqueue Style and Scripts Front End
        add_action('wp_enqueue_scripts', [$this, 'xrgLoadScripts']);

        // Enqueue Style and Scripts Admin Side
        add_action('admin_enqueue_scripts', [$this, 'xrgLoadAdminScripts']);

        // Add action link 'Settings' under plugin name on plugins list page
        add_filter('plugin_action_links_' . XRG_PLUGIN_BASE_NAME, [$this, 'xrgSettingsActionLink']);

        // Load KPIs existing data from DB against Period and Week number
        add_action( 'wp_ajax_xrg_kpis_data', [$this, 'xrgKpisData']);

        // Load Labor forecast existing data from DB against Period and Week number
        add_action( 'wp_ajax_xrg_labor_data', [$this, 'xrgLaborData']);

        // Load Staffing Pars existing data from DB against Region
        add_action( 'wp_ajax_xrg_staffing_data', [$this, 'xrgStaffData']);
    }

    /**
     * Create Tables to the DB and Default page creation on plugin activation hook
     *
     * @since    0.1
     * @access   public
     * @return   void
     */
    public function xrgAfterActivation(): void
    {
        // Create Table
        $this->xrgCreateTable();

        //Create Default Page
        XrgRdKpis::instance()->xrgTemplatePageInstance()->xrgCreateDefaultPage();
    }

    /**
     * Create Tables to the DB if not exist on plugin activation hook
     *
     * @since    0.1
     * @access   public
     * @return   void
     */

    public function xrgCreateTable(): void
    {
        global $wpdb;
	    $tableNameKPI = $wpdb->prefix . 'xrg_kpis';
        $tableNameStaffing = $wpdb->prefix . 'xrg_staffing_pars';
        $charsetCollate = $wpdb->get_charset_collate();

        $tableStructureKPI = "CREATE TABLE IF NOT EXISTS $tableNameKPI (
           id int(11) NOT NULL AUTO_INCREMENT,
           period_name varchar(50) DEFAULT NULL,
           region_name varchar(80) DEFAULT NULL,
           weekly_kpis_data mediumtext,
           weekly_labor_data mediumtext,
           date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
           PRIMARY KEY (id)
        ) $charsetCollate;";

        $tableStructureStaffing = "CREATE TABLE IF NOT EXISTS $tableNameStaffing  (
            id int(11) NOT NULL AUTO_INCREMENT,
            region_name varchar(150) DEFAULT NULL,
            staffing_data text,
            date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charsetCollate;";
    
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( [$tableStructureKPI, $tableStructureStaffing] );
        //dbDelta( $tableStructureStaffing );
    }

    /**
     * Enqueue scripts and styles for Front End
    */
    public function xrgLoadScripts() {
        wp_register_style('xrg-rd-kpis', XRG_PLUGIN_URI.'assets/css/xrg-rd-kpis-style.css');
        wp_enqueue_style('xrg-rd-kpis');

        wp_register_script('xrg-rd-kpis-main', XRG_PLUGIN_URI.'assets/js/xrg-rd-kpis-main.js', ['jquery'], '0.1', true);
        wp_enqueue_script('xrg-rd-kpis-main');
        wp_localize_script('xrg-rd-kpis-main', 'xrgMainObj', ['ajaxURL' => admin_url('admin-ajax.php')]);

    }

    /**
     * Enqueue scripts and styles for Admin Side
    */
    public function xrgLoadAdminScripts() {
        wp_register_style( 'xrg-rd-admin', XRG_PLUGIN_URI.'assets/css/xrg-rd-admin-style.css' );
        wp_enqueue_style( 'xrg-rd-admin' );

        wp_register_script('xrg-rd-admin-main', XRG_PLUGIN_URI.'assets/js/xrg-rd-admin-main.js', ['jquery'], '0.1', true);
        wp_enqueue_script( 'xrg-rd-admin-main' );
    }
    
    
    /**
     * Create Admin menu on admin panel for settings page
     *
     * @since    0.1
     * @access   public
     * @return   void
     */
    public function xrgCreateAdminMenu(): void
    {
        // Add options / setting page
        add_menu_page('XRG Regional Settings', 'XRG Regions', 'manage_options', 'xrg-regional-settings', [$this, 'xrgRegionsForm'], 'dashicons-admin-site', 2);
    }
    
    /**
     * Create Regional settings form to add locations on admin panel
     *
     * @since    0.1
     * @access   public
     * @return   void
     */
    public function xrgRegionsForm(): void
    {
        // Add options / setting page
        
    ?>
        <h1>Regional Settings Page</h1>
        <form action="options.php" method="post" class="xrg-settings-form">
            <?php 
                settings_fields('xrg_regional_data');
                do_settings_sections('xrg_regional');
                
                $regionalData = get_option( 'xrg_regional_data' );
            ?>
            <div class='regional-data-container'>
                <?php if(empty($regionalData)) : ?>
                    <!-- Default Form Fields  -->
                    <div class='region-container'>
                        <label>Region Name: </label>
                        <input id='xrg_regional_name_$indx' name='xrg_regional_data[0][region_name]' type='text' />
                        <div class='location-container'>
                            <div class="field-container">
                                <label>Location Name: </label>
                                <input name='xrg_regional_data[0][locations][]' type='text' />
                            </div>
                            <div class="button button-secondary add-location-btn" data-current-index="0">New Location</div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php 
                    $indx = 0;
                    foreach ($regionalData as $region) : ?>
                    <div class='region-container'>
                        <label>Region Name: </label>
                        <input name='xrg_regional_data[<?php echo esc_attr($indx); ?>][region_name]' type='text' value='<?php echo esc_attr( $region['region_name'] ); ?>' />
                        <div class="del-region-btn" data-current-index="<?php echo esc_attr($indx); ?>" aria-label="Delete Region" title="Delete Region"></div>
                        <div class='location-container'>
                            <?php foreach($region['locations'] as $location) : ?>
                                <div class="field-container">
                                    <label>Location Name: </label>
                                    <input name='xrg_regional_data[<?php echo esc_attr($indx); ?>][locations][]' type='text' value='<?php echo esc_attr( $location ); ?>' />
                                    <div class="del-location-btn" aria-label="Delete Location" title="Delete Location"></div>
                                </div>
                            <?php endforeach; ?>

                            <div class="button button-secondary add-location-btn" data-current-index="<?php echo esc_attr($indx); ?>">New Location</div>
                            
                            <?php if(empty($region['locations'])) : ?>
                                <div class="field-container">
                                    <label>Location Name: </label><input name='xrg_regional_data[<?php echo esc_attr($indx); ?>][locations][]' type='text' />
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php 
                    $indx++;
                endforeach; 
                ?>
            </div>
            <?php if(!empty($regionalData)) : ?>
                <div class="button button-secondary add-region-btn" data-current-index="<?php echo esc_attr(count($regionalData)); ?>">New Region</div>
            <?php endif; ?>

            <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
        </form>
    <?php
    }

    /**
     * Register settings to add fields in the form
     *
     * @since    0.1
     * @access   public
     * @return   void
     */
    public function xrgRegistersettings(): void
    {
        // Add / Register Sections and Fields
        register_setting('xrg_regional_data', 'xrg_regional_data');
        add_settings_section('xrg_regional_settings', '', [$this, 'xrgSectionText'], 'xrg_regional');
    }
    
    /**
     * Render Section Text
     *
     * @since    0.1
     * @access   public
     * @return   void
     */
    public function xrgSectionText(): void
    {
        echo '<p>Add Regions and thier associated locations</p>';
    }

    /**
     * Add settings adction link under plugin name, display on the plugins list page
     *
     * @since    0.1
     * @access   public
     * @param   $actions array containing default action links
     * @return   array
     */
    public function xrgSettingsActionLink(array $actions): array
    {
        $xrgLink = [
            '<a href="' . admin_url('admin.php?page=xrg-regional-settings') . '" aria-label="XRG Regions setting page">Settings</a>',
        ];

        return array_merge( $actions, $xrgLink );
    }

    /**
     * Get Kpis Data from back-end and send as response
     *
     * @since    0.1
     * @access   public
     * @return   void
     */
    public function xrgKpisData(): void
    {
        $kpisData = XrgRdKpis::instance()->xrgDBInstance()->xrgGetWeeklyData($_POST['xrg_period'], $_POST['xrg_region']);

        if(empty($kpisData)) {
            $response = array('status' => true, 'data_status' => false);
            wp_send_json($response, '200');
        }

        $xrgWeek = XrgHelperFunctions::xrgFormatArrayKeys($_POST['xrg_week']);
        $kpisData = unserialize($kpisData->weekly_kpis_data);

        if(! array_key_exists($xrgWeek, $kpisData)) {
            $response = array('status' => true, 'data_status' => false);
            wp_send_json($response, '200');
        }

        // Generating HTML response
        $weeklyData = $kpisData[$xrgWeek];
        $responseHTML = '';

        foreach($weeklyData['xrg_locations'] as $location) {

            $responseHTML .= '<div class="flex-body">
                        <div class="flex-col-form">
                            <span class="field_val">
                                <input type="text" name="xrg_locations[]" value="' . $location . '" readonly />
                            </span>
                        </div>';

                        $location = XrgHelperFunctions::xrgFormatArrayKeys($location);

                        $responseHTML .= '<div class="flex-col-form">
                            <span class="field_val">
                                <input type="text" name="' . $location . '[net_sales_wtd]" value="' . $weeklyData[$location]['net_sales_wtd'] . '" />
                            </span>
                        </div>
                        <div class="flex-col-form">
                            <span class="field_val">
                                <input type="text" name="' . $location . '[var_bgt_sale]" value="' . $weeklyData[$location]['var_bgt_sale'] . '" />
                            </span> 
                        </div>
                        <div class="flex-col-form">
                            <span class="field_val">
                                <input type="text" name="' . $location . '[net_profit]" value="' . $weeklyData[$location]['net_profit'] . '" />
                            </span> 
                        </div>
                        <div class="flex-col-form">
                            <span class="field_val">
                                <input type="text" name="' . $location . '[var_bgt_net_profit]" value="' . $weeklyData[$location]['var_bgt_net_profit'] . '" />
                            </span> 
                        </div>
                        <div class="flex-col-form">
                            <span class="field_val">
                                <input type="text" name="' . $location . '[theo_food_var]" value="' . $weeklyData[$location]['theo_food_var'] . '" />
                            </span>
                        </div>
                        <div class="flex-col-form">
                            <span class="field_val">
                                <input type="text" name="' . $location . '[theo_liq_var]" value="' . $weeklyData[$location]['theo_liq_var'] . '" />
                            </span>
                        </div>
                        <div class="flex-col-form">
                            <span class="field_val">
                                <input type="text" name="' . $location . '[end_food_inv]" value="' . $weeklyData[$location]['end_food_inv'] . '" />
                            </span>
                        </div>
                        <div class="flex-col-form">
                            <span class="field_val">
                                <input type="text" name="' . $location . '[end_liq_inv]" value="' . $weeklyData[$location]['end_liq_inv'] . '" />
                            </span>
                        </div>
                        <div class="flex-col-form">
                            <span class="field_val">
                                <input type="text" name="' . $location . '[theo_labor_wtd]" value="' . $weeklyData[$location]['theo_labor_wtd'] . '" />
                            </span>
                        </div>
                        <div class="flex-col-form">
                            <span class="field_val">
                                <input type="text" name="' . $location . '[training_pay_wtd]" value="' . $weeklyData[$location]['training_pay_wtd'] . '" />
                            </span>
                        </div>
                        <div class="flex-col-form">
                            <span class="field_val">
                                <input type="text" name="' . $location . '[training_weekly_bgt]" value="' . $weeklyData[$location]['training_weekly_bgt'] . '" />
                            </span>
                        </div>
                    </div>';
        }

        $response = array('status' => true, 'data_status' => true, 'res_data' => $responseHTML);
        wp_send_json($response, '200');
    }

    /**
     * Get Labor forecast data from back-end and send as response
     *
     * @since    0.1
     * @access   public
     * @return   void
     */
    public function xrgLaborData(): void
    {
        $laborData = XrgRdKpis::instance()->xrgDBInstance()->xrgGetWeeklyData($_POST['xrg_period'], $_POST['xrg_region']);

        if(empty($laborData)) {
            $response = array('status' => true, 'data_status' => false);
            wp_send_json($response, '200');
        }

        $xrgWeek = XrgHelperFunctions::xrgFormatArrayKeys($_POST['xrg_week']);
        $laborData = unserialize($laborData->weekly_labor_data);

        if(! array_key_exists($xrgWeek, $laborData)) {
            $response = array('status' => true, 'data_status' => false);
            wp_send_json($response, '200');
        }

        // Generating HTML response
        $weeklyData = $laborData[$xrgWeek];
        $responseHTML = '';

        foreach($weeklyData['xrg_locations'] as $location) {

            $responseHTML .= '<div class="flex-body">
                        <div class="flex-col-form">
                            <span class="field_val">
                                <input type="text" name="xrg_locations[]" value="' . $location . '" readonly />
                            </span>
                        </div>';

                        $location = XrgHelperFunctions::xrgFormatArrayKeys($location);

                        $responseHTML .= '<div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="' . $location . '[forecasted_sales]" value="' . $weeklyData[$location]['forecasted_sales'] . '" />
                        </span>
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="' . $location . '[forecasted_labor]" value="' . $weeklyData[$location]['forecasted_labor'] . '" />
                        </span> 
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="' . $location . '[budgeted_labor]" value="' . $weeklyData[$location]['budgeted_labor'] . '" />
                        </span> 
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="' . $location . '[theo_labor]" value="' . $weeklyData[$location]['theo_labor'] . '" />
                        </span> 
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="' . $location . '[scheduled_leader_hours]" value="' . $weeklyData[$location]['scheduled_leader_hours'] . '" />
                        </span>
                    </div>
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" name="' . $location . '[budgeted_leader_hours]" value="' . $weeklyData[$location]['budgeted_leader_hours'] . '" />
                        </span>
                    </div>
                </div>';
        }

        $response = array('status' => true, 'data_status' => true, 'res_data' => $responseHTML);
        wp_send_json($response, '200');
    }

    /**
     * Get staffing pars data from back-end and send as response
     *
     * @since    0.1
     * @access   public
     * @return   void
     */
    public function xrgStaffData(): void
    {
        $staffingData = XrgRdKpis::instance()->xrgDBInstance()->xrgStaffingParsData($_POST['xrg_region']);
        
        // Staffing Pars Types
        $staffingPars = [
            'Server' => 'Servers', 'Cocktail' => 'Cocktail', 'Host' => 'Host',
            'Bartender' => 'Bar', 'Busser/Runner' => 'Bus', 'Expo' => 'Expo',
            'Line Cook' => 'Cook', 'Prep Cook' => 'Prep', 'Dish' => 'Dish'
        ];

        if(empty($staffingData)) {
            $response = array('status' => true, 'data_status' => false);
            wp_send_json($response, '200');
        }

        $staffingData = unserialize($staffingData->staffing_data);

        // Generating HTML response
        $responseHTML = '';

        foreach($staffingData['xrg_locations'] as $location) {
            $responseHTML .= '<div class="location-staffing-container">
                    <div class="flex-col-form">
                        <span class="field_val">
                            <input type="text" class="location-name" name="xrg_locations[]" value="' . $location . '" readonly />
                        </span>
                    </div>';

                    $location = XrgHelperFunctions::xrgFormatArrayKeys($location);

                    foreach($staffingPars as $staffingPar) {
                        $responseHTML .= '<div class="staffing-type-row">
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
                                            ' . $staffingPar . '
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
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][am][Mon]" value="' . $staffingData[$location][$staffingPar]['am']['Mon'] . '" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][am][Tues]" value="' . $staffingData[$location][$staffingPar]['am']['Tues'] . '" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][am][Wed]" value="' . $staffingData[$location][$staffingPar]['am']['Wed'] . '" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][am][Thurs]" value="' . $staffingData[$location][$staffingPar]['am']['Thurs'] . '" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][am][Fri]" value="' . $staffingData[$location][$staffingPar]['am']['Fri'] . '" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][am][Sat]" value="' . $staffingData[$location][$staffingPar]['am']['Sat'] . '" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][am][Sun]" value="' . $staffingData[$location][$staffingPar]['am']['Sun'] . '" />
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
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][pm][Mon]" value="' . $staffingData[$location][$staffingPar]['pm']['Mon'] . '" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][pm][Tues]" value="' . $staffingData[$location][$staffingPar]['pm']['Tues'] . '" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][pm][Wed]" value="' . $staffingData[$location][$staffingPar]['pm']['Wed'] . '" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][pm][Thurs]" value="' . $staffingData[$location][$staffingPar]['pm']['Thurs'] . '" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][pm][Fri]" value="' . $staffingData[$location][$staffingPar]['pm']['Fri'] . '" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][pm][Sat]" value="' . $staffingData[$location][$staffingPar]['pm']['Sat'] . '" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form">
                                            <span class="field_val">
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][pm][Sun]" value="' . $staffingData[$location][$staffingPar]['pm']['Sun'] . '" />
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-body-right-content">
                                        <!-- Total Par Field -->
                                        <div class="flex-col-form par-type-total-label">
                                            <span class="field_label">
                                                ' . $staffingPar . ' In Training
                                            </span>
                                        </div>
                                        <div class="flex-col-form par-type-total-val">
                                            <span class="field_val">
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][in_training]" value="' . $staffingData[$location][$staffingPar]['in_training'] . '" />
                                            </span>
                                        </div>
                                        <div class="flex-col-form par-type-total-label">
                                            <span class="field_label">
                                                Total ' . $staffingPar . '
                                            </span>
                                        </div>
                                        <div class="flex-col-form par-type-total-val">
                                            <span class="field_val">
                                                <input type="text" name="' . $location . '[' . $staffingPar . '][total]" value="' . $staffingData[$location][$staffingPar]['total'] . '" />
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
            $responseHTML .= '<div class="staffing-type-row">   <!--  Max Table to Seat  -->
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
                                        <input type="text" name="' . $location . '[max_tables]" value="' . $staffingData[$location]['max_tables'] . '" />
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }

        $response = array('status' => true, 'data_status' => true, 'res_data' => $responseHTML);
        wp_send_json($response, '200');
    }

}
