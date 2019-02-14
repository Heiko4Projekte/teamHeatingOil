<?php

namespace Slashworks\SwCalc\Hooks;

use Contao\System;
use Slashworks\SwCalc\Classes\Formatter;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Models\Pricing;
use Slashworks\SwCalc\Models\Product;
use Slashworks\SwCalc\Models\ProductConfiguration;

class GetAttributesFromDca
{

    /**
     * Hook to add prices for several attributes, e. g. antifreeze and hose.
     *
     * @param $aAttributes
     * @param $oDca
     *
     * @return mixed
     */
    public function includePricesInLabels($aAttributes, $oDca)
    {
        try {


            if ($aAttributes['addPriceToLabel']) {
                $sFieldName = ($aAttributes['originalField']) ? $aAttributes['originalField'] : $aAttributes['name'];

                $aOptionValues = array();
                foreach ($aAttributes['options'] as $i => $aValueLabel) {
                    $aOptionValues[] = $aValueLabel['value'];
                }

                if ($sFieldName === 'antifreeze') {
                    $aPrices = Pricing::addPriceToAntifreezeLabel($aOptionValues);
                    $aAttributes = $this->addPricesToLabels($aAttributes, $aPrices);
                } else if ($sFieldName === 'hose') {
                    $aPrices = Pricing::addPriceToHoseLabel($aOptionValues);
                    $aAttributes = $this->addPricesToLabels($aAttributes, $aPrices);
                } else if ($sFieldName === 'oilType') {
                    $aPrices = Pricing::addPriceToOilTypeLabel($aOptionValues);
                    $aAttributes = $this->addPricesAsSeparateValues($aAttributes, $aPrices);
                } else if ($sFieldName === 'shipping') {
                    $aPrices = Pricing::addPriceToShippingLabel($aOptionValues);
                    $aAttributes = $this->addPricesAsSeparateValues($aAttributes, $aPrices);
                } else if ($sFieldName === 'payment') {
                    $aPrices = Pricing::addPriceToPaymetLabel($aOptionValues);
                    $aAttributes = $this->addPricesAsSeparateValues($aAttributes, $aPrices);
                }
            }

            return $aAttributes;
        } catch (\Exception $exception) {
            System::log($exception->getMessage() . '. In "' . $exception->getFile() . '" on line ' . $exception->getLine(),
                __METHOD__, TL_ERROR);
        }
    }

    /**
     * Add prices as a separate value and not at the end of the label.
     *
     * @param $aAttributes
     * @param $aPrices
     *
     * @return mixed
     */
    protected function addPricesAsSeparateValues($aAttributes, $aPrices)
    {
        $i = 0;
        foreach ($aAttributes['options'] as $aValueLabel) {
            if (isset($aPrices[$aValueLabel['value']])) {
                $aAttributes['options'][$i]['price']['net'] = Formatter::formatPriceWithCurrencyAndSign($aPrices[$aValueLabel['value']]['total']['net']);
                $aAttributes['options'][$i]['price']['gross'] = Formatter::formatPriceWithCurrencyAndSign($aPrices[$aValueLabel['value']]['total']['gross']);
            }
            $i++;
        }

        return $aAttributes;
    }

    /**
     * Add prices to the end of labels.
     *
     * @param $aAttributes
     * @param $aPrices
     *
     * @return mixed
     */
    protected function addPricesToLabels($aAttributes, $aPrices)
    {
        $i = 0;
        foreach ($aAttributes['options'] as $aValueLabel) {
            if (isset($aPrices[$aValueLabel['value']])) {
                $aAttributes['options'][$i]['label'] = $aValueLabel['label'] . ' (' . Formatter::formatPriceWithCurrencyAndSign($aPrices[$aValueLabel['value']]['total']['gross']) . ')';
            }
            $i++;
        }

        return $aAttributes;
    }

}
