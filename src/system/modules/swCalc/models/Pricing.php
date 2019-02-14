<?php

namespace Slashworks\SwCalc\Models;

use Contao\Database;
use Contao\Model;
use Haste\Number\Number;

/**
 * Class Pricing
 * @package Slashworks\SwCalc\Models
 */
class Pricing extends Model
{

    /**
     * Table name
     *
     * @var string
     */
    protected static $strTable = 'pricing';


    /**
     * @param string $sPostal
     * @return bool|static
     * @throws \Exception
     */
    public static function findByPostal($sPostal = '')
    {

        if (empty($sPostal)) {

            $config = \Slashworks\SwCalc\Models\Configuration::getActive();
            $GLOBALS['TL_CALC']['fallbackPostal'] = $config->fallbackPostal;
            $sPostal = (Collection::getCurrent()->postal) ? Collection::getCurrent()->postal : $GLOBALS['TL_CALC']['fallbackPostal'];
        }

        $oPricing = static::findOneBy('postal', $sPostal);

        return $oPricing;

    }

    public static function hasPostal($sPostal)
    {
        return (null !== static::findByPostal($sPostal));
    }

    /**
     * Return true, if the pricing table contains a specific amount column.
     * Otherwise return false.
     *
     * @param $sAmount
     * @return bool
     */
    public function hasAmountField($sAmount)
    {
        if ($this->{$sAmount} === null) {
            return false;
        }

        return true;
    }

    /**
     * Get the minimum value of the amount columns.
     *
     * @return mixed|void
     */
    public static function getMinAmount()
    {
        $objConf =  \Slashworks\SwCalc\Models\Configuration::getActive();
        return $objConf->minAmount;
    }

    /**
     * Get the maximum value of the amount columns.
     *
     * @return mixed|void
     */
    public static function getMaxAmount()
    {
        $objConf =  \Slashworks\SwCalc\Models\Configuration::getActive();

        return $objConf->maxAmount;
    }

    public static function getShippingDaysByShipping($sShipping)
    {
        $sColumn = $sShipping . 'Definition';

        $oPricing = static::findByPostal();

        return $oPricing->{$sColumn};
    }

    /**
     * Get the purchasePrice column value.
     *
     * @return float
     */
    public function getPurchasePrice()
    {
        return Number::create($this->purchasePrice)->getAsFloat();
    }


    /**
     * Get the price portion for a specific amount.
     * If a amount column exists, return its value.
     * Otherwise return an interpolated value between two columns.
     *
     * @param $sAmount
     * @return float|int|null
     */
    public function getPriceForAmount($sAmount)
    {
        if ($this->hasAmountField($sAmount)) {
            // Take amount surcharge directly from product property.
            $fAmountSurcharge = Number::create($this->$sAmount)->getAsFloat();
        } else {
            // Interpolate amount surcharge between two product properties.
            $fAmountSurcharge = $this->getInterpolatedValueBetweenTwoColumns($sAmount);
        }

        return $fAmountSurcharge;
    }

    /**
     * Get the price portion for a shipping type.
     *
     * @param $sShipping
     * @return float|int
     */
    public function getPriceForShipping($sShipping)
    {
        if ($sShipping === 'default') {
            return 0;
        }

        return Number::create($this->$sShipping)->getAsFloat();
    }

    /**
     * Get the price portion for a payment type.
     *
     * @param $sPayment
     * @return float|int
     */
    public function getPriceForPayment($sPayment)
    {
        if ($sPayment === 'default') {
            return 0;
        }

        return Number::create($this->$sPayment)->getAsFloat();
    }

    /**
     * Get the price portion for a hose type.
     *
     * @param $sHose
     * @return float|int
     */
    public function getPriceForHose($sHose)
    {
        if ($sHose === 'default') {
            return 0;
        }

        return Number::create($this->$sHose)->getAsFloat();
    }

    /**
     * Get the price portion for an oil type.
     *
     * @param $sOilType
     * @return float|int
     */
    public function getPriceForOilType($sOilType)
    {
        if ($sOilType === 'default') {
            return 0;
        }

        return Number::create($this->$sOilType)->getAsFloat();
    }

    /**
     * Get the price portion for a tanker type.
     *
     * @param $sTanker
     * @return float|int
     */
    public function getPriceForTanker($sTanker)
    {
        if ($sTanker === 'default') {
            return 0;
        }

        if (!$this->$sTanker) {
            return 0;
        }

        return Number::create($this->$sTanker)->getAsFloat();
    }

