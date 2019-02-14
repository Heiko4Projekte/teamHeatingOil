<?php

namespace Slashworks\SwCalc\Helper;

use Contao\FrontendTemplate;
use Slashworks\SwCalc\Models\Pricing;

/**
 * Generate several script templates.
 *
 * Class JavascriptHelper
 * @package Slashworks\SwCalc\Helper
 */
class JavascriptHelper
{

    /**
     * Generate the script for tooltips.
     *
     * @return string
     */
    public function getTooltipScript()
    {
        $oTemplate = new FrontendTemplate('j_tooltip');
        $oTemplate->amountMin = Pricing::getMinAmount();
        $oTemplate->amountMax = Pricing::getMaxAmount();

        return $oTemplate->parse();
    }

}