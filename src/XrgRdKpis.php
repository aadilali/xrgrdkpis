<?php

declare(strict_types=1);

// -*- coding: utf-8 -*-

namespace XRG\RD;

/**
 * Class XrgRdKpis
 *
 * @package XRG\RD
 */

class XrgRdKpis
{

    /**
     * @return XrgRdKpis
     *
     */

    public static function instance(): self
    {
        static $instance;
        if (! $instance) {
            $instance = new self();
            $instance->init();
        }

        return $instance;
    }

    /**
     * Initialize
     * @access private
     */

    private function init()
    {
        if (wp_installing()) {
            return;
        }

        $this->xrgRdKpisInstances('rd-entry-sheet', 'xrg-data-collection-template');
        $this->xrgRdKpisInstances('rd-view-sheet', 'xrg-data-view-template');

        // File upload endpoint
        $this->xrgRdKpisInstances('rd-file-upload', 'xrg-original-file-upload-template');
        
        // Enqueue Style and Scripts
        add_action( 'wp_enqueue_scripts', [$this, 'xrgLoadScripts'] );
    }

    /**
     * Create XrgKpisPageSettings instances
     *
     * @since    0.1
     * @access   public
     * @param string $endPoint arbitrary URL endpoint
     * @param string $templateFile name of template file to load
     * @return XrgKpisPageSettings
     */

    public function xrgRdKpisInstances(string $endPoint, string $templateFile): XrgKpisPageSettings
    {
        return new XrgKpisPageSettings($endPoint, $templateFile);
    }

    /**
     * Create XrgKpisReadSheet instances
     *
     * @since    0.1
     * @access   public
     * @return XrgKpisReadSheet
     */

    public function xrgLoadSpreadSheet(): XrgKpisReadSheet
    {
        return new XrgKpisReadSheet();
    }

    /**
     * Create XrgKPIsDB instances
     *
     * @since    0.1
     * @access   public
     * @return XrgKPIsDB
     */

    public function xrgDBInstance(): XrgKPIsDB
    {
        return new XrgKPIsDB();
    }

    /**
     * Enqueue scripts and styles
    */
    public function xrgLoadScripts() {
        wp_register_style( 'xrg-rd-kpis', XRG_PLUGIN_URI.'assets/css/xrg-rd-kpis-style.css' );
        wp_enqueue_style( 'xrg-rd-kpis' );

        wp_register_script('xrg-rd-kpis-main', XRG_PLUGIN_URI.'assets/js/xrg-rd-kpis-main.js', ['jquery'], '0.1', true);
        wp_enqueue_script( 'xrg-rd-kpis-main' );
    }

}
