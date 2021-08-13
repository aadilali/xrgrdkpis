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

        // Initialize General Settings Class
        $this->xrgGeneralInstance();

        // Load XrgTemplatePage intance
        $this->xrgTemplatePageInstance()->xrgHandleHooks();
    }

    /**
     * Create XrgKpisPageSettings instances
     *
     * @since    0.1
     * @access   public
     * @param string $endPoint arbitrary URL endpoint
     * @param string $templateFile name of template file to load
     * @return void
     */

    public function xrgRdKpisInstances(string $endPoint, string $templateFile): void
    {
        // Create new Instance and Register hooks
        $settingPageInstance = new XrgKpisPageSettings($endPoint, $templateFile);
        $settingPageInstance->xrgHandleHooks();
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
     * Initialize XrgGeneralSettings class
     *
     * @since    0.1
     * @access   public
     * @return void
     */
    public function xrgGeneralInstance(): void
    {
        $generalSettings = new XrgGeneralSettings();
        $generalSettings->xrgHandleHooks();
    }

    /**
     * Get XrgTemplatePage instances
     *
     * @since    0.1
     * @access   public
     * @return XrgTemplatePage
     */
    public function xrgTemplatePageInstance(): XrgTemplatePage
    {
        return new XrgTemplatePage();
    }
}
