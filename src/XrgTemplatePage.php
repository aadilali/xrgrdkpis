<?php

declare(strict_types=1);

// -*- coding: utf-8 -*-

namespace XRG\RD;

/**
 * Class XrgTemplatePage
 *
 * Responsible for creating, loading templates and page.
 *
 * @package XRG\RD
 */


class XrgTemplatePage
{
    /**
     * register callbacks against hooks.
     * @since    0.1
     * @access   public
     * @return   void
     * 
     */
    public function xrgHandleHooks(): void
    {
        // Add shortcode to load default template [xrg-regions-list]
        add_shortcode('xrg-regions-list', [$this, 'xrgRegionsList']);

        add_filter('template_include', [$this, 'xrgLoadTemplate'], 99);
    }

    /**
     * Locate template.
     *
     * Locate the called template.
     * Search Order:
     * 1. /child-themes/$template_name
     * 2. /parent-themes/$template_name
     * 3. /plugins/xrgrdkpis/templates/$template_name.
     *
     * @since    0.1
     * @access   public
     * @param   string 	$templateName			Template to load.
     * @return   string 						Path to the template file.
     */
    public function xrgLocateTemplate(string $templateName): string
    {
        // Set default plugin templates path.
         $defaultPath = XRG_PLUGIN_PATH . 'templates/'; // Path to the template folder

        // Search template file in theme folder.
        $template = locate_template($templateName . '.php');

        // Get plugins template file.
        if (! $template) {
            $template = $defaultPath . $templateName . '.php';
        }

        return apply_filters( 'xrg_locate_template', $template, $templateName, $defaultPath );
    }

    
    public function xrgLoadTemplate($template)
    {
        if (is_page('xrg-regions-list')) {

            $templateFile = $this->xrgLocateTemplate('xrg-regions-list-tpl');

            try {
                if ( !file_exists( $templateFile ) ) {
                    throw new \Exception('File template does not exist!');
                }

                // Load template file
                return $templateFile;
            } catch (\Exception $error) {
                echo $error->getMessage();
            }
        }

    return $template;
}

    /**
     * Load Regions list template through shortcode
     *
     * from the /templates/ folder.
     *
     * @since    0.1
     * @access   public
     * @return   string
     */
    public function xrgRegionsList(): string
    {
        // $templateFile = $this->xrgLocateTemplate('xrg-regions-list-tpl.php');

        // try {
        //     if ( ! file_exists( $templateFile ) ) {
        //         throw new \Exception('File template does not exist!');
        //     }
        // } catch (\Exception $error) {
        //     return $error->getMessage();
        // }

        // // Load template file
        // load_template($templateFile);
        return 'THIS IS TEST PAGE';
    }

    /**
     * Load Regions list template parts
     *
     * from the /templates/ folder.
     *
     * @since    0.1
     * @access   public
     * @param   string  $slug
     * @param   string  $name
     */
    public function xrgTemplatePart(string $slug, string $name)
    {
         $templateFile =  $this->xrgLocateTemplate($slug . '-' . $name);

        try {
             if ( ! file_exists( $templateFile ) ) {
                 throw new \Exception('File template does not exist!');
             }
         } catch (\Exception $error) {
             echo $error->getMessage();
         }

        // Load template file
        load_template($templateFile);
    }

    /**
     * Create default page to display the output
     * 
     * of [xrg-regions-list] shortcode
     *
     * @since    0.1
     * @access   public
     * @return   void
     */
    public function xrgCreateDefaultPage(): void
    {
        // Check if page is already exist
        $pageObj = get_page_by_title('XRG Regions List', 'OBJECT', 'page');
        if(! empty($pageObj)) {
            return;
        }

        // Create new page
        $pageDetails = [
            'post_title'    => 'XRG Regions List',
            'post_content'  => '[xrg-regions-list]',
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_type' => 'page'
        ];
        
        // Create the post of type page
        wp_insert_post( $pageDetails );
    }

}
