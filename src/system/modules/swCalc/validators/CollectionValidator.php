<?php

namespace Slashworks\SwCalc\Validators;

use Contao\Input;
use Haste\Form\Form;
use Slashworks\SwCalc\Classes\FormGroups;
use Slashworks\SwCalc\Models\Pricing;
use Slashworks\SwCalc\Models\UnloadingPoint;


/**
 * Class PaymentValidator
 *
 * @package Slashworks\SwCalc\Classes
 */
class CollectionValidator extends Validator
{


    /**
     * @return mixed|void
     * @throws \Exception
     */
    protected function checkValidation()
    {

        //shippingAddress
        $this->checkmandatoryFields(__FUNCTION__, 'shipping');

        // shippingpostal
        $oPrice = Pricing::findByPostal($this->shippingPostal);
        if (null === $oPrice) {
            return $this->generateMessage(shipping, 'postal');
        }

        //billingaddress
        if (!$this->collection->shippingAddressEqualsBillingAddress) {
            $this->checkmandatoryFields(__FUNCTION__, 'billing');
        }

        // payment
        switch ($this->collection->payment) {

            case 'invoice':
                $this->invoice();
                break;

            case 'zmzAccount':
                $this->checkmandatoryFields(__FUNCTION__, 'zmzAccount');
                break;
        }

        $this->unloadingPoints();

        if (count($this->data) < 1) {
            $this->setValidStatus('true');
        }

    }


    /**
     *
     */
    protected function invoice()
    {

        if ($this->collection->birthday) {
            $this->setValidStatus('true');
        } else {
            $this->generateMessage(__FUNCTION__, "Missed Field Data - Birthday");
        }

    }


    protected function unloadingPoints()
    {
        // Do not throw an error if only 1 unloading point is selected.
        if ($this->collection->unloadingPoints == 1) {
            return;
        }

        if (!UnloadingPoint::savedUnloadingPointsEqualSelectedValue()) {
            $this->generateMessage(__FUNCTION__, 'Missing field data for unloading points');
        }

        if ($this->collection->payment == 'zmzAccount') {
            $this->generateMessage('invoice', 'To many unloading Points');
        }


    }


    /**
     * @param $function
     * @param $group
     */
    protected function checkmandatoryFields($function, $group)
    {

        \Controller::loadDataContainer('tl_calc_collection');
        $dca = $GLOBALS['TL_DCA']['tl_calc_collection']['fields'];

        foreach ($dca as $field => $params) {

            if (is_array($params['eval']['group'])) {

                //filter group
                if (in_array($group, $params['eval']['group'])) {
                    if ($params['eval']['mandatory']) {
                        //check collection
                        if (!$this->collection->{$field}) {
                            $this->generateMessage($group, $field);
                            $this->setValidStatus(false);
                        }
                    }

                }
            }

        }
    }


    /**
     * @param $group
     * @param $fieldName
     */
    protected function generateMessage($group, $fieldName)
    {
        $this->data[$group][] = $fieldName;
    }


}