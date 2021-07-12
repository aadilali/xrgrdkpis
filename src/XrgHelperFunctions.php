<?php

declare(strict_types=1);

// -*- coding: utf-8 -*-

namespace XRG\RD;

/**
 * Class XrgHelperFunctions
 *
 * Responsible for shared helper functions
 *
 * @package XRG\RD
 */


class XrgHelperFunctions
{
/**
     * Format array keys, convert keys to lower case and replace spaces with underscores
     *
     * @since    0.1
     * @access   public
     * @param   string $arrayKey key of array 
     * @return   string
     */
    public static function xrgFormatArrayKeys(string $arrayKey): string
    {
        // Convert array key to lower case 
        $arrayKey = strtolower($arrayKey);
        
        // Replace space with _ (underscore) in multi word array key
        return preg_replace( '/\s+/','_', $arrayKey);
    }
}