    /**
     * Get the price portion of the adr column.
     *
     * @return float|int
     */
    public function getPriceForAdr()
    {
        $oCollection = Collection::getCurrent();
        return Number::create($this->adrFlat)->getAsFloat() * (int)$oCollection->unloadingPoints;
    }

    /**
     * Get the price portion of the antifreeze column.
     *
     * @return int
     */
    public function getPriceForAntifreeze($iAntifreeze)
    {
        if ($iAntifreeze == 0) {
            return 0;
        }

        if (!$this->antifreeze) {
            return 0;
        }

        return Number::create($this->antifreeze)->getAsFloat() * $iAntifreeze;
    }

    /**
     * Get all table columns from the pricing table.
     *
     * @return array
     */
    public function getTableColumns()
    {
        $aReturn = array();
        $aDbColumns = Database::getInstance()->listFields(static::$strTable);

        foreach ($aDbColumns as $aDbColumn) {
            // Skip columns of type 'index'.
            if ($aDbColumn['type'] === 'index') {
                continue;
            }

            $aReturn[] = $aDbColumn['name'];
        }

        return $aReturn;
    }

    /**
     * Get interpolated value between two sequential amount fields.
     *
     * @param $sAmount
     * @return float|int|null
     */
    public function getInterpolatedValueBetweenTwoColumns($sAmount)
    {
        $iAmount = (int)$sAmount;

        // Get all table columns from the product table.
        $aColumns = $this->getTableColumns();
        $iMinKey = null;
        $iMaxKey = null;

        // Amount = database column, e. g. 500, 1000, 2000, 5000
        $iMinAmount = null;
        $iMaxAmount = null;

        // Value = database column values, e. g. 12.00, 6.5, 3.6
        $iMinValue = null;
        $iMaxValue = null;

        // The interpolated value.
        $fInterpolatedValue = 0;

        // Remove all columns that are not numeric.
        foreach ($aColumns as $i => $sColumnName) {
            if (!is_numeric($sColumnName)) {
                unset($aColumns[$i]);
            }
        }

        // Reset the array keys
        $aColumns = array_values($aColumns);

        // If amount exceeds largest column name, return last column value.
        if ($iAmount > $aColumns[count($aColumns) - 1]) {
            $sLastColumnName = $aColumns[count($aColumns) - 1];
            $fMaxValue = Number::create($this->{$sLastColumnName})->getAsFloat();

            return $fMaxValue;
        }

        // If amount is below smallest column name, return first column value.
        if ($iAmount <= $aColumns[0]) {
            $sFirstColumnName = $aColumns[0];
            $fMinValue = Number::create($this->{$sFirstColumnName})->getAsFloat();

            return $fMinValue;
        }

        foreach ($aColumns as $i => $sColumnName) {
            if ($iAmount <= (int)$sColumnName) {
                $iMaxAmount = (int)$sColumnName;
                break;
            }

            $iMinAmount = (int)$sColumnName;
        }

        $iMinValue = Number::create($this->{$iMinAmount})->getAsFloat();
        $iMaxValue = Number::create($this->{$iMaxAmount})->getAsFloat();

        /**
         * Get interpolated value between two columns.
         * https://de.wikipedia.org/wiki/Interpolation_(Mathematik)#Lineare_Interpolation
         */
        $fInterpolatedValue = $iMinValue + ($iMaxValue - $iMinValue) / ($iMaxAmount - $iMinAmount) * ($iAmount - $iMinAmount);

        return $fInterpolatedValue;
    }

    /*
    public static function getPriceComponents($sFieldName, $aOptions)
    {
        $oCollection = Collection::getCurrent();
        $aOptionPrices = array();

        foreach ($aOptions as $sOption) {
            $oNewConfiguration = new ProductConfiguration();
            $oNewConfiguration->generateFromCollection();
            $oNewConfiguration->$sFieldName = $sOption;

            $oNewProduct = new Product($oNewConfiguration);

            $fNewTotal = $oNewProduct->getTotal() - (float) $oCollection->total;
            $fNewTotal = ($fNewTotal > -0.01 && $fNewTotal < 0.01) ? 0 : $fNewTotal;
            $aOptionPrices[$sOption]['total']['gross'] = $fNewTotal;
            $aOptionPrices[$sOption]['total']['net'] = $fNewTotal / ($oNewProduct->getTax() + 100) * 100;

            $fNewTotalPer100 = $oNewProduct->getTotalPer100() - (float) $oCollection->totalPer100;
            $fNewTotalPer100 = ($fNewTotalPer100 > -0.01 && $fNewTotalPer100 < 0.01) ? 0 : $fNewTotalPer100;
            $aOptionPrices[$sOption]['totalPer100']['gross'] = $fNewTotalPer100;
            $aOptionPrices[$sOption]['totalPer100']['net'] = $fNewTotalPer100 / ($oNewProduct->getTax() + 100) * 100;

            unset($oNewConfiguration);
            unset($oNewProduct);
        }

        return $aOptionPrices;
    }
    */

