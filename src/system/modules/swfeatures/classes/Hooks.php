<?php

namespace Slashworks\Swfeatures\Classes;

use Contao\Frontend;

class Hooks extends Frontend
{

    /**
     * Wrap content elements inside grid-class container if a grid column option is set.
     *
     * @param $oElement
     * @param $sBuffer
     * @return string
     */
    public function addGridWrapperToContentElements($oElement, $sBuffer)
    {

        $aWrapperClasses = array();

        if ($oElement->gridExclude) {
            $aWrapperClasses[] = 'no_col';
        } else {
            if ($oElement->gridColumns) {
                $aWrapperClasses[] = $oElement->gridColumns;
            } else {
                $aWrapperClasses[] = 'u_col';
            }

            if ($oElement->gridIndentation) {
                $aWrapperClasses[] = $oElement->gridIndentation;
            }

            if ($oElement->gridClear) {
                $aWrapperClasses[] = 'u_clear';
            }
        }

        if (!empty($aWrapperClasses)) {

            if (stripos($oElement->type, 'start') !== false) {
                $sBuffer = '<div class="' . implode(' ', $aWrapperClasses) . '">' . $sBuffer;
            } else if (stripos($oElement->type, 'stop') !== false) {
                $sBuffer = $sBuffer . '</div>';
            } else {
                $sBuffer = '<div class="' . implode(' ', $aWrapperClasses) . '">' . $sBuffer . '</div>';
            }

        }

        return $sBuffer;

    }

}