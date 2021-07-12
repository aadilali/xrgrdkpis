<?php

declare(strict_types=1);

// -*- coding: utf-8 -*-

namespace XRG\RD;

/**
 * Class XrgKpisPageSettings
 *
 * Responsible for creating arbitrary URLs and load templates.
 *
 * @package XRG\RD
 */


class XrgKpisPageSettings
{
    /**
     * @var string
     */
    private $xrgEndpoint;

    /**
     * @var string
     */
    private $xrgTemplateName;
    
    /**
     * Initialize the class, set its properties and register callbacks against hooks.
     * @since    0.1
     */
    public function __construct($endpoint, $templateName)
    {
        $this->xrgEndpoint = $endpoint;
        $this->xrgTemplateName = $templateName;

        add_action('generate_rewrite_rules', [$this, 'xrgEndpointRule']);
        add_filter('template_include', [$this, 'xrgLoadTemplate'], 20);
        add_filter('query_vars', [$this, 'registerQueryVars']);

        // Flush Parmalink Cache
        register_activation_hook( XRG_PLUGIN_PATH.'xrg-rd-kpis.php', [$this, 'xrgParmalinkOption'] );
        register_deactivation_hook( XRG_PLUGIN_PATH.'xrg-rd-kpis.php', [$this, 'xrgParmalinkOption'] );
    }

    /**
     * Create re-write rule for arbitrary url.
     *
     * @since    0.1
     * @access   public
     * @return    void
     */
    public function xrgEndpointRule(): void
    {
        /** 
         * @global WP_Rewrite $wp_rewrite 
         * 
         */

        global $wp_rewrite;
     
        $custom_rule["$this->xrgEndpoint/?$"] = "index.php?xrg_kpi_page=".$this->xrgEndpoint;
        $wp_rewrite->rules = $custom_rule + (array) $wp_rewrite->rules;

        if( !get_option('plugin_permalinks_flushed') ) {
            //flush_rewrite_rules(true);
            //$wp_rewrite->flush_rules( true );
            update_option('plugin_permalinks_flushed', 1);
        }
    }

    /**
     * Load custom template file, callback against template_include hook.
     *
     * @since    0.1
     * @access   public
     * @param string $template default template name
     * @return    string
     */
    public function xrgLoadTemplate(string $template): string
    {
        $page = get_query_var('xrg_kpi_page');

        if ($page === $this->xrgEndpoint) {
            $template = XRG_PLUGIN_PATH . 'views/' . $this->xrgTemplateName . '.php';
        }

        return $template;
    }

    /**
     * Register custom query vars
     *
     * @since    0.1
     * @access   public
     * @param array $vars The array of available query variables
     * @return array
     *
     */
    public function registerQueryVars(array $vars): array
    {
        $vars[] = 'xrg_kpi_page';
        return $vars;
    }

    /**
     * Update option settings against parmalink flush cache
     *
     * @since    0.1
     * @access   public
     * @return void
     *
     */
    public function xrgParmalinkOption(): void
    {
        update_option('plugin_permalinks_flushed', 0);
    }
}
