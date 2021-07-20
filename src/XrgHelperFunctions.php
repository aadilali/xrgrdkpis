<?php

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
     * @param string $xrgNumber
     * @param string $xrgFormat available formats are currency | percentage
     * @return string Return formatted value as a string 
     */ 
    public static function xrgFormatValue(string $xrgNumber, string $xrgFormat): string
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

    /**
     * Return filled array with default keys and value
     * @since    0.1
     * @access   public
     * @param string $type type of array data, available types kpis | labor
     * @return string Return filled keys array with default value
     */ 
    public static function xrgFillLocationKeys(string $type): array
    {
        if($type === 'kpis') {
            $tempKeys = ['net_sales_wtd', 'var_bgt_sale', 'net_profit', 'var_bgt_net_profit', 'theo_food_var', 'theo_liq_var', 'end_food_inv', 'end_liq_inv', 'theo_labor_wtd', 'training_pay_wtd', 'training_weekly_bgt', 'difference'];
            return array_fill_keys($tempKeys, 0);
        }
    }
}
