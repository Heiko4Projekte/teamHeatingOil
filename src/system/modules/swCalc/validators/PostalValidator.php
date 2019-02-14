<?php

namespace Slashworks\SwCalc\Validators;

use Contao\Widget;
use Haste\Form\Validator\ValidatorInterface;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Models\Pricing;

class PostalValidator implements ValidatorInterface
{

    /**
     * @param mixed $sValue
     * @param \Widget $oWidget
     * @param \Haste\Form\Form $oForm
     * @return mixed
     * @throws \Exception
     */
    public function validate($sValue, $oWidget, $oForm)
    {
        if ($oWidget->mandatory || !empty($sValue)) {

            if(self::checkCharacterError($oForm->fetch($oWidget->name))){
                $oWidget->addError(self::checkCharacterError($oForm->fetch($oWidget->name)));
            }

            if(self::checkDeliveryError($oForm->fetch($oWidget->name))){
                $oWidget->addError(self::checkDeliveryError($oForm->fetch($oWidget->name)));
            }
        }

        return $sValue;
    }


    /**
     * @param $iPostal
     * @return bool
     * @throws \Exception
     */
    public static function checkPostalError($iPostal)
    {
        if(self::checkCharacterError($iPostal) !== false){
            return self::checkCharacterError($iPostal);
        }

        if(self::checkDeliveryError($iPostal) !== false){

            return self::checkDeliveryError($iPostal);
        }

        return false;

    }


    /**
     * @param $iPostal
     * @return bool
     */
    public static function checkCharacterError($iPostal)
    {

        if (!preg_match('/^[0-9]{5}$/', $iPostal)) {
            return $GLOBALS['TL_LANG']['ERR']['postal']['invalid'];
        }

        return false;
    }


    /**
     * @param $iPostal
     * @return bool
     * @throws \Exception
     */
    public static function checkDeliveryError($iPostal)
    {
        if (Pricing::findByPostal($iPostal) === null) {
            // Check if postal is defined in the pricing table.
            return $GLOBALS['TL_LANG']['ERR']['postal']['outOfScope'];
        }

        return false;
    }

}