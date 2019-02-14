<?php

namespace Slashworks\SwCalc\Models;

use Contao\DataContainer;
use Contao\FrontendUser;
use Contao\Model;
use Contao\Session;
use Contao\SessionModel;
use Slashworks\SwCalc\Classes\Formatter;

/**
 * Class Collection
 *
 * @property string birthday
 * @property string payment
 * @property string shippingSalutation
 * @property string shippingCompany
 * @property string shippingFirstname
 * @property string shippingLastname
 * @property string shippingStreet
 * @property string shippingPostal
 * @property string shippingCity
 * @property boolean shippingAddressEqualsBillingAddress
 * @property string billingSalutation
 * @property string billigCompany
 * @property string billigFirstname
 * @property string billingLastname
 * @property string billingStreet
 * @property string billingPostal
 * @property string billingCity
 * @property string email
 * @property string phone
 * @property string mobile
 * @property string hose
 * @property string tanker
 * @property string notes
 * @property boolean acceptAgb
 * @property boolean sendNews
 * @property string postal
 * @property string unloadingPoints
 * @property string oilType
 * @property string shipping
 * @property string total
 * @property string totalPer100
 * @property string amountSurcharge
 * @property string shippingSurcharge
 * @property string shippingDate
 * @property string paymentSurcharge
 * @property string hoseSurcharge
 * @property string oilTypeSurcharge
 * @property string zmzCustomerId
 * @property string zmzChildSalutation
 * @property string zmzChildFirstname
 * @property string zmzChildLastname
 * @property string paymentmethod
 * @property string zmzChildBirthday
 * @property string sepaSalutation
 * @property string sepaFirstname
 * @property string sepaLastname
 * @property string sepaMonthlyPrice
 * @property string sepaDueDay
 * @property boolean sepaRecurringPayment
 * @property string sepaBank
 * @property string sepaBic
 * @property string sepaIban
 * @property boolean sepaAcceptPayment
 * @property string orderId
 * @property boolean noSelection
 * @property boolean showOriginalValues
 * @property string subTotal
 * @property string adr
 * @property string vat
 * @property string antifreeze
 * @property string subTotalPerAmount
 * @property string antifreezeSurcharge
 * @property string amount
 * @property string partialAmount
 *
 * @package Slashworks\SwCalc\Models
 */
class Collection extends Model
{

    /**
     * Table name.
     *
     * @var string
     */
    protected static $strTable = 'tl_calc_collection';

    /**
     * Modify the current row before it is stored in the database
     *
     * @param array $arrSet The data array
     *
     * @return array The modified data array
     */
    protected function preSave(array $arrSet)
    {
        // Reset partial amount for main address if no additional unloading points are selected.
        if ($arrSet['unloadingPoints'] == '1') {
            $arrSet['partialAmount'] = '';
        }

        // Update the tstamp every time a collection is saved.
        $arrSet['tstamp'] = time();

        return $arrSet;
    }

    /**
     * Automatically set postal value to shippingPostal field.
     *
     * @param $sValue
     * @param $oDc
     *
     * @return mixed
     */
    public function saveCallbackPostalField($sValue, $oDc)
    {
        $oCollection = static::getCurrent();
        $oCollection->shippingPostal = $sValue;

        return $sValue;
    }

    /**
     * Automatically set shippingPostal value to postal field.
     *
     * @param $sValue
     * @param $oDc
     *
     * @return mixed
     */
    public function saveCallbackShippingPostalField($sValue, $oDc)
    {
        $oCollection = static::getCurrent();
        $oCollection->postal = $sValue;

        return $sValue;
    }

    /**
     * Get the current collection, defined by the session id.
     *
     * @return Collection
     */
    public static function getCurrent()
    {
        $oCurrentCollection = static::findBySessionId(session_id());

        if ($oCurrentCollection === null) {
            $oCurrentCollection = static::createNewCollection();
        }

        return $oCurrentCollection;
    }


    /**
     * Automatically set shippingPostal value to postal field.
     *
     *
     * @return mixed
     */
    public static function getOrderId()
    {
        $query = "SELECT max(orderId) as orderId from tl_calc_collection";
        $result = \Database::getInstance()->query($query);

        if ($result->orderId < 394827) {
            $result->orderId = 394827;
        }

        return $result->orderId + 1;
    }

    /**
     * Create a new collection and fill it with default values.
     *
     * @return Collection
     */
    protected static function createNewCollection()
    {
        $oCollection = new Collection();
        $oCollection->setRow(array(
            'tstamp'    => time(),
            'sessionID' => session_id(),
        ));

        static::prefillCollectionWithDefaultValues($oCollection);

        $oCollection->save();

        return $oCollection;
    }

