<?php

namespace Slashworks\SwCalc\Modules;

use Contao\BackendTemplate;
use Contao\Environment;
use Contao\System;
use Slashworks\SwCalc\Models\Configuration;
use Slashworks\SwCalc\Models\Label;

/**
 * Class ProductList
 * @package Slashworks\SwCalc\Modules
 */
class ClosedShop extends \Module
{

    /**
     * The template name.
     *
     * @var string
     */
    protected $strTemplate = 'mod_closed';


    /**
     * @return string
     */
    public function generate()
    {
        if (TL_MODE === 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### Closed Shop ###';
            return $objTemplate->parse();
        }

        global $objPage;
        $objPage->customPageViewUrl = Environment::get('path') . '/shop-deaktiviert.html';


        return parent::generate();
    }


    /**
     * Parse the template.
     */
    protected function compile()
    {

        try {

            $oConfiguration = Configuration::findById(1);

            if ($oConfiguration->active){
                \Controller::redirect(\Environment::get('base'));
            }

            $config = \Slashworks\SwCalc\Models\Configuration::getActive();
            $GLOBALS['TL_CALC']['fallbackPostal'] = $config->fallbackPostal;
            $oPricingRow = \Slashworks\SwCalc\Models\Pricing::findByPostal($GLOBALS['TL_CALC']['fallbackPostal']);
            $team = \Slashworks\SwCalc\Models\Label::getTeamMember($oPricingRow);

            $this->Template->message = str_replace(['##team##'], [$team], Label::getLabel('checkout.step4.closed'));

            $this->Template->additionalText = Label::getLabel('checkout.step4.order.additionalText');

            $sCtaLink = Label::getLabel('checkout.step4.order.cta.link');
            $sCtaText = Label::getLabel('checkout.step4.order.cta.text');

            if (empty($sCtaText)) {
                $sCtaText = $sCtaLink;
            }

            $this->Template->ctaLink = $sCtaLink;
            $this->Template->ctaText = $sCtaText;



        } catch (\Exception $e) {
            System::log($e->getMessage() . '. In "' . $e->getFile() . '" on line ' . $e->getLine(), __METHOD__, TL_ERROR);
        }

    }
}