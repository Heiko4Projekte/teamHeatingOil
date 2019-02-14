<?php

namespace Slashworks\SwCalc\Validators;

use Haste\Form\Validator\ValidatorInterface;

/**
 * Custom validator for phone and mobile field.
 *
 * Class PhoneOrMobile
 * @package Slashworks\SwCalc\Validators
 */
class PhoneOrMobile implements ValidatorInterface
{

  /**
   * Add errors to the phone and mobile widget if they are both not filled.
   *
   * @param mixed $sValue
   * @param \Widget $oWidget
   * @param \Haste\Form\Form $oForm
   *
   * @return mixed
   */
  public function validate($sValue, $oWidget, $oForm)
  {
    $oPhoneWidget = $oForm->getWidget('phone');
    $oMobileWidget = $oForm->getWidget('mobile');

    if (empty($oPhoneWidget->value) && empty($oMobileWidget->value)) {
      $oPhoneWidget->addError('Es muss entweder eine Festnetznummer oder eine Mobilnummer angegeben werden.');
      $oMobileWidget->addError('Es muss entweder eine Festnetznummer oder eine Mobilnummer angegeben werden.');
    }

    return $sValue;
  }

}