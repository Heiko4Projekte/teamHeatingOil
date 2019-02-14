<?php

namespace Slashworks\SwCalc\Helper;

use Contao\Controller;
use Contao\FrontendTemplate;
use Contao\Input;
use Haste\Form\Form;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Models\Pricing;
use Slashworks\SwCalc\Models\UnloadingPoint;

class UnloadingPointsFormCreator
{

    /**
     * @var Form
     */
    protected $oForm;

    /**
     * @var Collection
     */
    protected $oCollection;

    /**
     * Flag to indicate, whether mainInformation area should be added to the form.
     *
     * @var boolean
     */
    protected $bAddMainInformation;

    /**
     * @var string
     */
    protected $sParentTable = 'tl_calc_collection';

    public function create($bAddMainInformation = true)
    {

        $this->bAddMainInformation = $bAddMainInformation;
        $this->oCollection = Collection::getCurrent();


        $this->oForm = new Form('unloading_points_form', 'POST', function ($oHaste) {
            return Input::post('FORM_SUBMIT') === $oHaste->getFormId();
        });


        // Disable html5 form validation.
        $this->oForm->setNoValidate(true);

        $this->addFields();

        if ($this->bAddMainInformation) {
            $this->oForm->getWidget('antifreeze')->value = $this->oCollection->antifreeze;
        }

        return $this->oForm;
    }

    protected function addFields()
    {
        if ($this->bAddMainInformation) {
            $this->addMainInformation();
        }
        $this->addUnloadingPoints();

        $this->oForm->addFormField('submit', array
        (
            'label' => 'Weiter {{svg::arrow-right}}',
            'inputType' => 'submit'
        ));
    }

    protected function addMainInformation()
    {

        \System::loadLanguageFile('tl_calc_collection');
        \Controller::loadDataContainer('tl_calc_collection');

        $aMainInformation = array();

        $aMainInformation['headline'] = 'Lieferadresse 1';
        $aMainInformation['company'] = $this->oCollection->shippingCompany;
        $aMainInformation['name'] = $GLOBALS['TL_LANG']['tl_calc_collection']['salutation'][$this->oCollection->shippingSalutation] . ' ' . $this->oCollection->shippingFirstname . ' ' . $this->oCollection->shippingLastname;
        $aMainInformation['street'] = $this->oCollection->shippingStreet;
        $aMainInformation['postal'] = $this->oCollection->shippingPostal;
        $aMainInformation['city'] = $this->oCollection->shippingCity;

        $aMainInformation['changeUrl'] = 'ajaxcontroller.html?action=getShippingAddress';
        $oMainInformationTemplate = new FrontendTemplate('partial_unloadingpoints_maininformation');
        $oMainInformationTemplate->mainInformation = $aMainInformation;
        $oMainInformationTemplate->changeUrl = 'ajaxcontroller.html?action=getShippingAddress';

        $this->oForm->addFormField('maininformation', array(
            'inputType' => 'explanation',
            'eval' => array
            (
                'text' => $oMainInformationTemplate->parse(),
                'class' => 'unloadingpoints-main-information'
            )
        ));

        $this->oForm->addFormField('partialAmount', array(
            'label' => '1. Teilmenge',
            'inputType' => 'text',
            'value' => ($this->oCollection->partialAmount) ? $this->oCollection->partialAmount : '',
            'eval' => array
            (
                'rgxp' => 'natural',
                'minval' => Pricing::getMinAmount(),
                'mandatory' => true,
            )
        ));

        $this->oForm->addFormField('antifreeze', $GLOBALS['TL_DCA']['tl_calc_collection']['fields']['antifreeze']);


    }

    protected function addUnloadingPoints()
    {
        Controller::loadDataContainer('tl_calc_unloadingpoint');

        for ($i = 1; $i < (int) $this->oCollection->unloadingPoints; $i++) {
            $iVisibleCount = $i + 1;

            $oUnloadingPoint = UnloadingPoint::findOneByPidParentAndOrder($this->oCollection->id, $this->sParentTable, $i);

            $this->oForm->addFormField('headline_unloadingpoints_' . $i, array
            (
                'inputType' => 'explanation',
                'eval' => array
                (
                    'text' => 'Lieferadresse ' . $iVisibleCount,
                    'class' => 'form-group-headline'
                )
            ));

            $aFields = $GLOBALS['TL_DCA']['tl_calc_unloadingpoint']['fields'];
            foreach ($aFields as $sFieldName => $aField) {
                if (!isset($aField['inputType'])) {
                    continue;
                }

                $aField['eval']['order'] = $i;
                $aField['eval']['originalField'] = $sFieldName;

                // Prefill with data from alread existent unloadingPoint data.
                if ($oUnloadingPoint !== null) {
                    $aField['value'] = $oUnloadingPoint->{$sFieldName};
                } else {
                    // Prefill with data from main address.
                    if ($sFieldName === 'shippingPostal') {
                        $aField['value'] = $this->oCollection->shippingPostal;
                    }
                    if ($sFieldName === 'shippingCity') {
                        $aField['value'] = $this->oCollection->shippingCity;
                    }
                }

                if ($sFieldName === 'partialAmount') {
                    $aField['label'] = $iVisibleCount . '. Teilmenge';
                }

                if ($sFieldName === 'shippingStreet') {
                    $aField['eval']['autocompleterValue'] = 'ctrl_shippingStreet_' . $i;
                } else if ($sFieldName === 'shippingPostal') {
                    $aField['eval']['autocompleterValue'] = 'ctrl_shippingPostal_' . $i;
                } else if ($sFieldName === 'shippingCity') {
                    $aField['eval']['autocompleterValue'] = 'ctrl_shippingCity_' . $i;
                }

                $this->oForm->addFormField($sFieldName . '_' . $i, $aField);
            }
        }

    }

}