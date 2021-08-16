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
        
        // Replace space with _ (underscore) in multi word array key remove special characters
        $arrayKey = preg_replace( '/\'+/','', $arrayKey);
        $arrayKey = preg_replace( '/\-+/','', $arrayKey);
        $arrayKey = preg_replace( '/\,+/','', $arrayKey);

        return preg_replace( '/\s+/','_', $arrayKey);
    }

    /**
     * Return formatted value
     * @since    0.1
     * @access   public
     * @param string $xrgNumber
     * @param string $xrgFormat available formats are currency | percentage | variance | abs-numeric
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
        } elseif($xrgFormat === 'abs-numeric') {
            $formattedNumber = ($xrgNumber < 0) ? '<span class="color-red">'. abs($xrgNumber) .'</span>' : '<span>'. $xrgNumber .'</span>';
        } elseif($xrgFormat === 'variance') {
            $formattedNumber = ($xrgNumber < 0) ? '<span class="color-red">('. abs($xrgNumber) .')</span>' : '<span>'. $xrgNumber .'</span>';
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

    /**
     * Save file to specific folder uploaded from front-end
     * @since    0.1
     * @access   public
     * @param array $pObj type of array data submitted through form
     * @return string bool file uploaded or not
     */ 
    public static function xrgHandleFileUpload(array $pObj): bool
    {
        // Move and rename upload file to /original-file/xrg-original-sheet-data.xlsx directory
        $allowed = ['xlsx'];
        $ext = pathinfo($pObj['xrg_file']['name'], PATHINFO_EXTENSION);
        if (!in_array($ext, $allowed)) {
            return false;
        }

        return move_uploaded_file($pObj['xrg_file']['tmp_name'], XRG_PLUGIN_PATH . 'original-file/xrg-original-sheet-data.xlsx');
    }

    /**
     * Add Two identical arrays value as per key name
     * @since    0.1
     * @access   public
     * @param array $originalArray array have value to be sum
     * @param array $sumArray array after sum the array values on respective indexes (keys)
     * @return array resulted sum array
     */ 
    public static function xrgSumKeysValue(array $originalArray, array $sumArray): array
    {
        foreach($originalArray['am'] as $key => $val) {
            $sumArray['am'][$key] += $val;
            $sumArray['pm'][$key] += $originalArray['pm'][$key];
        }

        return $sumArray;
    }

    /**
     * Create formula based on locations and index
     * @since    0.1
     * @access   public
     * @param string $index current index of cell
     * @return array resulted formula string
     */ 
    public static function xrgGenerateFoumula(string $index, $locations): string
    {
        $resultedArray = [];
        $resultedArray = array_map(function($location) use ($index) {
            $location = (count(explode(" ", $location)) > 1) ? '\'' . $location . '\'' : $location;
            return $location . '!' . $index;
        }, $locations);

        return (implode(',', $resultedArray));
    }

    /**
     * Get Staff count from array based on type and location
     * @since    0.1
     * @access   public
     * @param string $type type of staff job
     * @param array $staffData array containing data
     * @return int $staffCount
     */ 
    public static function xrgCountStaffByType(string $type, array $staffData): int
    {
        $staffCount = 0;

        foreach($staffData as $staff) {
            if($staff === $type) {
                $staffCount++;
            }
        }

        return $staffCount;
    }

    /**
     * Return user status, login with GM or Admin Role
     * @since    0.1
     * @access   public
     */ 
    public static function xrgIsUserAllowed()
    {
        
        $roles = [];

        if( is_user_logged_in() ) {

           $user = wp_get_current_user();
           $roles = ( array ) $user->roles;
           if(in_array('administrator', $roles) || in_array('general-manager', $roles)) {
               return true;
            }
        } 

        return false;
    }

}
