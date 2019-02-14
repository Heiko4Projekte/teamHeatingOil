<?php

namespace Slashworks\SwCalc\Modules;

use Contao\BackendTemplate;
use Contao\Controller;
use Contao\Input;
use Contao\Module;
use Contao\PageModel;
use Contao\Widget;
use Haste\Form\Form;
use Slashworks\SwCalc\Helper\UnloadingPointsFormCreator;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Models\UnloadingPoint;

class PaymentForm
{

    /**
     * The template name.
     *
     * @var string
     */
    protected $strTemplate = '';

    /**
     * @return string
     * @throws \Exception
     */
    public function generate()
    {

        return parent::generate();
    }

    /**
     * Parse the template.
     */
    public function create()
    {

        $collection = Collection::getCurrent();
        $paymentOptions = $GLOBALS['TL_CALC']['paymentValues'];

        //remove zmz account
        if($collection->unloadingPoints > 1){
            foreach ($paymentOptions as $key => $option){
                if($option == 'zmzAccount'){
                    unset($paymentOptions[$key]);
                }
            }
            unset($paymentOptions['zmzAccount']);
        }

        $this->oForm = new Form('form', 'POST', function ($oHaste) {
            return Input::post('FORM_SUBMIT') === $oHaste->getFormId();
        });

        $this->oForm->bindModel(Collection::getCurrent());
//        $this->oForm->setFormActionFromUri('ajaxcontroller.html?action=getPayment');

        $this->generateSelectableOptions($paymentOptions, 'payment');
        return $this->oForm;
    }


    /**
     * Generate selectable radio fields, including prices.
     *
     * @param $group
     * @param $name
     */
    protected function generateSelectableOptions($group, $name)
    {

        foreach ($group as $item) {
            $options[$item] = $item;
        }

        //add checkbox
        $this->oForm->addFormField($name, array
        (
            'inputType' => 'radio',
            'options'   => $options,
            'eval'      => array(
                'template' => 'widget_radioExplanation',
                'addPriceToLabel' => true
            ),
        ));

        $this->oForm->addFormField('submit', array
        (
            'label'     => 'Änderungen übernehmen {{svg::arrow-right}}',
            'inputType' => 'submit',
            'eval'      => array
            (
                'class' => 'button-mega',
            ),
        ));

    }


}