    public static function addPriceToAntifreezeLabel($aOptions)
    {
        $oCollection = Collection::getCurrent();
        $aOptionPrices = array();

        foreach ($aOptions as $sOption) {
            $oNewConfiguration = new ProductConfiguration();
            $oNewConfiguration->generateFromCollection();

            $oNewProduct = new Product($oNewConfiguration);
            $fOptionPrice = $oNewProduct->getAntifreezePrice() * (int) $sOption;

            $aOptionPrices[$sOption]['total']['net'] = $fOptionPrice;
            $aOptionPrices[$sOption]['total']['gross'] = $fOptionPrice * (1 + $oNewProduct->getTax() / 100);
        }

        return $aOptionPrices;
    }

    public static function addPriceToHoseLabel($aOptions)
    {
        $oCollection = Collection::getCurrent();
        $aOptionPrices = array();

        foreach ($aOptions as $sOption) {
            $oNewConfiguration = new ProductConfiguration();
            $oNewConfiguration->generateFromCollection();
            $oNewConfiguration->hose = $sOption;

            $oNewProduct = new Product($oNewConfiguration);
            $fOptionPrice = $oNewProduct->getHoseSurcharge();

            $aOptionPrices[$sOption]['total']['net'] = $fOptionPrice;
            $aOptionPrices[$sOption]['total']['gross'] = $fOptionPrice * (1 + $oNewProduct->getTax() / 100);
        }

        return $aOptionPrices;
    }

    public static function addPriceToOilTypeLabel($aOptions)
    {
        $oCollection = Collection::getCurrent();
        $aOptionPrices = array();

        foreach ($aOptions as $sOption) {
            $oNewConfiguration = new ProductConfiguration();
            $oNewConfiguration->generateFromCollection();
            $oNewConfiguration->oilType = $sOption;

            try {
                $oNewProduct = new Product($oNewConfiguration);
                $fOptionPrice = $oNewProduct->getOilTypeSurcharge();

                $aOptionPrices[$sOption]['total']['net'] = $fOptionPrice;
                $aOptionPrices[$sOption]['total']['gross'] = $fOptionPrice * (1 + $oNewProduct->getTax() / 100);
            } catch (\Exception $e) {

            }
        }

        return $aOptionPrices;
    }

    public static function addPriceToShippingLabel($aOptions)
    {
        $oCollection = Collection::getCurrent();
        $aOptionPrices = array();

        foreach ($aOptions as $sOption) {
            $oNewConfiguration = new ProductConfiguration();
            $oNewConfiguration->generateFromCollection();
            $oNewConfiguration->shipping = $sOption;

            $oNewProduct = new Product($oNewConfiguration);
            $fOptionPrice = $oNewProduct->getShippingSurcharge();

            $aOptionPrices[$sOption]['total']['net'] = $fOptionPrice;
            $aOptionPrices[$sOption]['total']['gross'] = $fOptionPrice * (1 + $oNewProduct->getTax() / 100);
        }

        return $aOptionPrices;
    }

    public static function addPriceToPaymetLabel($aOptions)
    {
        $oCollection = Collection::getCurrent();
        $aOptionPrices = array();

        foreach ($aOptions as $sOption) {
            $oNewConfiguration = new ProductConfiguration();
            $oNewConfiguration->generateFromCollection();
            $oNewConfiguration->payment = $sOption;

            $oNewProduct = new Product($oNewConfiguration);
            $fOptionPrice = $oNewProduct->getPaymentSurcharge();

            $aOptionPrices[$sOption]['total']['net'] = $fOptionPrice;
            $aOptionPrices[$sOption]['total']['gross'] = $fOptionPrice * (1 + $oNewProduct->getTax() / 100);
        }

        return $aOptionPrices;
    }

}