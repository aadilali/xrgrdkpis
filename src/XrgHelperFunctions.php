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


final class XrgHelperFunctions
{
    
    private function __construct()
    {

    }
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

    /**
     * Return formatted value
     * @since    0.1
     * @access   public
     * @param float $xrgNumber
     * @param string $xrgFormat available formats are currency | percentage
     * @return string Return formatted value as a string 
     */ 
    public static function xrgFormatValue(float $xrgNumber, string $xrgFormat): string
    {
        $formattedNumber = '';

        if(!is_numeric($xrgNumber) ) {
            return (string) $xrgNumber;
        }
        
        if($xrgFormat === 'currency') {
            if (substr(strval($xrgNumber), 0, 1) == "-") {
                $formattedNumber = '<span class="color-red">($'. number_format($xrgNumber, 2, '.' ,',') .')</span>';
            } else {
                $formattedNumber = '<span>$'. number_format($xrgNumber, 2, '.' ,',') .'</span>';
            }
        } elseif($xrgFormat === 'percentage') {
            $formattedNumber = '<span>'. number_format($xrgNumber, 2, '.' ,',') .'%</span>';
        } else {
            return (string) $xrgNumber;
        }

        return $formattedNumber;
    }
}
