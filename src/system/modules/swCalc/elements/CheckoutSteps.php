<?php

namespace Slashworks\SwCalc\Elements;

use Contao\ContentElement;
use Contao\Session;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Validators\AppValidator;

class CheckoutSteps extends ContentElement
{

    protected $strTemplate = 'ce_checkout_steps';

    public function generate()
    {
        // Hide checkout steps when no product has been selected.
        if (TL_MODE === 'FE') {
            $oValidator = new AppValidator();
            if (!$oValidator->validateCheckoutStepVisibility()) {
                return '';
            }
        }

        return parent::generate();
    }

    protected function compile()
    {
        $aSteps = array();
        $aCheckoutSteps = $GLOBALS['TL_CALC']['checkoutSteps'];

        foreach ($aCheckoutSteps as $iIndex => $sCheckoutStep) {
            $aNewStep = array();
            $sClass = '';
            $aNewStep['index'] = $iIndex;
            $aNewStep['name'] = $GLOBALS['TL_LANG']['checkoutSteps'][$sCheckoutStep];

            if ($iIndex == $this->activeCheckoutStep) {
                $sClass = 'active';
            } else if ($iIndex < $this->activeCheckoutStep) {
                $sClass = 'passed';
            }

            $aNewStep['class'] = $sClass;
            $aSteps[] = $aNewStep;
        }

        $this->Template->steps = $aSteps;
    }

}