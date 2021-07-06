<?php
/*
Plugin Name: XRG RD KPIs
Plugin URI: 
Description: Plugin to add the functionality for GMs to enter weekly forecast data into excel sheet.
Version: 0.1
Author: Adil Ali
Author URI: https://www.presstigers.com
*/


namespace XRG\RD;

define('PLUGIN_PATH', plugin_dir_path( __FILE__ ));

if (!class_exists(XrgRdKpis::class) && is_readable(__DIR__.'/vendor/autoload.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once __DIR__.'/vendor/autoload.php';
}


class_exists(XrgRdKpis::class) && XrgRdKpis::instance();
