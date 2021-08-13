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
        wp_register_style( 'xrg-rd-kpis', XRG_PLUGIN_URI.'assets/css/xrg-rd-kpis-style.css' );
        wp_enqueue_style( 'xrg-rd-kpis' );

        wp_register_script('xrg-rd-kpis-main', XRG_PLUGIN_URI.'assets/js/xrg-rd-kpis-main.js', ['jquery'], '0.1', true);
        wp_enqueue_script( 'xrg-rd-kpis-main' );
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

}
