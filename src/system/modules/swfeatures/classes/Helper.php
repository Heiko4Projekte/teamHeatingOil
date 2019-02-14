<?php

namespace Slashworks\Swfeatures\Classes;

/**
 * Provide several helper functions for easier access to general settings and options.
 *
 * Class Helper
 * @package Slashworks\Swfeatures\Classes
 */
class Helper
{

    public static function getGridColumns()
    {
        return array
        (
            'u_3',
            'u_4',
            'u_5',
            'u_6',
            'u_8',
            'u_9'
        );
    }


    public static function generateGridCssClasses($oElement)
    {
        $sReturn = '';
        $aGridClasses = array();

        if ($oElement->gridColumns) {
            $aGridClasses[] = $oElement->gridColumns;
        } else {
            $aGridClasses[] = 'u_col';
        }

        if ($oElement->gridClear) {
            $aGridClasses[] = 'u_clear';
        }

        if (!empty($aGridClasses)) {
            $sReturn = implode(' ', $aGridClasses);
        }

        return $sReturn;
    }

}