    /**
     * Fill a collection with default values.
     *
     * @param $oCollection
     */
    protected static function prefillCollectionWithDefaultValues($oCollection)
    {

        // Get ip based postal.
//        $oGeoInformation = new GeoInformation();
//        $sPostalByIp = $oGeoInformation->getPostalByIp();
        // Fill postal of collection with ip based postal or a default value.
//        $oCollection->postal = (!empty($sPostalByIp)) ? $sPostalByIp : $GLOBALS['TL_CALC']['fallbackPostal'];

        $config = \Slashworks\SwCalc\Models\Configuration::getActive();
        $oCollection->postal = $config->fallbackPostal;

        $oCollection->type = 'cart';
        $oCollection->amount = '3000';
        $oCollection->unloadingPoints = '1';
        $oCollection->shipping = 'deliveryStandard';
        $oCollection->payment = 'ecCard';
        $oCollection->hose = 'default';
        $oCollection->oilType = 'default';
        $oCollection->labelGroup = 'vario';
        $oCollection->tanker = 'default';
        $oCollection->antifreeze = 0;
        $oCollection->noSelection = 1;
    }

    /**
     * Check if the collection has the minimum configurations set.
     *
     * @return bool
     */
    public static function hasConfiguration()
    {
        $oCollection = static::getCurrent();

        return (!empty($oCollection->oilType) && !empty($oCollection->shipping) && !empty($oCollection->payment));
    }

    public function getAmount()
    {
        return static::getCurrent()->amount;
    }

    /**
     * Get the selected payment method.
     *
     * @return mixed
     */
    public function getPayment()
    {
        return static::getCurrent()->payment;
    }

    /**
     * Get the selected oil type.
     *
     * @return mixed
     */
    public function getOilType()
    {
        return static::getCurrent()->oilType;
    }

    public function getTotal()
    {
        return static::getCurrent()->total;
    }

    /**
     * @return mixed
     */
    public function getAntifreeze()
    {
        // If only 1 unloadingpoint is entered, take antifreeze amount of this unloadingpoint.
        if (static::getCurrent()->unloadingPoints == 1) {
            return static::getCurrent()->antifreeze;
        }

        // Get combined antifreeze amount of main address and all other unloading points.
        $iChildAntifreezeAmount = UnloadingPoint::getAntifreezeAmountsByPid(static::getCurrent()->id);
        if ($iChildAntifreezeAmount === null) {
            return static::getCurrent()->antifreeze;
        }

        return static::getCurrent()->antifreeze + $iChildAntifreezeAmount;
    }

    public function hasAntifreeze()
    {
        // If only 1 unloadingpoint is entered, take antifreeze amount if this unloadingpoint.
        if (static::getCurrent()->unloadingPoints == 1 && static::getCurrent()->antifreeze > 0) {
            return true;
        }

        // Get combined antifreeze amount of main address and all other unloading points.
        $iChildAntifreezeAmount = UnloadingPoint::getAntifreezeAmountsByPid(static::getCurrent()->id);
        return ($iChildAntifreezeAmount + static::getCurrent()->unloadingPoints > 0);
    }

    /**
     * Find a collection by its session id.
     *
     * @param       $sSessionId
     * @param array $aOptions
     *
     * @return mixed
     */
    public static function findBySessionId($sSessionId, array $aOptions = array())
    {
        $t = static::$strTable;

        return static::findOneBy('sessionId', $sSessionId, $aOptions);
    }

    /**
     * Get all configurable fields.
     *
     * @return array
     */
    public static function getConfigurableFields()
    {
        \Controller::loadDataContainer('tl_calc_collection');
        $aConfigurableFields = array();

        foreach ($GLOBALS['TL_DCA']['tl_calc_collection']['fields'] as $sFieldName => $aField) {
            if (is_array($aField['eval']['group'])) {
                if (in_array('fixedField', $aField['eval']['group']) || in_array('varioField',
                        $aField['eval']['group'])) {
                    $aConfigurableFields[] = $sFieldName;
                }
            }
        }

        return $aConfigurableFields;
    }

    /**
     * Get all vario fields.
     *
     * @return array
     */
    public static function getVarioFields()
    {
        \Controller::loadDataContainer('tl_calc_collection');

        $aVarioFields = array();

        foreach ($GLOBALS['TL_DCA']['tl_calc_collection']['fields'] as $sFieldName => $aField) {

            if (is_array($aField['eval']['group'])) {
                if (in_array('varioField', $aField['eval']['group'])) {
                    $aVarioFields[] = $sFieldName;
                }
            }
        }

        return $aVarioFields;
    }

    /**
     * Get all defined values for a field for dca.
     *
     * @param DataContainer $oDc
     *
     * @return array
     */
    public static function getValuesByFieldForDca(DataContainer $oDc)
    {
        $aValues = array();
        $oVarioFieldConfiguration = VarioValue::findByPk($oDc->activeRecord->id);

        if ($oVarioFieldConfiguration === null) {
            return $aValues;
        }

        $sField = $oVarioFieldConfiguration->varioField;

        if (empty($sField)) {
            return $aValues;
        }

        $aValues = $GLOBALS['TL_CALC'][$sField . 'Values'];

        return $aValues;
    }

