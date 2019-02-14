<?php

namespace Slashworks\SwCalc\Models;

/**
 * Class ProductConfiguration
 *
 * @package Slashworks\SwCalc\Models
 */
class ProductConfiguration extends \Model
{


    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_calc_product_configuration';

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * @return mixed
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return mixed
     */
    public function getHose()
    {
        return $this->hose;
    }

    /**
     * @return mixed
     */
    public function getOilType()
    {
        return $this->oilType;
    }

    /**
     * @return mixed
     */
    public function getTanker()
    {
        return $this->tanker;
    }

    /**
     * @return mixed
     */
    public function getAntifreeze()
    {
        // If only 1 unloadinpoint is entered, take antifreeze amount of this unloadinpoint.
        if (Collection::getCurrent()->unloadingPoints == 1) {
            return $this->antifreeze;
        }

        // Get combined antifreeze amount of main address and all other unloading points.
        $iChildAntifreezeAmount = UnloadingPoint::getAntifreezeAmountsByPid(Collection::getCurrent()->id);

        if ($iChildAntifreezeAmount === null) {
            return $this->antifreeze;
        }

        return $this->antifreeze + $iChildAntifreezeAmount;
    }

    /**
     * @return mixed
     */
    public function getLabelGroup()
    {
        return $this->labelGroup;
    }

    /**
     * @return mixed
     */
    public function getUspField()
    {
        return $this->uspField;
    }


    /**
     * Get all dca fields that are marked for vario usage.
     *
     * @return array
     */
    public static function getUspFields()
    {
        $aVarioFields = array();

        foreach ($GLOBALS['TL_DCA'][static::$strTable]['fields'] as $sFieldName => $aField) {
            if ($aField['eval']['varioField'] !== true) {
                continue;
            }

            $aVarioFields[] = $sFieldName;
        }

        return $aVarioFields;
    }

    public function isBestPrice()
    {
        return ($this->getLabelGroup() === 'bestprice');
    }

    /**
     * Generate a product configuration from the current collection.
     */
    public function generateFromCollection()
    {
        $oCollection = Collection::getCurrent();

        $this->title = $oCollection->title;
        $this->amount = $oCollection->amount;
        $this->shipping = $oCollection->shipping;
        $this->payment = $oCollection->payment;
        $this->hose = $oCollection->hose;
        $this->oilType = $oCollection->oilType;
        $this->labelGroup = ($oCollection->labelGroup) ? $oCollection->labelGroup : 'vario';
        $this->tanker = $oCollection->tanker;
        $this->uspField = $oCollection->uspField;
        $this->postal = $oCollection->postal;
        $this->unloadingPoints = $oCollection->unloadingPoints;
        $this->adr = $oCollection->adr;
        $this->antifreeze = $oCollection->antifreeze;
    }


}