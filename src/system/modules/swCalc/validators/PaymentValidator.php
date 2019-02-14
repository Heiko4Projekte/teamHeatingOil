<?php

namespace Slashworks\SwCalc\Validators;

use Contao\Input;
use Contao\Module;
use Haste\Form\Form;
use Contao\ModuleModel;
use Slashworks\SwCalc\Helper\zmzFormCreator;
use Slashworks\SwCalc\Modules\ZmzAccountForm;


/**
 * Class PaymentValidator
 * @package Slashworks\SwCalc\Classes
 */
class PaymentValidator extends Validator
{


    /**
     * @return mixed
     */
    protected function checkValidation(){

        switch ($this->collection->payment){

            case 'invoice':
                $this->invoice();

                break;

            case 'zmzAccount':
                $this->zmz();

                break;


            default:
                $this->setValidStatus('true');

        }

    }


    /**
     *
     */
    protected function invoice(){

        if($this->collection->birthday){
            $this->setValidStatus('true');
            return;
        }

        $this->oForm = new Form('birthdayForm', 'POST', function ($oHaste) {
            return Input::post('FORM_SUBMIT') === $oHaste->getFormId();
        });

        $this->oForm->setFormActionFromUri('ajaxcontroller.html?action=setBirthday');

        $this->oForm->bindModel($this->collection);

        $this->oForm->addFormField('birthday', array
        (
            'label' => 'Geburtsdatum (TT.MM.JJJJ)',
            'inputType' => 'text',
            'eval' => array
            (
                'mandatory' => true,
                'template' => 'widget_birthday',
                'placeholder' => 'TT.MM.JJJJ',
                'class' => 'widget-birthday',
                'additionalText' => $GLOBALS['TL_LANG']['birthdayForm']['additionalText']
            )
        ));

        $this->oForm->addFormField('submit', array
        (
            'label' => 'Änderungen übernehmen {{svg::arrow-right}}',
            'inputType' => 'submit',
            'eval' => array
            (
                'class' => 'button-mega'
            )
        ));

        $this->data = $this->oForm;

    }


    /**
     *
     */
    protected function zmz(){

        $oZmzForm = new zmzFormCreator();
        $this->oForm = $oZmzForm->create();
        $this->oForm->setFormActionFromUri('ajaxcontroller.html?action=setzmz');

        $this->data = $this->oForm;

    }


}