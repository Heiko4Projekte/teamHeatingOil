<?php

namespace Slashworks\SwCalc\Modules;

use Contao\BackendTemplate;
use Contao\Environment;
use Contao\Input;
use Contao\Module;
use Contao\System;
use Contao\Widget;
use Haste\Form\Form;
use Slashworks\SwCalc\Classes\FormGroups;
use Slashworks\SwCalc\Helper\JavascriptHelper;
use Slashworks\SwCalc\Models\Collection;
use Slashworks\SwCalc\Models\Pricing;
use Slashworks\SwCalc\Models\UnloadingPoint;
use Slashworks\SwCalc\Validators\AmountValidator;
use Slashworks\SwCalc\Validators\AppValidator;
use Slashworks\SwCalc\Validators\PostalValidator;
use Slashworks\SwCalc\Models\Label;


/**
 * Class ProductConfigurator
 * @package Slashworks\SwCalc\Modules
 */
class ProductConfigurator extends Module
{

    /**
     * The template name.
     *
     * @var string
     */
    protected $strTemplate = 'mod_calc_productconfigurator';

    /**
     * The haste form object.
     *
     * @var Form
     */
    protected $oForm;

    /**
     * The collection object.
     *
     * @var Collection
     */
    protected $oCollection;

    /**
     * @return string
     */
    public function generate()
    {
        if (TL_MODE === 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['productconfigurator'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }
        else {
            //validate session and call
            try {
                $App = new AppValidator();
                $App->validateActiveState();
                $App->validateGetParamsAndInit();
            } catch (\Exception $exception) {
                System::log($exception->getMessage() . '. In "' . $exception->getFile() . '" on line ' . $exception->getLine(),
                    __METHOD__, TL_ERROR);
            }

        }

        return parent::generate();
    }

    /**
     * Parse the template.
     */
    protected function compile()
    {
        // Create a new haste form.
        $this->oForm = new Form('productconfigurator_form', 'POST', function ($oHaste) {
            return Input::post('FORM_SUBMIT') === $oHaste->getFormId();
        });


        // Bind the form to the current collection.
        $this->oCollection = Collection::getCurrent();
        $this->oForm->bindModel($this->oCollection);

        // Disable html5 form validation.
        $this->oForm->setNoValidate(true);

        // delete dca hook  - probs with missing plz
        unset($GLOBALS['TL_HOOKS']['getAttributesFromDca']['includePricesInLabels']);

        // Add all fields.
        $this->addFields();

        $this->oForm->addValidator('amount', new AmountValidator());
//        $this->oForm->addValidator('postal', new PostalValidator());

        $config = \Slashworks\SwCalc\Models\Configuration::getActive();
        $GLOBALS['TL_CALC']['fallbackPostal'] = $config->fallbackPostal;
        $this->oForm->getWidget('postal')->value = ($this->oCollection->postal) ? $this->oCollection->postal : $GLOBALS['TL_CALC']['fallbackPostal'];

        // Validate form if it has been submitted.
        if ($this->oForm->isSubmitted()) {
            $this->validateForm();
        }

        $this->getFormFieldValues();

        $this->Template->form = $this->oForm->generate();

        // Add javascript for showing errors in form fields, e. g. amount and postal field.
        $oJavascriptHelper = new JavascriptHelper();
        $GLOBALS['TL_BODY']['formFieldTooltip'] = $oJavascriptHelper->getTooltipScript();

        // Add data to global collection data array for google tag manager
        global $objPage;
        if ($objPage->collectionData) {
            $collectionData = $objPage->collectionData;
            $collectionData = array_merge($collectionData, array('step' => 10));
        } else {
            $collectionData = array('step' => 10);
        }
        $objPage->collectionData = $collectionData;
    }

    /**
     * Add all necessary fields.
     */
    protected function addFields()
    {
        $this->addFixedFields();
        $this->addVarioFields();
        $this->addSubmitField();
    }

    /**
     * Add all fields that are flagged as "fixed".
     */
    protected function addFixedFields()
    {
        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'fixed');
        $this->oForm = FormGroups::addFormGroupFields($this->oForm, 'fixedField', '');
        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'fixed');

    }

    /**
     * Add all fields that are flagged as "vario".
     */
    protected function addVarioFields()
    {

        $this->oForm = FormGroups::addWrapperStart($this->oForm, 'vario');
        $this->oForm = FormGroups::addFormGroupFields($this->oForm, 'varioField', '');
        $this->oForm = FormGroups::addWrapperStop($this->oForm, 'vario');
    }

    /**
     * Add a submit field.
     */
    protected function addSubmitField()
    {
        // TODO: Use language variable for label.
        $this->oForm->addSubmitFormField('submit', 'Preis berechnen');
    }

    /**
     * Validate the form.
     */
    protected function validateForm()
    {
        if ($this->oForm->validate()) {

            $this->oCollection->noSelection = false;
            $this->oCollection->showOriginalValues = false;
            $this->oCollection->save();
        } else {

        }

    }

    protected function getFormFieldValues()
    {
        $oFormWidgets = $this->oForm->getWidgets();
        $aFormFields = array();

        $oWidgetPostal = $this->oForm->getWidget('postal');
        $oWidgetAmount = $this->oForm->getWidget('amount');
        $oWidgetUnloadingPoints = $this->oForm->getWidget('unloadingPoints');

        $aFormFields[] = array('label' => $oWidgetPostal->label, 'value' => $oWidgetPostal->value);
        $aFormFields[] = array('label' => $oWidgetAmount->label, 'value' => $oWidgetAmount->value . ' Liter');
        $aFormFields[] = array('label' => $oWidgetUnloadingPoints->label, 'value' => $GLOBALS['TL_LANG']['tl_calc_collection']['unloadingPoint'][$oWidgetUnloadingPoints->value]);

        $this->Template->formFieldValues = $aFormFields;
    }

}