    public function saveNewProductInCollection(Product $oProduct)
    {
        /** @var ProductConfiguration $oConfiguration */
        $oConfiguration = $oProduct->getConfiguration();

        // Which data should be save in the collection.
        $aConfigurationData = array
        (
            'title'             => $oConfiguration->getTitle(),
            'amount'            => $oConfiguration->getAmount(),
            'shipping'          => $oConfiguration->getShipping(),
            'payment'           => $oConfiguration->getPayment(),
            'hose'              => $oConfiguration->getHose(),
            'oilType'           => $oConfiguration->getOilType(),
            'labelGroup'        => ($oConfiguration->getLabelGroup()) ? $oConfiguration->getLabelGroup() : 'vario',
            'uspField'          => $oConfiguration->getUspField(),
            'postal'            => $oConfiguration->postal,
            'unloadingPoints'   => $oConfiguration->unloadingPoints,
            'shippingDate'      => $oProduct->getShippingDate(),
            'total'             => $oProduct->getTotal(),
            'totalPer100'       => $oProduct->getTotalPer100(),
            'subTotal'          => $oProduct->getSubTotal(),
            'subTotalPerAmount' => $oProduct->getSubTotalPerAmount(),
            'subTotalPer100'    => $oProduct->getSubTotalPer100(),
            'vat'               => $oProduct->getVat(),
            'adr'               => $oProduct->getAdr(),
            'antifreezeSurcharge' => $oProduct->getAntifreezeSurcharge(),
            'antifreezePrice' => $oProduct->getAntifreezePrice()
        );

        // Consider sepa fields when zmzAccount has been chosen as payment method.
        if ($oConfiguration->getPayment() === 'zmzAccount') {
            $aConfigurationData['yearlyAmount'] = $oProduct->getTotal();
            $aConfigurationData['yearlyPrice'] = Formatter::formatPrice($oProduct->getTotal());
            // Initial price for sepa equals half the total value.
            $aConfigurationData['sepaInitialPrice'] = Formatter::formatPrice($oProduct->getTotal() / 2);
            $aConfigurationData['sepaMonthlyPrice'] = Formatter::formatPrice($oProduct->getTotal() / 12);
        }

        $oCollection = static::getCurrent();

        foreach ($aConfigurationData as $sKey => $mValue) {
            $oCollection->{$sKey} = $mValue;
        }

        $oCollection->save();
    }

    public function updatePriceInCollection()
    {
        $oProduct = new Product();

        $this->total = $oProduct->getTotal();
        static::save();
    }

    /**
     * Update the collection if a field has been changed that influences the price.
     */
    public static function updateCollection($bForceChange = false)
    {



        // Define all fields that influence the price.
        $aFieldsToCheck = array
        (
            'amount',
            'partialAmount',
            'shippingPostal',
            'shipping',
            'payment',
            'hose',
            'tanker',
            'unloadingPoints',
            'oilType',
            'antifreeze',
        );

        $aBestPriceCheckFields = array
        (
            'shipping',
            'payment',
            'hose',
            'oilType',
            'tanker'
        );

        $oCollection = static::getCurrent();
        $oCollection->recalculateAmount();

        $bHasChanges = $bForceChange;

        $oProductConfigurations = ProductConfiguration::findAll();

        foreach ($oProductConfigurations as $oProductConfiguration) {
            $bIsMatch = true;

            foreach ($aBestPriceCheckFields as $sField) {

                if ($oProductConfiguration->$sField != $oCollection->$sField) {

                    //exception cash payment
                    if($oCollection->$sField == 'cash'){
                        continue;
                    }
                    $bIsMatch = false;
                    break;
                }

            }

            if ($bIsMatch) {
                $oCollection->labelGroup = $oProductConfiguration->labelGroup;
                break;
            } else {
                $oCollection->labelGroup = 'vario';
            }

        }

        // Compare changed values to original values.
        foreach ($aFieldsToCheck as $sField) {
            if ($oCollection->originalRow()[$sField] != $oCollection->$sField) {
                $bHasChanges = true;
                break;
            }
        }

        /**
         * This implementation entails a price update every time when antifreeze has been chosen.
         * TODO: Find a way to only update price if the antifreeze values has been changed.
         */
        // Consider antifreeze
        if ($oCollection->hasAntifreeze()) {
            $bHasChanges = true;
        }

        // If a price influencing value has been changed, save new product data in collection.
        if ($bHasChanges) {
            $oCollection->save();
            $oProduct = new \Slashworks\SwCalc\Models\Product();
            $oCollection->saveNewProductInCollection($oProduct);
        } else {
            $oCollection->save();
        }
    }


    public static function recalculateAmount(){

        $oCollection = Collection::getCurrent();

        if(!$oCollection->partialAmount){
            return;
        }

        $oCollection->amount = $oCollection->partialAmount;

        //get all unloadingpoints
        $oUnloadingPoints = UnloadingPoint::findByPid($oCollection->id);

        if(null !== $oUnloadingPoints){
            foreach ($oUnloadingPoints as $unloadingPoint){
                $oCollection->amount =  $oCollection->amount + $unloadingPoint->partialAmount;
            }
        }

    }

}