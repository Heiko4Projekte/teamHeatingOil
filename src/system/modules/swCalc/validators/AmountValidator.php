<?php

namespace Slashworks\SwCalc\Validators;

use Contao\Widget;
use Haste\Form\Validator\ValidatorInterface;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Models\Pricing;

class AmountValidator implements ValidatorInterface
{

    /**
     * Check if the value is a valid postal and the postal is defined in the pricing table.
     *
     * @param mixed $sValue
     * @param \Widget $oWidget
     * @param \Haste\Form\Form $oForm
     * @return mixed
     */
    public function validate($sValue, $oWidget, $oForm)
    {
        if (!preg_match('/^[0-9]/', $sValue)) {
            $oWidget->addError(sprintf($GLOBALS['TL_LANG']['ERR']['amount']['invalid'], Pricing::getMinAmount(), number_format(Pricing::getMaxAmount(), 0, '', '.')));
        } else if ((int) $sValue < Pricing::getMinAmount() || (int) $sValue > Pricing::getMaxAmount()) {
            $oWidget->addError(sprintf($GLOBALS['TL_LANG']['ERR']['amount']['outOfScope'], Pricing::getMinAmount(), number_format(Pricing::getMaxAmount(), 0, '', '.')));
        }

        return $sValue;
    }

}