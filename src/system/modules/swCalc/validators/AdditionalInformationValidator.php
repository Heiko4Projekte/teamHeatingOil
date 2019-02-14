<?php

namespace Slashworks\SwCalc\Validators;

use Contao\Input;
use Contao\Module;
use Haste\Form\Form;

/**
 * Class PaymentValidator
 * @package Slashworks\SwCalc\Classes
 */
class AdditionalInformationValidator extends Validator
{


    /**
     * @return mixed
     */
    protected function checkValidation(){

        $this->setValidStatus('true');

    }



}